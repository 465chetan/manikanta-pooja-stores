<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

$path = __DIR__ . '/js/products.js';
if (file_exists($path)) {
    $content = file_get_contents($path);
    echo "File size: " . strlen($content) . " bytes<br>";
    echo "Contains 'Sindoor': " . (str_contains($content, 'Sindoor') ? "YES" : "NO") . "<br>";
    echo "Contains 'coconut': " . (str_contains($content, 'coconut') ? "YES" : "NO") . "<br>";
    
    // Let's print out all the product IDs present in the file
    if (preg_match_all('/"id":\s*(\d+)/', $content, $matches)) {
        echo "Product IDs in file: " . implode(", ", $matches[1]) . "<br>";
    } else {
        echo "No IDs found!<br>";
    }
} else {
    echo "File does not exist!<br>";
}
