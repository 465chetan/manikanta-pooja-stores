<?php
// db_check.php - Quick database diagnostics
// Run: http://localhost/smps/db_check.php
// DELETE AFTER USE

header('Content-Type: text/plain');

echo "=== SMPS Database Diagnostics ===\n\n";

// Test 1: Can we reach MySQL at all?
echo "1. MySQL connection test:\n";
$configs = [
    ['host'=>'localhost', 'user'=>'root', 'pass'=>'',         'label'=>'root/empty'],
    ['host'=>'127.0.0.1', 'user'=>'root', 'pass'=>'',         'label'=>'127.0.0.1/empty'],
    ['host'=>'localhost', 'user'=>'root', 'pass'=>'root',      'label'=>'root/root'],
    ['host'=>'localhost', 'user'=>'root', 'pass'=>'laragon',   'label'=>'root/laragon'],
    ['host'=>'localhost', 'user'=>'root', 'pass'=>'password',  'label'=>'root/password'],
    ['host'=>'localhost', 'user'=>'root', 'pass'=>'Admin@1234','label'=>'root/Admin@1234'],
];

$workingConfig = null;
foreach ($configs as $cfg) {
    try {
        $pdo = new PDO(
            "mysql:host={$cfg['host']};charset=utf8mb4",
            $cfg['user'],
            $cfg['pass'],
            [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION, PDO::ATTR_TIMEOUT => 3]
        );
        echo "   [SUCCESS] {$cfg['label']}\n";
        $workingConfig = $cfg;
        break;
    } catch (PDOException $e) {
        echo "   [FAIL] {$cfg['label']}: " . $e->getMessage() . "\n";
    }
}

echo "\n";

if ($workingConfig) {
    $pdo = new PDO(
        "mysql:host={$workingConfig['host']};charset=utf8mb4",
        $workingConfig['user'],
        $workingConfig['pass']
    );

    // Test 2: Does smps_local database exist?
    echo "2. Database check:\n";
    $dbs = $pdo->query("SHOW DATABASES")->fetchAll(PDO::FETCH_COLUMN);
    echo "   Databases: " . implode(', ', $dbs) . "\n";
    echo "   smps_local exists: " . (in_array('smps_local', $dbs) ? 'YES' : 'NO') . "\n\n";

    if (in_array('smps_local', $dbs)) {
        $pdo->exec("USE smps_local");
        $tables = $pdo->query("SHOW TABLES")->fetchAll(PDO::FETCH_COLUMN);
        echo "3. Tables in smps_local: " . implode(', ', $tables) . "\n\n";

        // Check admin_users
        if (in_array('admin_users', $tables)) {
            $admins = $pdo->query("SELECT id, name, email, role FROM admin_users")->fetchAll();
            echo "4. Admin users:\n";
            foreach ($admins as $a) {
                echo "   ID:{$a['id']} | {$a['name']} | {$a['email']} | {$a['role']}\n";
            }
        } else {
            echo "4. admin_users table does NOT exist - need to run schema.sql\n";
        }
    } else {
        echo "3. smps_local database does not exist - need to create it\n";
        echo "   ACTION NEEDED: Open Laragon > Database > Create DB named 'smps_local'\n";
        echo "   Then run: http://localhost/smps/database/init.php\n";
    }

    echo "\n";
    echo "WORKING CONFIG: host={$workingConfig['host']}, user={$workingConfig['user']}, pass='" . str_repeat('*', strlen($workingConfig['pass'])) . "'\n";
    echo "Update this in: C:\\laragon\\www\\smps\\api\\config\\config.php\n";
} else {
    echo "NO WORKING MYSQL CONFIG FOUND!\n";
    echo "Please check if MySQL is running in Laragon.\n";
}

