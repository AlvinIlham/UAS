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

// Profile update logic
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_profile'])) {
    $username = trim($_POST['username']);
    $current_password = $_POST['current_password'];
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];

    // Validate current password
    $stmt = $conn->prepare("SELECT password FROM users WHERE id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $user_data = $result->fetch_assoc();

    if (!password_verify($current_password, $user_data['password'])) {
        header("Location: profile.php?error=Password saat ini tidak sesuai");
        exit;
    }

    // Check if username is taken
    if ($username !== $user['username']) {
        $stmt = $conn->prepare("SELECT id FROM users WHERE username = ? AND id != ?");
        $stmt->bind_param("si", $username, $user_id);
        $stmt->execute();
        if ($stmt->get_result()->num_rows > 0) {
            header("Location: profile.php?error=Username sudah digunakan");
            exit;
        }
    }

    // Update user data
    if (!empty($new_password)) {
        if ($new_password !== $confirm_password) {
            header("Location: profile.php?error=Password baru tidak cocok dengan konfirmasi");
            exit;
        }
        $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
        $stmt = $conn->prepare("UPDATE users SET username = ?, password = ? WHERE id = ?");
        $stmt->bind_param("ssi", $username, $hashed_password, $user_id);
    } else {
        $stmt = $conn->prepare("UPDATE users SET username = ? WHERE id = ?");
        $stmt->bind_param("si", $username, $user_id);
    }

    if ($stmt->execute()) {
        $_SESSION['username'] = $username;
        header("Location: profile.php?success=Profil berhasil diperbarui");
        exit;
    } else {
        header("Location: profile.php?error=Gagal memperbarui profil");
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profil | Perpustakaan Mini</title>
    <link rel="stylesheet" href="style.css">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
</head>
<body class="dashboard-bg-alt">
    <div class="dashboard-grid">
        <aside class="dashboard-side">
            <div class="avatar">
                <span><?php echo strtoupper(substr($user['username'], 0, 1)); ?></span>
            </div>
            <p class="admin-text"><?php echo htmlspecialchars($user['username']); ?></p>
            <p class="role-text"><?php echo ucfirst($user['role']); ?></p>
            
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
                <a href="return.php">
                    <span>📤</span> Pengembalian
                </a>
                <?php if ($_SESSION['role'] === 'admin'): ?>
                <a href="members.php">
                    <span>👥</span> Data Anggota
                </a>
                <?php endif; ?>
                <a href="profile.php" class="active">
                    <span>👤</span> Profil
                </a>
                <a href="logout.php">
                    <span>🚪</span> Logout
                </a>
            </nav>
        </aside>

        <main class="dashboard-main">
            <h1 class="dashboard-title">Profil Saya</h1>

            <?php if (isset($_GET['success'])): ?>
            <div class="alert alert-success">
                <span>✅</span>
                <?php echo htmlspecialchars($_GET['success']); ?>
            </div>
            <?php endif; ?>

            <?php if (isset($_GET['error'])): ?>
            <div class="alert alert-error">
                <span>❌</span>
                <?php echo htmlspecialchars($_GET['error']); ?>
            </div>
            <?php endif; ?>

            <div class="content-grid">
                <div class="card-glass">
                    <div class="profile-header">
                        <div class="profile-avatar">
                            <span><?php echo strtoupper(substr($user['username'], 0, 1)); ?></span>
                        </div>                        <div class="profile-info">
                            <h2><?php echo htmlspecialchars($user['username']); ?></h2>
                            <p class="role-badge"><?php echo ucfirst($user['role']); ?></p>
                        </div>
                        <button type="button" class="btn-primary" onclick="openProfileModal()">
                            <span class="material-icons">edit</span>
                            <span>Edit Profil</span>
                        </button>
                    </div>
                </div>

                <div class="card-glass">
                    <h2 class="section-title">Riwayat Peminjaman Terakhir</h2>
                    <div class="table-responsive">
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
                                    <td>
                                        <span class="status-badge <?php echo strtolower($borrow['status']); ?>">
                                            <?php echo ucfirst($borrow['status']); ?>
                                        </span>
                                    </td>
                                </tr>
                                <?php endwhile; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="4" class="text-center">
                                        Belum ada riwayat peminjaman.
                                    </td>
                                </tr>
                            <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <!-- Edit Profile Modal -->
    <div id="profileModal" class="modal">
        <div class="modal-content">
            <div class="auth-header">
                <i class="material-icons auth-icon">account_circle</i>
                <h2>Edit Profil</h2>
                <p class="auth-subtitle">Perbarui informasi profil Anda</p>
            </div>
            <form action="profile.php" method="POST" class="auth-form">
                <div class="input-group">
                    <label for="username">Username</label>
                    <div class="input-field">
                        <i class="material-icons">person</i>
                        <input type="text" id="username" name="username" value="<?php echo htmlspecialchars($user['username']); ?>" required maxlength="50">
                    </div>
                </div>
                
                <div class="input-group">
                    <label for="current_password">Password Saat Ini</label>
                    <div class="input-field">
                        <i class="material-icons">lock</i>
                        <input type="password" id="current_password" name="current_password" required>
                        <i class="material-icons toggle-password" onclick="togglePassword('current_password')">visibility_off</i>
                    </div>
                </div>

                <div class="input-group">
                    <label for="new_password">Password Baru</label>
                    <div class="input-field">
                        <i class="material-icons">vpn_key</i>
                        <input type="password" id="new_password" name="new_password" minlength="6" 
                               placeholder="Kosongkan jika tidak ingin mengubah">
                        <i class="material-icons toggle-password" onclick="togglePassword('new_password')">visibility_off</i>
                    </div>
                </div>

                <div class="input-group">
                    <label for="confirm_password">Konfirmasi Password Baru</label>
                    <div class="input-field">
                        <i class="material-icons">vpn_key</i>
                        <input type="password" id="confirm_password" name="confirm_password" minlength="6"
                               placeholder="Kosongkan jika tidak ingin mengubah">
                        <i class="material-icons toggle-password" onclick="togglePassword('confirm_password')">visibility_off</i>
                    </div>
                </div>

                <div class="form-actions">
                    <button type="button" class="btn-secondary" onclick="closeProfileModal()">
                        <i class="material-icons">close</i>
                        <span>Batal</span>
                    </button>
                    <button type="submit" name="update_profile" class="btn-primary">
                        <i class="material-icons">save</i>
                        <span>Simpan Perubahan</span>
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script src="script.js"></script>
</body>
</html>
<?php
$conn->close();
?>
