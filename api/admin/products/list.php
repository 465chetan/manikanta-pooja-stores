<?php
// â”€â”€ GET|POST|PUT|DELETE /api/admin/products/list.php â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../helpers/response.php';
require_once __DIR__ . '/../../helpers/validator.php';
require_once __DIR__ . '/../../helpers/auth.php';

setHeaders();
requireMethod('GET', 'POST', 'PUT', 'DELETE');

$admin  = requireAdmin();
$pdo    = db();
$method = $_SERVER['REQUEST_METHOD'];

// â”€â”€ GET: List all products for admin â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€
if ($method === 'GET') {
    $page   = max(1, (int)($_GET['page'] ?? 1));
    $limit  = 20;
    $offset = ($page - 1) * $limit;
    $search = sanitize($_GET['search'] ?? '');
    $cat    = sanitize($_GET['cat'] ?? '');

    $where = ['p.is_deleted = 0']; $params = [];
    if ($search) {
        $where[] = '(p.name LIKE ? OR p.sku LIKE ?)';
        $s = "%$search%"; $params[] = $s; $params[] = $s;
    }
    if ($cat) { $where[] = 'c.slug = ?'; $params[] = $cat; }

    $whereSQL = $where ? 'WHERE ' . implode(' AND ', $where) : '';

    $total = $pdo->prepare("SELECT COUNT(*) FROM products p JOIN categories c ON c.id = p.category_id $whereSQL");
    $total->execute($params);

    $stmt = $pdo->prepare("
        SELECT p.*, c.name AS category_name, c.slug AS category_slug
        FROM products p JOIN categories c ON c.id = p.category_id
        $whereSQL ORDER BY p.id DESC LIMIT $limit OFFSET $offset
    ");
    $stmt->execute($params);
    $products = $stmt->fetchAll();
    foreach ($products as &$p) {
        $p['images'] = json_decode($p['images'] ?? '[]', true);
        $p['sizes']  = json_decode($p['sizes']  ?? '[]', true);
        $p['tags']   = json_decode($p['tags']   ?? '[]', true);
    }

    success('OK', ['products' => $products, 'total' => (int)$total->fetchColumn()]);
}

// â”€â”€ POST: Create product â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€
if ($method === 'POST') {
    $body = getBody();
    $v = Validator::make($body, [
        'category_id'    => 'required|integer',
        'name'           => 'required|string|min:3|max:255',
        'description'    => 'required|string|min:10',
        'price'          => 'required|numeric|min_val:1',
        'original_price' => 'numeric',
        'stock_qty'      => 'integer',
    ]);
    if ($v->fails()) error($v->firstError(), 422);

    // Generate slug
    $slug = preg_replace('/[^a-z0-9]+/', '-', strtolower($body['name']));
    $slug = trim($slug, '-');

    // Ensure slug unique
    $slugCheck = $pdo->prepare('SELECT id FROM products WHERE slug = ?');
    $slugCheck->execute([$slug]);
    if ($slugCheck->fetch()) $slug .= '-' . time();

    $sizes = is_array($body['sizes'] ?? null) ? $body['sizes'] : [];
    $base_price = (float)$body['price'];
    $base_orig  = !empty($body['original_price']) ? (float)$body['original_price'] : null;

    if (!empty($sizes) && is_array($sizes[0])) {
        $min_price = null;
        foreach ($sizes as $s) {
            $p = (float)($s['price'] ?? 0);
            if ($min_price === null || $p < $min_price) {
                $min_price = $p;
                $base_orig = !empty($s['original_price']) ? (float)$s['original_price'] : null;
            }
        }
        if ($min_price !== null) $base_price = $min_price;
    }

    $stmt = $pdo->prepare("
        INSERT INTO products (category_id, name, telugu_name, slug, description, price, original_price, stock_qty, images, sizes, tags, badge, is_featured, is_active, rating, review_count)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 1, 0, 0)
    ");
    $stmt->execute([
        (int)$body['category_id'],
        sanitize($body['name']),
        sanitize($body['telugu_name'] ?? ''),
        $slug,
        sanitize($body['description']),
        $base_price,
        $base_orig,
        (int)($body['stock_qty'] ?? 100),
        json_encode(is_array($body['images'] ?? null) ? $body['images'] : []),
        json_encode($sizes),
        json_encode(is_array($body['tags'] ?? null) ? $body['tags'] : []),
        !empty($body['badge']) ? sanitize($body['badge']) : null,
        !empty($body['is_featured']) ? 1 : 0,
    ]);

    require_once __DIR__ . '/../helpers/regenerate_js.php';
    regenerate_products_js();

    success('Product created successfully.', ['id' => (int)$pdo->lastInsertId(), 'slug' => $slug], 201);
}

// â”€â”€ PUT: Update product â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€
if ($method === 'PUT') {
    $body = getBody();
    $id   = (int)($body['id'] ?? 0);
    if (!$id) error('Product ID required.', 400);

    $chk = $pdo->prepare('SELECT id FROM products WHERE id = ?');
    $chk->execute([$id]);
    if (!$chk->fetch()) notFound('Product not found.');

    $sizes = is_array($body['sizes'] ?? null) ? $body['sizes'] : [];
    $base_price = (float)($body['price'] ?? 0);
    $base_orig  = !empty($body['original_price']) ? (float)$body['original_price'] : null;

    if (!empty($sizes) && is_array($sizes[0])) {
        $min_price = null;
        foreach ($sizes as $s) {
            $p = (float)($s['price'] ?? 0);
            if ($min_price === null || $p < $min_price) {
                $min_price = $p;
                $base_orig = !empty($s['original_price']) ? (float)$s['original_price'] : null;
            }
        }
        if ($min_price !== null) $base_price = $min_price;
    }

    $pdo->prepare("
        UPDATE products SET
            category_id = ?, name = ?, telugu_name = ?, description = ?,
            price = ?, original_price = ?, stock_qty = ?, images = ?, sizes = ?, tags = ?,
            badge = ?, is_featured = ?, is_active = ?, updated_at = NOW()
        WHERE id = ?
    ")->execute([
        (int)($body['category_id'] ?? 0),
        sanitize($body['name'] ?? ''),
        sanitize($body['telugu_name'] ?? ''),
        sanitize($body['description'] ?? ''),
        $base_price,
        $base_orig,
        (int)($body['stock_qty'] ?? 0),
        json_encode(is_array($body['images'] ?? null) ? $body['images'] : []),
        json_encode($sizes),
        json_encode(is_array($body['tags'] ?? null) ? $body['tags'] : []),
        !empty($body['badge']) ? sanitize($body['badge']) : null,
        !empty($body['is_featured']) ? 1 : 0,
        !empty($body['is_active']) ? 1 : 0,
        $id,
    ]);

    require_once __DIR__ . '/../helpers/regenerate_js.php';
    regenerate_products_js();

    success('Product updated successfully.');
}

// â”€â”€ DELETE: Soft-delete product â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€
if ($method === 'DELETE') {
    $id = (int)($_GET['id'] ?? getBody()['id'] ?? 0);
    if (!$id) error('Product ID required.', 400);

    $pdo->prepare('UPDATE products SET is_active = 0, is_deleted = 1 WHERE id = ?')->execute([$id]);
    
    require_once __DIR__ . '/../helpers/regenerate_js.php';
    regenerate_products_js();

    success('Product removed from store.');
}

