<?php
require 'config.php';

    try {
    $dsn = "pgsql:host={$config['DB_HOST']};port={$config['DB_PORT']};dbname={$config['DB_NAME']};";
    $pdo = new PDO($dsn, $config['DB_USER'], $config['DB_PASS'], [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
    ]);
    }catch (PDOException $e) {
    die("❌ Database connection failed: " . $e->getMessage());

    $sql = "ALTER TABLE users ADD COLUMN pin TEXT NULL";
    $conn->exec($sql);
    echo "✅ Column 'pin' added successfully.";
} catch (PDOException $e) {
    echo "❌ PDO Error: " . $e->getMessage();
} catch (Exception $e) {
    echo "❌ General Error: " . $e->getMessage();
}
