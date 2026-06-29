<?php
/**
 * SMPS AUTO-FIXER
 * Upload this to public_html/ and visit it once.
 * It will fix all PHP require_once paths automatically.
 * DELETE IT after running!
 */

ini_set('display_errors', 1);
error_reporting(E_ALL);
header('Content-Type: text/html; charset=utf-8');

$apiDir  = __DIR__ . '/api';
$fixed   = [];
$skipped = [];
$errors  = [];

// Walk through every PHP file in the api folder
$iterator = new RecursiveIteratorIterator(
    new RecursiveDirectoryIterator($apiDir, FilesystemIterator::SKIP_DOTS)
);

foreach ($iterator as $file) {
    if ($file->getExtension() !== 'php') continue;
    // Never touch config.php
    if ($file->getFilename() === 'config.php') { $skipped[] = $file->getPathname(); continue; }

    $content  = file_get_contents($file->getPathname());
    $original = $content;

    // Calculate correct relative depth
    $relPath = str_replace($apiDir . DIRECTORY_SEPARATOR, '', $file->getPathname());
    $depth   = substr_count($relPath, DIRECTORY_SEPARATOR); // 0=api root, 1=api/auth, 2=api/admin/orders

    // Build correct prefix based on depth
    if ($depth === 1) {
        // Files like api/auth/register.php — one level deep
        $configPrefix  = "/../config";
        $helperPrefix  = "/../helpers";
    } elseif ($depth === 2) {
        // Files like api/admin/orders/list.php — two levels deep
        $configPrefix  = "/../../config";
        $helperPrefix  = "/../../helpers";
    } else {
        $skipped[] = $file->getPathname() . " (unexpected depth)";
        continue;
    }

    // Fix ALL variations of config/helpers paths
    $patterns = [
        // Wrong: too many ../ or too few
        "#require_once __DIR__ \. '(/\.\.)+/config/config\.php'#"    => "require_once __DIR__ . '{$configPrefix}/config.php'",
        "#require_once __DIR__ \. '(/\.\.)+/config/database\.php'#"  => "require_once __DIR__ . '{$configPrefix}/database.php'",
        "#require_once __DIR__ \. '(/\.\.)+/helpers/response\.php'#" => "require_once __DIR__ . '{$helperPrefix}/response.php'",
        "#require_once __DIR__ \. '(/\.\.)+/helpers/auth\.php'#"     => "require_once __DIR__ . '{$helperPrefix}/auth.php'",
        "#require_once __DIR__ \. '(/\.\.)+/helpers/validator\.php'#"=> "require_once __DIR__ . '{$helperPrefix}/validator.php'",
        "#require_once __DIR__ \. '(/\.\.)+/helpers/notifications\.php'#"=> "require_once __DIR__ . '{$helperPrefix}/notifications.php'",
    ];

    foreach ($patterns as $pattern => $replacement) {
        $content = preg_replace($pattern, $replacement, $content);
    }

    if ($content !== $original) {
        if (file_put_contents($file->getPathname(), $content) !== false) {
            $fixed[] = str_replace($apiDir, '/api', $file->getPathname());
        } else {
            $errors[] = $file->getPathname();
        }
    } else {
        $skipped[] = str_replace($apiDir, '/api', $file->getPathname());
    }
}

// Also test DB connection after fixing
$dbOk = false;
$dbMsg = '';
try {
    require_once $apiDir . '/config/config.php';
    $pdo = new PDO(
        'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=utf8mb4',
        DB_USER, DB_PASS,
        [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
    );
    $dbOk = true;
    $dbMsg = 'Connected to MySQL successfully!';
} catch (Exception $e) {
    $dbMsg = 'DB Error: ' . $e->getMessage();
}
?>
<!DOCTYPE html>
<html>
<head>
<title>SMPS Auto-Fixer</title>
<style>
  body{font-family:monospace;background:#0f172a;color:#e2e8f0;padding:24px;max-width:900px;margin:0 auto}
  h1{color:#f97316}
  .ok{color:#22c55e} .err{color:#ef4444} .skip{color:#94a3b8}
  .box{background:#1e293b;border-radius:8px;padding:16px;margin:12px 0}
  .big{font-size:1.4rem;font-weight:bold;margin-bottom:8px}
  ul{margin:4px 0;padding-left:20px}
  .warn{background:#7c2d12;border-radius:8px;padding:16px;margin-top:20px;color:#fca5a5;font-size:1.1rem}
</style>
</head>
<body>
<h1>🔧 SMPS Auto-Fixer</h1>

<div class="box">
  <div class="big <?= $dbOk ? 'ok' : 'err' ?>">
    <?= $dbOk ? '✅ Database:' : '❌ Database:' ?> <?= htmlspecialchars($dbMsg) ?>
  </div>
</div>

<div class="box">
  <div class="big ok">✅ Fixed (<?= count($fixed) ?> files)</div>
  <ul>
    <?php foreach ($fixed as $f): ?>
      <li class="ok"><?= htmlspecialchars($f) ?></li>
    <?php endforeach; ?>
    <?php if (!$fixed): ?><li class="skip">No files needed fixing</li><?php endif; ?>
  </ul>
</div>

<div class="box">
  <div class="big skip">⏭ Skipped (<?= count($skipped) ?> files)</div>
  <ul>
    <?php foreach (array_slice($skipped, 0, 5) as $f): ?>
      <li class="skip"><?= htmlspecialchars($f) ?></li>
    <?php endforeach; ?>
    <?php if (count($skipped) > 5): ?><li class="skip">... and <?= count($skipped)-5 ?> more</li><?php endif; ?>
  </ul>
</div>

<?php if ($errors): ?>
<div class="box">
  <div class="big err">❌ Errors (<?= count($errors) ?> files - permission problem)</div>
  <ul><?php foreach ($errors as $f): ?><li class="err"><?= htmlspecialchars($f) ?></li><?php endforeach; ?></ul>
</div>
<?php endif; ?>

<div class="warn">
  ⚠️ <strong>IMPORTANT: DELETE THIS FILE NOW!</strong><br>
  Go to cPanel → File Manager → public_html → find <strong>autofix.php</strong> → Delete it.<br>
  Then test your website registration at <a href="/login.html" style="color:#fca5a5">/login.html</a>
</div>
</body>
</html>
