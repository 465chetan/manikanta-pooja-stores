<?php
// â”€â”€ GET /api/user/dashboard.php â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../helpers/response.php';
require_once __DIR__ . '/../helpers/auth.php';

setHeaders();
requireMethod('GET');

$user = requireAuth();
$pdo  = db();
$uid  = $user['user_id'];

// Order stats
$statsStmt = $pdo->prepare("
    SELECT
        COUNT(*) AS total_orders,
        SUM(CASE WHEN order_status = 'delivered' THEN 1 ELSE 0 END) AS delivered,
        SUM(CASE WHEN order_status IN ('pending','confirmed','processing','shipped') THEN 1 ELSE 0 END) AS active,
        SUM(CASE WHEN order_status = 'cancelled' THEN 1 ELSE 0 END) AS cancelled,
        COALESCE(SUM(CASE WHEN order_status = 'delivered' THEN total ELSE 0 END), 0) AS total_spent
    FROM orders WHERE user_id = ?
");
$statsStmt->execute([$uid]);
$stats = $statsStmt->fetch();

// Recent orders
$recentStmt = $pdo->prepare("
    SELECT o.id, o.order_number, o.total, o.order_status, o.payment_status, o.created_at,
           COUNT(oi.id) AS item_count
    FROM orders o
    LEFT JOIN order_items oi ON oi.order_id = o.id
    WHERE o.user_id = ?
    GROUP BY o.id
    ORDER BY o.created_at DESC
");
$recentStmt->execute([$uid]);
$recentOrders = $recentStmt->fetchAll();

// Unread notifications
$notifStmt = $pdo->prepare("
    SELECT id, type, title, message, is_read, created_at
    FROM notifications WHERE user_id = ?
    ORDER BY created_at DESC LIMIT 10
");
$notifStmt->execute([$uid]);
$notifications = $notifStmt->fetchAll();

// Mark notifications as read
$pdo->prepare('UPDATE notifications SET is_read = 1 WHERE user_id = ? AND is_read = 0')->execute([$uid]);

success('OK', [
    'stats'         => $stats,
    'recent_orders' => $recentOrders,
    'notifications' => $notifications,
]);

