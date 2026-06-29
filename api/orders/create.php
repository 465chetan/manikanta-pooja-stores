<?php
// â”€â”€ POST /api/orders/create.php â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../helpers/response.php';
require_once __DIR__ . '/../helpers/validator.php';
require_once __DIR__ . '/../helpers/auth.php';
require_once __DIR__ . '/../helpers/notifications.php';

setHeaders();
requireMethod('POST');

$user = optionalAuth();
$body = getBody();

$v = Validator::make($body, [
    'items'          => 'required',
    'payment_method' => 'required|in:cod,upi',
    'full_name'      => 'required',
    'mobile'         => 'required',
    'address_line'   => 'required',
]);
if ($v->fails()) error($v->firstError(), 422);

$items         = $body['items'] ?? [];
$paymentMethod = $body['payment_method'];
$notes         = sanitize($body['notes'] ?? '');
$screenshotPath = sanitize($body['screenshot_path'] ?? '');

if (empty($items) || !is_array($items)) error('Cart is empty.', 400);

$pdo = db();

$userId = $user ? $user['user_id'] : null;

// Create address snapshot from raw payload instead of address_id
$addressSnapshot = json_encode([
    'full_name'    => sanitize($body['full_name']),
    'mobile'       => sanitize($body['mobile']),
    'address_line' => sanitize($body['address_line']),
    'landmark'     => sanitize($body['landmark'] ?? ''),
    'city'         => sanitize($body['city'] ?? 'Hyderabad'),
    'state'        => sanitize($body['state'] ?? 'Telangana'),
    'pincode'      => sanitize($body['pincode'] ?? ''),
]);

// Validate items and calculate totals
$orderItems = [];
$subtotal   = 0;

foreach ($items as $item) {
    if (empty($item['product_id'])) continue;
    $prodStmt = $pdo->prepare('SELECT id, name, price, stock_qty, images FROM products WHERE id = ? AND is_active = 1');
    $prodStmt->execute([(int)$item['product_id']]);
    $product = $prodStmt->fetch();

    if (!$product) error("Product #{$item['product_id']} is no longer available.", 400);
    if ((int)$product['stock_qty'] < (int)($item['qty'] ?? 1)) {
        error("Sorry, '{$product['name']}' is out of stock.", 400);
    }

    $qty   = max(1, (int)($item['qty'] ?? 1));
    $imgs  = json_decode($product['images'] ?? '[]', true);
    $price = (float)$product['price'];

    $orderItems[] = [
        'product_id' => $product['id'],
        'name'       => $product['name'],
        'variant'    => sanitize($item['variant'] ?? ''),
        'price'      => $price,
        'qty'        => $qty,
        'image'      => $imgs[0] ?? '',
    ];
    $subtotal += $price * $qty;
}

if (empty($orderItems)) error('No valid items in cart.', 400);

$deliveryCharge = isset($body['delivery_charge']) ? (float)$body['delivery_charge'] : ($subtotal >= FREE_DELIVERY_ABOVE ? 0 : DELIVERY_CHARGE);
$total          = $subtotal + $deliveryCharge;

$pdo->beginTransaction();
try {
    // Generate unique order number
    do {
        $orderNumber = generateOrderNumber();
        $chk = $pdo->prepare('SELECT id FROM orders WHERE order_number = ?');
        $chk->execute([$orderNumber]);
    } while ($chk->fetch());

    // Address snapshot is already prepared above

    // Insert order
    $paymentStatus = ($paymentMethod === 'upi') ? 'verifying' : 'pending';
    $stmt = $pdo->prepare("
        INSERT INTO orders (order_number, user_id, address_snapshot, subtotal, delivery_charge, total, payment_method, payment_status, order_status, notes)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, 'pending', ?)
    ");
    $stmt->execute([$orderNumber, $userId, $addressSnapshot, $subtotal, $deliveryCharge, $total, $paymentMethod, $paymentStatus, $notes]);
    $orderId = (int)$pdo->lastInsertId();

    if ($paymentMethod === 'upi') {
        $utrNumber = isset($body['utr_number']) ? trim($body['utr_number']) : null;
        $payStmt = $pdo->prepare("INSERT INTO payments (order_id, screenshot_path, amount, status, utr_number) VALUES (?, ?, ?, 'verifying', ?)");
        $payStmt->execute([$orderId, $screenshotPath, $total, $utrNumber]);
    }

    // Insert order items + decrease stock
    $itemStmt = $pdo->prepare("INSERT INTO order_items (order_id, product_id, name, variant, price, qty, image) VALUES (?, ?, ?, ?, ?, ?, ?)");
    $stockStmt = $pdo->prepare("UPDATE products SET stock_qty = stock_qty - ? WHERE id = ? AND stock_qty >= ?");
    foreach ($orderItems as $item) {
        $itemStmt->execute([$orderId, $item['product_id'], $item['name'], $item['variant'], $item['price'], $item['qty'], $item['image']]);
        $stockStmt->execute([$item['qty'], $item['product_id'], $item['qty']]);
    }

    $pdo->commit();

    // Send notifications (non-blocking â€” errors here shouldn't fail the order)
    try {
        $orderRow  = ['id' => $orderId, 'order_number' => $orderNumber, 'subtotal' => $subtotal, 'delivery_charge' => $deliveryCharge, 'total' => $total, 'payment_method' => $paymentMethod];
        $customer  = ['id' => $userId, 'full_name' => $body['full_name'], 'mobile' => $body['mobile'], 'email' => ''];
        notifyNewOrder($orderRow, $customer, $orderItems);
    } catch (\Throwable $e) {
        error_log('Notification error: ' . $e->getMessage());
    }

    $responseData = [
        'order_id'     => $orderId,
        'order_number' => $orderNumber,
        'total'        => $total,
        'payment_method' => $paymentMethod,
    ];

    // For COD
    if ($paymentMethod === 'cod') {
        success('Order placed successfully! We will confirm your order shortly.', $responseData, 201);
    }

    // For UPI
    success('Order placed! We will verify your UPI payment and confirm your order shortly.', $responseData, 201);

} catch (\Throwable $e) {
    $pdo->rollBack();
    error_log('Order creation error: ' . $e->getMessage());
    serverError('Failed to place order. Please try again.');
}

