<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

require 'db.php';

// Fetch user data
$user_id = $_SESSION['user_id'];
$stmt = $conn->prepare("SELECT username, email, nama, profile_image FROM users WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();
$stmt->close();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_id = $_SESSION['user_id'];
    $new_username = trim($_POST['editName'] ?? '');
    $new_password = $_POST['newPassword'] ?? '';
    $new_email = trim($_POST['email'] ?? '');
    $new_nama = trim($_POST['nama'] ?? '');
    
    if (empty($new_username)) {
        header("Location: profile.php?error=Username tidak boleh kosong");
        exit;
    }
    
    // Begin transaction
    $conn->begin_transaction();
    
    try {
        // Update username
        $stmt = $conn->prepare("UPDATE users SET username = ? WHERE id = ?");
        $stmt->bind_param("si", $new_username, $user_id);
        $stmt->execute();
        
        // Update email
        $stmt = $conn->prepare("UPDATE users SET email = ? WHERE id = ?");
        $stmt->bind_param("si", $new_email, $user_id);
        $stmt->execute();
        
        // Update nama
        $stmt = $conn->prepare("UPDATE users SET nama = ? WHERE id = ?");
        $stmt->bind_param("si", $new_nama, $user_id);
        $stmt->execute();
        
        // Update password if provided
        if (!empty($new_password)) {
            $hash = password_hash($new_password, PASSWORD_DEFAULT);
            $stmt = $conn->prepare("UPDATE users SET password = ? WHERE id = ?");
            $stmt->bind_param("si", $hash, $user_id);
            $stmt->execute();
        }
        
        $conn->commit();
        $_SESSION['username'] = $new_username;
        header("Location: profile.php?success=Profil berhasil diperbarui");
        
    } catch (Exception $e) {
        $conn->rollback();
        header("Location: profile.php?error=" . urlencode($e->getMessage()));
    }
    
    $conn->close();
    exit;
}

header("Location: profile.php");
exit;
?>

<div class="profile-container">
    <div class="card-glass profile-form">
        <h2 class="section-title">Update Profile</h2>
        <?php if (isset($error)): ?>
            <div class="alert alert-error">
                <i class="material-icons">error</i>
                <span><?php echo $error; ?></span>
            </div>
        <?php endif; ?>
        <?php if (isset($success)): ?>
            <div class="alert alert-success">
                <i class="material-icons">check_circle</i>
                <span><?php echo $success; ?></span>
            </div>
        <?php endif; ?>
        
        <form method="POST" enctype="multipart/form-data" class="edit-form">
            <div class="profile-image-container">
                <div class="profile-image">
                    <?php if ($profile_image): ?>
                        <img src="<?php echo htmlspecialchars($profile_image); ?>" alt="Profile Image">
                    <?php else: ?>
                        <i class="material-icons default-avatar">account_circle</i>
                    <?php endif; ?>
                </div>
                <div class="image-upload">
                    <label for="profile_image" class="btn-secondary">
                        <i class="material-icons">add_photo_alternate</i>
                        <span>Change Photo</span>
                    </label>
                    <input type="file" id="profile_image" name="profile_image" accept="image/*" class="hidden">
                </div>
            </div>

            <div class="input-group">
                <label for="nama">Nama Lengkap</label>
                <div class="input-field">
                    <i class="material-icons">person</i>
                    <input type="text" id="nama" name="nama" value="<?php echo htmlspecialchars($user['nama']); ?>" required>
                </div>
            </div>

            <div class="input-group">
                <label for="email">Email</label>
                <div class="input-field">
                    <i class="material-icons">email</i>
                    <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" required>
                </div>
            </div>

            <div class="input-group">
                <label for="password">New Password (optional)</label>
                <div class="input-field">
                    <i class="material-icons">lock</i>
                    <input type="password" id="password" name="password">
                    <button type="button" class="toggle-password material-icons">visibility_off</button>
                </div>
            </div>

            <button type="submit" class="btn-primary">
                <i class="material-icons">save</i>
                <span>Save Changes</span>
            </button>
        </form>
    </div>
</div>