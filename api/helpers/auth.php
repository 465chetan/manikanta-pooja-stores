<?php
// ============================================================
// SRI MANIKANTA POOJA STORES â€” JWT AUTH HELPERS
// ============================================================

require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/database.php';

/**
 * Generate a JWT token (HS256, pure PHP â€” no libraries needed)
 */
function jwtEncode(array $payload): string {
    $header  = base64UrlEncode(json_encode(['alg' => 'HS256', 'typ' => 'JWT']));
    $payload = base64UrlEncode(json_encode($payload));
    $sig     = base64UrlEncode(hash_hmac('sha256', "$header.$payload", JWT_SECRET, true));
    return "$header.$payload.$sig";
}

/**
 * Decode and verify a JWT token. Returns payload array or null on failure.
 */
function jwtDecode(string $token): ?array {
    $parts = explode('.', $token);
    if (count($parts) !== 3) return null;

    [$header, $payload, $sig] = $parts;
    $expectedSig = base64UrlEncode(hash_hmac('sha256', "$header.$payload", JWT_SECRET, true));

    // Constant-time comparison to prevent timing attacks
    if (!hash_equals($expectedSig, $sig)) return null;

    $data = json_decode(base64UrlDecode($payload), true);
    if (!$data) return null;

    // Check expiry
    if (isset($data['exp']) && $data['exp'] < time()) return null;

    return $data;
}

function base64UrlEncode(string $data): string {
    return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
}

function base64UrlDecode(string $data): string {
    return base64_decode(strtr($data, '-_', '+/') . str_repeat('=', (4 - strlen($data) % 4) % 4));
}

/**
 * Extract JWT from Authorization header or cookie
 */
function getBearerToken(): ?string {
    $auth = $_SERVER['HTTP_AUTHORIZATION'] ?? $_SERVER['REDIRECT_HTTP_AUTHORIZATION'] ?? '';
    
    // Fallback for Apache if header is stripped
    if (empty($auth) && function_exists('apache_request_headers')) {
        $headers = apache_request_headers();
        $auth = $headers['Authorization'] ?? $headers['authorization'] ?? '';
    }
    
    if (preg_match('/Bearer\s+(.+)/i', $auth, $m)) return $m[1];
    
    // Also check cookie (for browser-based requests)
    return $_COOKIE['smps_token'] ?? null;
}

/**
 * Require a valid customer login. Returns user payload.
 */
function requireAuth(): array {
    $token = getBearerToken();
    if (!$token) unauthorized('Please login to continue.');

    $payload = jwtDecode($token);
    if (!$payload || !isset($payload['user_id'])) unauthorized('Session expired. Please login again.');

    // Verify user still exists and is active
    $stmt = db()->prepare('SELECT id, full_name, mobile, email, is_active FROM users WHERE id = ?');
    $stmt->execute([$payload['user_id']]);
    $user = $stmt->fetch();

    if (!$user || !$user['is_active']) unauthorized('Account not found or deactivated.');

    return array_merge($payload, $user);
}

/**
 * Require a valid admin login. Returns admin payload.
 */
function requireAdmin(): array {
    $token = getBearerToken();
    if (!$token) unauthorized('Admin access required.');

    $payload = jwtDecode($token);
    if (!$payload || !isset($payload['admin_id'])) unauthorized('Admin session expired.');

    $stmt = db()->prepare('SELECT id, name, email, role, is_active FROM admin_users WHERE id = ?');
    $stmt->execute([$payload['admin_id']]);
    $admin = $stmt->fetch();

    if (!$admin || !$admin['is_active']) unauthorized('Admin account not found or deactivated.');

    return array_merge($payload, $admin);
}

/**
 * Optional auth â€” returns user if logged in, null if not
 */
function optionalAuth(): ?array {
    try {
        return requireAuth();
    } catch (\Throwable $e) {
        return null;
    }
}

/**
 * Hash a password securely
 */
function hashPassword(string $password): string {
    return password_hash($password, PASSWORD_BCRYPT, ['cost' => 12]);
}

/**
 * Verify a password against its hash
 */
function verifyPassword(string $password, string $hash): bool {
    return password_verify($password, $hash);
}

/**
 * Generate a random 6-digit OTP
 */
function generateOTP(): string {
    return str_pad((string) random_int(100000, 999999), 6, '0', STR_PAD_LEFT);
}

/**
 * Generate a random alphanumeric string (for tokens, etc.)
 */
function generateToken(int $length = 32): string {
    return bin2hex(random_bytes($length / 2));
}

/**
 * Generate order number: SMPS-YYYY-NNNNN
 */
function generateOrderNumber(): string {
    $year   = date('Y');
    $random = str_pad((string) random_int(1, 99999), 5, '0', STR_PAD_LEFT);
    return "SMPS-$year-$random";
}

/**
 * Rate limiting â€” protect against brute force attacks
 */
function checkRateLimit(string $identifier, string $action, int $maxAttempts, int $windowSeconds): void {
    $pdo  = db();
    $now  = date('Y-m-d H:i:s');
    $stmt = $pdo->prepare('SELECT * FROM rate_limits WHERE identifier = ? AND action = ?');
    $stmt->execute([$identifier, $action]);
    $row = $stmt->fetch();

    if ($row) {
        // Check if currently blocked
        if ($row['blocked_until'] && $row['blocked_until'] > $now) {
            $remaining = ceil((strtotime($row['blocked_until']) - time()) / 60);
            error("Too many attempts. Please try again in $remaining minute(s).", 429);
        }

        // Check if outside the window (reset)
        $windowAgo = date('Y-m-d H:i:s', time() - $windowSeconds);
        if ($row['last_attempt'] < $windowAgo) {
            // Reset the counter
            $pdo->prepare('UPDATE rate_limits SET attempts=1, blocked_until=NULL, last_attempt=NOW() WHERE identifier=? AND action=?')
                ->execute([$identifier, $action]);
            return;
        }

        // Increment and check
        $newAttempts = $row['attempts'] + 1;
        if ($newAttempts >= $maxAttempts) {
            $blockUntil = date('Y-m-d H:i:s', time() + $windowSeconds);
            $pdo->prepare('UPDATE rate_limits SET attempts=?, blocked_until=?, last_attempt=NOW() WHERE identifier=? AND action=?')
                ->execute([$newAttempts, $blockUntil, $identifier, $action]);
            error('Too many failed attempts. Your account is temporarily locked.', 429);
        } else {
            $pdo->prepare('UPDATE rate_limits SET attempts=?, last_attempt=NOW() WHERE identifier=? AND action=?')
                ->execute([$newAttempts, $identifier, $action]);
        }
    } else {
        // First attempt
        $pdo->prepare('INSERT INTO rate_limits (identifier, action, attempts, last_attempt) VALUES (?, ?, 1, NOW()) ON DUPLICATE KEY UPDATE attempts=attempts+1, last_attempt=NOW()')
            ->execute([$identifier, $action]);
    }
}

/**
 * Clear rate limit on successful action (e.g., after successful login)
 */
function clearRateLimit(string $identifier, string $action): void {
    db()->prepare('DELETE FROM rate_limits WHERE identifier = ? AND action = ?')
        ->execute([$identifier, $action]);
}

