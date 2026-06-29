<?php
// â”€â”€ POST /api/admin/categories/create.php â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../helpers/response.php';
require_once __DIR__ . '/../../helpers/validator.php';
require_once __DIR__ . '/../../helpers/auth.php';

setHeaders();
requireMethod('POST');
$admin = requireAdmin();
$pdo = db();

$body = getBody();
$v = Validator::make($body, [
    'name' => 'required|string|min:2|max:100'
]);

if ($v->fails()) {
    error($v->firstError(), 422);
}

$name = sanitize($body['name']);
$telugu = sanitize($body['telugu'] ?? '');
$slug = preg_replace('/[^a-z0-9]+/', '-', strtolower($name));
$slug = trim($slug, '-');

// Ensure unique slug
$stmt = $pdo->prepare('SELECT id FROM categories WHERE slug = ?');
$stmt->execute([$slug]);
if ($stmt->fetch()) {
    $slug .= '-' . time();
}

$stmt = $pdo->prepare("
    INSERT INTO categories (slug, name, telugu, sort_order, is_active)
    VALUES (?, ?, ?, 99, 1)
");
$stmt->execute([$slug, $name, $telugu]);

require_once __DIR__ . '/../helpers/regenerate_js.php';
regenerate_products_js();

success('Category created successfully.', [
    'id' => (int)$pdo->lastInsertId(),
    'name' => $name,
    'slug' => $slug
], 201);
