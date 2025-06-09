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
                <a href="books.php" class="active">
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
                <a href="profile.php">
                    <span>👤</span> Profil
                </a>
                <a href="logout.php">
                    <span>🚪</span> Logout
                </a>
            </nav>
        </aside>

        <main class="dashboard-main">
            <div class="content-header">
                <h1 class="dashboard-title">Tambah Buku Baru</h1>
            </div>
            
            <?php if (isset($error)): ?>
            <div class="alert alert-error">
                <span class="material-icons">error</span>
                <?php echo htmlspecialchars($error); ?>
            </div>
            <?php endif; ?>
            
            <div class="card-glass">
                <div class="auth-header">
                    <i class="material-icons auth-icon">library_books</i>
                    <h2>Form Tambah Buku</h2>
                    <p class="auth-subtitle">Silahkan isi data buku dengan lengkap</p>
                </div>

                <form method="POST" class="auth-form">
                    <div class="input-group">
                        <label for="judul">Judul Buku</label>
                        <div class="input-field">
                            <i class="material-icons">book</i>
                            <input type="text" id="judul" name="judul" required maxlength="100"
                                   value="<?php echo htmlspecialchars($_POST['judul'] ?? ''); ?>"
                                   placeholder="Masukkan judul buku">
                        </div>
                    </div>
                    
                    <div class="input-group">
                        <label for="penulis">Penulis</label>
                        <div class="input-field">
                            <i class="material-icons">create</i>
                            <input type="text" id="penulis" name="penulis" required maxlength="100"
                                   value="<?php echo htmlspecialchars($_POST['penulis'] ?? ''); ?>"
                                   placeholder="Masukkan nama penulis">
                        </div>
                    </div>
                    
                    <div class="input-group">
                        <label for="tahun">Tahun Terbit</label>
                        <div class="input-field">
                            <i class="material-icons">event</i>
                            <input type="number" id="tahun" name="tahun" required min="1900" 
                                   max="<?php echo date('Y'); ?>"
                                   value="<?php echo htmlspecialchars($_POST['tahun'] ?? ''); ?>"
                                   placeholder="Masukkan tahun terbit">
                        </div>
                    </div>
                    
                    <div class="input-group">
                        <label for="kategori">Kategori</label>
                        <div class="input-field">
                            <i class="material-icons">category</i>
                            <input type="text" id="kategori" name="kategori" required maxlength="50"
                                   value="<?php echo htmlspecialchars($_POST['kategori'] ?? ''); ?>"
                                   placeholder="Masukkan kategori buku">
                        </div>
                    </div>
                    
                    <div class="input-group">
                        <label for="stok">Stok</label>
                        <div class="input-field">
                            <i class="material-icons">inventory</i>
                            <input type="number" id="stok" name="stok" required min="0"
                                   value="<?php echo htmlspecialchars($_POST['stok'] ?? '0'); ?>"
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
                            <span>Simpan Buku</span>
                        </button>
                    </div>
                </form>
            </div>
        </main>
    </div>
</body>
</html>
