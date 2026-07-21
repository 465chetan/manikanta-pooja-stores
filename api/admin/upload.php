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
if (!in_array($mimeType, UPLOAD_ALLOWED, true)) {
    error('Invalid file type. Only JPEG, PNG and WebP are allowed.', 400);
}

// Create upload directory if not exists
if (!is_dir($uploadDir)) {
    mkdir($uploadDir, 0755, true);
}

// Generate safe unique filename
$filename = 'prod_' . time() . '_' . bin2hex(random_bytes(4)) . '.webp';
$destPath = $uploadDir . $filename;

// Auto-convert to WebP
$image = null;
if ($mimeType === 'image/jpeg') {
    $image = @imagecreatefromjpeg($file['tmp_name']);
} elseif ($mimeType === 'image/png') {
    $image = @imagecreatefrompng($file['tmp_name']);
    if ($image !== false) {
        imagepalettetotruecolor($image);
        imagealphablending($image, true);
        imagesavealpha($image, true);
    }
} elseif ($mimeType === 'image/webp') {
    $image = @imagecreatefromwebp($file['tmp_name']);
}

if ($image !== false && $image !== null) {
    // Convert and save as WebP with 85% quality
    if (!imagewebp($image, $destPath, 85)) {
        imagedestroy($image);
        serverError('Failed to save WebP image.');
    }
    imagedestroy($image);
} else {
    // Fallback if GD fails (e.g. not installed or invalid image)
    if (!move_uploaded_file($file['tmp_name'], $destPath)) {
        serverError('Failed to save image. Check server write permissions.');
    }
}

success('Image uploaded successfully.', [
    'filename' => $filename,
    'url'      => UPLOAD_URL . $filename,
]);

