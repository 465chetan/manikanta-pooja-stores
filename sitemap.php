<?php
require_once __DIR__ . '/api/config/config.php';
require_once __DIR__ . '/api/config/database.php';

header('Content-Type: application/xml; charset=utf-8');

\ = db();
\ = \->query('SELECT id, updated_at FROM products WHERE is_active = 1')->fetchAll();

echo '<?xml version=\
1.0\ encoding=\UTF-8\?>';
?>
<urlset xmlns=\http://www.sitemaps.org/schemas/sitemap/0.9\>
  <!-- Core Pages -->
  <url>
    <loc><?= APP_URL ?>/</loc>
    <changefreq>daily</changefreq>
    <priority>1.0</priority>
  </url>
  <url>
    <loc><?= APP_URL ?>/shop.html</loc>
    <changefreq>daily</changefreq>
    <priority>0.8</priority>
  </url>
  <url>
    <loc><?= APP_URL ?>/contact.html</loc>
    <changefreq>monthly</changefreq>
    <priority>0.5</priority>
  </url>
  <url>
    <loc><?= APP_URL ?>/about.html</loc>
    <changefreq>monthly</changefreq>
    <priority>0.5</priority>
  </url>
  
  <!-- Dynamic Products -->
  <?php foreach (\ as \): ?>
  <url>
    <loc><?= APP_URL ?>/product.html?id=<?= \['id'] ?></loc>
    <lastmod><?= date('Y-m-d', strtotime(\['updated_at'])) ?></lastmod>
    <changefreq>weekly</changefreq>
    <priority>0.7</priority>
  </url>
  <?php endforeach; ?>
</urlset>
