<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$config = include __DIR__ . '/config.php';
$dsn = "mysql:host={$config['DB_HOST']};port={$config['DB_PORT']};dbname={$config['DB_NAME']}";
$pdo = new PDO($dsn, $config['DB_USER'], $config['DB_PASS']);
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

// Get current user
$user_id = $_SESSION['user_id'];
$stmt = $pdo->prepare("SELECT email, wallet_balance FROM users WHERE id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

$balance = $user['wallet_balance'];
$email = $user['email'];
?>
<!DOCTYPE html>
<html>
<head>
    <title>Dashboard - LightsmartTopup</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
</head>
<body class="bg-light">
<div class="container mt-5">
    <div class="card shadow p-4">
        <h2 class="text-center text-primary">⚡ LightsmartTopup Dashboard</h2>
        <h4 class="text-center">Welcome, <?php echo htmlspecialchars($email); ?></h4>
        <h5 class="text-center">💰 Wallet Balance: ₦<?php echo number_format($balance, 2); ?></h5>

        <div class="row text-center mt-4">
            <div class="col-md-4 mb-3">
                <a href="buy_airtime.php" class="btn btn-primary w-100">📱 Buy Airtime</a>
            </div>
            <div class="col-md-4 mb-3">
                <a href="buy_data.php" class="btn btn-success w-100">🌐 Buy Data</a>
            </div>
            <div class="col-md-4 mb-3">
                <a href="fund_wallet.php" class="btn btn-warning w-100">💵 Fund Wallet</a>
            </div>
        </div>

        <div class="text-center mt-4">
            <a href="transactions.php" class="btn btn-info">📜 View Transactions</a>
            <a href="logout.php" class="btn btn-danger">🚪 Logout</a>
        </div>
    </div>
</div>
</body>
</html>
