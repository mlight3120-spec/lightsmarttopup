<?php
$config = include __DIR__ . '/config.php';

$dsn = "mysql:host={$config['DB_HOST']};port={$config['DB_PORT']};dbname={$config['DB_NAME']}";
$pdo = new PDO($dsn, $config['DB_USER'], $config['DB_PASS']);
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

// Get total commission
$stmt = $pdo->query("SELECT SUM(amount) as total FROM commission");
$total = $stmt->fetch(PDO::FETCH_ASSOC)['total'] ?? 0;

echo "<h1>💰 Total Commission Earned: ₦{$total}</h1>";
?>
