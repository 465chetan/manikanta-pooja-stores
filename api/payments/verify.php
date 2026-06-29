<?php
// â”€â”€ POST /api/payments/verify.php â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€
// Verify Razorpay payment signature (MUST run server-side)
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../helpers/response.php';
require_once __DIR__ . '/../helpers/auth.php';
require_once __DIR__ . '/../helpers/notifications.php';

setHeaders();
requireMethod('POST');

$user = requireAuth();
$body = getBody();

$rzpOrderId   = $body['razorpay_order_id']   ?? '';
$rzpPaymentId = $body['razorpay_payment_id'] ?? '';
$rzpSignature = $body['razorpay_signature']  ?? '';
$ourOrderId   = (int)($body['order_id'] ?? 0);

if (!$rzpOrderId || !$rzpPaymentId || !$rzpSignature || !$ourOrderId) {
    error('Missing payment verification data.', 400);
}

// CRITICAL: Verify HMAC-SHA256 signature
$expectedSig = hash_hmac('sha256', "$rzpOrderId|$rzpPaymentId", RAZORPAY_KEY_SECRET);
if (!hash_equals($expectedSig, $rzpSignature)) {
    error_log("Payment signature mismatch! Order: $ourOrderId");
    error('Payment verification failed. Please contact support.', 400);
}

$pdo  = db();

// Verify order belongs to this user
$stmt = $pdo->prepare('SELECT * FROM orders WHERE id = ? AND user_id = ?');
$stmt->execute([$ourOrderId, $user['user_id']]);
$order = $stmt->fetch();
if (!$order) notFound('Order not found.');
if ($order['payment_status'] === 'paid') {
    success('Payment already confirmed.', ['order_number' => $order['order_number']]);
}

$pdo->beginTransaction();
try {
    // Update payment record
    $pdo->prepare("
        UPDATE payments SET razorpay_payment_id = ?, razorpay_signature = ?, status = 'captured'
        WHERE order_id = ? AND razorpay_order_id = ?
    ")->execute([$rzpPaymentId, $rzpSignature, $ourOrderId, $rzpOrderId]);

    // Update order payment status
    $pdo->prepare("UPDATE orders SET payment_status = 'paid', order_status = 'confirmed' WHERE id = ?")
        ->execute([$ourOrderId]);

    $pdo->commit();

    // Notify customer of confirmed order
    try {
        $updatedOrder = array_merge($order, ['order_status' => 'confirmed', 'payment_status' => 'paid']);
        $customer = ['id' => $user['user_id'], 'full_name' => $user['full_name'], 'mobile' => $user['mobile'], 'email' => $user['email'] ?? ''];
        notifyOrderStatusUpdate($updatedOrder, $customer);
    } catch (\Throwable $e) {
        error_log('Post-payment notification error: ' . $e->getMessage());
    }

    success('Payment successful! Your order is confirmed.', [
        'order_number'      => $order['order_number'],
        'razorpay_payment_id' => $rzpPaymentId,
    ]);

} catch (\Throwable $e) {
    $pdo->rollBack();
    error_log('Payment verification DB error: ' . $e->getMessage());
    serverError('Payment recorded but order update failed. Please contact support with your payment ID: ' . $rzpPaymentId);
}

