<?php
session_start();
$role = isset($_SESSION['role']) ? $_SESSION['role'] : 'user';
if (!isset($_SESSION['user_id']) || $role !== 'admin') {
    header("Location: login.html");
    exit;
}

require 'db.php';

$id = $_GET['id'] ?? 0;
$error = null;
$success = null;

// Get book data
$stmt = $conn->prepare("SELECT * FROM books WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$book = $result->fetch_assoc();

if (!$book) {
    header("Location: books.php");
    exit;
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $judul = trim($_POST['judul'] ?? '');
    $penulis = trim($_POST['penulis'] ?? '');
    $tahun = trim($_POST['tahun'] ?? '');
    $kategori = trim($_POST['kategori'] ?? '');
    $stok = trim($_POST['stok'] ?? '');
    
    if (empty($judul) || empty($penulis) || empty($tahun) || empty($kategori) || empty($stok)) {
        $error = "Semua field harus diisi!";
    } else {
        $stmt = $conn->prepare("UPDATE books SET judul = ?, penulis = ?, tahun = ?, kategori = ?, stok = ? WHERE id = ?");
        $stmt->bind_param("ssssii", $judul, $penulis, $tahun, $kategori, $stok, $id);
        
        if ($stmt->execute()) {
            header("Location: books.php?status=success&message=Buku berhasil diperbarui");
            exit;
        } else {
            $error = "Gagal memperbarui buku: " . $conn->error;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Buku | Perpustakaan Mini</title>
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
                <a href="books.php" class="active">
                    <span class="material-icons">library_books</span>
                    Data Buku
                </a>
                <a href="borrow.php">
                    <span class="material-icons">book</span>
                    Peminjaman
                </a>
                <a href="return.php">
                    <span class="material-icons">assignment_return</span>
                    Pengembalian                </a>
                <?php if ($role === 'admin'): ?>
                <a href="members.php">
                    <span class="material-icons">people</span>
                    Data Anggota
                </a>
                <?php endif; ?>
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
            <h1 class="dashboard-title">Edit Buku</h1>
            
            <?php if ($error): ?>
            <div class="alert alert-error">
                <span class="material-icons">error</span>
                <?php echo htmlspecialchars($error); ?>
            </div>
            <?php endif; ?>
            
            <div class="card-glass">
                <div class="auth-header">
                    <i class="material-icons auth-icon">edit</i>
                    <h2>Form Edit Buku</h2>
                    <p class="auth-subtitle">Silahkan edit data buku</p>
                </div>

                <form method="POST" class="auth-form">
                    <div class="input-group">
                        <label for="judul">Judul Buku</label>
                        <div class="input-field">
                            <i class="material-icons">book</i>
                            <input type="text" id="judul" name="judul" required maxlength="100"
                                   value="<?php echo htmlspecialchars($book['judul']); ?>"
                                   placeholder="Masukkan judul buku">
                        </div>
                    </div>
                    
                    <div class="input-group">
                        <label for="penulis">Penulis</label>
                        <div class="input-field">
                            <i class="material-icons">create</i>
                            <input type="text" id="penulis" name="penulis" required maxlength="100"
                                   value="<?php echo htmlspecialchars($book['penulis']); ?>"
                                   placeholder="Masukkan nama penulis">
                        </div>
                    </div>
                    
                    <div class="input-group">
                        <label for="tahun">Tahun Terbit</label>
                        <div class="input-field">
                            <i class="material-icons">event</i>
                            <input type="number" id="tahun" name="tahun" required min="1900" 
                                   max="<?php echo date('Y'); ?>"
                                   value="<?php echo htmlspecialchars($book['tahun']); ?>"
                                   placeholder="Masukkan tahun terbit">
                        </div>
                    </div>
                    
                    <div class="input-group">
                        <label for="kategori">Kategori</label>
                        <div class="input-field">
                            <i class="material-icons">category</i>
                            <input type="text" id="kategori" name="kategori" required maxlength="50"
                                   value="<?php echo htmlspecialchars($book['kategori']); ?>"
                                   placeholder="Masukkan kategori buku">
                        </div>
                    </div>
                    
                    <div class="input-group">
                        <label for="stok">Stok</label>
                        <div class="input-field">
                            <i class="material-icons">inventory</i>
                            <input type="number" id="stok" name="stok" required min="0"
                                   value="<?php echo htmlspecialchars($book['stok']); ?>"
                                   placeholder="Masukkan jumlah stok">
                        </div>
                    </div>
                    
                    <div class="form-actions">
                        <a href="books.php" class="btn-secondary">
                            <i class="material-icons">arrow_back</i>
                            <span>Kembali</span>
                        </a>
                        <button type="submit" class="btn-primary">
                            <i class="material-icons">save</i>
                            <span>Simpan Perubahan</span>
                        </button>
                    </div>
                </form>
            </div>
        </main>
    </div>
</body>
</html>
<?php
$conn->close();
?>
