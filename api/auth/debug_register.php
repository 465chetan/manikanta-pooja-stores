<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);
header('Content-Type: text/plain');

echo "=== REGISTER.PHP DEBUG ===\n\n";
echo "PHP: " . phpversion() . "\n";
echo "Dir: " . __DIR__ . "\n\n";

// Check if required files exist
$files = [
    '../config/config.php' => __DIR__ . '/../config/config.php',
    '../config/database.php' => __DIR__ . '/../config/database.php',
    '../helpers/response.php' => __DIR__ . '/../helpers/response.php',
    '../helpers/validator.php' => __DIR__ . '/../helpers/validator.php',
    '../helpers/auth.php' => __DIR__ . '/../helpers/auth.php',
];
foreach ($files as $label => $path) {
    $real = realpath($path);
    echo "$label: " . ($real ? "EXISTS: $real" : "MISSING at $path") . "\n";
}

echo "\n--- Trying to load config ---\n";
try {
    require_once __DIR__ . '/../config/config.php';
    echo "config.php: OK\n";
} catch (Throwable $e) {
    echo "config.php ERROR: " . $e->getMessage() . "\n";
}

echo "\n--- Trying to load database ---\n";
try {
    require_once __DIR__ . '/../config/database.php';
    echo "database.php: OK\n";
} catch (Throwable $e) {
    echo "database.php ERROR: " . $e->getMessage() . "\n";
}

echo "\n--- Trying to load response ---\n";
try {
    require_once __DIR__ . '/../helpers/response.php';
    echo "response.php: OK\n";
} catch (Throwable $e) {
    echo "response.php ERROR: " . $e->getMessage() . "\n";
}

echo "\n--- Trying to load validator ---\n";
try {
    require_once __DIR__ . '/../helpers/validator.php';
    echo "validator.php: OK\n";
} catch (Throwable $e) {
    echo "validator.php ERROR: " . $e->getMessage() . "\n";
}

echo "\n--- Trying to load auth helper ---\n";
try {
    require_once __DIR__ . '/../helpers/auth.php';
    echo "auth.php: OK\n";
} catch (Throwable $e) {
    echo "auth.php ERROR: " . $e->getMessage() . "\n";
}

echo "\nDONE\n";
