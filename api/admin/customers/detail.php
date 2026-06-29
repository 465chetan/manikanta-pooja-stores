<?php
// ── GET /api/admin/customers/detail.php?id=1 ─────────────────────────
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../helpers/response.php';
require_once __DIR__ . '/../../helpers/auth.php';

setHeaders();
requireMethod('GET');

$admin = requireAdmin();
$userId = (int)($_GET['id'] ?? 0);
if (!$userId) error('Customer ID required.', 400);

$pdo = db();

// Fetch customer details and stats
$stmt = $pdo->prepare("
    SELECT u.id, u.full_name, u.mobile, u.email, u.is_active, u.created_at,
           (SELECT COUNT(id) FROM orders WHERE user_id = u.id AND order_status != 'cancelled') AS order_count,
           (SELECT COALESCE(SUM(total), 0) FROM orders WHERE user_id = u.id AND order_status = 'delivered') AS total_spent,
           (SELECT COALESCE(AVG(total), 0) FROM orders WHERE user_id = u.id AND order_status = 'delivered') AS avg_spent
    FROM users u
    WHERE u.id = ?
");
$stmt->execute([$userId]);
$customer = $stmt->fetch();

if (!$customer) notFound('Customer not found.');

// Fetch order history for this customer
$ordersStmt = $pdo->prepare("
    SELECT o.id, o.order_number, o.total, o.order_status, o.payment_method, o.payment_status, o.created_at,
           (SELECT SUM(qty) FROM order_items WHERE order_id = o.id) AS item_count
    FROM orders o
    WHERE o.user_id = ?
    ORDER BY o.created_at DESC
");
$ordersStmt->execute([$userId]);
$orders = $ordersStmt->fetchAll();

// Fetch items for these orders
if (!empty($orders)) {
    $orderIds = array_column($orders, 'id');
    $placeholders = str_repeat('?,', count($orderIds) - 1) . '?';
    
    $itemsStmt = $pdo->prepare("
        SELECT oi.order_id, p.name, oi.qty, oi.price 
        FROM order_items oi
        JOIN products p ON p.id = oi.product_id
        WHERE oi.order_id IN ($placeholders)
    ");
    $itemsStmt->execute($orderIds);
    $allItems = $itemsStmt->fetchAll();
    
    $itemsByOrder = [];
    foreach ($allItems as $item) {
        $itemsByOrder[$item['order_id']][] = $item;
    }
    
    foreach ($orders as &$order) {
        $order['items'] = $itemsByOrder[$order['id']] ?? [];
    }
} else {
    foreach ($orders as &$order) {
        $order['items'] = [];
    }
}

success('OK', [
    'customer' => $customer,
    'orders'   => $orders
]);
