<?php
// Load DB config
$config = include(__DIR__ . '/config.php');

try {
    // Connect to PostgreSQL
    $dsn = "pgsql:host={$config['DB_HOST']};port={$config['DB_PORT']};dbname={$config['DB_NAME']};";
    $pdo = new PDO($dsn, $config['DB_USER'], $config['DB_PASS'], [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
    ]);

    echo "✅ Connected to Render PostgreSQL successfully.<br>";

    // Create tables (adjust as needed)
    $queries = [

        // Users table
        "CREATE TABLE IF NOT EXISTS users (
            id SERIAL PRIMARY KEY,
            name VARCHAR(100) NOT NULL,
            email VARCHAR(100) UNIQUE NOT NULL,
            password VARCHAR(255) NOT NULL,
            balance NUMERIC(12,2) DEFAULT 0,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        );",

        // Transactions table
        "CREATE TABLE IF NOT EXISTS transactions (
            id SERIAL PRIMARY KEY,
            user_id INT REFERENCES users(id) ON DELETE CASCADE,
            type VARCHAR(50) NOT NULL, -- airtime, data, wallet_fund
            amount NUMERIC(12,2) NOT NULL,
            status VARCHAR(50) DEFAULT 'pending',
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        );",

        // Wallet funding commissions
        "CREATE TABLE IF NOT EXISTS commissions (
            id SERIAL PRIMARY KEY,
            user_id INT REFERENCES users(id) ON DELETE CASCADE,
            funding_amount NUMERIC(12,2) NOT NULL,
            commission NUMERIC(12,2) DEFAULT 50,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        );"
    ];

    foreach ($queries as $sql) {
        $pdo->exec($sql);
    }

    echo "✅ All tables created successfully in PostgreSQL.<br>";
    echo "👉 Now you can delete install.php for security.";

} catch (PDOException $e) {
    echo "❌ Setup failed: " . $e->getMessage();
}
