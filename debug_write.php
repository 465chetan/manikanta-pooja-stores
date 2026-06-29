<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

$path = __DIR__ . '/js/products.js';
echo "Checking path: " . $path . "<br>";
echo "File exists: " . (file_exists($path) ? "Yes" : "No") . "<br>";
echo "Is writable: " . (is_writable($path) ? "Yes" : "No") . "<br>";
echo "Dir is writable: " . (is_writable(dirname($path)) ? "Yes" : "No") . "<br>";

$testContent = "// Test write " . time() . "\n";
$res = @file_put_contents($path, $testContent, FILE_APPEND);
if ($res === false) {
    $err = error_get_last();
    echo "Write failed! Error: " . ($err['message'] ?? 'Unknown error') . "<br>";
} else {
    echo "Write succeeded! Bytes written: " . $res . "<br>";
}

// Let's also check the owner of the file
if (function_exists('posix_getpwuid')) {
    $owner = posix_getpwuid(fileowner($path));
    echo "File owner: " . $owner['name'] . "<br>";
    $process = posix_getpwuid(posix_geteuid());
    echo "Process running as: " . $process['name'] . "<br>";
}
