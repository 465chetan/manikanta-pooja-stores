<?php
require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/config/database.php';

// Force XML content type
header('Content-Type: application/xml; charset=utf-8');

try {
    \ = db();
    
    // Get all active products
    \ = \->query("SELECT id, updated_at FROM products WHERE is_active = 1")->fetchAll();
    
    // Get all active categories
    \ = \->query("SELECT id FROM categories WHERE is_active = 1")->fetchAll();

} catch (Exception \) {
    // If DB fails, just output basic sitemap
    \ = [];
    \ = [];
}

\ = "https://manikantapoojastore.com";

echo '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
?>
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9"
        xmlns:image="http://www.google.com/schemas/sitemap-image/1.1">
  
  <!-- Core Static Pages -->
  <url>
    <loc><?= \ ?>/</loc>
    <changefreq>daily</changefreq>
    <priority>1.0</priority>
  </url>
  <url>
    <loc><?= \ ?>/shop.html</loc>
    <changefreq>daily</changefreq>
    <priority>0.9</priority>
  </url>
  <url>
    <loc><?= \ ?>/about.html</loc>
    <changefreq>monthly</changefreq>
    <priority>0.5</priority>
  </url>
  <url>
    <loc><?= \ ?>/contact.html</loc>
    <changefreq>monthly</changefreq>
    <priority>0.5</priority>
  </url>

  <!-- Dynamic Categories -->
  <?php foreach (\ as \): ?>
  <url>
    <loc><?= \ ?>/shop.html?category=<?= \['id'] ?></loc>
    <changefreq>weekly</changefreq>
    <priority>0.8</priority>
  </url>
  <?php endforeach; ?>

  <!-- Dynamic Products -->
  <?php foreach (\ as \): ?>
  <url>
    <loc><?= \ ?>/product.html?id=<?= \['id'] ?></loc>
    <lastmod><?= date('c', strtotime(\['updated_at'] ?? 'now')) ?></lastmod>
    <changefreq>weekly</changefreq>
    <priority>0.7</priority>
  </url>
  <?php endforeach; ?>

</urlset>
