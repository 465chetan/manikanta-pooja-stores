<?php
// â”€â”€ GET /api/admin/orders/detail.php?id=1 â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../helpers/response.php';
require_once __DIR__ . '/../../helpers/auth.php';

setHeaders();
requireMethod('GET');

$admin   = requireAdmin();
$orderId = (int)($_GET['id'] ?? 0);
if (!$orderId) error('Order ID required.', 400);

$pdo  = db();

// Get full order with customer info and address
$stmt = $pdo->prepare("
    SELECT o.*,
           u.full_name AS customer_name, u.mobile AS customer_mobile, u.email AS customer_email,
           a.full_name  AS addr_name,  a.mobile   AS addr_mobile,
           a.address_line, a.landmark, a.city, a.state, a.pincode
    FROM orders o
    JOIN users u ON u.id = o.user_id
    LEFT JOIN addresses a ON a.id = o.address_id
    WHERE o.id = ?
");
$stmt->execute([$orderId]);
$order = $stmt->fetch();

if (!$order) notFound('Order not found.');

// Get order items with product details
$itemStmt = $pdo->prepare("
    SELECT oi.*, p.name, p.telugu_name, COALESCE(oi.image, 'images/placeholder.jpg') AS image
    FROM order_items oi
    LEFT JOIN products p ON p.id = oi.product_id
    WHERE oi.order_id = ?
");
$itemStmt->execute([$orderId]);
$items = $itemStmt->fetchAll();

// Get payment info
$payStmt = $pdo->prepare('
    SELECT utr_number, screenshot_path, amount, status, upi_id_used, created_at
    FROM payments
    WHERE order_id = ?
    ORDER BY id DESC LIMIT 1
');
$payStmt->execute([$orderId]);
$payment = $payStmt->fetch();

// Build address object from joined address or snapshot
$addressData = $order['address_line']
    ? [
        'full_name'    => $order['addr_name']    ?? $order['customer_name'],
        'mobile'       => $order['addr_mobile']  ?? $order['customer_mobile'],
        'address_line' => $order['address_line'],
        'landmark'     => $order['landmark'],
        'city'         => $order['city'],
        'state'        => $order['state'],
        'pincode'      => $order['pincode'],
      ]
    : (json_decode($order['address_snapshot'] ?? '{}', true) ?: []);

success('OK', [
    'order'   => array_merge($order, ['address' => $addressData]),
    'items'   => $items,
    'payment' => $payment ?: null,
]);
