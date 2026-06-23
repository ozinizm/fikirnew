<?php
error_reporting(E_ALL);
ini_set('display_errors', '1');

echo "<h3>Environment Diagnostics</h3>";
echo "Current PHP Version: " . phpversion() . "<br>";
echo "Current Directory: " . __DIR__ . "<br>";

$root_dir = dirname(__DIR__);
echo "Root Directory: " . $root_dir . "<br>";

// 1. Check debug file
$debug_file = $root_dir . '/tmp/env_keys_debug.txt';
if (file_exists($debug_file)) {
    echo "<b>env_keys_debug.txt found:</b><br><pre>" . htmlspecialchars(file_get_contents($debug_file)) . "</pre><br>";
} else {
    echo "env_keys_debug.txt does NOT exist in tmp folder.<br>";
}

// 2. Check for .env files in root
echo "<b>Listing all .env* files in root:</b><br>";
foreach (glob($root_dir . '/.env*') as $filename) {
    echo "- " . basename($filename) . " (" . filesize($filename) . " bytes)<br>";
}
echo "<br>";

$env_file = $root_dir . '/.env.local';
echo "Checking .env.local file: " . ($env_file) . "<br>";
if (file_exists($env_file)) {
    echo ".env.local exists. Size: " . filesize($env_file) . " bytes<br>";
    $lines = file($env_file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        $line = trim($line);
        if ($line === '' || str_starts_with($line, '#') || !str_contains($line, '=')) {
            continue;
        }
        [$key, $value] = array_map('trim', explode('=', $line, 2));
        echo "Key found: " . htmlspecialchars($key) . " (value length: " . strlen($value) . ")<br>";
    }
} else {
    echo ".env.local does NOT exist in root directory!<br>";
}

echo "<h3>Testing Database Connection</h3>";
require_once '../includes/db.php';
if (isset($db) && $db instanceof PDO) {
    echo "Success: Connected to database using db.php!<br>";
    try {
        $stmt = $db->query("SELECT COUNT(*) FROM yoneticiler");
        $count = $stmt->fetchColumn();
        echo "Admin users count: " . $count . "<br>";
    } catch (Exception $e) {
        echo "Error running query: " . $e->getMessage() . "<br>";
    }
} else {
    echo "Fail: \$db is not set or not a PDO instance.<br>";
}
