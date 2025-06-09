<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

require 'db.php';

$user_id = $_SESSION['user_id'];
$stmt = $conn->prepare("SELECT * FROM users WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

// Get borrowing history
$query = "SELECT b.*, bk.judul 
         FROM borrows b 
         JOIN books bk ON b.book_id = bk.id 
         WHERE b.user_id = ? 
         ORDER BY b.tanggal_pinjam DESC 
         LIMIT 5";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$borrows = $stmt->get_result();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profil | Perpustakaan Mini</title>
    <link rel="stylesheet" href="style.css">
</head>
<body class="dashboard-bg-alt">
    <div class="dashboard-grid">
        <aside class="dashboard-side">
            <a href="dashboard.php" class="dashboard-logout-alt" style="margin-bottom: 24px">← Dashboard</a>
            <a href="logout.php" class="dashboard-logout-alt">Logout</a>
        </aside>
        <main class="dashboard-main">
            <h1 class="dashboard-title-alt">Profil Saya</h1>

            <?php if (isset($_GET['success'])): ?>
            <div class="alert alert-success">
                <?php echo htmlspecialchars($_GET['success']); ?>
            </div>
            <?php endif; ?>

            <?php if (isset($_GET['error'])): ?>
            <div class="alert alert-error">
                <?php echo htmlspecialchars($_GET['error']); ?>
            </div>
            <?php endif; ?>

            <div class="profile-container">
                <div class="profile-info">                    
                    <div class="profile-details">
                        <h2><?php echo htmlspecialchars($user['username']); ?></h2>
                        <p>Role: <?php echo ucfirst($user['role']); ?></p>
                        
                        <button class="btn-primary" onclick="openProfileModal()">Edit Profil</button>
                    </div>
                </div>

                <div class="borrow-history">
                    <h3>Riwayat Peminjaman Terakhir</h3>
                    <table class="books-table">
                        <thead>
                            <tr>
                                <th>Judul Buku</th>
                                <th>Tanggal Pinjam</th>
                                <th>Tanggal Kembali</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php if ($borrows->num_rows > 0): ?>
                            <?php while ($borrow = $borrows->fetch_assoc()): ?>
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
                                    Belum ada riwayat peminjaman.
                                </td>
                            </tr>
                        <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </main>
    </div>

    <!-- Modal Edit Profile -->
    <div id="profileModal" class="modal-profile" style="display: none">
        <div class="modal-content-profile">
            <span class="close-profile" onclick="closeProfileModal()">&times;</span>
            <h2>Edit Profile</h2>
            <form action="profile_update.php" method="POST" class="edit-form">
                <div class="input-group">
                    <label for="editName">Username</label>
                    <input type="text" id="editName" name="editName" 
                           value="<?php echo htmlspecialchars($user['username']); ?>" required>
                </div>
                <div class="input-group">
                    <label for="newPassword">Password Baru (opsional)</label>
                    <input type="password" id="newPassword" name="newPassword" minlength="6">
                </div>                <button type="submit" class="btn-primary">Simpan Perubahan</button>
            </form>
        </div>
    </div>

    <script src="script.js"></script>
</body>
</html>
<?php
$conn->close();
?>
