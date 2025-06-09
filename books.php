<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}
$role = $_SESSION['role'];

// Handle delete action
if ($role === 'admin' && isset($_GET['action']) && $_GET['action'] === 'delete' && isset($_GET['id'])) {
    require 'db.php';
    $id = $_GET['id'];
    
    $stmt = $conn->prepare("DELETE FROM books WHERE id = ?");
    $stmt->bind_param("i", $id);
    
    if ($stmt->execute()) {
        $message = "Buku berhasil dihapus";
        $status = "success";
    } else {
        $message = "Gagal menghapus buku";
        $status = "error";
    }
    
    $stmt->close();
    $conn->close();
    
    header("Location: books.php?status=$status&message=$message");
    exit;
}

// Get total books count for pagination
require 'db.php';
$count_result = $conn->query("SELECT COUNT(*) as total FROM books");
$total_books = $count_result->fetch_assoc()['total'];

// Pagination settings
$books_per_page = 10;
$total_pages = ceil($total_books / $books_per_page);
$current_page = isset($_GET['page']) ? max(1, min($total_pages, intval($_GET['page']))) : 1;
$offset = ($current_page - 1) * $books_per_page;

?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Data Buku | Perpustakaan Mini</title>
    <link rel="stylesheet" href="style.css">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
</head>
<body class="dashboard-bg">
    <div class="dashboard-grid">
        <!-- Sidebar -->
        <aside class="dashboard-side">
            <div class="sidebar-header">
                <div class="avatar">
                    <?php echo strtoupper(substr($_SESSION['username'], 0, 1)); ?>
                </div>
                <h3 class="admin-text"><?php echo $_SESSION['username']; ?></h3>
                <p class="role-text"><?php echo ucfirst($role); ?></p>
            </div>

            <nav class="dashboard-menu">
                <ul>
                    <li>
                        <a href="dashboard.php">
                            <span class="material-icons">dashboard</span>
                            Dashboard
                        </a>
                    </li>
                    <li class="active">
                        <a href="books.php">
                            <span class="material-icons">library_books</span>
                            Data Buku
                        </a>
                    </li>
                    <li>
                        <a href="borrow.php">
                            <span class="material-icons">book</span>
                            Peminjaman
                        </a>
                    </li>
                    <li>
                        <a href="return.php">
                            <span class="material-icons">assignment_return</span>
                            Pengembalian
                        </a>
                    </li>
                    <?php if ($role === 'admin'): ?>
                    <li>
                        <a href="members.php">
                            <span class="material-icons">people</span>
                            Data Anggota
                        </a>
                    </li>
                    <?php endif; ?>
                    <li>
                        <a href="profile.php">
                            <span class="material-icons">person</span>
                            Profil
                        </a>
                    </li>
                    <li>
                        <a href="logout.php">
                            <span class="material-icons">logout</span>
                            Logout
                        </a>
                    </li>
                </ul>
            </nav>
        </aside>

        <!-- Main Content -->
        <main class="dashboard-main">
            <div class="content-header">
                <button class="mobile-nav-toggle">
                    <span class="material-icons">menu</span>
                </button>
                <h1 class="dashboard-title">Data Buku</h1>
            </div>

            <?php if (isset($_GET['status']) && isset($_GET['message'])): ?>
                <div class="alert alert-<?php echo $_GET['status'] === 'success' ? 'success' : 'error'; ?>">
                    <span class="material-icons"><?php echo $_GET['status'] === 'success' ? 'check_circle' : 'error'; ?></span>
                    <?php echo htmlspecialchars($_GET['message']); ?>
                </div>
            <?php endif; ?>

            <div class="card-glass">
                <?php if ($role === 'admin'): ?>
                    <div class="action-bar">
                        <a href="books_add.php" class="btn-add">
                            <span class="material-icons">add</span>
                            Tambah Buku
                        </a>
                    </div>
                <?php endif; ?>

                <div class="table-container">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Judul</th>
                                <th>Penulis</th>
                                <th>Tahun</th>
                                <th>Kategori</th>
                                <th>Stok</th>
                                <?php if ($role === 'admin'): ?>
                                    <th>Aksi</th>
                                <?php endif; ?>
                            </tr>
                        </thead>
                        <tbody>
                        <?php
                        $query = "SELECT * FROM books ORDER BY id ASC LIMIT ? OFFSET ?";
                        $stmt = $conn->prepare($query);
                        $stmt->bind_param("ii", $books_per_page, $offset);
                        $stmt->execute();
                        $result = $stmt->get_result();
                        
                        if ($result && $result->num_rows > 0):
                            $no = $offset + 1;
                            while ($row = $result->fetch_assoc()):
                        ?>
                            <tr>
                                <td><?php echo $no++; ?></td>
                                <td><?php echo htmlspecialchars($row['judul']); ?></td>
                                <td><?php echo htmlspecialchars($row['penulis']); ?></td>
                                <td><?php echo htmlspecialchars($row['tahun']); ?></td>
                                <td><?php echo htmlspecialchars($row['kategori']); ?></td>
                                <td><?php echo htmlspecialchars($row['stok']); ?></td>
                                <?php if ($role === 'admin'): ?>
                                <td class="actions">
                                    <a href="books_edit.php?id=<?php echo $row['id']; ?>" class="btn-action btn-edit" title="Edit">
                                        <span class="material-icons">edit</span>
                                    </a>
                                    <button onclick="confirmDelete(<?php echo $row['id']; ?>, '<?php echo htmlspecialchars($row['judul']); ?>')" 
                                            class="btn-action btn-delete" title="Hapus">
                                        <span class="material-icons">delete</span>
                                    </button>
                                </td>
                                <?php endif; ?>
                            </tr>
                        <?php 
                            endwhile;
                        else:
                        ?>
                            <tr>
                                <td colspan="<?php echo ($role === 'admin' ? 7 : 6); ?>" class="text-center">
                                    Belum ada data buku.
                                </td>
                            </tr>
                        <?php 
                        endif;
                        $stmt->close();
                        $conn->close();
                        ?>
                        </tbody>
                    </table>
                </div>

                <?php if ($total_pages > 1): ?>
                <div class="pagination">
                    <?php if ($current_page > 1): ?>
                        <a href="?page=<?php echo ($current_page - 1); ?>" class="btn-page">
                            <span class="material-icons">chevron_left</span>
                        </a>
                    <?php endif; ?>
                    
                    <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                        <a href="?page=<?php echo $i; ?>" 
                           class="btn-page <?php echo $i === $current_page ? 'active' : ''; ?>">
                            <?php echo $i; ?>
                        </a>
                    <?php endfor; ?>
                    
                    <?php if ($current_page < $total_pages): ?>
                        <a href="?page=<?php echo ($current_page + 1); ?>" class="btn-page">
                            <span class="material-icons">chevron_right</span>
                        </a>
                    <?php endif; ?>
                </div>
                <?php endif; ?>
            </div>
        </main>
    </div>

    <!-- Delete Confirmation Dialog -->
    <div id="deleteDialog" class="dialog-overlay" style="display: none;">
        <div class="dialog">
            <h3 class="dialog-title">Konfirmasi Hapus</h3>
            <p class="dialog-content">Apakah Anda yakin ingin menghapus buku "<span id="bookTitle"></span>"?</p>
            <div class="dialog-actions">
                <button onclick="hideDeleteDialog()" class="btn-secondary">Batal</button>
                <a href="#" id="confirmDelete" class="btn-primary btn-delete">Hapus</a>
            </div>
        </div>
    </div>

    <script>
    function confirmDelete(id, title) {
        document.getElementById('deleteDialog').style.display = 'flex';
        document.getElementById('bookTitle').textContent = title;
        document.getElementById('confirmDelete').href = `books.php?action=delete&id=${id}`;
    }

    function hideDeleteDialog() {
        document.getElementById('deleteDialog').style.display = 'none';
    }
    </script>

    <script src="script.js"></script>
</body>
</html>