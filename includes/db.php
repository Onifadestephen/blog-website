<?php
// File: /includes/db.php
require_once __DIR__ . '/../config/config.php';

$databaseUrl = getenv("DATABASE_URL");

if ($databaseUrl) {
    // Parse the DATABASE_URL from Render
    $url = parse_url($databaseUrl);

    $host = $url["host"];
    $db   = ltrim($url["path"], "/");
    $user = $url["user"];
    $pass = $url["pass"];

    try {
        $pdo = new PDO("pgsql:host=$host;dbname=$db", $user, $pass);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    } catch (PDOException $e) {
        die("Remote DB connection failed: " . $e->getMessage());
    }
} else {
    // Local development settings
    $host = 'localhost';
    $db = 'blog-website';
    $user = 'postgres';
    $pass = '2000';

    try {
        $pdo = new PDO("pgsql:host=$host;dbname=$db", $user, $pass);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    } catch (PDOException $e) {
        die("Local DB connection failed: " . $e->getMessage());
    }
}
