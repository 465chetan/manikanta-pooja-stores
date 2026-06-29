<?php
// â”€â”€ POST /api/auth/register.php â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../helpers/response.php';
require_once __DIR__ . '/../helpers/validator.php';
require_once __DIR__ . '/../helpers/auth.php';

setHeaders();
requireMethod('POST');

$body = getBody();

// Validate input
$v = Validator::make($body, [
    'full_name' => 'required|string|min:2|max:100',
    'mobile'    => 'required|mobile',
    'email'     => 'max:150|email',
    'password'  => 'required|password|min:8',
]);

if ($v->fails()) {
    error($v->firstError(), 422, ['errors' => $v->errors()]);
}

$mobile   = sanitizeMobile($body['mobile']);
$email    = !empty($body['email']) ? strtolower(trim($body['email'])) : null;
$fullName = sanitize($body['full_name']);
$password = $body['password']; // Don't sanitize passwords

$pdo = db();

// Check if mobile already exists
$stmt = $pdo->prepare('SELECT id FROM users WHERE mobile = ?');
$stmt->execute([$mobile]);
if ($stmt->fetch()) {
    error('This mobile number is already registered. Please login instead.', 409);
}

// Check if email already exists (if provided)
if ($email) {
    $stmt = $pdo->prepare('SELECT id FROM users WHERE email = ?');
    $stmt->execute([$email]);
    if ($stmt->fetch()) {
        error('This email address is already registered.', 409);
    }
}

// Create user
$hash = hashPassword($password);
$stmt = $pdo->prepare('INSERT INTO users (full_name, mobile, email, password_hash, is_verified) VALUES (?, ?, ?, ?, 1)');
$stmt->execute([$fullName, $mobile, $email, $hash]);
$userId = (int)$pdo->lastInsertId();

// Generate JWT
$token = jwtEncode([
    'user_id' => $userId,
    'mobile'  => $mobile,
    'iat'     => time(),
    'exp'     => time() + JWT_EXPIRY,
]);

success('Account created successfully! Welcome to Sri Manikanta Pooja Stores.', [
    'token' => $token,
    'user'  => [
        'id'        => $userId,
        'full_name' => $fullName,
        'mobile'    => $mobile,
        'email'     => $email,
    ],
], 201);

