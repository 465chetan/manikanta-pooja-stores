<?php
require_once __DIR__ . '/../helpers/auth.php';
require_once __DIR__ . '/../helpers/response.php';

setHeaders();
requireMethod('GET');

$pdo   = db();
$isAdmin = false;

// Check if requester is admin
try {
    $token = getBearerToken();
    if ($token) {
        $payload = jwtDecode($token);
        if ($payload && isset($payload['admin_id'])) {
            $isAdmin = true;
        }
    }
} catch (\Throwable $e) {
    $isAdmin = false;
}

$where = $isAdmin ? 'p.is_deleted = 0 AND ' : 'p.is_active = 1 AND ';
$param = null;

if (!empty($_GET['id'])) {
    $where .= 'p.id = ?';
    $param  = (int)$_GET['id'];
} elseif (!empty($_GET['slug'])) {
    $where .= 'p.slug = ?';
    $param  = sanitize($_GET['slug']);
} else {
    error('Product ID or slug is required.', 400);
}

$stmt = $pdo->prepare("
    SELECT p.*, c.slug AS category, c.name AS category_name
    FROM products p
    JOIN categories c ON p.category_id = c.id
    WHERE $where
");
$stmt->execute([$param]);
$product = $stmt->fetch();

if (!$product) notFound('Product not found.');

// Parse JSON fields
$product['images']      = json_decode($product['images'] ?? '[]', true);
$product['sizes']       = json_decode($product['sizes']  ?? '[]', true);
$product['tags']        = json_decode($product['tags']   ?? '[]', true);
$product['in_stock']    = (int)$product['stock_qty'] > 0;
$product['discount_pct'] = $product['original_price'] > 0
    ? round((($product['original_price'] - $product['price']) / $product['original_price']) * 100)
    : 0;

if (!$isAdmin) {
    unset($product['stock_qty']);
}

// Related products (same category, exclude current)
$relStmt = $pdo->prepare("
    SELECT p.id, p.name, p.slug, p.price, p.original_price, p.rating, p.images, p.badge
    FROM products p
    WHERE p.category_id = ? AND p.id != ? AND p.is_active = 1
    ORDER BY p.is_featured DESC, p.rating DESC
    LIMIT 6
");
$relStmt->execute([$product['category_id'], $product['id']]);
$related = $relStmt->fetchAll();
foreach ($related as &$r) {
    $imgs = json_decode($r['images'] ?? '[]', true);
    $r['image'] = $imgs[0] ?? '';
    unset($r['images']);
}

success('OK', ['product' => $product, 'related' => $related]);

