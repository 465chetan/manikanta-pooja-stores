<?php
require_once __DIR__ . '/api/config/config.php';
require_once __DIR__ . '/api/config/database.php';
require_once __DIR__ . '/api/admin/helpers/regenerate_js.php';

$pdo = db();
$products = $pdo->query("SELECT id, price, original_price, sizes FROM products")->fetchAll(PDO::FETCH_ASSOC);

$updated = 0;
foreach ($products as $p) {
    $sizes = json_decode($p['sizes'] ?? '[]', true);
    if (!empty($sizes) && is_array($sizes[0])) {
        $first_price = (float)($sizes[0]['price'] ?? 0);
        $first_orig = !empty($sizes[0]['original_price']) ? (float)$sizes[0]['original_price'] : null;
        
        if ($p['price'] != $first_price || $p['original_price'] != $first_orig) {
            $stmt = $pdo->prepare("UPDATE products SET price = ?, original_price = ? WHERE id = ?");
            $stmt->execute([$first_price, $first_orig, $p['id']]);
            $updated++;
        }
    }
}

regenerate_products_js();
echo "Successfully updated $updated products to use their first variant's price.";
