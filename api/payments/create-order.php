<?php
// â”€â”€ POST /api/payments/create-order.php â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€
// Creates a Razorpay order for online payment
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../helpers/response.php';
require_once __DIR__ . '/../helpers/auth.php';

setHeaders();
requireMethod('POST');

$user = requireAuth();
$body = getBody();

$orderId = (int)($body['order_id'] ?? 0);
if (!$orderId) error('Order ID required.', 400);

$pdo  = db();
$stmt = $pdo->prepare('SELECT * FROM orders WHERE id = ? AND user_id = ? AND payment_method = ?');
$stmt->execute([$orderId, $user['user_id'], 'razorpay']);
$order = $stmt->fetch();

if (!$order) notFound('Order not found.');
if ($order['payment_status'] === 'paid') error('This order is already paid.', 400);

$amountPaise = (int)round($order['total'] * 100); // Razorpay uses paise

// Create Razorpay order via API
$rzpPayload = json_encode([
    'amount'          => $amountPaise,
    'currency'        => RAZORPAY_CURRENCY,
    'receipt'         => $order['order_number'],
    'notes'           => [
        'order_id'    => $order['id'],
        'order_number' => $order['order_number'],
        'customer'    => $user['full_name'],
    ],
]);

$ch = curl_init('https://api.razorpay.com/v1/orders');
curl_setopt_array($ch, [
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_POST           => true,
    CURLOPT_POSTFIELDS     => $rzpPayload,
    CURLOPT_HTTPHEADER     => ['Content-Type: application/json'],
    CURLOPT_USERPWD        => RAZORPAY_KEY_ID . ':' . RAZORPAY_KEY_SECRET,
    CURLOPT_TIMEOUT        => 30,
    CURLOPT_SSL_VERIFYPEER => true,
]);
$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

if ($httpCode !== 200) {
    error_log('Razorpay error: ' . $response);
    serverError('Payment gateway error. Please try again.');
}

$rzpOrder = json_decode($response, true);

// Store the Razorpay order ID in payments table
$pdo->prepare('INSERT INTO payments (order_id, razorpay_order_id, amount, status) VALUES (?, ?, ?, "created")')
    ->execute([$orderId, $rzpOrder['id'], $order['total']]);

success('Razorpay order created.', [
    'razorpay_order_id' => $rzpOrder['id'],
    'amount'            => $amountPaise,
    'currency'          => RAZORPAY_CURRENCY,
    'key_id'            => RAZORPAY_KEY_ID,
    'order_number'      => $order['order_number'],
    'customer' => [
        'name'    => $user['full_name'],
        'mobile'  => $user['mobile'],
        'email'   => $user['email'] ?? '',
    ],
]);

