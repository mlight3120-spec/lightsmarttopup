<?php
$config = include __DIR__ . '/config.php';

try {
    $dsn = "pgsql:host={$config['DB_HOST']};port={$config['DB_PORT']};dbname={$config['DB_NAME']}";
    $pdo = new PDO($dsn, $config['DB_USER'], $config['DB_PASS']);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    echo "✅ Connection successful!";
} catch (Exception $e) {
    echo "❌ Connection failed: " . $e->getMessage();
}
