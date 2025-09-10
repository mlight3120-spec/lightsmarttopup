<?php
require 'config.php';

try {
    // Check if $conn exists
    if (!isset($conn)) {
        throw new Exception("Database connection not available. Check config.php.");
    }

    $sql = "ALTER TABLE users ADD COLUMN pin TEXT NULL";
    $conn->exec($sql);
    echo "✅ Column 'pin' added successfully.";
} catch (PDOException $e) {
    echo "❌ PDO Error: " . $e->getMessage();
} catch (Exception $e) {
    echo "❌ General Error: " . $e->getMessage();
}
