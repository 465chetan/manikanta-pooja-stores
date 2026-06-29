<?php
// â”€â”€ GET /api/admin/customers/list.php â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../helpers/response.php';
require_once __DIR__ . '/../../helpers/validator.php';
require_once __DIR__ . '/../../helpers/auth.php';

setHeaders();
requireMethod('GET');

$admin  = requireAdmin();
$pdo    = db();
$page   = max(1, (int)($_GET['page'] ?? 1));
$limit  = 20;
$offset = ($page - 1) * $limit;
$search = sanitize($_GET['search'] ?? '');

$where  = [];
$params = [];
if ($search) {
    $where[]  = '(u.full_name LIKE ? OR u.mobile LIKE ? OR u.email LIKE ?)';
    $s = "%$search%"; $params[] = $s; $params[] = $s; $params[] = $s;
}
$whereSQL = $where ? 'WHERE ' . implode(' AND ', $where) : '';

$total = $pdo->prepare("SELECT COUNT(*) FROM users u $whereSQL");
$total->execute($params);
$totalCount = (int)$total->fetchColumn();

$stmt = $pdo->prepare("
    SELECT u.id, u.full_name, u.mobile, u.email, u.is_active, u.created_at,
           (SELECT COUNT(id) FROM orders WHERE user_id = u.id AND order_status != 'cancelled') AS order_count,
           (SELECT COALESCE(SUM(total), 0) FROM orders WHERE user_id = u.id AND order_status = 'delivered') AS total_spent,
           (SELECT MAX(created_at) FROM orders WHERE user_id = u.id AND order_status != 'cancelled') AS last_order_date
    FROM users u
    $whereSQL
    ORDER BY u.created_at DESC
    LIMIT $limit OFFSET $offset
");
$stmt->execute($params);

success('OK', [
    'customers'  => $stmt->fetchAll(),
    'pagination' => ['total' => $totalCount, 'page' => $page, 'limit' => $limit, 'total_pages' => (int)ceil($totalCount / $limit)],
]);

