<?php
$config = include __DIR__ . '/config.php';
?>
<!DOCTYPE html>
<html>
<head>
    <title>LightsmartTopup</title>
</head>
<body>
    <h1>Welcome to LightsmartTopup</h1>
    <p>Your reliable platform for Airtime & Data Topup.</p>
    if ($db_not_connected) {
    die("❌ Database connection failed. Please contact admin.");
}

</body>
</html>
