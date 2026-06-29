<?php
/**
 * SMPS Quick Admin Setup - Password Guesser & DB Initializer
 * Access: http://smps.test/smps_init.php OR http://localhost/smps/smps_init.php
 */
header('Content-Type: text/html; charset=utf-8');

// Extended password list including common MySQL Workbench/Windows MySQL installer defaults
$allPasswords = [
    '', '1', '2', '3', 'root', 'admin', 'mysql', 'toor', 'password',
    'Admin@2025', 'Admin@1234', 'Admin@123', 'Mysql@2025', 'Root@1234',
    'mysql1', '123456', '1234', '12345', 'test', 'admin123',
    'Welcome1', 'welcome1', 'Welcome@1', 'Password1', 'P@ssw0rd',
    'Mysql1234', 'MySQL1234', 'MysqlRoot1', 'rootroot', 'rootpass',
    'SmpsAdmin', 'smps2025', 'SMPS@2025', 'pooja2025', 'Pooja@2025',
];

$found    = null;
$foundPDO = null;

foreach ($allPasswords as $pass) {
    foreach (['127.0.0.1', 'localhost'] as $host) {
        try {
            $dsn = "mysql:host=$host;port=3306;charset=utf8mb4";
            $pdo = new PDO($dsn, 'root', $pass, [
                PDO::ATTR_ERRMODE   => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_TIMEOUT   => 2,
            ]);
            $found    = ['host' => $host, 'pass' => $pass];
            $foundPDO = $pdo;
            break 2; // exit both loops
        } catch (Exception $e) {
            // continue
        }
    }
}

