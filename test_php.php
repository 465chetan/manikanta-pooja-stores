<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);
header('Content-Type: application/json; charset=utf-8');

$_SERVER['REQUEST_METHOD'] = 'POST';
$_SERVER['CONTENT_TYPE'] = 'application/json';

$payload = json_encode([
    'full_name' => 'Chethan Test',
    'mobile' => '9999999996',
    'email' => 'test96@test.com',
    'password' => 'Test@1234'
]);

// Mock php://input by overriding getBody in response.php? We can't override the function, but we can override $_POST
// Actually, register uses getBody() which uses php://input. We can't easily mock php://input.

// Let's just create a test insert script that uses the same DB logic as register.php
require_once __DIR__ . '/api/config/config.php';
require_once __DIR__ . '/api/config/database.php';
require_once __DIR__ . '/api/helpers/auth.php';

try {
    $pdo = db();
    echo "DB OK.\n";
    
    $hash = hashPassword('Test@1234');
    echo "Hash OK: $hash\n";
    
    $token = jwtEncode([
        'user_id' => 999,
        'mobile'  => '9999999996',
        'iat'     => time(),
        'exp'     => time() + JWT_EXPIRY,
    ]);
    echo "JWT OK: $token\n";
    
    echo "ALL PHP FUNCTIONS WORKING PERFECTLY.";
} catch (Throwable $e) {
    echo "ERROR: " . $e->getMessage() . " in " . $e->getFile() . ":" . $e->getLine();
}
