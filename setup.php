<?php
// ============================================================
// SRI MANIKANTA POOJA STORES — FIRST RUN INSTALLER
// Run this ONCE after uploading files to cPanel.
// Then DELETE this file immediately for security!
// Access at: https://yourdomain.com/setup.php
// ============================================================

error_reporting(E_ALL);
ini_set('display_errors', 1);

// ── SETUP CONFIG — Edit BEFORE running ───────────────────────
$config = [
    'db_host' => 'localhost',
    'db_name' => 'manikant_smpsdb',
    'db_user' => 'manikant_admin',
    'db_pass' => 'Sai2353L@0807',
];

$steps = [];
$allOk = true;

// Step 1: Check PHP version
$phpOk = version_compare(PHP_VERSION, '8.0', '>=');
$steps[] = ['label' => 'PHP Version (' . PHP_VERSION . ')', 'ok' => $phpOk, 'note' => $phpOk ? '' : 'Requires PHP 8.0+. Set in cPanel > MultiPHP Manager'];

// Step 2: Check extensions
foreach (['pdo', 'pdo_mysql', 'json', 'curl', 'mbstring', 'openssl'] as $ext) {
    $ok = extension_loaded($ext);
    $steps[] = ['label' => "PHP Extension: $ext", 'ok' => $ok, 'note' => $ok ? '' : "Missing extension. Ask your hosting to enable it."];
    if (!$ok) $allOk = false;
}

// Step 3: Check file write permissions
$dirs = ['uploads/products', 'logs'];
foreach ($dirs as $dir) {
    if (!is_dir($dir)) mkdir($dir, 0755, true);
    $ok = is_writable($dir);
    $steps[] = ['label' => "Directory writable: $dir/", 'ok' => $ok, 'note' => $ok ? '' : "Run: chmod 755 $dir"];
    if (!$ok) $allOk = false;
}

