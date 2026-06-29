<?php
// ── POST /api/admin/orders/update.php ────────────────────────────────
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../helpers/response.php';
require_once __DIR__ . '/../../helpers/auth.php';

setHeaders();
requireMethod('POST');

// Ensure admin is logged in
$admin = requireAdmin();

$body = getBody();
if (!isset($body['order_id'])) {
    error('Order ID is required.', 400);
}

$orderId = (int)$body['order_id'];
$pdo = db();

// Check if order exists
$stmt = $pdo->prepare('SELECT * FROM orders WHERE id = ?');
$stmt->execute([$orderId]);
$order = $stmt->fetch();

if (!$order) {
    error('Order not found.', 404);
}

// Prepare updates
$updates = [];
$params = [];

if (isset($body['payment_status'])) {
    $rawStatus = $body['payment_status'];
    
    // Map to orders.payment_status ('pending','verifying','paid','failed','refunded')
    $orderPaymentStatus = $rawStatus;
    if ($rawStatus === 'verified') {
        $orderPaymentStatus = 'paid';
    }
    
    // Map to payments.status ('pending','verifying','verified','failed')
    $paymentTableStatus = $rawStatus;
    if ($rawStatus === 'paid') {
        $paymentTableStatus = 'verified';
    }
    
    $updates[] = 'payment_status = ?';
    $params[] = $orderPaymentStatus;
    
    // Also update the payments table status if it exists
    $payStmt = $pdo->prepare('UPDATE payments SET status = ? WHERE order_id = ?');
    $payStmt->execute([$paymentTableStatus, $orderId]);
}

if (isset($body['order_status'])) {
    $updates[] = 'order_status = ?';
    $params[] = $body['order_status'];
}

if (empty($updates)) {
    error('No updates provided.', 400);
}

// Append order_id for the WHERE clause
$params[] = $orderId;

$sql = 'UPDATE orders SET ' . implode(', ', $updates) . ' WHERE id = ?';
$updateStmt = $pdo->prepare($sql);
$updateStmt->execute($params);

success('Order updated successfully.');
