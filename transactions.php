<?php
$config = include __DIR__ . '/config.php';

$dsn = "mysql:host={$config['DB_HOST']};port={$config['DB_PORT']};dbname={$config['DB_NAME']}";
$pdo = new PDO($dsn, $config['DB_USER'], $config['DB_PASS']);
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

$user_id = $_GET['user_id'] ?? 1;  // demo user

$stmt = $pdo->prepare("SELECT * FROM transactions WHERE user_id = ? ORDER BY created_at DESC");
$stmt->execute([$user_id]);
$transactions = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo "<h1>📋 Transaction History</h1>";
if (!$transactions) {
    echo "<p>No transactions yet.</p>";
} else {
    echo "<table border='1' cellpadding='5'>
            <tr><th>ID</th><th>Type</th><th>Amount</th><th>Status</th><th>Date</th></tr>";
    foreach ($transactions as $t) {
        echo "<tr>
                <td>{$t['id']}</td>
                <td>{$t['type']}</td>
                <td>₦{$t['amount']}</td>
                <td>{$t['status']}</td>
                <td>{$t['created_at']}</td>
              </tr>";
    }
    echo "</table>";
}
?>
