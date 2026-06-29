<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

require_once __DIR__ . '/api/config/config.php';
require_once __DIR__ . '/api/config/database.php';

$pdo = db();
$user_id = 1;
$limit = 10;
$offset = 0;

$countStmt = $pdo->prepare('SELECT COUNT(*) FROM orders WHERE user_id = ?');
$countStmt->execute([$user_id]);
$total = (int)$countStmt->fetchColumn();
echo "Total: $total<br>";

try {
    $stmt = $pdo->prepare("
        SELECT o.id, o.order_number, o.total, o.delivery_charge, o.payment_method,
               o.payment_status, o.order_status, o.created_at,
               COUNT(oi.id) AS item_count,
               GROUP_CONCAT(oi.image ORDER BY oi.id SEPARATOR '|') AS item_images
        FROM orders o
        LEFT JOIN order_items oi ON oi.order_id = o.id
        WHERE o.user_id = ?
        GROUP BY o.id
        ORDER BY o.created_at DESC
        LIMIT $limit OFFSET $offset
    ");
    $stmt->execute([$user_id]);
    $orders = $stmt->fetchAll();
    echo "Orders fetched: " . count($orders);
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
?>
