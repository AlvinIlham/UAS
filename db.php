<?php
$host = 'localhost';
$user = 'root';
$pass = '';
$db   = 'db_perpustakaan';

$conn = new mysqli($host, $user, $pass, $db);

if ($conn->connect_error) {
    die('Koneksi ke database gagal: ' . $conn->connect_error);
}
?>