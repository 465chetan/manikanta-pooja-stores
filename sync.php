<?php
require 'api/config/database.php';
$pdo = db();

// Disable foreign key checks
$pdo->exec('SET FOREIGN_KEY_CHECKS = 0');
$pdo->exec('TRUNCATE TABLE products');

$json = file_get_contents('php://input');
$products = json_decode($json, true);

if (!$products) {
    echo "No products provided.\n";
    exit;
}

$stmt = $pdo->prepare("INSERT INTO products (id, category_id, name, telugu_name, slug, description, price, original_price, stock_qty, images, sizes, tags, badge, is_featured) 
VALUES (?, ?, ?, ?, ?, ?, ?, ?, 100, ?, ?, ?, ?, ?)");

$catMap = [
    'agarbatti' => 1, 'camphor' => 2, 'kumkum' => 3, 'oils' => 4,
    'diyas' => 5, 'photos' => 6, 'idols' => 7, 'thali' => 8,
    'malas' => 9, 'havan' => 10, 'festivals' => 11, 'wedding' => 12
];

$count = 0;
foreach ($products as $p) {
    $catId = $catMap[$p['category']] ?? 1;
    $slug = strtolower(preg_replace('/[^a-z0-9]+/', '-', $p['name']));
    $images = json_encode($p['images'] ?? []);
    $sizes = json_encode($p['sizes'] ?? []);
    $tags = json_encode($p['tags'] ?? []);
    $badge = $p['badge'] ?? null;
    $featured = !empty($p['featured']) ? 1 : 0;
    
    try {
        $stmt->execute([
            $p['id'], $catId, $p['name'], $p['telugu'] ?? '', $slug, 
            $p['description'] ?? '', $p['price'], $p['originalPrice'], 
            $images, $sizes, $tags, $badge, $featured
        ]);
        $count++;
    } catch (Exception $e) {
        echo "Error inserting " . $p['name'] . ": " . $e->getMessage() . "\n";
    }
}

$pdo->exec('SET FOREIGN_KEY_CHECKS = 1');
echo "Successfully synced $count products to database.\n";
