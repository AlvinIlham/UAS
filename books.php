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
<body>
    <div class="wrapper">
        <!-- Sidebar -->
        <nav class="sidebar">
            <div class="sidebar-header">
                <div class="avatar">
                    <?php echo strtoupper(substr($_SESSION['username'],0,1)); ?>
                </div>
                <div class="user-info">
                    <h3><?php echo $_SESSION['username']; ?></h3>
                    <span><?php echo ucfirst($role); ?></span>
                </div>
            </div>

            <ul class="menu">
                <li>
                    <a href="dashboard.php">
                        <i class="material-icons">dashboard</i>
                        <span>Dashboard</span>
                    </a>
                </li>
                <li class="active">
                    <a href="books.php">
                        <i class="material-icons">library_books</i>
                        <span>Data Buku</span>
                    </a>
                </li>
                <li>
                    <a href="borrow.php">
                        <i class="material-icons">book</i>
                        <span>Peminjaman</span>
                    </a>
                </li>
                <li>
                    <a href="return.php">
                        <i class="material-icons">assignment_return</i>
                        <span>Pengembalian</span>
                    </a>
                </li>
                <?php if ($role === 'admin'): ?>
                <li>
                    <a href="members.php">
                        <i class="material-icons">people</i>
                        <span>Data Anggota</span>
                    </a>
                </li>
                <?php endif; ?>
                <li>
                    <a href="profile.php">
                        <i class="material-icons">person</i>
                        <span>Profil</span>
                    </a>
                </li>
                <li>
                    <a href="logout.php">
                        <i class="material-icons">logout</i>
                        <span>Logout</span>
                    </a>
                </li>
            </ul>
        </nav>

        <!-- Main Content -->
        <div class="main-content">
            <header>
                <button class="menu-toggle">
                    <i class="material-icons">menu</i>
                </button>
                <h1>Data Buku</h1>
            </header>

            <div class="content">
                <?php if ($role === 'admin'): ?>
                <div class="action-bar">
                    <a href="books_add.php" class="btn-add">
                        <i class="material-icons">add</i>
                        Tambah Buku
                    </a>
                </div>
                <?php endif; ?>

                <?php if (isset($_GET['status']) && isset($_GET['message'])): ?>
                <div class="alert alert-<?php echo $_GET['status'] === 'success' ? 'success' : 'error'; ?>">
                    <?php echo htmlspecialchars($_GET['message']); ?>
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
                        require 'db.php';
                        $no = 1;
                        $query = "SELECT * FROM books ORDER BY id ASC";
                        $result = $conn->query($query);
                        
                        if ($result && $result->num_rows > 0):
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
                                        <i class="material-icons">edit</i>
                                    </a>
                                    <a href="books.php?action=delete&id=<?php echo $row['id']; ?>" 
                                       class="btn-action btn-delete" 
                                       onclick="return confirm('Yakin hapus buku ini?')"
                                       title="Hapus">
                                        <i class="material-icons">delete</i>
                                    </a>
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
                        $conn->close();
                        ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <script src="script.js"></script>
</body>
</html>