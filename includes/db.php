<?php
declare(strict_types=1);
/**
 * Koneksi PDO aplikasi mandiri "Interface 6 View".
 * Tidak mengimpor config.php maupun file PHP dari website CUD lama.
 */
function db(): PDO
{
    static $conn = null;
    if ($conn instanceof PDO) {
        return $conn;
    }

    $file = dirname(__DIR__) . DIRECTORY_SEPARATOR . 'config.local.php';
    if (!is_file($file)) {
        throw new RuntimeException('File config.local.php belum dibuat.');
    }
    require_once $file;

    if (!defined('DB_HOST') || !defined('DB_NAME') || !defined('DB_USER') || !defined('DB_PASS')) {
        throw new RuntimeException('Konfigurasi DB_HOST, DB_NAME, DB_USER, DB_PASS belum lengkap.');
    }
    $port = defined('DB_PORT') ? (int)DB_PORT : 3306;
    $dsn = sprintf('mysql:host=%s;port=%d;dbname=%s;charset=utf8mb4', DB_HOST, $port, DB_NAME);
    $conn = new PDO($dsn, DB_USER, DB_PASS, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
    ]);
    return $conn;
}
