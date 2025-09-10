<?php
// Load config.php which sets up $config and $pdo
require __DIR__ . '/config.php';

try {
    $pdo->exec("ALTER TABLE users ADD COLUMN pin TEXT NULL");
    echo "✅ Column 'pin' added successfully.";
} catch (PDOException $e) {
    echo "❌ Error: " . $e->getMessage();
}
