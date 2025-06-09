<?php
session_start();
require 'db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    // REGISTER
    if ($action === 'register') {
        $username = trim($_POST['username'] ?? '');
        $password = $_POST['password'] ?? '';
        $confirm  = $_POST['confirm_password'] ?? '';

        // Validasi sederhana
        if ($username === '' || $password === '' || $confirm === '') {
            header("Location: register.php?error=Semua field harus diisi!");
            exit;
        }
        if ($password !== $confirm) {
            header("Location: register.php?error=Password tidak cocok!");
            exit;
        }

        // Cek username sudah ada
        $stmt = $conn->prepare("SELECT id FROM users WHERE username = ?");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $stmt->store_result();
        if ($stmt->num_rows > 0) {
            header("Location: register.php?error=Username sudah digunakan!");
            exit;
        }        // Simpan user baru
        $hash = password_hash($password, PASSWORD_DEFAULT);
        $role = 'user';
        $stmt = $conn->prepare("INSERT INTO users (username, password, role) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $username, $hash, $role);
        if ($stmt->execute()) {
            header("Location: login.php?success=Registrasi berhasil! Silakan login.");
        } else {
            header("Location: register.php?error=Gagal mendaftar: " . $conn->error);
        }
        exit;
    }

    // LOGIN
    if ($action === 'login') {
        $username = trim($_POST['username'] ?? '');
        $password = $_POST['password'] ?? '';

        if ($username === '' || $password === '') {
            header("Location: login.php?error=Username dan password harus diisi!");
            exit;
        }

        $stmt = $conn->prepare("SELECT id, username, password, role FROM users WHERE username = ?");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows === 1) {
            $user = $result->fetch_assoc();
            if (password_verify($password, $user['password'])) {
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['username'] = $user['username'];
                $_SESSION['role'] = $user['role'];
                header("Location: dashboard.php");
                exit;
            }
        }
        header("Location: login.php?error=Username atau password salah!");
        exit;
    }
}

// Jika bukan POST
header("Location: login.php");
exit;