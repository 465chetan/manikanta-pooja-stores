<?php
// ============================================================
// SRI MANIKANTA POOJA STORES â€” JSON RESPONSE HELPERS
// ============================================================

/**
 * Send a standardized JSON response and exit
 */
function respond(bool $success, string $message, array $data = [], int $code = 200): void {
    http_response_code($code);
    echo json_encode(array_merge(
        ['success' => $success, 'message' => $message],
        $data
    ), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    exit;
}

function success(string $message = 'OK', array $data = [], int $code = 200): void {
    respond(true, $message, $data, $code);
}

function error(string $message, int $code = 400, array $data = []): void {
    respond(false, $message, $data, $code);
}

function notFound(string $message = 'Not found'): void {
    error($message, 404);
}

function unauthorized(string $message = 'Unauthorized. Please login.'): void {
    error($message, 401);
}

function forbidden(string $message = 'Access denied.'): void {
    error($message, 403);
}

function serverError(string $message = 'Internal server error.'): void {
    error($message, 500);
}

/**
 * Set CORS and content-type headers for every API response
 */
function setHeaders(): void {
    // Allow requests from same domain (handles http/https and www/non-www)
    $origin = $_SERVER['HTTP_ORIGIN'] ?? '';
    $host   = $_SERVER['HTTP_HOST'] ?? '';

    // Allow if origin matches this server's host OR no origin (same-origin requests)
    if (empty($origin) || str_contains($origin, $host)) {
        header("Access-Control-Allow-Origin: $origin");
    } else {
        // Allow the configured APP_URL as fallback
        header('Access-Control-Allow-Origin: ' . APP_URL);
    }

    header('Content-Type: application/json; charset=UTF-8');
    header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
    header('Access-Control-Allow-Headers: Content-Type, Authorization, X-CSRF-Token');
    
    // Prevent browser caching of API responses
    header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
    header('Cache-Control: post-check=0, pre-check=0', false);
    header('Pragma: no-cache');
    header('Access-Control-Allow-Credentials: true');
    header('X-Content-Type-Options: nosniff');
    header('X-Frame-Options: SAMEORIGIN');
    header('X-XSS-Protection: 1; mode=block');

    // Handle preflight OPTIONS request
    if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
        http_response_code(204);
        exit;
    }
}

/**
 * Get request body as parsed JSON or form data
 */
function getBody(): array {
    $contentType = $_SERVER['CONTENT_TYPE'] ?? '';
    if (str_contains($contentType, 'application/json')) {
        $raw = file_get_contents('php://input');
        return json_decode($raw, true) ?? [];
    }
    return array_merge($_GET, $_POST);
}

/**
 * Require a specific HTTP method
 */
function requireMethod(string ...$methods): void {
    if (!in_array($_SERVER['REQUEST_METHOD'], $methods, true)) {
        error('Method not allowed.', 405);
    }
}

