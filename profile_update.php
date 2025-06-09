<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

require 'db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_id = $_SESSION['user_id'];
    $new_username = trim($_POST['editName'] ?? '');
    $new_password = $_POST['newPassword'] ?? '';
    
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