<?php
// â”€â”€ GET /api/orders/list.php â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../helpers/response.php';
require_once __DIR__ . '/../helpers/auth.php';

setHeaders();
requireMethod('GET');

$user  = requireAuth();
$pdo   = db();
$page  = max(1, (int)($_GET['page'] ?? 1));
$limit = 10;
$offset = ($page - 1) * $limit;

$countStmt = $pdo->prepare('SELECT COUNT(*) FROM orders WHERE user_id = ?');
$countStmt->execute([$user['user_id']]);
$total = (int)$countStmt->fetchColumn();

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
$stmt->execute([$user['user_id']]);
$orders = $stmt->fetchAll();

foreach ($orders as &$o) {
    $o['item_images'] = $o['item_images'] ? explode('|', $o['item_images']) : [];
    $o['total'] = (float)$o['total'];
}

success('OK', [
    'orders' => $orders,
    'pagination' => ['total' => $total, 'page' => $page, 'limit' => $limit, 'total_pages' => (int)ceil($total / $limit)],
]);

