<?php
$config = include __DIR__ . '/config.php';

$dsn = "mysql:host={$config['DB_HOST']};port={$config['DB_PORT']};dbname={$config['DB_NAME']}";
$pdo = new PDO($dsn, $config['DB_USER'], $config['DB_PASS']);
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

$user_id = 1; // Demo logged-in user
$stmt = $pdo->prepare("SELECT wallet_balance FROM users WHERE id = ?");
$stmt->execute([$user_id]);
$balance = $stmt->fetchColumn();
?>
<!DOCTYPE html>
<html>
<head>
    <title>LightsmartTopup - Dashboard</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
</head>
<body class="bg-light">
<div class="container mt-5">
    <div class="card shadow p-4">
        <h1 class="text-center text-primary">💡 LightsmartTopup</h1>
        <h4 class="text-center">Welcome, Demo User</h4>
        <h3 class="text-center text-success">Wallet Balance: ₦<?php echo number_format($balance, 2); ?></h3>
        <hr>
        <div class="row text-center">
            <div class="col-md-4 mb-3">
                <form action="fund_wallet.php" method="POST">
                    <input type="hidden" name="user_id" value="1">
                    <input type="number" name="amount" class="form-control mb-2" placeholder="Enter amount">
                    <button class="btn btn-primary w-100">💳 Fund Wallet</button>
                </form>
            </div>
            <div class="col-md-4 mb-3">
                <form action="buy_airtime.php" method="POST">
                    <input type="hidden" name="user_id" value="1">
                    <select name="network" class="form-control mb-2">
                        <option>MTN</option>
                        <option>GLO</option>
                        <option>Airtel</option>
                        <option>9mobile</option>
                    </select>
                    <input type="text" name="phone" class="form-control mb-2" placeholder="Phone Number">
                    <input type="number" name="amount" class="form-control mb-2" placeholder="Amount">
                    <button class="btn btn-warning w-100">📱 Buy Airtime</button>
                </form>
            </div>
            <div class="col-md-4 mb-3">
                <form action="buy_data.php" method="POST">
                    <input type="hidden" name="user_id" value="1">
                    <select name="network" class="form-control mb-2">
                        <option>MTN</option>
                        <option>GLO</option>
                        <option>Airtel</option>
                        <option>9mobile</option>
                    </select>
                    <input type="text" name="phone" class="form-control mb-2" placeholder="Phone Number">
                    <select name="plan" class="form-control mb-2">
                        <option value="1GB">1GB - ₦300</option>
                        <option value="2GB">2GB - ₦500</option>
                        <option value="5GB">5GB - ₦1200</option>
                    </select>
                    <input type="hidden" name="price" value="300" id="dataPrice">
                    <button class="btn btn-success w-100">🌐 Buy Data</button>
                </form>
            </div>
        </div>
        <div class="text-center mt-4">
            <a href="transactions.php?user_id=1" class="btn btn-dark">📋 View Transactions</a>
            <a href="admin_commission.php" class="btn btn-secondary">💰 Admin Commission</a>
        </div>
    </div>
</div>
<script>
document.querySelector("select[name='plan']").addEventListener("change", function(){
    let priceField = document.getElementById("dataPrice");
    if (this.value === "1GB") priceField.value = 300;
    if (this.value === "2GB") priceField.value = 500;
    if (this.value === "5GB") priceField.value = 1200;
});
</script>
</body>
</html>
