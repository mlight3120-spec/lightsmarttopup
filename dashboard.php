<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
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

// Fetch user info
$stmt = $pdo->prepare("SELECT balance, name FROM users WHERE id = :id");
$stmt->execute([':id' => $_SESSION['user_id']]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);
$balance = $user['balance'];

// Handle wallet funding
if (isset($_POST['fund_wallet'])) {
    $amount = floatval($_POST['amount']);
    if ($amount > 0) {
        $pdo->prepare("UPDATE users SET balance = balance + :amt WHERE id = :id")
            ->execute([':amt' => $amount, ':id' => $_SESSION['user_id']]);

        $pdo->prepare("INSERT INTO transactions (user_id, type, amount, status) VALUES (:uid, 'wallet_fund', :amt, 'successful')")
            ->execute([':uid' => $_SESSION['user_id'], ':amt' => $amount]);

        $pdo->prepare("INSERT INTO commissions (user_id, funding_amount, commission) VALUES (:uid, :amt, 50)")
            ->execute([':uid' => $_SESSION['user_id'], ':amt' => $amount]);

        header("Location: dashboard.php");
        exit;
    }
}

// Handle Airtime Purchase
if (isset($_POST['buy_airtime'])) {
    $network = $_POST['network'];
    $phone = $_POST['phone'];
    $amount = floatval($_POST['amount']);

    if ($amount > 0 && $balance >= $amount) {
        $pdo->prepare("UPDATE users SET balance = balance - :amt WHERE id = :id")
            ->execute([':amt' => $amount, ':id' => $_SESSION['user_id']]);

        $pdo->prepare("INSERT INTO transactions (user_id, type, amount, status) VALUES (:uid, 'airtime - $network', :amt, 'successful')")
            ->execute([':uid' => $_SESSION['user_id'], ':amt' => $amount]);

        header("Location: dashboard.php");
        exit;
    }
}

// Handle Data Purchase
if (isset($_POST['buy_data'])) {
    $network = $_POST['network'];
    $phone = $_POST['phone'];
    $plan = $_POST['plan'];
    $price = ($plan == "1GB") ? 300 : (($plan == "2GB") ? 500 : 1000);

    if ($balance >= $price) {
        $pdo->prepare("UPDATE users SET balance = balance - :amt WHERE id = :id")
            ->execute([':amt' => $price, ':id' => $_SESSION['user_id']]);

        $pdo->prepare("INSERT INTO transactions (user_id, type, amount, status) VALUES (:uid, 'data - $network - $plan', :amt, 'successful')")
            ->execute([':uid' => $_SESSION['user_id'], ':amt' => $price]);

        header("Location: dashboard.php");
        exit;
    }
}

// Fetch transactions
$stmt = $pdo->prepare("SELECT * FROM transactions WHERE user_id = :id ORDER BY created_at DESC");
$stmt->execute([':id' => $_SESSION['user_id']]);
$transactions = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Dashboard - Lightsmart Topup</title>
    <style>
        body { font-family: Arial, sans-serif; background: #f4f4f9; padding: 20px; }
        .card { background: #fff; padding: 20px; border-radius: 12px; box-shadow: 0 4px 10px rgba(0,0,0,0.1); margin-bottom: 20px; }
        h2 { margin-top: 0; }
        form { margin-top: 10px; }
        input, select { padding: 10px; border: 1px solid #ccc; border-radius: 8px; margin: 5px 0; width: 100%; }
        button { padding: 10px; background: #4CAF50; color: #fff; border: none; border-radius: 8px; cursor: pointer; }
        button:hover { background: #45a049; }
        table { width: 100%; border-collapse: collapse; margin-top: 15px; }
        table th, table td { border: 1px solid #ddd; padding: 10px; text-align: center; }
        table th { background: #f2f2f2; }
        .logout { float: right; }
    </style>
</head>
<body>

<div class="card">
    <h2>Welcome, <?= htmlspecialchars($user['name']) ?> 👋</h2>
    <p><strong>Wallet Balance:</strong> ₦<?= number_format($balance, 2) ?></p>
    <form method="post">
        <input type="number" name="amount" placeholder="Enter amount" required>
        <button type="submit" name="fund_wallet">Fund Wallet</button>
    </form>
    <p><a href="logout.php" class="logout">Logout</a></p>
</div>

<div class="card">
    <h2>Buy Airtime</h2>
    <form method="post">
        <select name="network" required>
            <option value="">Select Network</option>
            <option value="MTN">MTN</option>
            <option value="GLO">GLO</option>
            <option value="AIRTEL">AIRTEL</option>
            <option value="9MOBILE">9MOBILE</option>
        </select>
        <input type="text" name="phone" placeholder="Phone Number" required>
        <input type="number" name="amount" placeholder="Amount" required>
        <button type="submit" name="buy_airtime">Purchase Airtime</button>
    </form>
</div>

<div class="card">
    <h2>Buy Data</h2>
    <form method="post">
        <select name="network" required>
            <option value="">Select Network</option>
            <option value="MTN">MTN</option>
            <option value="GLO">GLO</option>
            <option value="AIRTEL">AIRTEL</option>
            <option value="9MOBILE">9MOBILE</option>
        </select>
        <input type="text" name="phone" placeholder="Phone Number" required>
        <select name="plan" required>
            <option value="">Select Data Plan</option>
            <option value="1GB">1GB - ₦300</option>
            <option value="2GB">2GB - ₦500</option>
            <option value="5GB">5GB - ₦1000</option>
        </select>
        <button type="submit" name="buy_data">Purchase Data</button>
    </form>
</div>

<div class="card">
    <h2>Your Transactions</h2>
    <table>
        <tr>
            <th>ID</th>
            <th>Type</th>
            <th>Amount</th>
            <th>Status</th>
            <th>Date</th>
        </tr>
        <?php foreach ($transactions as $tx): ?>
            <tr>
                <td><?= $tx['id'] ?></td>
                <td><?= htmlspecialchars($tx['type']) ?></td>
                <td>₦<?= number_format($tx['amount'], 2) ?></td>
                <td><?= htmlspecialchars($tx['status']) ?></td>
                <td><?= $tx['created_at'] ?></td>
            </tr>
        <?php endforeach; ?>
    </table>
</div>

</body>
</html>
