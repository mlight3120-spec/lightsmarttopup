<?php
session_start();

<?php
session_start();

// include your config file
require_once __DIR__ . "/config.php";

try {
    // use the variables from config.php
    $dsn = "pgsql:host=$host;port=$port;dbname=$dbname;";
    $pdo = new PDO($dsn, $user, $pass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
    ]);
} catch (PDOException $e) {
    die("❌ Database connection failed: " . $e->getMessage());
}

require __DIR__ . '/config.php'; // this only defines $host, $port, $dbname, $user, $pass

// Create connection here
try {
    $dsn = "pgsql:host=$host;port=$port;dbname=$dbname;";
    $pdo = new PDO($dsn, $user, $pass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
    ]);
} catch (PDOException $e) {
    die("❌ Database connection failed: " . $e->getMessage());
}

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
