<?php
error_reporting(E_ALL); ini_set('display_errors', 1);
// â”€â”€ GET/PUT /api/admin/orders/list.php + update-status â”€â”€â”€â”€â”€â”€â”€â”€
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../helpers/response.php';
require_once __DIR__ . '/../../helpers/validator.php';
require_once __DIR__ . '/../../helpers/auth.php';
require_once __DIR__ . '/../../helpers/notifications.php';

setHeaders();
requireMethod('GET', 'PUT');

$admin = requireAdmin();
$pdo   = db();

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $page   = max(1, (int)($_GET['page'] ?? 1));
    $limit  = 20;
    $offset = ($page - 1) * $limit;
    $status = sanitize($_GET['status'] ?? '');
    $search = sanitize($_GET['search'] ?? '');

    $where  = [];
    $params = [];

    if ($status) {
        $where[]  = 'o.order_status = ?';
        $params[] = $status;
    }
    if ($search) {
        $where[]  = '(o.order_number LIKE ? OR u.mobile LIKE ? OR u.full_name LIKE ?)';
        $s = "%$search%";
        $params[] = $s; $params[] = $s; $params[] = $s;
    }

    $whereSQL = $where ? 'WHERE ' . implode(' AND ', $where) : '';

    $total = $pdo->prepare("SELECT COUNT(*) FROM orders o JOIN users u ON u.id = o.user_id $whereSQL");
    $total->execute($params);
    $totalCount = (int)$total->fetchColumn();

    $stmt = $pdo->prepare("
        SELECT o.id, o.order_number, o.subtotal, o.delivery_charge, o.total,
               o.payment_method, o.payment_status, o.order_status, o.notes, o.created_at, o.updated_at,
               u.full_name AS customer_name, u.mobile AS customer_mobile, u.email AS customer_email,
               (SELECT COUNT(id) FROM order_items WHERE order_id = o.id) AS item_count
        FROM orders o
        JOIN users u ON u.id = o.user_id
        $whereSQL
        ORDER BY o.created_at DESC
        LIMIT $limit OFFSET $offset
    ");
    $stmt->execute($params);
    $orders = $stmt->fetchAll();

    success('OK', [
        'orders' => $orders,
        'pagination' => ['total' => $totalCount, 'page' => $page, 'limit' => $limit, 'total_pages' => (int)ceil($totalCount / $limit)],
    ]);
}

// PUT â€” Update order status
if ($_SERVER['REQUEST_METHOD'] === 'PUT') {
    $body    = getBody();
    $orderId = (int)($body['order_id'] ?? 0);
    $status  = $body['status'] ?? '';
    $note    = sanitize($body['admin_notes'] ?? '');

    $validStatuses = ['pending', 'confirmed', 'processing', 'shipped', 'delivered', 'cancelled'];
    if (!$orderId || !in_array($status, $validStatuses, true)) {
        error('Invalid order ID or status.', 400);
    }

    $stmt = $pdo->prepare('SELECT o.*, u.id AS uid, u.full_name, u.mobile, u.email FROM orders o JOIN users u ON u.id = o.user_id WHERE o.id = ?');
    $stmt->execute([$orderId]);
    $order = $stmt->fetch();
    if (!$order) notFound('Order not found.');

    $verifyPayment = $body['verify_payment'] ?? false;
    
    if ($verifyPayment && $order['payment_method'] === 'upi') {
        $pdo->prepare("UPDATE orders SET order_status = ?, payment_status = 'paid', admin_notes = ?, updated_at = NOW() WHERE id = ?")
            ->execute([$status, $note, $orderId]);
    } elseif ($status === 'delivered' && $order['payment_method'] === 'cod') {
        $pdo->prepare("UPDATE orders SET order_status = ?, payment_status = 'paid', admin_notes = ?, updated_at = NOW() WHERE id = ?")
            ->execute([$status, $note, $orderId]);
    } else {
        $pdo->prepare("UPDATE orders SET order_status = ?, admin_notes = ?, updated_at = NOW() WHERE id = ?")
            ->execute([$status, $note, $orderId]);
    }

    // Notify customer
    try {
        $updatedOrder = array_merge($order, ['order_status' => $status]);
        $customer = ['id' => $order['uid'], 'full_name' => $order['full_name'], 'mobile' => $order['mobile'], 'email' => $order['email'] ?? ''];
        notifyOrderStatusUpdate($updatedOrder, $customer);
    } catch (\Throwable $e) {
        error_log('Status update notification error: ' . $e->getMessage());
    }

    success("Order status updated to: $status");
}

