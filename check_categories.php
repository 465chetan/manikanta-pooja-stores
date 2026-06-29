<?php
require 'api/config/config.php';
require 'api/config/database.php';
$pdo = db();
print_r($pdo->query("DESCRIBE categories")->fetchAll(PDO::FETCH_ASSOC));
