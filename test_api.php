<?php
require 'api/config/config.php';
require 'api/config/database.php';
require 'api/helpers/auth.php';

// Generate admin token
$token = jwtEncode([
    'admin_id' => 1,
    'role' => 'superadmin',
    'exp' => time() + 3600
]);

$ch = curl_init('http://localhost/smps/api/admin/products/list.php');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Authorization: Bearer ' . $token
]);
$response = curl_exec($ch);
$httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

echo "HTTP $httpcode\n";
echo "Response: $response\n";
