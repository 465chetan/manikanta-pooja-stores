<?php
// ── POST /api/auth/forgot-password.php ────────────────────────
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../helpers/response.php';
require_once __DIR__ . '/../helpers/validator.php';
require_once __DIR__ . '/../helpers/auth.php';
require_once __DIR__ . '/../helpers/notifications.php';

setHeaders();
requireMethod('POST');

$body = getBody();

$v = Validator::make($body, ['email' => 'required|email']);
if ($v->fails()) error($v->firstError(), 422);

$email = strtolower(trim($body['email']));
$ip    = $_SERVER['REMOTE_ADDR'] ?? 'unknown';

checkRateLimit("$ip:$email", 'forgot_password', RATE_LIMIT_OTP_ATTEMPTS, RATE_LIMIT_OTP_WINDOW);

$pdo  = db();
$stmt = $pdo->prepare('SELECT id, full_name, email FROM users WHERE email = ? AND is_active = 1');
$stmt->execute([$email]);
$user = $stmt->fetch();

// Always return success even if user not found (prevents user enumeration)
if (!$user) {
    success('If this email is registered, you will receive an OTP shortly.');
}

$otp     = generateOTP();
$expires = date('Y-m-d H:i:s', time() + 600); // 10 minutes

$pdo->prepare('UPDATE users SET otp_code = ?, otp_expires = ?, otp_attempts = 0 WHERE id = ?')
    ->execute([$otp, $expires, $user['id']]);

// Send OTP via Email (100% FREE via cPanel SMTP)
$html = getEmailTemplate('password_reset_otp', [
    '{{CUSTOMER_NAME}}' => htmlspecialchars($user['full_name']),
    '{{OTP}}'           => $otp,
]);
$sent = sendEmail($user['email'], $user['full_name'], 'Password Reset OTP — Sri Manikanta Pooja Stores', $html);

$response = ['expires_in' => 600];

// In development, return OTP for testing
if (DEBUG_MODE) {
    $response['debug_otp'] = $otp;
}

success('OTP sent to your email. Please check your inbox (and spam folder).', $response);
