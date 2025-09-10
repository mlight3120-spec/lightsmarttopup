<?php
$config = include __DIR__ . '/config.php';

$dsn = "pgsql:host={$config['DB_HOST']};port={$config['DB_PORT']};dbname={$config['DB_NAME']};";
$pdo = new PDO($dsn, $config['DB_USER'], $config['DB_PASS'], [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
]);

$user_id = $_POST['user_id'] ?? 1;  // demo user
$network = $_POST['network'] ?? 'MTN';
$phone   = $_POST['phone'] ?? '08012345678';
$amount  = $_POST['amount'] ?? 100;

try {
    // Get user balance
    $stmt = $pdo->prepare("SELECT wallet_balance FROM users WHERE id = ?");
    $stmt->execute([$user_id]);
    $wallet = $stmt->fetchColumn();

    if ($wallet < $amount) {
        die("❌ Insufficient balance.");
    }

    // Deduct from wallet
    $stmt = $pdo->prepare("UPDATE users SET wallet_balance = wallet_balance - ? WHERE id = ?");
    $stmt->execute([$amount, $user_id]);

    // Record transaction
    $stmt = $pdo->prepare("INSERT INTO transactions (user_id, type, amount, status) VALUES (?, 'airtime', ?, 'success')");
    $stmt->execute([$user_id, $amount]);

    echo "✅ Airtime of ₦{$amount} purchased for {$phone} ({$network}).";
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage();
}
?>
