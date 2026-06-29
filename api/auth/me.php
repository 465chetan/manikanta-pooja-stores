<?php
// â”€â”€ GET /api/auth/me.php â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../helpers/response.php';
require_once __DIR__ . '/../helpers/auth.php';

setHeaders();
requireMethod('GET');

$user = requireAuth();

// Get unread notification count
$stmt = db()->prepare('SELECT COUNT(*) as unread FROM notifications WHERE user_id = ? AND is_read = 0');
$stmt->execute([$user['user_id']]);
$unread = (int)$stmt->fetchColumn();

success('OK', [
    'user' => [
        'id'        => $user['id'],
        'full_name' => $user['full_name'],
        'mobile'    => $user['mobile'],
        'email'     => $user['email'],
    ],
    'unread_notifications' => $unread,
]);

