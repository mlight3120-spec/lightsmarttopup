<?php
$config = include __DIR__ . '/config.php';

$dsn = "mysql:host={$config['DB_HOST']};port={$config['DB_PORT']};dbname={$config['DB_NAME']}";
$pdo = new PDO($dsn, $config['DB_USER'], $config['DB_PASS']);
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

$user_id = $_POST['user_id'] ?? 1;  // demo user
$network = $_POST['network'] ?? 'MTN';
$phone   = $_POST['phone'] ?? '08012345678';
$plan    = $_POST['plan'] ?? '1GB';
$price   = $_POST['price'] ?? 300;

try {
    // Get user balance
    $stmt = $pdo->prepare("SELECT wallet_balance FROM users WHERE id = ?");
    $stmt->execute([$user_id]);
    $wallet = $stmt->fetchColumn();

    if ($wallet < $price) {
        die("❌ Insufficient balance.");
    }

    // Deduct from wallet
    $stmt = $pdo->prepare("UPDATE users SET wallet_balance = wallet_balance - ? WHERE id = ?");
    $stmt->execute([$price, $user_id]);

    // Record transaction
    $stmt = $pdo->prepare("INSERT INTO transactions (user_id, type, amount, status) VALUES (?, 'data', ?, 'success')");
    $stmt->execute([$user_id, $price]);

    echo "✅ Data plan {$plan} ({$network}) purchased for {$phone} at ₦{$price}.";
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage();
}
?>
