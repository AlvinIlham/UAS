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
    <meta name="viewport" content="width=device-width, initial-scale=1.0">    <title>Login | Perpustakaan Mini</title>
    <link rel="stylesheet" href="style.css">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
</head>
<body class="auth-bg">
    <div class="auth-container">
        <div class="auth-header">
            <i class="material-icons auth-icon">library_books</i>
            <h2>Selamat Datang</h2>
            <p class="auth-subtitle">Silahkan login untuk melanjutkan</p>
        </div>
        <?php if (isset($_GET['error'])): ?>
        <div class="alert alert-error">
            <i class="material-icons">error</i>
            <?php echo htmlspecialchars($_GET['error']); ?>
        </div>
        <?php endif; ?>
        <form action="auth.php" method="POST" class="auth-form" autocomplete="off">
            <input type="hidden" name="action" value="login">            <div class="input-group">
                <label for="username">Username</label>
                <div class="input-field">
                    <i class="material-icons">person</i>
                    <input type="text" id="username" name="username" required maxlength="50" autofocus placeholder="Masukkan username">
                </div>
            </div>
            <div class="input-group">
                <label for="password">Password</label>
                <div class="input-field">
                    <i class="material-icons">lock</i>
                    <input type="password" id="password" name="password" required minlength="6" placeholder="Masukkan password">
                    <i class="material-icons toggle-password">visibility_off</i>
                </div>
            </div>
            <button type="submit" class="btn-primary">
                <i class="material-icons">login</i>
                <span>Login</span>
            </button>
            <p class="auth-link">Belum punya akun? <a href="register.php">Daftar</a></p>
        </form>
    </div>
</body>
</html>
