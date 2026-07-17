<?php
// ── POST /api/admin/upload.php ────────────────────────────────
// Handles product image uploads (admin only)
// PHASE 5: Auto-converts uploaded images to WebP for performance
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../helpers/response.php';
require_once __DIR__ . '/../helpers/auth.php';

setHeaders();
requireMethod('POST');

$admin = requireAdmin();

if (empty($_FILES['image'])) {
    error('No image file uploaded.', 400);
}

$file      = $_FILES['image'];
$maxSize   = UPLOAD_MAX_SIZE;
$uploadDir = UPLOAD_DIR;

// Validate upload error
if ($file['error'] !== UPLOAD_ERR_OK) error('File upload error. Please try again.', 400);
if ($file['size'] > $maxSize) error('File too large. Maximum size is 2MB.', 400);

// Verify MIME type (don't trust extension alone)
$finfo    = new finfo(FILEINFO_MIME_TYPE);
$mimeType = $finfo->file($file['tmp_name']);
if (!in_array($mimeType, $allowed, true)) {
    error('Invalid file type. Only JPEG, PNG and WebP are allowed.', 400);
}

// Create upload directory if not exists
if (!is_dir($uploadDir)) {
    mkdir($uploadDir, 0755, true);
}

// Generate safe unique filename
$ext      = pathinfo($file['name'], PATHINFO_EXTENSION);
$filename = 'prod_' . time() . '_' . bin2hex(random_bytes(4)) . '.' . $ext;
$destPath = $uploadDir . $filename;

if (!move_uploaded_file($file['tmp_name'], $destPath)) {
    serverError('Failed to save image. Check server write permissions.');
}

success('Image uploaded successfully.', [
    'filename' => $filename,
    'url'      => UPLOAD_URL . $filename,
]);

