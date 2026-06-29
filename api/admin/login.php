<?php
// â”€â”€ POST /api/admin/login.php â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../helpers/response.php';
require_once __DIR__ . '/../helpers/validator.php';
require_once __DIR__ . '/../helpers/auth.php';

setHeaders();
requireMethod('POST');

$body = getBody();

$v = Validator::make($body, [
    'email'    => 'required|email',
    'password' => 'required',
]);
if ($v->fails()) error($v->firstError(), 422);

$email = strtolower(trim($body['email']));
$ip    = $_SERVER['REMOTE_ADDR'] ?? 'unknown';

checkRateLimit("admin:$ip", 'admin_login', 5, 900);

$pdo  = db();
$stmt = $pdo->prepare('SELECT * FROM admin_users WHERE email = ? AND is_active = 1');
$stmt->execute([$email]);
$admin = $stmt->fetch();

if (!$admin || !verifyPassword($body['password'], $admin['password_hash'])) {
    error('Invalid email or password.', 401);
}

clearRateLimit("admin:$ip", 'admin_login');

// Update last login
$pdo->prepare('UPDATE admin_users SET last_login = NOW() WHERE id = ?')->execute([$admin['id']]);

$token = jwtEncode([
    'admin_id' => (int)$admin['id'],
    'email'    => $admin['email'],
    'role'     => $admin['role'],
    'iat'      => time(),
    'exp'      => time() + (60 * 60 * 8), // 8 hour admin session
]);

success('Admin login successful.', [
    'token' => $token,
    'admin' => [
        'id'   => (int)$admin['id'],
        'name' => $admin['name'],
        'email' => $admin['email'],
        'role' => $admin['role'],
    ],
]);

