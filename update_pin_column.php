<?php
$config = include __DIR__ . '/config.php';

try {
    $dsn = "pgsql:host={$config['DB_HOST']};port={$config['DB_PORT']};dbname={$config['DB_NAME']};";
    $pdo = new PDO($dsn, $config['DB_USER'], $config['DB_PASS'], [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
    ]);

    $pdo->exec("ALTER TABLE users ADD COLUMN pin TEXT NULL");
    echo "✅ Column 'pin' added successfully.";
} catch (PDOException $e) {
    echo "❌ Error: " . $e->getMessage();
}
