<?php
session_start();

// 🔒 Optional: simple admin login (change password)
$admin_password = "lightsmart2025";
if (!isset($_SESSION['is_admin'])) {
    if (isset($_POST['admin_pass'])) {
        if ($_POST['admin_pass'] === $admin_password) {
            $_SESSION['is_admin'] = true;
            header("Location: admin.php");
            exit;
        } else {
            $error = "Invalid password!";
        }
    }
    ?>
    <!DOCTYPE html>
    <html>
    <head>
        <title>Admin Login</title>
        <style>
            body { font-family: Arial, sans-serif; background: #f4f4f9; display: flex; justify-content: center; align-items: center; height: 100vh; }
            .card { background: #fff; padding: 20px; border-radius: 12px; box-shadow: 0 4px 10px rgba(0,0,0,0.1); width: 300px; }
            input { padding: 10px; width: 100%; margin: 10px 0; border-radius: 8px; border: 1px solid #ccc; }
            button { padding: 10px; background: #4CAF50; color: white; border: none; border-radius: 8px; width: 100%; }
            button:hover { background: #45a049; }
            .error { color: red; text-align: center; }
        </style>
    </head>
    <body>
        <div class="card">
            <h2>Admin Login</h2>
            <?php if (!empty($error)) echo "<p class='error'>$error</p>"; ?>
            <form method="post">
                <input type="password" name="admin_pass" placeholder="Enter Password" required>
                <button type="submit">Login</button>
            </form>
        </div>
    </body>
    </html>
    <?php
    exit;
}

$config = include(__DIR__ . '/config.php');

try {
    $dsn = "pgsql:host={$config['DB_HOST']};port={$config['DB_PORT']};dbname={$config['DB_NAME']};";
    $pdo = new PDO($dsn, $config['DB_USER'], $config['DB_PASS'], [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
    ]);
} catch (PDOException $e) {
    die("❌ Database connection failed: " . $e->getMessage());
}

// Fetch all users
$users = $pdo->query("SELECT id, name, email, balance, created_at FROM users ORDER BY id ASC")->fetchAll(PDO::FETCH_ASSOC);

// Fetch transactions
$transactions = $pdo->query("SELECT * FROM transactions ORDER BY created_at DESC")->fetchAll(PDO::FETCH_ASSOC);

// Fetch commissions
$commissions = $pdo->query("SELECT * FROM commissions ORDER BY created_at DESC")->fetchAll(PDO::FETCH_ASSOC);
$total_commission = $pdo->query("SELECT SUM(commission) as total FROM commissions")->fetch(PDO::FETCH_ASSOC)['total'];
?>
<!DOCTYPE html>
<html>
<head>
    <title>Admin Dashboard - Lightsmart</title>
    <style>
        body { font-family: Arial, sans-serif; background: #f4f4f9; padding: 20px; }
        .card { background: #fff; padding: 20px; border-radius: 12px; box-shadow: 0 4px 10px rgba(0,0,0,0.1); margin-bottom: 30px; }
        h2 { margin-top: 0; }
        table { width: 100%; border-collapse: collapse; margin-top: 15px; }
        table th, table td { border: 1px solid #ddd; padding: 8px; text-align: center; }
        table th { background: #f2f2f2; }
        .logout { float: right; color: red; text-decoration: none; }
    </style>
</head>
<body>

<div class="card">
    <h2>Admin Dashboard</h2>
    <p><a href="logout.php" class="logout">Logout</a></p>
    <p><strong>Total Commission Earned:</strong> ₦<?= number_format($total_commission, 2) ?></p>
</div>

<div class="card">
    <h2>Users</h2>
    <table>
        <tr>
            <th>ID</th><th>Name</th><th>Email</th><th>Balance</th><th>Joined</th>
        </tr>
        <?php foreach ($users as $u): ?>
            <tr>
                <td><?= $u['id'] ?></td>
                <td><?= htmlspecialchars($u['name']) ?></td>
                <td><?= htmlspecialchars($u['email']) ?></td>
                <td>₦<?= number_format($u['balance'], 2) ?></td>
                <td><?= $u['created_at'] ?></td>
            </tr>
        <?php endforeach; ?>
    </table>
</div>

<div class="card">
    <h2>Transactions</h2>
    <table>
        <tr>
            <th>ID</th><th>User ID</th><th>Type</th><th>Amount</th><th>Status</th><th>Date</th>
        </tr>
        <?php foreach ($transactions as $t): ?>
            <tr>
                <td><?= $t['id'] ?></td>
                <td><?= $t['user_id'] ?></td>
                <td><?= htmlspecialchars($t['type']) ?></td>
                <td>₦<?= number_format($t['amount'], 2) ?></td>
                <td><?= $t['status'] ?></td>
                <td><?= $t['created_at'] ?></td>
            </tr>
        <?php endforeach; ?>
    </table>
</div>

<div class="card">
    <h2>Commissions</h2>
    <table>
        <tr>
            <th>ID</th><th>User ID</th><th>Funding Amount</th><th>Commission</th><th>Date</th>
        </tr>
        <?php foreach ($commissions as $c): ?>
            <tr>
                <td><?= $c['id'] ?></td>
                <td><?= $c['user_id'] ?></td>
                <td>₦<?= number_format($c['funding_amount'], 2) ?></td>
                <td>₦<?= number_format($c['commission'], 2) ?></td>
                <td><?= $c['created_at'] ?></td>
            </tr>
        <?php endforeach; ?>
    </table>
</div>

</body>
</html>
