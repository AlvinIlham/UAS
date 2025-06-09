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
</head>
<body class="dashboard-bg-alt">
    <button class="mobile-nav-toggle">
      <span>☰</span>
    </button>
    <div class="dashboard-grid">
        <aside class="dashboard-side">
            <nav class="dashboard-menu-alt">
                <a href="dashboard.php"><span>🏠</span>Dashboard</a>
                <a href="profile.php"><span>👤</span>Profil Saya</a>
                <a href="books.php"><span>📚</span>Data Buku</a>
                <a href="borrow.php"><span>📥</span>Peminjaman</a>
                <a href="return.php"><span>📤</span>Pengembalian</a>
                <a href="members.php"><span>👥</span>Data Anggota</a>
                <a href="logout.php" class="dashboard-logout-alt">Logout</a>
            </nav>
        </aside>
        <main class="dashboard-main">
            <h1 class="dashboard-title-alt">Data Anggota</h1>

            <?php if (isset($_GET['status']) && isset($_GET['message'])): ?>
            <div class="alert alert-<?php echo $_GET['status'] === 'success' ? 'success' : 'error'; ?>">
                <?php echo htmlspecialchars($_GET['message']); ?>
            </div>
            <?php endif; ?>

            <div class="table-container">
                <div class="table-actions">
                    <button onclick="showAddMemberForm()" class="btn-primary">+ Tambah Anggota</button>
                </div>

                <!-- Add Member Form -->
                <div id="addMemberForm" style="display: none; margin-bottom: 20px;">
                    <form action="member_add.php" method="POST" class="form-container">
                        <div class="form-group">
                            <label for="username">Username</label>
                            <input type="text" id="username" name="username" required>
                        </div>
                        <div class="form-group">
                            <label for="password">Password</label>
                            <input type="password" id="password" name="password" required>
                        </div>
                        <div class="form-group">
                            <button type="submit" class="btn-primary">Simpan</button>
                            <button type="button" onclick="hideAddMemberForm()" class="btn-secondary">Batal</button>
                        </div>
                    </form>
                </div>

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
                             (SELECT COUNT(*) FROM borrows WHERE user_id = u.id) as total_pinjam 
                             FROM users u 
                             WHERE u.role != 'admin' 
                             ORDER BY u.id DESC";
                    $result = $conn->query($query);
                    
                    if ($result && $result->num_rows > 0):
                        $no = 1;
                        while ($row = $result->fetch_assoc()):
                    ?>
                        <tr>
                            <td><?php echo $no++; ?></td>
                            <td><?php echo htmlspecialchars($row['username']); ?></td>
                            <td><?php echo ucfirst($row['role']); ?></td>
                            
                            <td><?php echo isset($row['total_pinjam']) ? $row['total_pinjam'] : '0'; ?></td>
                            <td>
                                <a href="members.php?action=delete&id=<?php echo $row['id']; ?>" 
                                   class="btn-delete"
                                   onclick="return confirm('Yakin hapus anggota ini?')">Hapus</a>
                            </td>
                        </tr>
                    <?php 
                        endwhile;
                    else:
                    ?>
                        <tr>
                            <td colspan="6" style="text-align: center; color: #b2b8c6">
                                Belum ada data anggota.
                            </td>
                        </tr>
                    <?php 
                    endif;
                    $conn->close();
                    ?>
                    </tbody>
                </table>
            </div>
        </main>
    </div>

    <script>
        function showAddMemberForm() {
            document.getElementById('addMemberForm').style.display = 'block';
        }
        
        function hideAddMemberForm() {
            document.getElementById('addMemberForm').style.display = 'none';
        }
    </script>
</body>
</html>
