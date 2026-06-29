<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);
require 'api/admin/helpers/regenerate_js.php';
try {
    regenerate_products_js();
    echo 'OK';
} catch (Exception $e) {
    echo 'Error: ' . $e->getMessage() . "\n" . $e->getTraceAsString();
}
