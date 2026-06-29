<?php
require 'c:/Users/Laggoni chethan sai/trail web/api/config/database.php';
$db = Database::getConnection();
try {
    $db->exec('ALTER TABLE payments ADD COLUMN transaction_id VARCHAR(100) NULL AFTER amount');
    echo 'Column added.';
} catch (Exception $e) {
    echo $e->getMessage();
}
