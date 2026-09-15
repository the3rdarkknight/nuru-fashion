<?php
/**
 * Database connection + site settings.
 * Edit the values below to match your hosting environment.
 */

// ---- Database credentials ----
define('DB_HOST', 'localhost');
define('DB_NAME', 'nuru_fashion');
define('DB_USER', 'root');
define('DB_PASS', '');

// ---- Site settings ----
define('SITE_NAME', 'Nuru Fashion');
define('CURRENCY_SYMBOL', 'KES');

// WhatsApp number that "Order via WhatsApp" buttons message.
// Use the full international format with NO plus sign, spaces or dashes.
// Example: Kenyan number 0712 345 678 -> 254712345678
define('WHATSAPP_NUMBER', '+254799417993');

// Base URL of the site (used for building image/share links). No trailing slash.
define('SITE_URL', 'http://localhost/nuru-fashion');

session_start();

try {
    $pdo = new PDO(
        "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4",
        DB_USER,
        DB_PASS,
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        ]
    );
} catch (PDOException $e) {
    die('Database connection failed. Check config/database.php — ' . htmlspecialchars($e->getMessage()));
}