<?php
require 'config.php';

try {
    $sql = "ALTER TABLE users ADD COLUMN pin TEXT NULL";
    $conn->exec($sql);
    echo "✅ Column 'pin' added successfully.";
} catch (PDOException $e) {
    echo "❌ Error: " . $e->getMessage();
}
