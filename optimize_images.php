<?php
/**
 * Image Re-Optimization Script
 * Compresses and resizes all WebP images to optimal display sizes
 * Run ONCE at: manikantapoojastore.com/optimize_images.php
 * DELETE this file after running!
 */

set_time_limit(300);
ini_set('memory_limit', '256M');

$results = [];
$imageDir = __DIR__ . '/images/';

// Define optimal sizes for each image type
$sizeRules = [
    // Category thumbnails - displayed at 300x300
    'cat_'       => ['w' => 400,  'h' => 400,  'q' => 70],
    // Hero slides - displayed at full width ~1440px
    'hero_'      => ['w' => 1280, 'h' => 640,  'q' => 75],
    // Festival images
    'festival_'  => ['w' => 600,  'h' => 400,  'q' => 72],
    // Store/gallery images
    'store_'     => ['w' => 800,  'h' => 600,  'q' => 72],
    'gallery_'   => ['w' => 600,  'h' => 500,  'q' => 72],
    // Product images
    'prod_'      => ['w' => 600,  'h' => 600,  'q' => 72],
];

$defaultRule = ['w' => 800, 'h' => 800, 'q' => 72];

function getRule($filename, $rules, $default) {
    foreach ($rules as $prefix => $rule) {
        if (str_starts_with($filename, $prefix)) return $rule;
    }
    return $default;
}

function resizeWebP($srcPath, $destPath, $maxW, $maxH, $quality) {
    $before = filesize($srcPath);
    
    // Read source
    $src = @imagecreatefromwebp($srcPath);
    if (!$src) return ['error' => 'Cannot read: ' . basename($srcPath)];
    
    $origW = imagesx($src);
    $origH = imagesy($src);
    
    // Calculate new dimensions (maintain aspect ratio, don't upscale)
    $ratio = min($maxW / $origW, $maxH / $origH, 1.0);
    $newW  = (int)round($origW * $ratio);
    $newH  = (int)round($origH * $ratio);
    
    if ($ratio >= 1.0 && $quality >= 80) {
        // Already small enough and good quality - skip
        imagedestroy($src);
        return ['skipped' => true, 'size' => $before];
    }
    
    // Create resized image
    $dst = imagecreatetruecolor($newW, $newH);
    imagealphablending($dst, false);
    imagesavealpha($dst, true);
    $transparent = imagecolorallocatealpha($dst, 0, 0, 0, 127);
    imagefilledrectangle($dst, 0, 0, $newW, $newH, $transparent);
    
    imagecopyresampled($dst, $src, 0, 0, 0, 0, $newW, $newH, $origW, $origH);
    
    imagewebp($dst, $destPath, $quality);
    
    imagedestroy($src);
    imagedestroy($dst);
    
    $after = filesize($destPath);
    return [
        'before' => $before,
        'after'  => $after,
        'saved'  => $before - $after,
        'pct'    => round(($before - $after) / $before * 100) . '%',
        'dims'   => "{$origW}x{$origH} → {$newW}x{$newH}"
    ];
}

// Process all WebP files
$files = glob($imageDir . '*.webp');
$totalSaved = 0;
$totalBefore = 0;

foreach ($files as $srcPath) {
    $filename = basename($srcPath);
    $rule = getRule($filename, $sizeRules, $defaultRule);
    
    $result = resizeWebP($srcPath, $srcPath, $rule['w'], $rule['h'], $rule['q']);
    $result['file'] = $filename;
    $results[] = $result;
    
    if (isset($result['saved'])) {
        $totalSaved += $result['saved'];
        $totalBefore += $result['before'];
    }
}

$savedMB  = round($totalSaved / 1048576, 2);
$beforeMB = round($totalBefore / 1048576, 2);
?>
<!DOCTYPE html>
<html>
<head>
<title>Image Optimization Results</title>
<style>
  body { font-family: monospace; background: #111; color: #0f0; padding: 20px; }
  h1 { color: #ff0; }
  .summary { background: #222; padding: 15px; border: 1px solid #0f0; margin: 20px 0; font-size: 18px; }
  table { width: 100%; border-collapse: collapse; margin-top: 20px; }
  th { background: #333; color: #ff0; padding: 8px; text-align: left; }
  td { padding: 6px 8px; border-bottom: 1px solid #333; }
  .saved { color: #0f0; }
  .skipped { color: #888; }
  .error { color: #f00; }
  .warning { background: #f00; color: #fff; padding: 15px; margin-top: 30px; font-size: 16px; font-weight: bold; }
</style>
</head>
<body>
<h1>✅ Image Optimization Complete</h1>

<div class="summary">
  📦 Total processed: <?= count($files) ?> images<br>
  📉 Space saved: <strong><?= $savedMB ?> MB</strong> out of <?= $beforeMB ?> MB<br>
  💡 Run PageSpeed Insights now to see the improvement!
</div>

<table>
  <tr><th>File</th><th>Before</th><th>After</th><th>Saved</th><th>Dimensions</th></tr>
  <?php foreach ($results as $r): ?>
  <tr>
    <td><?= $r['file'] ?></td>
    <?php if (isset($r['error'])): ?>
      <td colspan="4" class="error">❌ <?= $r['error'] ?></td>
    <?php elseif (isset($r['skipped'])): ?>
      <td colspan="4" class="skipped">⏭️ Skipped (already optimized, <?= round($r['size']/1024) ?>KB)</td>
    <?php else: ?>
      <td><?= round($r['before']/1024) ?> KB</td>
      <td><?= round($r['after']/1024) ?> KB</td>
      <td class="saved">-<?= $r['pct'] ?> (-<?= round($r['saved']/1024) ?> KB)</td>
      <td><?= $r['dims'] ?></td>
    <?php endif; ?>
  </tr>
  <?php endforeach; ?>
</table>

<div class="warning">
  ⚠️ IMPORTANT: Delete this file after running!<br>
  Go to cPanel File Manager → public_html → delete optimize_images.php
</div>
</body>
</html>
