<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.html");
    exit;
}
$user_id = $_SESSION['user_id'];
$role = $_SESSION['role'];

require 'db.php';

// Handle return submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $borrow_id = $_POST['borrow_id'] ?? 0;
    
    if ($borrow_id) {
        // Begin transaction
        $conn->begin_transaction();
        
        try {
            // Get book_id from borrow record
            $stmt = $conn->prepare("SELECT book_id FROM borrows WHERE id = ? AND status = 'dipinjam'");
            $stmt->bind_param("i", $borrow_id);
            $stmt->execute();
            $result = $stmt->get_result();
            $borrow = $result->fetch_assoc();
            
            if ($borrow) {
                // Update borrow status
                $stmt = $conn->prepare("UPDATE borrows SET status = 'dikembalikan', tanggal_pengembalian = CURRENT_DATE WHERE id = ?");
                $stmt->bind_param("i", $borrow_id);
                $stmt->execute();
                
                // Update book stock
                $stmt = $conn->prepare("UPDATE books SET stok = stok + 1 WHERE id = ?");
                $stmt->bind_param("i", $borrow['book_id']);
                $stmt->execute();
                
                $conn->commit();
                $success = "Buku berhasil dikembalikan";
            } else {
                throw new Exception("Data peminjaman tidak ditemukan");
            }
        } catch (Exception $e) {
            $conn->rollback();
            $error = "Gagal mengembalikan buku: " . $e->getMessage();
        }
    }
}

// Get user's borrowed books
$borrows_query = "SELECT b.*, bk.judul 
                 FROM borrows b 
                 JOIN books bk ON b.book_id = bk.id 
                 WHERE b.user_id = ? AND b.status = 'dipinjam'
                 ORDER BY b.tanggal_pinjam DESC";
$stmt = $conn->prepare($borrows_query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$borrows_result = $stmt->get_result();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pengembalian Buku | Perpustakaan Mini</title>
    <link rel="stylesheet" href="style.css">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
</head>
<body class="dashboard-bg-alt">
    <div class="dashboard-grid">
        <aside class="dashboard-side">            <div class="avatar">
                <span><?php echo strtoupper(substr($_SESSION['username'], 0, 1)); ?></span>
            </div>
            <p class="admin-text"><?php echo htmlspecialchars($_SESSION['username']); ?></p>
            <p class="role-text"><?php echo ucfirst($_SESSION['role']); ?></p>
            
            <nav class="dashboard-menu">
                <a href="dashboard.php">
                    <span>⬜</span> Dashboard
                </a>
                <a href="books.php">
                    <span>📚</span> Data Buku
                </a>
                <a href="borrow.php">
                    <span>📥</span> Peminjaman
                </a>
                <a href="return.php" class="active">
                    <span>📤</span> Pengembalian
                </a>
                <?php if ($_SESSION['role'] === 'admin'): ?>
                <a href="members.php">
                    <span>👥</span> Data Anggota
                </a>
                <?php endif; ?>
                <a href="profile.php">
                    <span>👤</span> Profil
                </a>
                <a href="logout.php">
                    <span>🚪</span> Logout
                </a>
            </nav>
        </aside>

        <main class="dashboard-main">
            <h1 class="dashboard-title">Pengembalian Buku</h1>
            
            <?php if (isset($success)): ?>
            <div class="alert alert-success">
                <i class="material-icons">check_circle</i>
                <span><?php echo $success; ?></span>
            </div>
            <?php endif; ?>
            
            <?php if (isset($error)): ?>
            <div class="alert alert-error">
                <i class="material-icons">error</i>
                <span><?php echo $error; ?></span>
            </div>
            <?php endif; ?>
            
            <div class="card-glass">
                <h2 class="section-title">Buku yang Sedang Dipinjam</h2>
                <div class="table-responsive">
                    <table class="books-table">
                        <thead>
                            <tr>
                                <th>Judul Buku</th>
                                <th>Tanggal Pinjam</th>
                                <th>Batas Kembali</th>
                                <th>Status</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php if ($borrows_result->num_rows > 0): 
                            while ($borrow = $borrows_result->fetch_assoc()): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($borrow['judul']); ?></td>
                                <td><?php echo date('d/m/Y', strtotime($borrow['tanggal_pinjam'])); ?></td>
                                <td><?php echo date('d/m/Y', strtotime($borrow['tanggal_kembali'])); ?></td>
                                <td>
                                    <span class="status-badge <?php echo strtolower($borrow['status']); ?>">
                                        <?php echo htmlspecialchars($borrow['status']); ?>
                                    </span>
                                </td>
                                <td>
                                    <form method="POST" style="display: inline;">
                                        <input type="hidden" name="borrow_id" value="<?php echo $borrow['id']; ?>">
                                        <button type="submit" class="btn-secondary">
                                            <i class="material-icons">assignment_return</i>
                                            <span>Kembalikan</span>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                            <?php endwhile;
                        else: ?>
                            <tr>
                                <td colspan="5" class="text-center">Tidak ada buku yang sedang dipinjam</td>
                            </tr>
                        <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </main>
    </div>

    <script src="script.js"></script>
</body>
</html>
<?php
$conn->close();
?>