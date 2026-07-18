<?php
require_once __DIR__ . '/api/config/config.php';
require_once __DIR__ . '/api/config/database.php';
$pdo = db();

// Force all products to be active and not deleted
$stmt = $pdo->prepare("UPDATE products SET is_active = 1, is_deleted = 0");
$stmt->execute();
$count = $stmt->rowCount();

// Also update all categories to be active
$stmt = $pdo->prepare("UPDATE categories SET is_active = 1");
$stmt->execute();
$catCount = $stmt->rowCount();

echo "<pre>";
echo "Products restored: $count\n";
echo "Categories restored: $catCount\n";
echo "DONE!";
echo "</pre>";
