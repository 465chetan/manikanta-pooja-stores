<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once __DIR__ . '/api/config/config.php';
require_once __DIR__ . '/api/config/database.php';

$pdo = db();
$stmt = $pdo->prepare("SELECT id, name, is_active, is_deleted FROM products WHERE id IN (12, 14)");
$stmt->execute();
$prods = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo "<pre>";
print_r($prods);
echo "</pre>";
