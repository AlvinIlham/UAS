<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.html");
    exit;
}
$user_id = $_SESSION['user_id'];
$role = $_SESSION['role'];

require 'db.php';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $book_id = $_POST['book_id'] ?? 0;
    $tanggal_pinjam = date('Y-m-d');
    $tanggal_kembali = date('Y-m-d', strtotime('+7 days'));
    $status = 'dipinjam';
    
    // Check book availability
    $stmt = $conn->prepare("SELECT stok FROM books WHERE id = ?");
    $stmt->bind_param("i", $book_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $book = $result->fetch_assoc();
    
    if ($book && $book['stok'] > 0) {
        // Begin transaction
        $conn->begin_transaction();
        
        try {
            // Insert borrow record
            $stmt = $conn->prepare("INSERT INTO borrows (user_id, book_id, tanggal_pinjam, tanggal_kembali, status) VALUES (?, ?, ?, ?, ?)");
            $stmt->bind_param("iisss", $user_id, $book_id, $tanggal_pinjam, $tanggal_kembali, $status);
            $stmt->execute();
            
            // Update book stock
            $stmt = $conn->prepare("UPDATE books SET stok = stok - 1 WHERE id = ?");
            $stmt->bind_param("i", $book_id);
            $stmt->execute();
            
            $conn->commit();
            $success = "Buku berhasil dipinjam. Harap dikembalikan sebelum " . date('d/m/Y', strtotime($tanggal_kembali));
        } catch (Exception $e) {
            $conn->rollback();
            $error = "Gagal meminjam buku: " . $e->getMessage();
        }
    } else {
        $error = "Buku tidak tersedia untuk dipinjam";
    }
}

// Get available books
$books_query = "SELECT * FROM books WHERE stok > 0 ORDER BY judul ASC";
$books_result = $conn->query($books_query);

// Get user's current borrows
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
    <title>Peminjaman Buku | Perpustakaan Mini</title>
    <link rel="stylesheet" href="style.css">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
</head>
<body class="dashboard-bg-alt">    <div class="dashboard-grid">
        <aside class="dashboard-side">
            <div class="avatar">
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
                <a href="borrow.php" class="active">
                    <span>📥</span> Peminjaman
                </a>
                <a href="return.php">
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
            <h1 class="dashboard-title">Peminjaman Buku</h1>
            
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
              <div class="content-grid">
                <div class="card-glass">
                    <h2 class="section-title">
                        <span class="material-icons">book</span>
                        Pinjam Buku
                    </h2>
                    <form method="POST" class="edit-form">
                        <div class="input-group">
                            <label for="book_id">Pilih Buku</label>
                            <div class="input-field">
                                <span class="material-icons">library_books</span>
                                <select id="book_id" name="book_id" required>
                                    <option value="">-- Pilih Buku --</option>
                                    <?php if ($books_result->num_rows > 0):
                                        while ($book = $books_result->fetch_assoc()): ?>
                                            <option value="<?php echo $book['id']; ?>">
                                                <?php echo htmlspecialchars($book['judul']); ?>
                                                (Stok: <?php echo $book['stok']; ?>)
                                            </option>
                                        <?php endwhile;
                                    endif; ?>
                                </select>
                            </div>
                        </div>
                        <button type="submit" class="btn-primary">
                            <span class="material-icons">add_circle</span>
                            Pinjam Buku
                        </button>
                    </form>
                </div>
                  <div class="card-glass">
                    <h2 class="section-title">
                        <span class="material-icons">assignment</span>
                        Buku yang Sedang Dipinjam
                    </h2>
                    <div class="table-responsive">
                        <table class="books-table">
                            <thead>
                                <tr>
                                    <th>
                                        <span class="material-icons">book</span>
                                        Judul Buku
                                    </th>
                                    <th>
                                        <span class="material-icons">event</span>
                                        Tanggal Pinjam
                                    </th>
                                    <th>
                                        <span class="material-icons">event_available</span>
                                        Batas Kembali
                                    </th>
                                    <th>
                                        <span class="material-icons">info</span>
                                        Status
                                    </th>
                                </tr>
                            </thead>
                            <tbody>
                            <?php if ($borrows_result->num_rows > 0): 
                                while ($borrow = $borrows_result->fetch_assoc()): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($borrow['judul']); ?></td>
                                    <td><?php echo date('d/m/Y', strtotime($borrow['tanggal_pinjam'])); ?></td>
                                    <td><?php echo date('d/m/Y', strtotime($borrow['tanggal_kembali'])); ?></td>
                                    <td><span class="status-badge"><?php echo htmlspecialchars($borrow['status']); ?></span></td>
                                </tr>
                                <?php endwhile;
                            else: ?>
                                <tr>
                                    <td colspan="4" class="text-center">Belum ada buku yang dipinjam</td>
                                </tr>
                            <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
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