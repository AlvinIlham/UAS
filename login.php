<?php
session_start();
if (isset($_SESSION['user_id'])) {
    header("Location: dashboard.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login | Perpustakaan Mini</title>
    <link rel="stylesheet" href="style.css">
</head>
<body class="auth-bg">
    <div class="auth-container">
        <h2>Login Akun</h2>
        <?php if (isset($_GET['error'])): ?>
        <div class="alert alert-error">
            <?php echo htmlspecialchars($_GET['error']); ?>
        </div>
        <?php endif; ?>
        <form action="auth.php" method="POST" class="auth-form" autocomplete="off">
            <input type="hidden" name="action" value="login">
            <div class="input-group">
                <label for="username">Username</label>
                <input type="text" id="username" name="username" required maxlength="50" autofocus>
            </div>
            <div class="input-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" required minlength="6">
            </div>
            <button type="submit" class="btn-primary">Login</button>
            <p class="auth-link">Belum punya akun? <a href="register.php">Daftar</a></p>
        </form>
    </div>
</body>
</html>
