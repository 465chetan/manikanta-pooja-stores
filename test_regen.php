<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "Loading config.php...<br>";
require_once __DIR__ . '/api/config/config.php';

echo "Loading database.php...<br>";
require_once __DIR__ . '/api/config/database.php';

echo "Loading regenerate_js.php...<br>";
require_once __DIR__ . '/api/admin/helpers/regenerate_js.php';

echo "Calling regenerate_products_js()...<br>";
try {
    regenerate_products_js();
    echo "Regeneration complete!<br>";
    
    // Check file size and content
    $path = __DIR__ . '/js/products.js';
    if (file_exists($path)) {
        echo "File size: " . filesize($path) . " bytes<br>";
        echo "Last modified: " . date("Y-m-d H:i:s", filemtime($path)) . "<br>";
    } else {
        echo "File does not exist!<br>";
    }
} catch (Throwable $e) {
    echo "Error: " . htmlspecialchars($e->getMessage()) . "<br>";
    echo "<pre>" . htmlspecialchars($e->getTraceAsString()) . "</pre><br>";
}
