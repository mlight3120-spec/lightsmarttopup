<?php
session_start();
$config = include(__DIR__ . '/config.php');

try {
    $dsn = "pgsql:host={$config['DB_HOST']};port={$config['DB_PORT']};dbname={$config['DB_NAME']};";
    $pdo = new PDO($dsn, $config['DB_USER'], $config['DB_PASS'], [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
    ]);
} catch (PDOException $e) {
    die("❌ Database connection failed: " . $e->getMessage());
}

// Handle Signup
if (isset($_POST['action']) && $_POST['action'] === 'signup') {
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $password = password_hash($_POST['password'], PASSWORD_BCRYPT);

    try {
        $stmt = $pdo->prepare("INSERT INTO users (name, email, password) VALUES (:name, :email, :password)");
        $stmt->execute([
            ':name' => $name,
            ':email' => $email,
            ':password' => $password
        ]);
        $success = "✅ Account created successfully. Please login.";
    } catch (PDOException $e) {
        $error = "❌ Signup failed: " . $e->getMessage();
    }
}

// Handle Login
if (isset($_POST['action']) && $_POST['action'] === 'login') {
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    $stmt = $pdo->prepare("SELECT * FROM users WHERE email = :email LIMIT 1");
    $stmt->execute([':email' => $email]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['name'] = $user['name'];
        header("Location: dashboard.php");
        exit;
    } else {
        $error = "❌ Invalid email or password.";
    }
}

// If already logged in
if (isset($_SESSION['user_id'])) {
    header("Location: dashboard.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Lightsmart Topup - Login & Signup</title>
    <style>
        body { font-family: Arial, sans-serif; background: #f4f4f9; display: flex; justify-content: center; align-items: center; height: 100vh; }
        .container { background: #fff; padding: 20px; border-radius: 12px; box-shadow: 0 4px 10px rgba(0,0,0,0.1); width: 350px; }
        h2 { text-align: center; margin-bottom: 20px; }
        form { display: flex; flex-direction: column; }
        input { margin: 8px 0; padding: 10px; border: 1px solid #ccc; border-radius: 8px; }
        button { padding: 10px; background: #4CAF50; color: #fff; border: none; border-radius: 8px; cursor: pointer; }
        button:hover { background: #45a049; }
        .toggle { text-align: center; margin-top: 15px; }
        .toggle a { color: #4CAF50; text-decoration: none; }
        .message { text-align: center; margin-bottom: 10px; color: red; }
        .success { color: green; }
    </style>
    <script>
        function showForm(id) {
            document.getElementById('loginForm').style.display = 'none';
            document.getElementById('signupForm').style.display = 'none';
            document.getElementById(id).style.display = 'block';
        }
    </script>
</head>
<body>

<div class="container">
    <h2>Lightsmart Topup</h2>

    <?php if (!empty($error)): ?>
        <div class="message"><?= $error ?></div>
    <?php endif; ?>
    <?php if (!empty($success)): ?>
        <div class="message success"><?= $success ?></div>
    <?php endif; ?>

    <!-- Login Form -->
    <form id="loginForm" method="post" style="display:block;">
        <input type="hidden" name="action" value="login">
        <input type="email" name="email" placeholder="Email" required />
        <input type="password" name="password" placeholder="Password" required />
        <button type="submit">Login</button>
        <div class="toggle">New here? <a href="#" onclick="showForm('signupForm')">Sign Up</a></div>
    </form>

    <!-- Signup Form -->
    <form id="signupForm" method="post" style="display:none;">
        <input type="hidden" name="action" value="signup">
        <input type="text" name="name" placeholder="Full Name" required />
        <input type="email" name="email" placeholder="Email" required />
        <input type="password" name="password" placeholder="Password" required />
        <button type="submit">Sign Up</button>
        <div class="toggle">Already have an account? <a href="#" onclick="showForm('loginForm')">Login</a></div>
    </form>
</div>

</body>
</html>