// Step 4: Test DB connection
$dbOk = false;
$dbMsg = '';
try {
    $pdo = new PDO(
        "mysql:host={$config['db_host']};charset=utf8mb4",
        $config['db_user'],
        $config['db_pass'],
        [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
    );
    // Try to create and select database
    $pdo->exec("CREATE DATABASE IF NOT EXISTS `{$config['db_name']}` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
    $pdo->exec("USE `{$config['db_name']}`");
    $dbOk = true;
    $dbMsg = "Connected successfully to MySQL";
} catch (\PDOException $e) {
    $dbMsg = "Connection failed: " . $e->getMessage();
    $allOk = false;
}
$steps[] = ['label' => "Database connection", 'ok' => $dbOk, 'note' => $dbMsg];

// Step 5: Run schema if requested
$schemaRan = false;
$schemaMsg = '';
if ($allOk && isset($_POST['install'])) {
    $schemaFile = __DIR__ . '/database/schema.sql';
    if (!file_exists($schemaFile)) {
        $schemaMsg = "Schema file not found at database/schema.sql";
    } else {
        try {
            // ── Step A: Drop all existing tables for a clean install ──
            $pdo->exec("SET FOREIGN_KEY_CHECKS = 0");
            $tables = $pdo->query("SHOW TABLES")->fetchAll(PDO::FETCH_COLUMN);
            foreach ($tables as $table) {
                $pdo->exec("DROP TABLE IF EXISTS `$table`");
            }
            $pdo->exec("SET FOREIGN_KEY_CHECKS = 1");

            // ── Step B: Run fresh schema ──
            $sql = file_get_contents($schemaFile);
            // Remove comments and split by semicolon
            $sql = preg_replace('/--[^\n]*\n/', "\n", $sql); // remove -- comments
            $statements = array_filter(array_map('trim', explode(';', $sql)));
            foreach ($statements as $stmt) {
                if (!empty($stmt)) {
                    $pdo->exec($stmt);
                }
            }
            $schemaRan = true;
            $schemaMsg = "✅ Database tables and seed data created successfully!";
        } catch (\PDOException $e) {
            $schemaMsg = "Schema error: " . $e->getMessage();
        }
    }
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Setup Installer — Sri Manikanta Pooja Stores</title>
<style>
body{font-family:system-ui,sans-serif;max-width:760px;margin:40px auto;padding:20px;background:#f5f5f5}
.card{background:white;border-radius:12px;padding:28px;box-shadow:0 4px 16px rgba(0,0,0,0.1);margin-bottom:20px}
h1{color:#7B1A1A;margin:0 0 4px}
.step{display:flex;align-items:center;gap:12px;padding:10px 0;border-bottom:1px solid #f0f0f0}
.step:last-child{border-bottom:none}
.step-icon{width:24px;height:24px;border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:12px;flex-shrink:0}
.step-ok{background:#d1fae5;color:#065f46}
.step-fail{background:#fee2e2;color:#991b1b}
.step-label{font-weight:600;font-size:0.9rem}
.step-note{font-size:0.78rem;color:#6b7280}
.btn{padding:14px 32px;border:none;border-radius:8px;font-size:1rem;font-weight:700;cursor:pointer;background:linear-gradient(135deg,#FF7722,#e86b00);color:white;width:100%;margin-top:16px}
.btn:disabled{opacity:0.5;cursor:not-allowed}
.alert{padding:14px;border-radius:8px;margin-bottom:16px}
.alert-success{background:#d1fae5;color:#065f46;border:1px solid #a7f3d0}
.alert-warning{background:#fef3c7;color:#92400e;border:1px solid #fde68a}
.cred-box{background:#f9f4ff;border:1px solid #e0d5f5;border-radius:8px;padding:16px;margin-top:12px;font-family:monospace;font-size:0.85rem}
</style>
</head>
<body>
<div class="card">
  <h1>🕉️ Sri Manikanta Pooja Stores</h1>
  <p style="color:#6b7280;margin:0 0 20px">One-time Setup Installer — Run this once, then delete this file!</p>

  <?php if ($schemaRan): ?>
  <div class="alert alert-success">
    <?= $schemaMsg ?><br><br>
    <strong>🎉 Installation complete!</strong><br>
    ⚠️ <strong>Delete this file (setup.php) immediately!</strong><br><br>
    <strong>Default Admin Credentials:</strong>
    <div class="cred-box">
      Email: admin@srimanikanta.com<br>
      Password: Admin@2025<br>
      <br>
      ⚠️ CHANGE THESE IMMEDIATELY after first login!
    </div>
    <br>
    <a href="admin/login.html" style="color:#065f46;font-weight:700">→ Go to Admin Login</a>
  </div>
  <?php elseif (!empty($schemaMsg)): ?>
  <div class="alert" style="background:#fee2e2;color:#991b1b;border:1px solid #fecaca"><?= htmlspecialchars($schemaMsg) ?></div>
  <?php endif; ?>

  <h3 style="color:#374151;margin-bottom:16px">System Check</h3>

  <?php foreach ($steps as $step): ?>
  <div class="step">
    <div class="step-icon <?= $step['ok'] ? 'step-ok' : 'step-fail' ?>">
      <?= $step['ok'] ? '✓' : '✗' ?>
    </div>
    <div>
      <div class="step-label"><?= htmlspecialchars($step['label']) ?></div>
      <?php if ($step['note']): ?>
        <div class="step-note"><?= htmlspecialchars($step['note']) ?></div>
      <?php endif; ?>
    </div>
  </div>
  <?php endforeach; ?>

  <?php if ($allOk && !$schemaRan): ?>
  <div class="alert alert-warning" style="margin-top:16px">
    ⚠️ Before clicking Install, make sure you have updated <code>api/config/config.php</code> with your DB credentials, Razorpay keys, and email settings.
  </div>
  <form method="POST">
    <button type="submit" name="install" class="btn">🚀 Install Database & Create Tables</button>
  </form>
  <?php elseif (!$allOk): ?>
  <div class="alert" style="background:#fee2e2;color:#991b1b;border:1px solid #fecaca;margin-top:16px">
    ❌ Please fix the issues above before installing.
  </div>
  <?php endif; ?>
</div>

<div class="card">
  <h3 style="margin:0 0 12px;color:#374151">Next Steps After Installation</h3>
  <ol style="color:#374151;line-height:2">
    <li>Update <code>api/config/config.php</code> with your domain, DB, Razorpay, email settings</li>
    <li>Login to admin panel at <a href="admin/login.html">admin/login.html</a></li>
    <li>Change the default admin password</li>
    <li>Add your Razorpay API keys (test mode first)</li>
    <li><strong style="color:#991b1b">DELETE this setup.php file!</strong></li>
  </ol>
</div>
</body>
</html>
