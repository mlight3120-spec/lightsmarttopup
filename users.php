<?php
session_start();
$config = include __DIR__ . '/config.php';

$dsn = "mysql:host={$config['DB_HOST']};port={$config['DB_PORT']};dbname={$config['DB_NAME']}";
$pdo = new PDO($dsn, $config['DB_USER'], $config['DB_PASS']);
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

// ✅ Protect for admin only
$admin_email = "admin@lightsmarttopup.com";
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}
$stmt = $pdo->prepare("SELECT email FROM users WHERE id = ?");
$stmt->execute([$_SESSION['user_id']]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);
if (!$user || $user['email'] !== $admin_email) {
    echo "⛔ Access denied. Admin only.";
    exit;
}

$users = $pdo->query("SELECT id, email, wallet_balance, created_at FROM users ORDER BY id DESC")->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html>
<head>
    <title>Manage Users - LightsmartTopup</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
</head>
<body class="bg-light">
<div class="container mt-5">
    <div class="card shadow p-4">
        <h2 class="text-center">👥 Manage Users</h2>
        <table class="table table-bordered table-striped mt-3">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Email</th>
                    <th>Wallet Balance</th>
                    <th>Joined</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($users as $u): ?>
                <tr>
                    <td><?php echo $u['id']; ?></td>
                    <td><?php echo htmlspecialchars($u['email']); ?></td>
                    <td>₦<?php echo number_format($u['wallet_balance'], 2); ?></td>
                    <td><?php echo $u['created_at']; ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <a href="admin.php" class="btn btn-secondary">⬅ Back to Dashboard</a>
    </div>
</div>
</body>
</html>
