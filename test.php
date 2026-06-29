<?php
$passwords = ['root', 'password', 'Sai2353L@0807', '1234', '12345', '12345678', 'admin'];
foreach ($passwords as $p) {
    try {
        $pdo = new PDO('mysql:host=localhost;port=3306;charset=utf8mb4', 'root', $p, [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
        echo 'SUCCESS:' . $p;
        exit;
    } catch (Exception $e) { }
}
echo 'FAIL';
?>
