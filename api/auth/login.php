<?php
// â”€â”€ POST /api/auth/login.php â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€
// Accepts: { email: "...", password: "..." }
// OR:      { mobile: "...", password: "..." }  (backward compat)
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../helpers/response.php';
require_once __DIR__ . '/../helpers/validator.php';
require_once __DIR__ . '/../helpers/auth.php';

setHeaders();
requireMethod('POST');

$body     = getBody();
$password = $body['password'] ?? '';
$email    = trim($body['email']   ?? '');
$mobile   = trim($body['mobile']  ?? '');

// Require either email or mobile
if (empty($email) && empty($mobile)) {
    error('Please provide your email address and password.', 422);
}
if (empty($password)) {
    error('Please enter your password.', 422);
}

$ip  = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
$pdo = db();

// Find user by email (primary) or mobile (backward compat)
if (!empty($email)) {
    // Login by email
    $email = strtolower($email);
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        error('Please enter a valid email address.', 422);
    }
    $limitKey = "$ip:email:$email";
    checkRateLimit($limitKey, 'login', RATE_LIMIT_LOGIN_ATTEMPTS, RATE_LIMIT_LOGIN_WINDOW);

    $stmt = $pdo->prepare(
        'SELECT id, full_name, mobile, email, password_hash, is_active FROM users WHERE LOWER(email) = ?'
    );
    $stmt->execute([$email]);
} else {
    // Login by mobile (backward compatibility)
    $mobile   = sanitizeMobile($mobile);
    $limitKey = "$ip:mobile:$mobile";
    checkRateLimit($limitKey, 'login', RATE_LIMIT_LOGIN_ATTEMPTS, RATE_LIMIT_LOGIN_WINDOW);

    $stmt = $pdo->prepare(
        'SELECT id, full_name, mobile, email, password_hash, is_active FROM users WHERE mobile = ?'
    );
    $stmt->execute([$mobile]);
}

$user = $stmt->fetch();

// Verify user exists AND password matches
if (!$user || !verifyPassword($password, $user['password_hash'])) {
    error('Incorrect email or password. Please try again.', 401);
}

if (!$user['is_active']) {
    error('Your account has been deactivated. Please contact support.', 403);
}

// Success â€” clear rate limit and issue JWT token
clearRateLimit($limitKey, 'login');

$token = jwtEncode([
    'user_id' => (int)$user['id'],
    'mobile'  => $user['mobile'],
    'email'   => $user['email'],
    'iat'     => time(),
    'exp'     => time() + JWT_EXPIRY,
]);

success('Login successful! Welcome back.', [
    'token' => $token,
    'user'  => [
        'id'        => (int)$user['id'],
        'full_name' => $user['full_name'],
        'mobile'    => $user['mobile'],
        'email'     => $user['email'],
    ],
]);

