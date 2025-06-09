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
</head>
<body class="dashboard-bg-alt">
    <div class="dashboard-grid">
        <aside class="dashboard-side">
            <a href="dashboard.php" class="dashboard-logout-alt" style="margin-bottom: 24px">← Dashboard</a>
            <a href="logout.php" class="dashboard-logout-alt">Logout</a>
        </aside>
        <main class="dashboard-main">
            <h1 class="dashboard-title-alt">Peminjaman Buku</h1>
            
            <?php if (isset($success)): ?>
            <div class="alert alert-success">
                <?php echo $success; ?>
            </div>
            <?php endif; ?>
            
            <?php if (isset($error)): ?>
            <div class="alert alert-error">
                <?php echo $error; ?>
            </div>
            <?php endif; ?>
            
            <div class="borrow-container">            <div class="borrow-form">
                    <h2>Pinjam Buku</h2>
                    <form method="POST" class="edit-form">
                        <div class="input-group">
                            <label for="book_id">Pilih Buku</label>
                            <select id="book_id" name="book_id" required class="book-select">
                                <option value="">-- Pilih Buku --</option>
                                <?php if ($books_result && $books_result->num_rows > 0): ?>
                                    <?php while ($book = $books_result->fetch_assoc()): ?>
                                    <option value="<?php echo $book['id']; ?>">
                                        <?php echo htmlspecialchars($book['judul']); ?> 
                                        (Stok: <?php echo $book['stok']; ?>)
                                    </option>
                                    <?php endwhile; ?>
                                <?php else: ?>
                                    <option value="" disabled>Tidak ada buku tersedia</option>
                                <?php endif; ?>
                            </select>
                        </div>
                        <button type="submit" class="btn-primary">Pinjam Buku</button>
                    </form>
                </div>
                
                <div class="borrowed-books">
                    <h2>Buku yang Sedang Dipinjam</h2>
                    <table class="books-table">
                        <thead>
                            <tr>
                                <th>Judul Buku</th>
                                <th>Tanggal Pinjam</th>
                                <th>Batas Kembali</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php if ($borrows_result->num_rows > 0): ?>
                            <?php while ($borrow = $borrows_result->fetch_assoc()): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($borrow['judul']); ?></td>
                                <td><?php echo date('d/m/Y', strtotime($borrow['tanggal_pinjam'])); ?></td>
                                <td><?php echo date('d/m/Y', strtotime($borrow['tanggal_kembali'])); ?></td>
                                <td><?php echo ucfirst($borrow['status']); ?></td>
                            </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="4" style="text-align: center; color: #b2b8c6">
                                    Belum ada buku yang dipinjam.
                                </td>
                            </tr>
                        <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </main>
    </div>
</body>
</html>
<?php
$conn->close();
?>