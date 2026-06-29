<?php
// ── GET|DELETE /api/admin/categories/list.php ──────────────────
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../helpers/response.php';
require_once __DIR__ . '/../../helpers/auth.php';

setHeaders();
requireMethod('GET', 'PUT');

$admin = requireAdmin();
$pdo   = db();
$method = $_SERVER['REQUEST_METHOD'];

if ($method === 'GET') {
    $stmt = $pdo->query("
        SELECT c.*, COUNT(p.id) AS product_count
        FROM categories c
        LEFT JOIN products p ON p.category_id = c.id AND p.is_active = 1 AND p.is_deleted = 0
        GROUP BY c.id
        ORDER BY c.sort_order ASC, c.name ASC
    ");
    $categories = $stmt->fetchAll();
    success('OK', ['categories' => $categories]);
}

if ($method === 'PUT') {
    $body = getBody();
    $id   = (int)($body['id'] ?? 0);
    if (!$id) error('Category ID required.', 400);

    $pdo->prepare("UPDATE categories SET name=?, description=?, is_active=? WHERE id=?")
        ->execute([
            sanitize($body['name'] ?? ''),
            sanitize($body['description'] ?? ''),
            !empty($body['is_active']) ? 1 : 0,
            $id,
        ]);

    require_once __DIR__ . '/../helpers/regenerate_js.php';
    regenerate_products_js();

    success('Category updated.');
}
