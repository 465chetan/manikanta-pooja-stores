<?php
// â”€â”€ GET|POST|PUT|DELETE /api/user/addresses.php â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../helpers/response.php';
require_once __DIR__ . '/../helpers/validator.php';
require_once __DIR__ . '/../helpers/auth.php';

setHeaders();
requireMethod('GET', 'POST', 'PUT', 'DELETE');

$user   = requireAuth();
$pdo    = db();
$method = $_SERVER['REQUEST_METHOD'];

if ($method === 'GET') {
    $stmt = $pdo->prepare('SELECT * FROM addresses WHERE user_id = ? ORDER BY is_default DESC, id DESC');
    $stmt->execute([$user['user_id']]);
    success('OK', ['addresses' => $stmt->fetchAll()]);
}

$body = getBody();

if ($method === 'POST') {
    $v = Validator::make($body, [
        'label'        => 'string|max:50',
        'full_name'    => 'required|string|min:2|max:100',
        'mobile'       => 'required|mobile',
        'address_line' => 'required|string|min:10|max:255',
        'city'         => 'required|string|max:100',
        'pincode'      => 'required|pincode',
    ]);
    if ($v->fails()) error($v->firstError(), 422);

    // Max 5 addresses per user
    $count = $pdo->prepare('SELECT COUNT(*) FROM addresses WHERE user_id = ?');
    $count->execute([$user['user_id']]);
    if ($count->fetchColumn() >= 5) error('Maximum 5 saved addresses allowed.', 400);

    $isDefault = (bool)($body['is_default'] ?? false);
    if ($isDefault) {
        $pdo->prepare('UPDATE addresses SET is_default = 0 WHERE user_id = ?')->execute([$user['user_id']]);
    }

    $stmt = $pdo->prepare("INSERT INTO addresses (user_id, label, full_name, mobile, address_line, landmark, city, state, pincode, is_default) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->execute([
        $user['user_id'],
        sanitize($body['label'] ?? 'Home'),
        sanitize($body['full_name']),
        sanitizeMobile($body['mobile']),
        sanitize($body['address_line']),
        sanitize($body['landmark'] ?? ''),
        sanitize($body['city']),
        sanitize($body['state'] ?? 'Telangana'),
        sanitize($body['pincode']),
        $isDefault ? 1 : 0,
    ]);

    success('Address saved successfully.', ['id' => (int)$pdo->lastInsertId()], 201);
}

if ($method === 'PUT') {
    $id = (int)($body['id'] ?? 0);
    if (!$id) error('Address ID required.', 400);

    $stmt = $pdo->prepare('SELECT id FROM addresses WHERE id = ? AND user_id = ?');
    $stmt->execute([$id, $user['user_id']]);
    if (!$stmt->fetch()) notFound('Address not found.');

    $isDefault = (bool)($body['is_default'] ?? false);
    if ($isDefault) {
        $pdo->prepare('UPDATE addresses SET is_default = 0 WHERE user_id = ?')->execute([$user['user_id']]);
    }

    $pdo->prepare("UPDATE addresses SET label=?, full_name=?, mobile=?, address_line=?, landmark=?, city=?, state=?, pincode=?, is_default=? WHERE id = ? AND user_id = ?")
        ->execute([
            sanitize($body['label'] ?? 'Home'),
            sanitize($body['full_name'] ?? ''),
            sanitizeMobile($body['mobile'] ?? ''),
            sanitize($body['address_line'] ?? ''),
            sanitize($body['landmark'] ?? ''),
            sanitize($body['city'] ?? ''),
            sanitize($body['state'] ?? 'Telangana'),
            sanitize($body['pincode'] ?? ''),
            $isDefault ? 1 : 0,
            $id, $user['user_id'],
        ]);

    success('Address updated successfully.');
}

if ($method === 'DELETE') {
    $id = (int)($_GET['id'] ?? $body['id'] ?? 0);
    if (!$id) error('Address ID required.', 400);

    $del = $pdo->prepare('DELETE FROM addresses WHERE id = ? AND user_id = ?');
    $del->execute([$id, $user['user_id']]);

    if ($del->rowCount() === 0) notFound('Address not found.');
    success('Address deleted.');
}

