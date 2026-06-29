<?php
// â”€â”€ DELETE /api/admin/categories/delete.php â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../helpers/response.php';
require_once __DIR__ . '/../../helpers/auth.php';

setHeaders();
requireMethod('POST', 'DELETE');
requireAdmin();

$pdo = db();
$body = getBody();
$id = (int)($_GET['id'] ?? $body['id'] ?? 0);

if (!$id) error('Category ID is required.', 400);

// Check if category has products
$stmt = $pdo->prepare('SELECT COUNT(*) FROM products WHERE category_id = ? AND is_deleted = 0');
$stmt->execute([$id]);
if ((int)$stmt->fetchColumn() > 0) {
    error('Cannot delete this category because it contains active products. Please delete or move those products first.');
}

$pdo->prepare('DELETE FROM categories WHERE id = ?')->execute([$id]);

require_once __DIR__ . '/../helpers/regenerate_js.php';
regenerate_products_js();

success('Category deleted successfully.');
