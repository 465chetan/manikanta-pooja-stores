<?php
// Fix category images in the database so they persist permanently
require_once __DIR__ . '/api/config/config.php';
require_once __DIR__ . '/api/config/database.php';
require_once __DIR__ . '/api/admin/helpers/regenerate_js.php';

try {
    $pdo = db();
    
    $updates = [
        'camphor'       => 'images/cat_camphor.png',
        'kumkum'        => 'images/cat_kumkum.png',
        'oils'          => 'images/cat_oils.png',
        'diyas'         => 'images/cat_diyas.png',
        'photos'        => 'images/cat_photos.png',
        'idols'         => 'images/cat_idols.png',
        'thali'         => 'images/cat_thali.png',
        'malas'         => 'images/cat_malas.png',
        'havan'         => 'images/cat_havan.png',
        'festivals'     => 'images/cat_festival.png',
        'wedding'       => 'images/cat_wedding.png',
        'dhoop-sticks'  => 'images/cat_dhoop.png',
        'more-and-other'=> 'images/cat_other.png',
        'agarbatti'     => 'images/cat_agarbatti.png',
    ];
    
    $stmt = $pdo->prepare("UPDATE categories SET image = :img WHERE slug = :slug");
    
    foreach ($updates as $slug => $img) {
        $stmt->execute([':img' => $img, ':slug' => $slug]);
        $rows = $stmt->rowCount();
        echo "Updated '$slug' -> $img ($rows row updated)\n";
    }
    
    echo "\nAll categories updated!\n";
    
    // Regenerate the JS file
    regenerate_products_js($pdo);
    echo "JavaScript regenerated successfully!\n";
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
