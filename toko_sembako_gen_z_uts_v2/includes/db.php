<?php

require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/helpers.php';

function db(): PDO
{
    static $pdo = null;

    if ($pdo instanceof PDO) {
        return $pdo;
    }

    try {

        $pdo = new PDO(
            'mysql:host=' . DB_HOST .
            ';port=3306;dbname=' . DB_NAME .
            ';charset=utf8mb4',

            DB_USER,
            DB_PASS,

            [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
            ]
        );

        return $pdo;

    } catch (PDOException $e) {

        http_response_code(500);

        echo "Koneksi database gagal:<br><br>";
        echo htmlspecialchars(
            $e->getMessage(),
            ENT_QUOTES,
            'UTF-8'
        );

        exit;
    }
}