<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.html");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    require 'db.php';
    
    $judul = trim($_POST['judul'] ?? '');
    $penulis = trim($_POST['penulis'] ?? '');
    $tahun = trim($_POST['tahun'] ?? '');
    $kategori = trim($_POST['kategori'] ?? '');
    $stok = trim($_POST['stok'] ?? '');
    
    if (empty($judul) || empty($penulis) || empty($tahun) || empty($kategori) || empty($stok)) {
        $error = "Semua field harus diisi!";
    } else {
        $stmt = $conn->prepare("INSERT INTO books (judul, penulis, tahun, kategori, stok) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("ssssi", $judul, $penulis, $tahun, $kategori, $stok);
        
        if ($stmt->execute()) {
            header("Location: books.php?status=success&message=Buku berhasil ditambahkan");
            exit;
        } else {
            $error = "Gagal menambahkan buku: " . $conn->error;
        }
        $stmt->close();
    }
    $conn->close();
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Buku | Perpustakaan Mini</title>
    <link rel="stylesheet" href="style.css">
</head>
<body class="dashboard-bg-alt">
    <div class="dashboard-grid">
        <aside class="dashboard-side">
            <nav class="dashboard-menu-alt">
                <a href="dashboard.php"><span>🏠</span>Dashboard</a>
                <a href="profile.php"><span>👤</span>Profil Saya</a>
                <a href="books.php"><span>📚</span>Data Buku</a>
                <a href="borrow.php"><span>📥</span>Peminjaman</a>
                <a href="return.php"><span>📤</span>Pengembalian</a>
                <?php if ($_SESSION['role'] === 'admin'): ?>
                <a href="members.php"><span>👥</span>Data Anggota</a>
                <?php endif; ?>
                <a href="logout.php" class="dashboard-logout-alt">Logout</a>
            </nav>
        </aside>
        <main class="dashboard-main">
            <h1 class="dashboard-title-alt">Tambah Buku Baru</h1>
            
            <?php if (isset($error)): ?>
            <div class="alert alert-error">
                <?php echo htmlspecialchars($error); ?>
            </div>
            <?php endif; ?>
            
            <div class="form-container">
                <form method="POST" class="edit-form">
                    <div class="input-group">
                        <label for="judul">Judul Buku</label>
                        <input type="text" id="judul" name="judul" required maxlength="100"
                               value="<?php echo htmlspecialchars($_POST['judul'] ?? ''); ?>">
                    </div>
                    
                    <div class="input-group">
                        <label for="penulis">Penulis</label>
                        <input type="text" id="penulis" name="penulis" required maxlength="100"
                               value="<?php echo htmlspecialchars($_POST['penulis'] ?? ''); ?>">
                    </div>
                    
                    <div class="input-group">
                        <label for="tahun">Tahun Terbit</label>
                        <input type="number" id="tahun" name="tahun" required min="1900" max="<?php echo date('Y'); ?>"
                               value="<?php echo htmlspecialchars($_POST['tahun'] ?? ''); ?>">
                    </div>
                    
                    <div class="input-group">
                        <label for="kategori">Kategori</label>
                        <input type="text" id="kategori" name="kategori" required maxlength="50"
                               value="<?php echo htmlspecialchars($_POST['kategori'] ?? ''); ?>">
                    </div>
                    
                    <div class="input-group">
                        <label for="stok">Stok</label>
                        <input type="number" id="stok" name="stok" required min="0"
                               value="<?php echo htmlspecialchars($_POST['stok'] ?? '0'); ?>">
                    </div>
                    
                    <button type="submit" class="btn-primary">Simpan Buku</button>
                </form>
            </div>
        </main>
    </div>
</body>
</html>
