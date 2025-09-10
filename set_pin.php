<?php
session_start();
require __DIR__ . '/config.php';

// User must be logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$message = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $pin = trim($_POST['pin']);
    $user_id = $_SESSION['user_id'];

    if (preg_match('/^[0-9]{4}$/', $pin)) {
        try {
            // Build connection fresh with $config values
            $dsn = "pgsql:host={$config['DB_HOST']};port={$config['DB_PORT']};dbname={$config['DB_NAME']};";
            $pdo = new PDO($dsn, $config['DB_USER'], $config['DB_PASS'], [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
            ]);

            $stmt = $pdo->prepare("UPDATE users SET pin = :pin WHERE id = :id");
            $stmt->execute([
                ':pin' => password_hash($pin, PASSWORD_DEFAULT),
                ':id'  => $user_id
            ]);

            $message = "✅ PIN set successfully!";
        } catch (PDOException $e) {
            $message = "❌ Error: " . $e->getMessage();
        }
    } else {
        $message = "❌ PIN must be exactly 4 digits.";
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Set Transaction PIN</title>
</head>
<body>
    <h2>Set Transaction PIN</h2>
    <?php if ($message): ?>
        <p><?php echo $message; ?></p>
    <?php endif; ?>
    <form method="post">
        <label>Enter 4-Digit PIN:</label>
        <input type="password" name="pin" maxlength="4" required>
        <button type="submit">Save PIN</button>
    </form>
</body>
</html>