if (!$foundPDO && isset($_POST['custom_pass'])) {
    $cp = trim($_POST['custom_pass']);
    foreach (['127.0.0.1', 'localhost'] as $host) {
        try {
            $pdo = new PDO("mysql:host=$host;port=3306;charset=utf8mb4", 'root', $cp, [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION, PDO::ATTR_TIMEOUT => 3]);
            $found    = ['host' => $host, 'pass' => $cp];
            $foundPDO = $pdo;
            break;
        } catch (Exception $e) {}
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>SMPS Admin Setup</title>
<style>
 * { box-sizing: border-box; margin: 0; padding: 0; }
 body { font-family: 'Segoe UI', sans-serif; background: #0d0d0d; color: #e2e8f0; padding: 30px; min-height: 100vh; }
 h1 { color: #ffd166; margin-bottom: 4px; font-size: 1.6rem; }
 .sub { color: #6b7280; font-size: 0.85rem; margin-bottom: 30px; }
 .card { background: #1a1a2e; border: 1px solid #2d2d44; border-radius: 12px; padding: 24px; margin-bottom: 20px; }
 .card h2 { font-size: 1rem; margin-bottom: 16px; color: #94a3b8; text-transform: uppercase; letter-spacing: 1px; }
 .ok   { color: #4ade80; display: block; padding: 4px 0; }
 .fail { color: #f87171; display: block; padding: 3px 0; font-size: 0.82rem; }
 .warn { color: #fbbf24; display: block; padding: 3px 0; }
 .success-box { background: #064e3b; border: 2px solid #4ade80; border-radius: 12px; padding: 28px; margin-top: 24px; }
 .success-box h2 { color: #4ade80; font-size: 1.3rem; margin-bottom: 16px; }
 table { width: 100%; border-collapse: collapse; }
 td { padding: 10px 14px; border-bottom: 1px solid #2d2d44; }
 td:first-child { color: #94a3b8; width: 140px; font-weight: 600; }
 td:last-child { color: #ffd166; font-family: monospace; font-size: 1.05rem; }
 .btn { display: inline-block; padding: 14px 28px; background: #7b1a1a; color: white; border-radius: 10px; text-decoration: none; font-weight: 700; margin-top: 16px; border: none; cursor: pointer; font-size: 1rem; }
 .btn:hover { background: #9b2c2c; }
 input[type=text], input[type=password] { width: 320px; padding: 12px 16px; border: 1.5px solid #4a4a6a; border-radius: 8px; background: #111; color: white; font-size: 0.95rem; }
 input:focus { outline: none; border-color: #ffd166; }
 .form-row { display: flex; gap: 12px; align-items: center; margin-top: 16px; flex-wrap: wrap; }
 .note { background: #7c2d12; border-radius: 8px; padding: 12px 16px; font-size: 0.85rem; color: #fed7aa; margin-top: 16px; }
</style>
</head>
<body>

<h1>🛠️ Sri Manikanta Pooja Stores — Admin Setup</h1>
<p class="sub">One-time database initialization and admin account creation</p>

<?php if ($foundPDO): ?>

<div class="card">
  <h2>✅ Step 1 — MySQL Connected</h2>
  <span class="ok">✅ Connected to MySQL at <strong><?= htmlspecialchars($found['host']) ?></strong></span>
  <span class="ok">✅ Password: <strong>"<?= $found['pass'] === '' ? '(empty)' : htmlspecialchars($found['pass']) ?>"</strong></span>
</div>

<?php
// Update config.php
$configFile = __DIR__ . '/api/config/config.php';
$config     = file_get_contents($configFile);
$updatedConfig = preg_replace("/define\('DB_PASS',\s*'[^']*'\)/", "define('DB_PASS',    '" . addslashes($found['pass']) . "')", $config);
$updatedConfig = preg_replace("/define\('DB_HOST',\s*'[^']*'\)/", "define('DB_HOST',    '" . $found['host'] . "')", $updatedConfig);
file_put_contents($configFile, $updatedConfig);
echo '<div class="card"><h2>✅ Step 2 — Config Updated</h2>';
echo '<span class="ok">✅ config.php updated with correct DB_HOST and DB_PASS</span>';
echo '</div>';

// Create database
$foundPDO->exec("CREATE DATABASE IF NOT EXISTS smps_local CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
$foundPDO->exec("USE smps_local");
$tables = $foundPDO->query("SHOW TABLES")->fetchAll(PDO::FETCH_COLUMN);

echo '<div class="card"><h2>Step 3 — Database & Tables</h2>';
echo '<span class="ok">✅ Database smps_local ready</span>';
echo '<span class="ok">✅ Tables found: ' . (empty($tables) ? 'none (will run schema)' : implode(', ', $tables)) . '</span>';

if (!in_array('admin_users', $tables)) {
    $schemaFile = __DIR__ . '/database/schema.sql';
    if (file_exists($schemaFile)) {
        $sql = file_get_contents($schemaFile);
        // Execute each statement
        $stmts = array_filter(array_map('trim', explode(';', $sql)));
        $ok = $fail = 0;
        foreach ($stmts as $stmt) {
            if (empty($stmt)) continue;
            try { $foundPDO->exec($stmt); $ok++; }
            catch (Exception $e) {
                if (!preg_match('/(already exists|Duplicate|duplicate)/i', $e->getMessage())) {
                    $fail++;
                }
            }
        }
        echo "<span class='ok'>✅ Schema executed: $ok statements, $fail errors</span>";
    }
    $tables = $foundPDO->query("SHOW TABLES")->fetchAll(PDO::FETCH_COLUMN);
    echo '<span class="ok">✅ Tables now: ' . implode(', ', $tables) . '</span>';
}
echo '</div>';

// Admin user
echo '<div class="card"><h2>Step 4 — Admin Account</h2>';
$foundPDO->exec("USE smps_local");
try {
    $admins = $foundPDO->query("SELECT id, name, email, role FROM admin_users")->fetchAll();
} catch (Exception $e) { $admins = []; }

$adminEmail = 'admin@srimanikanta.com';
$adminPass  = 'Admin@2025';

if (empty($admins)) {
    $hash = password_hash($adminPass, PASSWORD_BCRYPT, ['cost' => 10]);
    $foundPDO->prepare("INSERT INTO admin_users (name, email, password_hash, role) VALUES (?, ?, ?, ?)")
             ->execute(['Store Owner', $adminEmail, $hash, 'superadmin']);
    echo '<span class="ok">✅ Admin account CREATED!</span>';
} else {
    foreach ($admins as $a) {
        echo '<span class="ok">✅ Admin exists: ' . htmlspecialchars($a['name']) . ' &lt;' . htmlspecialchars($a['email']) . '&gt; [' . $a['role'] . ']</span>';
    }
    $adminEmail = $admins[0]['email'];
}
echo '</div>';
?>

<div class="success-box">
  <h2>🎉 Setup Complete!</h2>
  <table>
    <tr><td>Admin Login URL</td><td><a href="/smps/admin/login.html" style="color:#4ade80">/smps/admin/login.html</a></td></tr>
    <tr><td>Email</td><td><?= htmlspecialchars($adminEmail) ?></td></tr>
    <tr><td>Password</td><td><?= htmlspecialchars($adminPass) ?></td></tr>
    <tr><td>DB Host</td><td><?= htmlspecialchars($found['host']) ?></td></tr>
    <tr><td>DB Name</td><td>smps_local</td></tr>
  </table>
  <div class="note">⚠️ <strong>IMPORTANT:</strong> Delete this file after setup: <code>smps_init.php</code></div>
  <br>
  <a class="btn" href="/smps/admin/login.html">→ Go to Admin Login</a>
</div>

<?php else: ?>
<div class="card">
  <h2>Step 1 — MySQL Connection</h2>
  <span class="fail">❌ Could not connect automatically with <?= count($allPasswords) ?> common passwords.</span>
  <span class="warn">Please enter your MySQL root password manually:</span>
  <form method="POST">
    <div class="form-row">
      <input type="password" name="custom_pass" placeholder="Enter MySQL root password" autofocus>
      <button type="submit" class="btn" style="margin:0;padding:12px 20px">Try →</button>
    </div>
  </form>
  <?php if (isset($_POST['custom_pass'])): ?>
  <span class="fail" style="margin-top:12px">❌ That password did not work either. Please check Laragon > HeidiSQL to find the correct password.</span>
  <?php endif; ?>
  <div class="note" style="margin-top:20px">
    <strong>How to find your MySQL password:</strong><br>
    1. Open <strong>Laragon</strong> app<br>
    2. Click <strong>Database</strong> button (opens HeidiSQL)<br>
    3. Look at the <strong>Password</strong> field in the saved session<br>
    4. Enter it above
  </div>
</div>
<?php endif; ?>

</body>
</html>
