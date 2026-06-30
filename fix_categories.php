<?php
require_once __DIR__ . '/api/config/config.php';
require_once __DIR__ . '/api/config/database.php';
require_once __DIR__ . '/api/admin/helpers/regenerate_js.php';

try {
    $pdo = db();
    
    // Update Dhoop Sticks
    $stmt1 = $pdo->prepare("UPDATE categories SET image = 'images/cat_dhoop.png' WHERE slug = 'dhoop-sticks'");
    $stmt1->execute();
    
    // Update More and Other
    $stmt2 = $pdo->prepare("UPDATE categories SET image = 'images/cat_other.png' WHERE slug = 'more-and-other'");
    $stmt2->execute();
    
    echo "Categories updated successfully.\n";
    
    // Regenerate JS
    regenerate_products_js($pdo);
    echo "JavaScript regenerated successfully.\n";
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
