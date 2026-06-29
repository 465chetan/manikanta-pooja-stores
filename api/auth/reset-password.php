<?php
// ── POST /api/auth/reset-password.php ────────────────────────────
// action=verify  → validates OTP only
// action=reset   → validates OTP + sets new password
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../helpers/response.php';
require_once __DIR__ . '/../helpers/validator.php';
require_once __DIR__ . '/../helpers/auth.php';

setHeaders();
requireMethod('POST');

$body   = getBody();
$action = $body['action'] ?? 'reset';

$v = Validator::make($body, [
    'email' => 'required|email',
    'otp'   => 'required|min:6|max:6',
]);
if ($v->fails()) error($v->firstError(), 422);

if ($action === 'reset') {
    $vp = Validator::make($body, ['new_password' => 'required|min:8']);
    if ($vp->fails()) error($vp->firstError(), 422);
}

$email = strtolower(trim($body['email']));
$otp   = trim($body['otp']);
$now   = date('Y-m-d H:i:s');

$pdo  = db();
$stmt = $pdo->prepare('SELECT id, otp_code, otp_expires, otp_attempts FROM users WHERE email = ? AND is_active = 1');
$stmt->execute([$email]);
$user = $stmt->fetch();

if (!$user)              error('Invalid request.', 400);
if (!$user['otp_code'])  error('No OTP requested. Please request a new OTP.', 400);
if ($user['otp_expires'] < $now) error('OTP has expired. Please request a new one.', 400);
if ($user['otp_attempts'] >= 5)  error('Too many incorrect OTP attempts. Please request a new OTP.', 429);

if ($user['otp_code'] !== $otp) {
    $pdo->prepare('UPDATE users SET otp_attempts = otp_attempts + 1 WHERE id = ?')->execute([$user['id']]);
    error('Incorrect OTP. Please try again.', 400);
}

// OTP valid
if ($action === 'verify') {
    success('OTP verified successfully.', ['reset_token' => $otp]);
}

// Reset password
$hash = hashPassword($body['new_password']);
$pdo->prepare('UPDATE users SET password_hash = ?, otp_code = NULL, otp_expires = NULL, otp_attempts = 0 WHERE id = ?')
    ->execute([$hash, $user['id']]);

success('Password reset successfully! You can now login with your new password.');
