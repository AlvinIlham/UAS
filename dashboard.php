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
<body class="dashboard-bg">
    <div class="dashboard-grid">
        <!-- Sidebar -->
        <aside class="dashboard-side">
            <div class="sidebar-header">
                <div class="avatar">
                    <?php echo strtoupper(substr($username, 0, 1)); ?>
                </div>
                <h3 class="admin-text"><?php echo $username; ?></h3>
                <p class="role-text"><?php echo ucfirst($role); ?></p>
            </div>

            <nav class="dashboard-menu">
                <a href="dashboard.php" class="active">
                    <span class="material-icons">dashboard</span>
                    Dashboard
                </a>
                <a href="books.php">
                    <span class="material-icons">library_books</span>
                    Data Buku
                </a>
                <a href="borrow.php">
                    <span class="material-icons">book</span>
                    Peminjaman
                </a>
                <a href="return.php">
                    <span class="material-icons">assignment_return</span>
                    Pengembalian
                </a>
                <?php if ($role === 'admin'): ?>
                <a href="members.php">
                    <span class="material-icons">people</span>
                    Data Anggota
                </a>
                <?php endif; ?>
                <a href="profile.php">
                    <span class="material-icons">person</span>
                    Profil
                </a>
                <a href="logout.php">
                    <span class="material-icons">logout</span>
                    Logout
                </a>
            </nav>
        </aside>

        <!-- Main Content -->
        <main class="dashboard-main">
            <div class="content-header">
                <button class="mobile-nav-toggle">
                    <span class="material-icons">menu</span>
                </button>
                <div class="header-content">
                    <h1 class="dashboard-title">Dashboard</h1>
                    <p class="dashboard-subtitle">Selamat datang kembali, <?php echo $username; ?>!</p>
                </div>
            </div>

            <!-- Statistics Cards -->
            <div class="stats-grid">
                <div class="stat-card">
                    <div class="stat-icon books">
                        <span class="material-icons">library_books</span>
                    </div>
                    <div class="stat-info">
                        <h3>Total Buku</h3>
                        <p class="number"><?php echo $total_books; ?></p>
                        <span class="trend positive">Koleksi Perpustakaan</span>
                    </div>
                </div>

                <div class="stat-card">
                    <div class="stat-icon borrowed">
                        <span class="material-icons">book</span>
                    </div>
                    <div class="stat-info">
                        <h3>Sedang Dipinjam</h3>
                        <p class="number"><?php echo $total_borrow; ?></p>
                        <span class="trend">Buku yang sedang dipinjam</span>
                    </div>
                </div>

                <div class="stat-card">
                    <div class="stat-icon returned">
                        <span class="material-icons">assignment_turned_in</span>
                    </div>
                    <div class="stat-info">
                        <h3>Sudah Dikembalikan</h3>
                        <p class="number"><?php echo $total_return; ?></p>
                        <span class="trend positive">Total pengembalian</span>
                    </div>
                </div>
            </div>

            <!-- Quick Access Menu -->
            <div class="quick-access">
                <h2>Menu Cepat</h2>
                <div class="quick-grid">
                    <a href="books.php" class="quick-card">
                        <span class="material-icons">library_books</span>
                        <div class="quick-info">
                            <h3>Data Buku</h3>
                            <p>Kelola data buku perpustakaan</p>
                        </div>
                        <span class="material-icons arrow">arrow_forward</span>
                    </a>

                    <a href="borrow.php" class="quick-card">
                        <span class="material-icons">book</span>
                        <div class="quick-info">
                            <h3>Peminjaman</h3>
                            <p>Proses peminjaman buku</p>
                        </div>
                        <span class="material-icons arrow">arrow_forward</span>
                    </a>

                    <a href="return.php" class="quick-card">
                        <span class="material-icons">assignment_return</span>
                        <div class="quick-info">
                            <h3>Pengembalian</h3>
                            <p>Proses pengembalian buku</p>
                        </div>
                        <span class="material-icons arrow">arrow_forward</span>
                    </a>

                    <?php if ($role === 'admin'): ?>
                    <a href="members.php" class="quick-card">
                        <span class="material-icons">people</span>
                        <div class="quick-info">
                            <h3>Data Anggota</h3>
                            <p>Kelola data anggota perpustakaan</p>
                        </div>
                        <span class="material-icons arrow">arrow_forward</span>
                    </a>
                    <?php endif; ?>
                </div>
            </div>
        </main>
    </div>

    <script src="script.js"></script>
</body>
</html>
