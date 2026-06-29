<?php
define('APP_ENV',  'production');
define('APP_NAME', 'Sri Manikanta Pooja Stores');

$protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http";
$host = isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : 'localhost';
define('APP_URL',  $protocol . '://' . $host);

// ── Database (cPanel Production) ──
define('DB_HOST',    'localhost');
define('DB_NAME',    'manikant_smps_db');
define('DB_USER',    'manikant_smps_user');
define('DB_PASS',    'Sai2353L@1234');
define('DB_PORT',    3306);
define('DB_CHARSET', 'utf8mb4');

define('JWT_SECRET', 'SmPs@LocalTest#2025$PoOjA&StOrE^DiLsUkH~9110582086');
define('JWT_EXPIRY', 60 * 60 * 24 * 30);

define('RAZORPAY_KEY_ID',     '');
define('RAZORPAY_KEY_SECRET', '');
define('RAZORPAY_CURRENCY',   'INR');

define('MAIL_HOST',       'mail.manikantapoojastore.com');
define('MAIL_PORT',       587);
define('MAIL_USERNAME',   'orders@manikantapoojastore.com');
define('MAIL_PASSWORD',   'Sai2353L@123');
define('MAIL_FROM_EMAIL', 'orders@manikantapoojastore.com');
define('MAIL_FROM_NAME',  'Sri Manikanta Pooja Stores');
define('MAIL_ENCRYPTION', 'tls');

define('OWNER_EMAIL',    'orders@manikantapoojastore.com');
define('OWNER_WHATSAPP', '919110582086');
define('OWNER_NAME',     'Manikanta');
define('SMS_API_KEY',    'eAtCfY3ZrRqmoGBLVuwOSQijsUX2l9n6dkEWNaFTzDvHMpPKg8ACKL5gxy2wN6teHmYV0c8TlQZbjRXU');

define('WHATSAPP_API_ENABLED', false);
define('WHATSAPP_API_KEY',     '');
define('WHATSAPP_API_URL',     '');

define('STORE_PHONE1',    '+919110582086');
define('STORE_PHONE2',    '+919849985423');
define('STORE_WHATSAPP',  '919849985423');
define('STORE_INSTAGRAM', 'https://www.instagram.com/srimanikanta_poojastores?igsh=MWNqYmdxeGl1Nm9qdw==');
define('STORE_ADDRESS',   'Konark Theatre Ln, Dilsukhnagar, Hyderabad, Telangana 500060');

define('FREE_DELIVERY_ABOVE', 599);
define('DELIVERY_CHARGE',     60);

define('RATE_LIMIT_LOGIN_ATTEMPTS', 5);
define('RATE_LIMIT_LOGIN_WINDOW',   900);
define('RATE_LIMIT_OTP_ATTEMPTS',   3);
define('RATE_LIMIT_OTP_WINDOW',     600);

define('UPLOAD_DIR',      __DIR__ . '/../../uploads/products/');
define('UPLOAD_URL',      APP_URL . '/uploads/products/');
define('UPLOAD_MAX_SIZE', 2 * 1024 * 1024);
define('UPLOAD_ALLOWED',  ['image/jpeg', 'image/png', 'image/webp']);

define('DEBUG_MODE', false);
error_reporting(0);
ini_set('display_errors', 0);
ini_set('log_errors', 1);
ini_set('error_log', __DIR__ . '/../../logs/php_errors.log');
