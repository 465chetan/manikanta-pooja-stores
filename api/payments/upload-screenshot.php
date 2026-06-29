<?php
// â”€â”€ POST /api/payments/upload-screenshot.php â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€
// Allows logged-in customers to upload UPI payment screenshots
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../helpers/response.php';
require_once __DIR__ . '/../helpers/auth.php';

// Must allow CORS and multipart
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Headers: Authorization, Content-Type');
header('Access-Control-Allow-Methods: POST, OPTIONS');
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') { http_response_code(200); exit; }

requireMethod('POST');
$user = optionalAuth();

if (empty($_FILES['screenshot'])) {
    error('No screenshot uploaded.', 400);
}

$file    = $_FILES['screenshot'];
$maxSize = 5 * 1024 * 1024; // 5MB
$allowed = ['image/jpeg', 'image/png', 'image/webp', 'image/jpg'];

if ($file['error'] !== UPLOAD_ERR_OK) error('File upload error: ' . $file['error'], 400);
if ($file['size'] > $maxSize)         error('File too large. Maximum is 5MB.', 400);

$finfo    = new finfo(FILEINFO_MIME_TYPE);
$mimeType = $finfo->file($file['tmp_name']);
if (!in_array($mimeType, $allowed, true)) {
    error('Invalid file. Only JPEG and PNG images are allowed.', 400);
}

// Save to uploads/screenshots/
$uploadDir = __DIR__ . '/../../uploads/screenshots/';
if (!is_dir($uploadDir)) mkdir($uploadDir, 0755, true);

$ext      = in_array($mimeType, ['image/jpeg','image/jpg']) ? 'jpg' : 'png';
$uid      = $user ? $user['user_id'] : 'guest';
$filename = 'ss_' . $uid . '_' . time() . '_' . bin2hex(random_bytes(4)) . '.' . $ext;
$destPath = $uploadDir . $filename;

if (!move_uploaded_file($file['tmp_name'], $destPath)) {
    serverError('Failed to save screenshot. Check server permissions.');
}

success('Screenshot uploaded.', [
    'filename' => $filename,
    'path'     => 'uploads/screenshots/' . $filename,
]);

