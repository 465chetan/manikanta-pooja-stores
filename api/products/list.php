<?php
// â”€â”€ GET /api/products/list.php â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€
// Supports: ?cat=agarbatti&search=kumkum&sort=price_asc&page=1&limit=12&featured=1
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../helpers/response.php';

setHeaders();
requireMethod('GET');

$pdo   = db();
$page  = max(1, (int)($_GET['page'] ?? 1));
$limit = min(48, max(1, (int)($_GET['limit'] ?? 12)));
$offset = ($page - 1) * $limit;

$where  = ['p.is_active = 1', 'p.is_deleted = 0'];
$params = [];

// Category filter
if (!empty($_GET['cat'])) {
    $where[]  = 'c.slug = ?';
    $params[] = sanitize($_GET['cat']);
}

// Search
if (!empty($_GET['search'])) {
    $search   = '%' . sanitize($_GET['search']) . '%';
    $where[]  = '(p.name LIKE ? OR p.telugu_name LIKE ? OR p.description LIKE ?)';
    $params[] = $search;
    $params[] = $search;
    $params[] = $search;
}

// Featured filter
if (isset($_GET['featured']) && $_GET['featured'] === '1') {
    $where[] = 'p.is_featured = 1';
}

// Price range
if (!empty($_GET['min_price'])) {
    $where[]  = 'p.price >= ?';
    $params[] = (float)$_GET['min_price'];
}
if (!empty($_GET['max_price'])) {
    $where[]  = 'p.price <= ?';
    $params[] = (float)$_GET['max_price'];
}

$whereSQL = $where ? 'WHERE ' . implode(' AND ', $where) : '';

// Sorting
$sortMap = [
    'price_asc'    => 'p.price ASC',
    'price_desc'   => 'p.price DESC',
    'rating'       => 'p.rating DESC',
    'newest'       => 'p.created_at DESC',
    'popular'      => 'p.review_count DESC',
    'name_asc'     => 'p.name ASC',
];
$sort    = $_GET['sort'] ?? 'popular';
$orderBy = $sortMap[$sort] ?? $sortMap['popular'];

// Total count
$countStmt = $pdo->prepare("
    SELECT COUNT(*) FROM products p
    JOIN categories c ON p.category_id = c.id
    $whereSQL
");
$countStmt->execute($params);
$total = (int)$countStmt->fetchColumn();

// Fetch products
$stmt = $pdo->prepare("
    SELECT p.id, p.name, p.telugu_name, p.slug, p.description,
           p.price, p.original_price, p.stock_qty, p.images, p.sizes,
           p.badge, p.is_featured, p.rating, p.review_count,
           c.slug AS category, c.name AS category_name
    FROM products p
    JOIN categories c ON p.category_id = c.id
    $whereSQL
    ORDER BY $orderBy
    LIMIT $limit OFFSET $offset
");
$stmt->execute($params);
$products = $stmt->fetchAll();

// Parse JSON fields
foreach ($products as &$p) {
    $p['images']        = json_decode($p['images'] ?? '[]', true);
    $p['sizes']         = json_decode($p['sizes']  ?? '[]', true);
    $p['in_stock']      = (int)$p['stock_qty'] > 0;
    $p['discount_pct']  = $p['original_price'] > 0
        ? round((($p['original_price'] - $p['price']) / $p['original_price']) * 100)
        : 0;
    unset($p['stock_qty']); // Don't expose exact stock
}

success('OK', [
    'products'   => $products,
    'pagination' => [
        'total'       => $total,
        'page'        => $page,
        'limit'       => $limit,
        'total_pages' => (int)ceil($total / $limit),
    ],
]);

