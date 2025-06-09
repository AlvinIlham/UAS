<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit;
}

require 'db.php';

// Handle delete action
if (isset($_GET['action']) && $_GET['action'] === 'delete' && isset($_GET['id'])) {
    $id = $_GET['id'];
    
    $stmt = $conn->prepare("DELETE FROM users WHERE id = ? AND role != 'admin'");
    $stmt->bind_param("i", $id);
    
    if ($stmt->execute()) {
        $message = "Anggota berhasil dihapus";
        $status = "success";
    } else {
        $message = "Gagal menghapus anggota";
        $status = "error";
    }
    
    header("Location: members.php?status=$status&message=$message");
    exit;
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Data Anggota | Perpustakaan Mini</title>
    <link rel="stylesheet" href="style.css">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
</head>
<body class="dashboard-bg-alt">
    <div class="dashboard-grid">
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
                <a href="borrow.php">
                    <span>📥</span> Peminjaman
                </a>
                <a href="return.php">
                    <span>📤</span> Pengembalian
                </a>
                <a href="members.php" class="active">
                    <span>👥</span> Data Anggota
                </a>
                <a href="profile.php">
                    <span>👤</span> Profil
                </a>
                <a href="logout.php">
                    <span>🚪</span> Logout
                </a>
            </nav>
        </aside>

        <main class="dashboard-main">
            <h1 class="dashboard-title">Data Anggota</h1>

            <?php if (isset($_GET['status']) && isset($_GET['message'])): ?>
            <div class="alert alert-<?php echo $_GET['status'] === 'success' ? 'success' : 'error'; ?>">
                <i class="material-icons"><?php echo $_GET['status'] === 'success' ? 'check_circle' : 'error'; ?></i>
                <span><?php echo htmlspecialchars($_GET['message']); ?></span>
            </div>
            <?php endif; ?>

            <div class="table-actions">
                <button onclick="showAddMemberForm()" class="btn-primary">
                    <span>👥</span> Tambah Anggota
                </button>
            </div>

            <div class="card-glass">
                <div id="addMemberForm" class="form-popup" style="display: none;">
                    <form action="member_add.php" method="POST" class="edit-form">
                        <h3>Tambah Anggota Baru</h3>
                        <div class="input-group">
                            <label for="username">Username</label>
                            <div class="input-field">
                                <span>👤</span>
                                <input type="text" id="username" name="username" required maxlength="50">
                            </div>
                        </div>
                        <div class="input-group">
                            <label for="password">Password</label>
                            <div class="input-field">
                                <span>🔒</span>
                                <input type="password" id="password" name="password" required minlength="6">
                                <button type="button" class="toggle-password">👁️</button>
                            </div>
                        </div>
                        <div class="form-actions">
                            <button type="submit" class="btn-primary">
                                <span>💾</span> Simpan
                            </button>
                            <button type="button" onclick="hideAddMemberForm()" class="btn-secondary">
                                <span>❌</span> Batal
                            </button>
                        </div>
                    </form>
                </div>

                <div class="table-responsive">
                    <table class="books-table">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Username</th>
                                <th>Role</th>
                                <th>Total Peminjaman</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php
                        $query = "SELECT u.*, 
                                (SELECT COUNT(*) FROM borrows WHERE user_id = u.id) as total_borrows 
                                FROM users u 
                                WHERE u.role != 'admin' 
                                ORDER BY u.id DESC";
                        $result = $conn->query($query);
                        
                        if ($result && $result->num_rows > 0):
                            while ($row = $result->fetch_assoc()):
                        ?>
                            <tr>
                                <td><?php echo $row['id']; ?></td>
                                <td><?php echo htmlspecialchars($row['username']); ?></td>
                                <td><?php echo ucfirst(htmlspecialchars($row['role'])); ?></td>
                                <td><?php echo $row['total_borrows']; ?></td>
                                <td>
                                    <a href="members.php?action=delete&id=<?php echo $row['id']; ?>" 
                                       onclick="return confirm('Apakah Anda yakin ingin menghapus anggota ini?')"
                                       class="btn-danger">
                                        <span>🗑️</span>
                                        <span>Hapus</span>
                                    </a>
                                </td>
                            </tr>
                        <?php 
                            endwhile;
                        else:
                        ?>
                            <tr>
                                <td colspan="5" class="text-center">Tidak ada data anggota</td>
                            </tr>
                        <?php 
                        endif;
                        $conn->close();
                        ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </main>
    </div>

    <script src="script.js"></script>
</body>
</html>
