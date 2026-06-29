<?php
require_once __DIR__ . '/api/config/config.php';
require_once __DIR__ . '/api/config/database.php';
require_once __DIR__ . '/api/helpers/auth.php';

$payload = [
    'user_id' => 1,
    'email'   => 'chethansailaggoni@gmail.com',
    'iat'     => time(),
    'exp'     => time() + JWT_EXPIRY
];
$token = jwtEncode($payload);

$url = 'http://localhost/smps/api/orders/list.php?page=1';
$ch = curl_init($url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Authorization: Bearer ' . $token,
    'Content-Type: application/json'
]);
$response = curl_exec($ch);
$httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

echo "HTTP Code: " . $httpcode . "\n";
echo "Response: " . $response . "\n";
?>
