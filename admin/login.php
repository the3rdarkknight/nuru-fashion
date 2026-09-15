<?php
// 1. Start the session first!
session_start(); 

require_once '../config/database.php';

// If already logged in, send to dashboard
if (!empty($_SESSION['admin_id'])) {
    header('Location: dashboard.php');
    exit;
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    $stmt = $pdo->prepare("SELECT * FROM admin_users WHERE username = ?");
    $stmt->execute([$username]);
    $admin = $stmt->fetch();

    if ($admin && password_verify($password, $admin['password_hash'])) {
        // 2. These will now save properly
        $_SESSION['admin_id'] = $admin['id'];
        $_SESSION['admin_username'] = $admin['username'];
        
        header('Location: dashboard.php');
        exit;
    } else {
        $error = 'Incorrect username or password.';
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Admin Login — Nuru Fashion</title>
<link rel="stylesheet" href="admin.css">
</head>
<body>
<div class="login-shell">
    <div class="login-card">
        <h1>Nuru<span style="color:#E1226B">Fashion</span></h1>
        <p class="sub">Admin panel</p>
        <?php if ($error): ?>
            <div class="alert error"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>
        <form method="POST">
            <label for="username">Username</label>
            <input type="text" id="username" name="username" required autofocus>
            <label for="password">Password</label>
            <input type="password" id="password" name="password" required>
            <div class="form-actions">
                <button type="submit" class="btn btn-magenta" style="width:100%; justify-content:center;">Log in</button>
            </div>
        </form>
    </div>
</div>
</body>
</html>