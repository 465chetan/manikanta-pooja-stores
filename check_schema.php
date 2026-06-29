<?php
require 'api/config/config.php';
require 'api/config/database.php';
$pdo = db();
$stmt = $pdo->query('DESCRIBE products');
print_r($stmt->fetchAll(PDO::FETCH_ASSOC));
