<?php
session_start();
$config = include __DIR__ . '/config.php';

$dsn = "mysql:host={$config['DB_HOST']};port={$config['DB_PORT']};dbname={$config['DB_NAME']}";
$pdo = new PDO($dsn, $config['DB_USER'], $config['DB_PASS']);
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

// ✅ Set your admin email here
$admin_email = "admin@lightsmarttopup.com";

// Protect page
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

// Get logged in user
$stmt = $pdo->prepare("SELECT email FROM users WHERE id = ?");
$stmt->execute([$_SESSION['user_id']]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$user || $user['email'] !== $admin_email) {
    echo "⛔ Access denied. Admin only.";
    exit;
}

// Fetch stats
$total_users = $pdo->query("SELECT COUNT(*) FROM users")->fetchColumn();
$total_transactions = $pdo->query("SELECT COUNT(*) FROM transactions")->fetchColumn();
$total_commission = $pdo->query("SELECT SUM(amount) FROM commissions")->fetchColumn() ?: 0;
?>
<!DOCTYPE html>
<html>
<head>
    <title>Admin Dashboard - LightsmartTopup</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
</head>
<body class="bg-light">
<div class="container mt-5">
    <div class="card shadow p-4">
        <h2 class="text-center text-primary">⚡ Admin Dashboard</h2>
        <p class="text-center">Welcome, <?php echo htmlspecialchars($user['email']); ?></p>

        <div class="row text-center mt-4">
            <div class="col-md-4">
                <div class="card bg-info text-white p-3">
                    <h3><?php echo $total_users; ?></h3>
                    <p>👥 Total Users</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card bg-success text-white p-3">
                    <h3><?php echo $total_transactions; ?></h3>
                    <p>💳 Total Transactions</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card bg-warning text-dark p-3">
                    <h3>₦<?php echo number_format($total_commission, 2); ?></h3>
                    <p>💰 Total Commission</p>
                </div>
            </div>
        </div>

        <h4 class="mt-4">Quick Actions</h4>
        <div class="list-group">
            <a href="transactions.php" class="list-group-item list-group-item-action">📜 View All Transactions</a>
            <a href="users.php" class="list-group-item list-group-item-action">👤 Manage Users</a>
            <a href="admin_commission.php" class="list-group-item list-group-item-action">💵 View Commission Log</a>
        </div>

        <a href="logout.php" class="btn btn-danger mt-3">🚪 Logout</a>
    </div>
</div>
</body>
</html>
