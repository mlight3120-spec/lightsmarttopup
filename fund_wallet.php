<?php
$config = include __DIR__ . '/config.php';

$dsn = "pgsql:host={$config['DB_HOST']};port={$config['DB_PORT']};dbname={$config['DB_NAME']};";
$pdo = new PDO($dsn, $config['DB_USER'], $config['DB_PASS'], [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
]);

// Example: funding request from user (in production, you integrate Paystack callback here)
$user_id = $_POST['user_id'] ?? 1;   // default demo user
$amount  = $_POST['amount'] ?? 1000; // demo amount

try {
    $pdo->beginTransaction();

    // Update user wallet
    $stmt = $pdo->prepare("UPDATE users SET wallet_balance = wallet_balance + ? WHERE id = ?");
    $stmt->execute([$amount, $user_id]);

    // Add transaction record
    $stmt = $pdo->prepare("INSERT INTO transactions (user_id, type, amount, status) VALUES (?, 'funding', ?, 'success')");
    $stmt->execute([$user_id, $amount]);

    // Add commission (fixed ₦50 for every funding)
    $stmt = $pdo->prepare("INSERT INTO commission (amount) VALUES (?)");
    $stmt->execute([$config['FUNDING_COMMISSION_FIXED']]);

    $pdo->commit();

    echo "✅ Wallet funded with ₦{$amount}. ₦{$config['FUNDING_COMMISSION_FIXED']} commission added.";
} catch (Exception $e) {
    $pdo->rollBack();
    echo "❌ Error: " . $e->getMessage();
}
?>
