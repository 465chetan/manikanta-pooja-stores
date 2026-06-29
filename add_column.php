<?php
require_once __DIR__ . '/api/config/database.php';
$pdo = db();
try {
    $pdo->exec("ALTER TABLE orders ADD COLUMN delivery_charge DECIMAL(10,2) NOT NULL DEFAULT 0 AFTER subtotal");
    echo "Column added successfully.\n";
} catch (\Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
