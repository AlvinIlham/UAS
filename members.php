<?php
session_start();
$role = isset($_SESSION['role']) ? $_SESSION['role'] : 'user';
if (!isset($_SESSION['user_id']) || $role !== 'admin') {
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
<body class="dashboard-bg-alt">    <div class="dashboard-grid">
        <!-- Navbar -->
        <nav class="dashboard-navbar">
            <div class="navbar-brand">
                <div class="avatar">
                    <?php echo strtoupper(substr($_SESSION['username'], 0, 1)); ?>
                </div>
                <div class="navbar-user">
                    <div class="user-info">                        <h3 class="admin-text"><?php echo htmlspecialchars($_SESSION['username']); ?></h3>
                        <p class="role-text"><?php echo ucfirst($role); ?></p>
                    </div>
                </div>
            </div>

            <div class="dashboard-menu">
                <a href="dashboard.php">
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
                <a href="members.php" class="active">
                    <span class="material-icons">people</span>
                    Data Anggota
                </a>
                <a href="profile.php">
                    <span class="material-icons">person</span>
                    Profil
                </a>
                <a href="logout.php">
                    <span class="material-icons">logout</span>
                    Logout
                </a>
            </div>

            <button class="mobile-menu-toggle">
                <span class="material-icons">menu</span>
            </button>
        </nav>

        <main class="dashboard-main">
            <h1 class="dashboard-title">Data Anggota</h1>

            <?php if (isset($_GET['status']) && isset($_GET['message'])): ?>
            <div class="alert alert-<?php echo $_GET['status'] === 'success' ? 'success' : 'error'; ?>">
                <i class="material-icons"><?php echo $_GET['status'] === 'success' ? 'check_circle' : 'error'; ?></i>
                <span><?php echo htmlspecialchars($_GET['message']); ?></span>
            </div>
            <?php endif; ?>            <div class="table-actions">
                <button onclick="showAddMemberForm()" class="btn-primary btn-add">
                    <i class="material-icons">person_add</i>
                    <span>Tambah Anggota</span>
                </button>
            </div>

            <!-- Modal Add Member -->
            <div id="addMemberModal" class="modal">
                <div class="modal-content card-glass">
                    <div class="auth-header">
                        <i class="material-icons auth-icon">group_add</i>
                        <h2>Tambah Anggota Baru</h2>
                        <p class="auth-subtitle">Silahkan isi data anggota dengan lengkap</p>
                    </div>
                    <form action="member_add.php" method="POST" class="auth-form">
                        <div class="input-group">
                            <label for="username">Username</label>
                            <div class="input-field">
                                <i class="material-icons">person</i>
                                <input type="text" id="username" name="username" required maxlength="50" placeholder="Masukkan username">
                            </div>
                        </div>                        <div class="input-group">
                            <label for="password">Password</label>
                            <div class="input-field">
                                <i class="material-icons">lock</i>
                                <input type="password" id="password" name="password" required minlength="6" placeholder="Masukkan password">
                                <button type="button" class="toggle-password" aria-label="Toggle password visibility">
                                    <i class="material-icons">visibility_off</i>
                                </button>
                            </div>
                        </div>
                        <div class="form-actions">
                            <button type="button" onclick="hideAddMemberForm()" class="btn-secondary">
                                <i class="material-icons">close</i>
                                <span>Batal</span>
                            </button>
                            <button type="submit" class="btn-primary">
                                <i class="material-icons">save</i>
                                <span>Simpan</span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <div class="card-glass">

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
                        ?>                            <tr>
                                <td><?php echo $row['id']; ?></td>
                                <td><?php echo htmlspecialchars($row['username']); ?></td>
                                <td><?php echo ucfirst(htmlspecialchars($row['role'])); ?></td>
                                <td><?php echo $row['total_borrows']; ?></td>
                                <td>
                                    <button onclick="confirmDelete(<?php echo $row['id']; ?>, '<?php echo htmlspecialchars($row['username']); ?>')" 
                                            class="btn-icon btn-danger" title="Hapus Anggota">
                                        <i class="material-icons">delete</i>
                                    </button>
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
                </div>            </div>
        </main>
    </div>

    <!-- Delete Confirmation Modal -->
    <div id="deleteModal" class="modal">
        <div class="modal-content card-glass">
            <div class="auth-header">
                <i class="material-icons auth-icon text-danger">warning</i>
                <h2>Konfirmasi Hapus</h2>
                <p class="auth-subtitle">Apakah Anda yakin ingin menghapus anggota <span id="memberName"></span>?</p>
            </div>
            <div class="form-actions">
                <button onclick="hideDeleteModal()" class="btn-secondary">
                    <i class="material-icons">close</i>
                    <span>Batal</span>
                </button>
                <a href="#" id="confirmDelete" class="btn-danger">
                    <i class="material-icons">delete</i>
                    <span>Hapus</span>
                </a>
            </div>
        </div>
    </div>

    <script>
    function showAddMemberForm() {
        const modal = document.getElementById('addMemberModal');
        modal.style.display = 'flex';
        document.body.style.overflow = 'hidden';
    }

    function hideAddMemberForm() {
        const modal = document.getElementById('addMemberModal');
        modal.style.display = 'none';
        document.body.style.overflow = 'auto';
    }

    function confirmDelete(id, username) {
        const modal = document.getElementById('deleteModal');
        const memberNameSpan = document.getElementById('memberName');
        const confirmDeleteLink = document.getElementById('confirmDelete');
        
        memberNameSpan.textContent = username;
        confirmDeleteLink.href = `members.php?action=delete&id=${id}`;
        modal.style.display = 'flex';
        document.body.style.overflow = 'hidden';
    }

    function hideDeleteModal() {
        const modal = document.getElementById('deleteModal');
        modal.style.display = 'none';
        document.body.style.overflow = 'auto';
    }

    // Close modals when clicking outside
    window.onclick = function(event) {
        const addModal = document.getElementById('addMemberModal');
        const deleteModal = document.getElementById('deleteModal');
        
        if (event.target === addModal) {
            hideAddMemberForm();
        }
        if (event.target === deleteModal) {
            hideDeleteModal();
        }
    }

    // Close modals with Escape key
    document.addEventListener('keydown', function(event) {
        if (event.key === 'Escape') {
            hideAddMemberForm();
            hideDeleteModal();
        }
    });
    </script>

    <script src="script.js"></script>
</body>
</html>
