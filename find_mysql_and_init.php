<?php
// find_mysql_and_init.php
// Run: http://localhost/smps/find_mysql_and_init.php
// DELETE AFTER USE!
header('Content-Type: text/html; charset=utf-8');
?>
<!DOCTYPE html>
<html>
<head>
<title>SMPS Database Setup</title>
<style>
body{font-family:monospace;padding:30px;background:#0f0f0f;color:#00ff88}
.ok{color:#00ff88}.fail{color:#ff4444}.warn{color:#ffd166}
h2{color:#fff}.box{background:#1a1a1a;padding:20px;border-radius:8px;margin:16px 0;border:1px solid #333}
a.btn{display:inline-block;background:#7b1a1a;color:white;padding:12px 24px;border-radius:8px;text-decoration:none;font-weight:bold;margin-top:12px}
</style>
</head>
<body>
<h2>🛠️ SMPS Database Setup</h2>

<?php
$passwords  = ['', '1', '2', 'root', 'admin', 'laragon', 'Admin@2025', 'mysql', 'password', '123456', 'toor'];
$foundPass  = null;
$foundPDO   = null;

echo '<div class="box"><strong>Step 1: Finding MySQL password...</strong><br>';
foreach ($passwords as $pass) {
    try {
        $pdo = new PDO("mysql:host=127.0.0.1;port=3306;charset=utf8mb4", 'root', $pass,
            [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION, PDO::ATTR_TIMEOUT => 3]);
        echo "<span class='ok'>✅ SUCCESS with password: '" . ($pass === '' ? '(empty)' : htmlspecialchars($pass)) . "'</span><br>";
        $foundPass = $pass;
        $foundPDO  = $pdo;
        break;
    } catch (Exception $e) {
        echo "<span class='fail'>❌ '" . ($pass === '' ? '(empty)' : htmlspecialchars($pass)) . "': " . htmlspecialchars($e->getMessage()) . "</span><br>";
    }
}
echo '</div>';

if (!$foundPDO) {
    echo '<div class="box warn">⚠️ Could not connect to MySQL with any tested password.<br>
    Please open Laragon > Database (HeidiSQL) and check your root password, then enter it below:<br><br>
    <form method="POST">
      <input type="text" name="custom_pass" placeholder="Enter MySQL root password" style="padding:8px;width:300px;font-size:14px">
      <button type="submit" style="padding:8px 16px;background:#7b1a1a;color:white;border:none;cursor:pointer">Try →</button>
    </form>';
    
    if (isset($_POST['custom_pass'])) {
        $cp = $_POST['custom_pass'];
        try {
            $pdo = new PDO("mysql:host=127.0.0.1;port=3306;charset=utf8mb4", 'root', $cp,
                [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION, PDO::ATTR_TIMEOUT => 3]);
            echo "<span class='ok'>✅ SUCCESS with custom password!</span>";
            $foundPass = $cp;
            $foundPDO  = $pdo;
        } catch (Exception $e) {
            echo "<span class='fail'>❌ Still failed: " . htmlspecialchars($e->getMessage()) . "</span>";
        }
    }
    echo '</div>';
}

if ($foundPDO) {
    // Step 2: Update config.php
    echo '<div class="box"><strong>Step 2: Updating config.php...</strong><br>';
    $configFile = __DIR__ . '/api/config/config.php';
    $config = file_get_contents($configFile);
    $config = preg_replace("/define\('DB_PASS',\s*'[^']*'\)/", "define('DB_PASS',    '" . addslashes($foundPass) . "')", $config);
    $config = preg_replace("/define\('DB_HOST',\s*'[^']*'\)/", "define('DB_HOST',    '127.0.0.1')", $config);
    if (file_put_contents($configFile, $config)) {
        echo "<span class='ok'>✅ config.php updated (password: '" . ($foundPass===''?'(empty)':htmlspecialchars($foundPass)) . "', host: 127.0.0.1)</span><br>";
    } else {
        echo "<span class='fail'>❌ Could not write config.php</span><br>";
    }
    echo '</div>';

    // Step 3: Create/check database
    echo '<div class="box"><strong>Step 3: Database setup...</strong><br>';
    $dbs = $foundPDO->query("SHOW DATABASES")->fetchAll(PDO::FETCH_COLUMN);
    echo "Existing databases: " . implode(', ', $dbs) . "<br>";
    
    if (!in_array('smps_local', $dbs)) {
        $foundPDO->exec("CREATE DATABASE smps_local CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
        echo "<span class='ok'>✅ Created database: smps_local</span><br>";
    } else {
        echo "<span class='ok'>✅ Database smps_local already exists</span><br>";
    }
    
    // Step 4: Run schema
    $foundPDO->exec("USE smps_local");
    $tables = $foundPDO->query("SHOW TABLES")->fetchAll(PDO::FETCH_COLUMN);
    echo "Existing tables: " . (empty($tables) ? '(none)' : implode(', ', $tables)) . "<br>";
    
    if (!in_array('admin_users', $tables)) {
        echo "<span class='warn'>⚠️ Tables missing — running schema.sql...</span><br>";
        $schemaFile = __DIR__ . '/database/schema.sql';
        if (file_exists($schemaFile)) {
            $sql = file_get_contents($schemaFile);
            // Split by semicolons and run each statement
            $statements = array_filter(array_map('trim', explode(';', $sql)));
            $ok = 0; $fail = 0;
            foreach ($statements as $stmt) {
                if (empty($stmt) || strpos($stmt, '--') === 0) continue;
                try {
                    $foundPDO->exec($stmt);
                    $ok++;
                } catch (Exception $e) {
                    // Ignore "table already exists" etc.
                    if (!str_contains($e->getMessage(), 'already exists') && !str_contains($e->getMessage(), 'Duplicate')) {
                        echo "<span class='fail'>SQL Error: " . htmlspecialchars($e->getMessage()) . "</span><br>";
                        $fail++;
                    }
                }
            }
            echo "<span class='ok'>✅ Schema executed: $ok statements OK, $fail errors</span><br>";
        } else {
            echo "<span class='fail'>❌ schema.sql not found at: $schemaFile</span><br>";
        }
    } else {
        echo "<span class='ok'>✅ All tables already exist</span><br>";
    }
    echo '</div>';

    // Step 5: Verify admin user
    echo '<div class="box"><strong>Step 4: Admin user check...</strong><br>';
    $foundPDO->exec("USE smps_local");
    $admins = $foundPDO->query("SELECT id, name, email, role FROM admin_users")->fetchAll();
    if (empty($admins)) {
        // Insert default admin
        $hash = password_hash('Admin@2025', PASSWORD_BCRYPT, ['cost'=>10]);
        $foundPDO->prepare("INSERT INTO admin_users (name, email, password_hash, role) VALUES (?, ?, ?, ?)")
            ->execute(['Store Owner', 'admin@srimanikanta.com', $hash, 'superadmin']);
        echo "<span class='ok'>✅ Admin account created!</span><br>";
        $admins = $foundPDO->query("SELECT id, name, email, role FROM admin_users")->fetchAll();
    }
    foreach ($admins as $a) {
        echo "<span class='ok'>✅ Admin: {$a['name']} | {$a['email']} | {$a['role']}</span><br>";
    }
    echo '</div>';

    echo '<div class="box" style="border-color:#00ff88">
    <strong>🎉 SETUP COMPLETE!</strong><br><br>
    <table style="border-collapse:collapse;width:100%">
    <tr><td style="padding:6px 12px"><strong>Admin Login URL:</strong></td><td><a href="/smps/admin/login.html" style="color:#00aaff">/smps/admin/login.html</a></td></tr>
    <tr><td style="padding:6px 12px"><strong>Email:</strong></td><td>admin@srimanikanta.com</td></tr>
    <tr><td style="padding:6px 12px"><strong>Password:</strong></td><td>Admin@2025</td></tr>
    </table>
    <br>
    <span class="warn">⚠️ DELETE this file after setup! Also delete db_check.php and setup_admin.php</span><br>
    <a class="btn" href="/smps/admin/login.html">Go to Admin Login →</a>
    </div>';
}
?>
</body>
</html>
