<?php
declare(strict_types=1);

if (!defined('ROOT_DIR')) {
    define('ROOT_DIR', dirname(__DIR__));
}


$host = getenv('DB_HOST') ?: '127.0.0.1';
$port = getenv('DB_PORT') ?: '3306';
$name = getenv('DB_NAME') ?: 'fikircreative';
$user = getenv('DB_USER') ?: 'root';
$pass = getenv('DB_PASSWORD') ?: '';

if (file_exists(__DIR__ . '/../.env.local')) {
    $lines = file(__DIR__ . '/../.env.local', FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        $line = trim($line);
        if ($line === '' || str_starts_with($line, '#') || !str_contains($line, '=')) {
            continue;
        }

        [$key, $value] = array_map('trim', explode('=', $line, 2));
        $value = trim($value, "\"'");
        if ($key === 'DB_HOST') $host = $value;
        if ($key === 'DB_PORT') $port = $value;
        if ($key === 'DB_NAME') $name = $value;
        if ($key === 'DB_USER') $user = $value;
        if ($key === 'DB_PASSWORD') $pass = $value;
    }
}

$dsn = "mysql:host={$host};port={$port};dbname={$name};charset=utf8mb4";

try {
    $db = new PDO($dsn, $user, $pass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
    ]);
} catch (PDOException $e) {
    // TCP bağlantısı başarısız olursa Unix Socket (localhost) deneyelim
    if ($host === '127.0.0.1') {
        try {
            $dsn_fallback = "mysql:host=localhost;port={$port};dbname={$name};charset=utf8mb4";
            $db = new PDO($dsn_fallback, $user, $pass, [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
            ]);
        } catch (PDOException $e2) {
            http_response_code(500);
            die('Veritabanı bağlantısı kurulamadı (TCP ve Socket denendi): ' . htmlspecialchars($e2->getMessage(), ENT_QUOTES, 'UTF-8'));
        }
    } else {
        http_response_code(500);
        die('Veritabanı bağlantısı kurulamadı: ' . htmlspecialchars($e->getMessage(), ENT_QUOTES, 'UTF-8'));
    }
}

if (!function_exists('getAdminAssetPath')) {
    function getAdminAssetPath($val) {
        if (empty($val)) return '';
        $val = trim($val);
        if (preg_match('/^https?:\/\//i', $val)) {
            return $val;
        }
        return '../' . ltrim($val, '/');
    }
}

