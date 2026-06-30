<?php
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/regenerate_js.php';

echo "<h2>Regenerating js/products.js</h2>";

try {
    regenerate_products_js();
    echo "<h3 style='color:green;'>SUCCESS: js/products.js was regenerated successfully!</h3>";
    echo "<p>Go to your website and hard refresh (Ctrl + F5). The deleted products should now be gone.</p>";
} catch (Exception $e) {
    echo "<h3 style='color:red;'>ERROR: " . $e->getMessage() . "</h3>";
    echo "<p>Please ensure that the <b>js/</b> folder and <b>js/products.js</b> have the correct file permissions on cPanel (chmod 755 for folder, 664 for file).</p>";
}
