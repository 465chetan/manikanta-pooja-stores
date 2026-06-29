<?php
require_once __DIR__ . '/api/config/config.php';
require_once __DIR__ . '/api/config/database.php';
$pdo = db();
$stmt = $pdo->prepare("UPDATE orders SET delivery_charge = 0, total = subtotal WHERE order_number = 'SMPS-2026-63584'");
$stmt->execute();
echo "Fixed order SMPS-2026-63584 delivery charge.";
