<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.html");
    exit;
}
$username = htmlspecialchars($_SESSION['username']);
$role = htmlspecialchars($_SESSION['role']);

require 'db.php';

// Get real statistics from database
$stmt = $conn->query("SELECT COUNT(*) as total FROM books");
$total_books = $stmt->fetch_assoc()['total'];

$stmt = $conn->query("SELECT COUNT(*) as total FROM borrows WHERE status='dipinjam'");
$total_borrow = $stmt->fetch_assoc()['total'];

$stmt = $conn->query("SELECT COUNT(*) as total FROM borrows WHERE status='dikembalikan'");
$total_return = $stmt->fetch_assoc()['total'];

$conn->close();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard | Perpustakaan Mini</title>
    <link rel="stylesheet" href="style.css">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
</head>
<body>
    <div class="wrapper">
        <!-- Sidebar -->
        <nav class="sidebar">
            <div class="sidebar-header">
                <div class="avatar">
                    <?php echo strtoupper(substr($username,0,1)); ?>
                </div>
                <div class="user-info">
                    <h3><?php echo $username; ?></h3>
                    <span><?php echo ucfirst($role); ?></span>
                </div>
            </div>

            <ul class="menu">
                <li class="active">
                    <a href="dashboard.php">
                        <i class="material-icons">dashboard</i>
                        <span>Dashboard</span>
                    </a>
                </li>
                <li>
                    <a href="books.php">
                        <i class="material-icons">library_books</i>
                        <span>Data Buku</span>
                    </a>
                </li>
                <li>
                    <a href="borrow.php">
                        <i class="material-icons">book</i>
                        <span>Peminjaman</span>
                    </a>
                </li>
                <li>
                    <a href="return.php">
                        <i class="material-icons">assignment_return</i>
                        <span>Pengembalian</span>
                    </a>
                </li>
                <?php if ($role === 'admin'): ?>
                <li>
                    <a href="members.php">
                        <i class="material-icons">people</i>
                        <span>Data Anggota</span>
                    </a>
                </li>
                <?php endif; ?>
                <li>
                    <a href="profile.php">
                        <i class="material-icons">person</i>
                        <span>Profil</span>
                    </a>
                </li>
                <li>
                    <a href="logout.php">
                        <i class="material-icons">logout</i>
                        <span>Logout</span>
                    </a>
                </li>
            </ul>
        </nav>

        <!-- Main Content -->
        <div class="main-content">
            <header>
                <button class="menu-toggle">
                    <i class="material-icons">menu</i>
                </button>
                <h1>Dashboard</h1>
            </header>

            <div class="content">
                <div class="stats-grid">
                    <div class="stat-card">
                        <i class="material-icons">library_books</i>
                        <div class="stat-info">
                            <h3>Total Buku</h3>
                            <p><?php echo $total_books; ?></p>
                        </div>
                    </div>
                    <div class="stat-card">
                        <i class="material-icons">book</i>
                        <div class="stat-info">
                            <h3>Sedang Dipinjam</h3>
                            <p><?php echo $total_borrow; ?></p>
                        </div>
                    </div>
                    <div class="stat-card">
                        <i class="material-icons">assignment_turned_in</i>
                        <div class="stat-info">
                            <h3>Sudah Dikembalikan</h3>
                            <p><?php echo $total_return; ?></p>
                        </div>
                    </div>
                </div>

                <div class="quick-actions">
                    <h2>Menu Cepat</h2>
                    <div class="action-grid">
                        <a href="books.php" class="action-card">
                            <i class="material-icons">library_books</i>
                            <span>Data Buku</span>
                        </a>
                        <a href="borrow.php" class="action-card">
                            <i class="material-icons">book</i>
                            <span>Peminjaman</span>
                        </a>
                        <a href="return.php" class="action-card">
                            <i class="material-icons">assignment_return</i>
                            <span>Pengembalian</span>
                        </a>
                        <?php if ($role === 'admin'): ?>
                        <a href="members.php" class="action-card">
                            <i class="material-icons">people</i>
                            <span>Data Anggota</span>
                        </a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="script.js"></script>
</body>
</html>
