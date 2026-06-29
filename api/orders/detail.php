<?php
// â”€â”€ GET /api/orders/detail.php?id=1 â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../helpers/response.php';
require_once __DIR__ . '/../helpers/auth.php';

setHeaders();
requireMethod('GET');

$user    = requireAuth();
$orderId = (int)($_GET['id'] ?? 0);
if (!$orderId) error('Order ID required.', 400);

$pdo  = db();
$stmt = $pdo->prepare("
    SELECT o.*, a.address_line, a.city, a.state, a.pincode, a.landmark
    FROM orders o
    LEFT JOIN addresses a ON a.id = o.address_id
    WHERE o.id = ? AND o.user_id = ?
");
$stmt->execute([$orderId, $user['user_id']]);
$order = $stmt->fetch();

if (!$order) notFound('Order not found.');

// Get order items
$itemStmt = $pdo->prepare('SELECT * FROM order_items WHERE order_id = ?');
$itemStmt->execute([$orderId]);
$items = $itemStmt->fetchAll();

// Get payment info (including screenshot for admin display)
$payStmt = $pdo->prepare('SELECT utr_number, screenshot_path, amount, status, upi_id_used, created_at FROM payments WHERE order_id = ? ORDER BY id DESC LIMIT 1');
$payStmt->execute([$orderId]);
$payment = $payStmt->fetch();


// Decode address snapshot if no live address
$addressData = $order['address_line']
    ? ['address_line' => $order['address_line'], 'city' => $order['city'], 'state' => $order['state'], 'pincode' => $order['pincode'], 'landmark' => $order['landmark']]
    : json_decode($order['address_snapshot'] ?? '{}', true);

// Status timeline
$statusOrder = ['pending', 'confirmed', 'processing', 'shipped', 'delivered'];
$currentIdx  = array_search($order['order_status'], $statusOrder);
$timeline    = [];
foreach ($statusOrder as $idx => $st) {
    $timeline[] = [
        'status'    => $st,
        'label'     => ucfirst($st),
        'completed' => $idx <= $currentIdx && $order['order_status'] !== 'cancelled',
        'current'   => $idx === $currentIdx,
    ];
}

success('OK', [
    'order'   => array_merge($order, ['address' => $addressData]),
    'items'   => $items,
    'payment' => $payment,
    'timeline' => $order['order_status'] === 'cancelled' ? [] : $timeline,
]);

