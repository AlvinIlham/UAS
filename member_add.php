<?php
session_start();
$role = isset($_SESSION['role']) ? $_SESSION['role'] : 'user';
if (!isset($_SESSION['user_id']) || $role !== 'admin') {
    header("Location: login.php");
    exit;
}

require 'db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST['username']);
    $password = $_POST['password'];
    $status = "error";
    $message = "";

    // Validate input
    if (empty($username) || empty($password)) {
        $message = "Username dan password harus diisi";
    } else {
        // Check if username already exists
        $stmt = $conn->prepare("SELECT id FROM users WHERE username = ?");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows > 0) {
            $message = "Username sudah digunakan";
        } else {
            // Hash password and insert new user
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            $role = 'user';
            $stmt = $conn->prepare("INSERT INTO users (username, password, role) VALUES (?, ?, ?)");
            $stmt->bind_param("sss", $username, $hashed_password, $role);
            
            if ($stmt->execute()) {
                $status = "success";
                $message = "Anggota berhasil ditambahkan";
            } else {
                $message = "Gagal menambahkan anggota";
            }
        }
    }
    
    $conn->close();
    header("Location: members.php?status=$status&message=$message");
    exit;
}
?>
