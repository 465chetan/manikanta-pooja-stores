<?php
require __DIR__ . '/api/config/config.php';
$pdo = new PDO('mysql:host='.DB_HOST.';port='.DB_PORT.';dbname='.DB_NAME, DB_USER, DB_PASS);

// Set password "Admin@123" for both admin accounts
$newPassword = password_hash('Admin@123', PASSWORD_BCRYPT);
$pdo->prepare('UPDATE admin_users SET password_hash = ? WHERE email IN (?, ?)')
    ->execute([$newPassword, 'admin@srimanikanta.com', 'admin@smps.com']);

echo 'Passwords updated successfully!';
echo PHP_EOL.'Both accounts now use password: Admin@123';
