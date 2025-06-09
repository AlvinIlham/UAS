<?php
require 'db.php';

if ($conn) {
    echo "Koneksi ke database BERHASIL!";
} else {
    echo "Koneksi ke database GAGAL!";
}
?>