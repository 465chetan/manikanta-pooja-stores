<?php
// ============================================================
// SRI MANIKANTA POOJA STORES — cPANEL PRODUCTION CONFIG
// Domain: https://manikantapoojastore.com
// ⚠️ UPLOAD THIS to cPanel replacing api/config/config.php
// ============================================================

define('APP_ENV',  'production');
define('APP_NAME', 'Sri Manikanta Pooja Stores');
define('APP_URL',  'https://manikantapoojastore.com');

// ── Database (cPanel MySQL) ──────────────────────────────────
// Get these from cPanel → MySQL Databases
define('DB_HOST',    'localhost');
define('DB_NAME',    'manikant_smps_db');      // your cPanel DB name
define('DB_USER',    'manikant_smps_user');    // your cPanel DB user
define('DB_PASS',    'Sai2353L@0807');         // your DB password
define('DB_PORT',    3306);
define('DB_CHARSET', 'utf8mb4');

// ── JWT ──────────────────────────────────────────────────────
define('JWT_SECRET', 'SmPs@Prod#2025$PoOjA&StOrE^DiLsUkH~9110582086!Live');
define('JWT_EXPIRY', 60 * 60 * 24 * 30); // 30 days

// ── Razorpay (add keys if you use online payments) ───────────
define('RAZORPAY_KEY_ID',     'rzp_live_XXXXXXXXXXXXXXX');
define('RAZORPAY_KEY_SECRET', 'XXXXXXXXXXXXXXXXXXXXXXXX');
define('RAZORPAY_CURRENCY',   'INR');

// ── Email via cPanel SMTP ────────────────────────────────────
// These settings use your orders@manikantapoojastore.com account
// Get password from cPanel → Email Accounts → Manage → Password
define('MAIL_HOST',       'mail.manikantapoojastore.com'); // cPanel mail server
define('MAIL_PORT',       587);                            // or 465 for SSL
define('MAIL_USERNAME',   'orders@manikantapoojastore.com');
define('MAIL_PASSWORD',   'Sai2353L@123');                // ✅ Set
define('MAIL_FROM_EMAIL', 'orders@manikantapoojastore.com');
define('MAIL_FROM_NAME',  'Sri Manikanta Pooja Stores');
define('MAIL_ENCRYPTION', 'tls');

// ── SMS OTP via Fast2SMS ─────────────────────────────────────
// Register FREE at https://www.fast2sms.com
// Go to Dashboard → Dev API → Copy your API Key
define('SMS_API_KEY', 'eAtCfY3ZrRqmoGBLVuwOSQijsUX2l9n6dkEWNaFTzDvHMpPKg8ACKL5gxy2wN6teHmYV0c8TlQZbjRXU');

// ── Store Owner ──────────────────────────────────────────────
define('OWNER_EMAIL',    'orders@manikantapoojastore.com');
define('OWNER_WHATSAPP', '919110582086');
define('OWNER_NAME',     'Manikanta');

// ── WhatsApp ─────────────────────────────────────────────────
define('WHATSAPP_API_ENABLED', false);
define('WHATSAPP_API_KEY',     '');
define('WHATSAPP_API_URL',     'https://api.interakt.ai/v1/public/message/');

// ── Store Info ───────────────────────────────────────────────
define('STORE_PHONE1',    '+919110582086');
define('STORE_PHONE2',    '+919849985423');
define('STORE_WHATSAPP',  '919849985423');
define('STORE_INSTAGRAM', 'https://www.instagram.com/srimanikanta_poojastores?igsh=MWNqYmdxeGl1Nm9qdw==');
define('STORE_ADDRESS',   'Konark Theatre Ln, Dilsukhnagar, Hyderabad, Telangana 500060');

// ── Delivery ─────────────────────────────────────────────────
define('FREE_DELIVERY_ABOVE', 599);
define('DELIVERY_CHARGE',     60);

// ── Security ─────────────────────────────────────────────────
define('RATE_LIMIT_LOGIN_ATTEMPTS', 5);
define('RATE_LIMIT_LOGIN_WINDOW',   900);
define('RATE_LIMIT_OTP_ATTEMPTS',   3);
define('RATE_LIMIT_OTP_WINDOW',     600);

// ── Uploads ──────────────────────────────────────────────────
define('UPLOAD_DIR',      __DIR__ . '/../../uploads/products/');
define('UPLOAD_URL',      APP_URL . '/uploads/products/');
define('UPLOAD_MAX_SIZE', 2 * 1024 * 1024); // 2MB
define('UPLOAD_ALLOWED',  ['image/jpeg', 'image/png', 'image/webp']);

// ── Debug: ALWAYS OFF in production ──────────────────────────
define('DEBUG_MODE', false);
error_reporting(0);
ini_set('display_errors', 0);
ini_set('log_errors', 1);
ini_set('error_log', __DIR__ . '/../../logs/php_errors.log');
