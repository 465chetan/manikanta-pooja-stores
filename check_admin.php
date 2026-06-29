<?php
require __DIR__ . '/api/config/config.php';
$pdo = new PDO('mysql:host='.DB_HOST.';port='.DB_PORT.';dbname='.DB_NAME, DB_USER, DB_PASS);
$rows = $pdo->query('SELECT id, name, email, role, is_active FROM admin_users')->fetchAll(PDO::FETCH_ASSOC);
foreach($rows as $r) {
    echo $r['id'].' | '.$r['name'].' | '.$r['email'].' | '.$r['role'].' | active='.$r['is_active'].PHP_EOL;
}
echo 'Total: '.count($rows).' admin user(s)';
