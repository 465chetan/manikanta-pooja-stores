<?php
// â”€â”€ GET|PUT /api/user/profile.php â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../helpers/response.php';
require_once __DIR__ . '/../helpers/validator.php';
require_once __DIR__ . '/../helpers/auth.php';

setHeaders();
requireMethod('GET', 'PUT');

$user = requireAuth();
$pdo  = db();

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $stmt = $pdo->prepare('SELECT id, full_name, mobile, email, created_at FROM users WHERE id = ?');
    $stmt->execute([$user['user_id']]);
    $profile = $stmt->fetch();
    success('OK', ['user' => $profile]);
}

// PUT â€” update profile
$body = getBody();
$v = Validator::make($body, [
    'full_name' => 'required|string|min:2|max:100',
    'email'     => 'email|max:150',
]);
if ($v->fails()) error($v->firstError(), 422);

$fullName = sanitize($body['full_name']);
$email    = !empty($body['email']) ? strtolower(trim($body['email'])) : null;

// Check email not taken by another user
if ($email) {
    $stmt = $pdo->prepare('SELECT id FROM users WHERE email = ? AND id != ?');
    $stmt->execute([$email, $user['user_id']]);
    if ($stmt->fetch()) error('This email is already used by another account.', 409);
}

$pdo->prepare('UPDATE users SET full_name = ?, email = ? WHERE id = ?')
    ->execute([$fullName, $email, $user['user_id']]);

success('Profile updated successfully.', ['full_name' => $fullName, 'email' => $email]);

