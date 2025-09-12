<?php
session_start();

// load database config
require_once __DIR__ . "/config.php";

try {
    // connect to PostgreSQL using config.php variables
    $dsn = "pgsql:host=$host;port=$port;dbname=$dbname;";
    $pdo = new PDO($dsn, $user, $pass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
    ]);
} catch (PDOException $e) {
    die("❌ Database connection failed: " . $e->getMessage());
}

// handle form submit
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $pin = $_POST['pin'] ?? '';

    if (preg_match('/^\d{4}$/', $pin)) {
        $user_id = $_SESSION['user_id'] ?? null;

        if ($user_id) {
            $stmt = $pdo->prepare("UPDATE users SET pin = :pin WHERE id = :id");
            $stmt->execute([
                ':pin' => password_hash($pin, PASSWORD_BCRYPT),
                ':id'  => $user_id
            ]);
            echo "✅ Transaction PIN set successfully!";
        } else {
            echo "❌ User not logged in.";
        }
    } else {
        echo "❌ Invalid PIN. Enter exactly 4 digits.";
    }
}
?>

<!-- PIN setup form -->
<h2>Set Transaction PIN</h2>
<form method="post">
    <label>Enter 4-Digit PIN:</label>
    <input type="password" name="pin" maxlength="4" required>
    <button type="submit">Save PIN</button>
</form>
