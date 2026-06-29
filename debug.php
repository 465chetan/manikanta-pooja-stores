<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);
header('Content-Type: text/plain');

echo "=== SMPS SERVER DEBUGGER ===\n\n";

// 1. Check PHP Version
echo "PHP Version: " . phpversion() . "\n";

// 2. Check Extensions
echo "PDO Installed: " . (extension_loaded('pdo') ? 'YES' : 'NO') . "\n";
echo "PDO MySQL Installed: " . (extension_loaded('pdo_mysql') ? 'YES' : 'NO') . "\n\n";

// 3. Test Database Connection
require_once __DIR__ . '/api/config/config.php';

echo "Trying to connect to DB...\n";
echo "Host: " . DB_HOST . "\n";
echo "DB: " . DB_NAME . "\n";
echo "User: " . DB_USER . "\n\n";

try {
    $pdo = new PDO(
        'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=utf8mb4',
        DB_USER,
        DB_PASS,
        [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
    );
    echo "SUCCESS: Database connected perfectly!\n";
} catch (Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
    echo "\nIF YOU SEE 'Access denied': You probably forgot to add the User to the Database with 'All Privileges' in cPanel!\n";
}
?>
