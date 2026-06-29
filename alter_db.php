<?php
require 'api/config/config.php';
require 'api/config/database.php';
$pdo = db();
$pdo->exec("ALTER TABLE products ADD COLUMN is_deleted TINYINT(1) DEFAULT 0 AFTER is_active");
echo "Added is_deleted column successfully.";
