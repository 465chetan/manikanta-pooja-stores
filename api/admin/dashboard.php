<?php
// â”€â”€ GET /api/admin/dashboard.php â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../helpers/response.php';
require_once __DIR__ . '/../helpers/auth.php';

setHeaders();
requireMethod('GET');
$admin = requireAdmin();

$pdo = db();

// Order stats
$stats = $pdo->query("
    SELECT
        COUNT(*) AS total_orders,
        SUM(CASE WHEN order_status = 'pending' THEN 1 ELSE 0 END) AS pending,
        SUM(CASE WHEN order_status = 'confirmed' THEN 1 ELSE 0 END) AS confirmed,
        SUM(CASE WHEN order_status = 'shipped' THEN 1 ELSE 0 END) AS shipped,
        SUM(CASE WHEN order_status = 'delivered' THEN 1 ELSE 0 END) AS delivered,
        SUM(CASE WHEN order_status = 'cancelled' THEN 1 ELSE 0 END) AS cancelled,
        COALESCE(SUM(CASE WHEN order_status = 'delivered' AND payment_status = 'paid' THEN total ELSE 0 END), 0) AS revenue_paid,
        COALESCE(SUM(CASE WHEN order_status = 'delivered' THEN total ELSE 0 END), 0) AS revenue_total
    FROM orders
")->fetch();

// Today's orders
$todayStats = $pdo->query("
    SELECT COUNT(*) AS today_orders, COALESCE(SUM(CASE WHEN order_status = 'delivered' THEN total ELSE 0 END), 0) AS today_revenue
    FROM orders WHERE DATE(created_at) = CURDATE()
")->fetch();

// This Week's orders
$weeklyStats = $pdo->query("
    SELECT COUNT(*) AS weekly_orders, COALESCE(SUM(CASE WHEN order_status = 'delivered' THEN total ELSE 0 END), 0) AS weekly_revenue
    FROM orders WHERE YEARWEEK(created_at, 1) = YEARWEEK(CURDATE(), 1)
")->fetch();

// This Month's orders
$thisMonthStats = $pdo->query("
    SELECT COUNT(*) AS this_month_orders, COALESCE(SUM(CASE WHEN order_status = 'delivered' THEN total ELSE 0 END), 0) AS this_month_revenue
    FROM orders WHERE MONTH(created_at) = MONTH(CURDATE()) AND YEAR(created_at) = YEAR(CURDATE())
")->fetch();

// This Year's orders
$thisYearStats = $pdo->query("
    SELECT COUNT(*) AS this_year_orders, COALESCE(SUM(CASE WHEN order_status = 'delivered' THEN total ELSE 0 END), 0) AS this_year_revenue
    FROM orders WHERE YEAR(created_at) = YEAR(CURDATE())
")->fetch();

// Daily breakdown (last 30 days)
$dailyBreakdown = $pdo->query("
    SELECT 
        DATE(created_at) as date,
        COUNT(*) as orders,
        COALESCE(SUM(CASE WHEN order_status = 'delivered' THEN total ELSE 0 END), 0) as revenue
    FROM orders
    WHERE created_at >= DATE_SUB(CURDATE(), INTERVAL 30 DAY)
    GROUP BY DATE(created_at)
    ORDER BY DATE(created_at) DESC
")->fetchAll();

// Recent orders
$recentOrders = $pdo->query("
    SELECT o.id, o.order_number, o.total, o.order_status, o.payment_status, o.payment_method,
           o.created_at, u.full_name AS customer_name, u.mobile AS customer_mobile
    FROM orders o
    JOIN users u ON u.id = o.user_id
    ORDER BY o.created_at DESC
    LIMIT 10
")->fetchAll();

// Top products
$topProducts = $pdo->query("
    SELECT p.name, SUM(oi.qty) AS units_sold, SUM(oi.price * oi.qty) AS revenue
    FROM order_items oi
    JOIN products p ON p.id = oi.product_id
    JOIN orders o ON o.id = oi.order_id
    WHERE o.order_status = 'delivered'
    GROUP BY oi.product_id
    ORDER BY units_sold DESC
    LIMIT 5
")->fetchAll();

// Counts
$counts = $pdo->query("
    SELECT
        (SELECT COUNT(*) FROM users WHERE is_active = 1) AS customers,
        (SELECT COUNT(*) FROM products WHERE is_active = 1) AS products,
        (SELECT COUNT(*) FROM categories WHERE is_active = 1) AS categories
")->fetch();

// Monthly stats (last 12 months)
$monthlyStats = $pdo->query("
    SELECT
        DATE_FORMAT(created_at, '%Y-%m') as month,
        COUNT(*) as total_orders,
        COALESCE(SUM(CASE WHEN order_status = 'delivered' THEN total ELSE 0 END), 0) AS revenue
    FROM orders
    WHERE created_at >= DATE_SUB(CURDATE(), INTERVAL 12 MONTH)
    GROUP BY DATE_FORMAT(created_at, '%Y-%m')
    ORDER BY month ASC
")->fetchAll();

success('OK', [
    'stats'        => array_merge($stats, $todayStats, $weeklyStats, $thisMonthStats, $thisYearStats),
    'recent_orders' => $recentOrders,
    'top_products'  => $topProducts,
    'counts'        => $counts,
    'monthly_stats' => $monthlyStats,
    'daily_breakdown' => $dailyBreakdown,
]);

