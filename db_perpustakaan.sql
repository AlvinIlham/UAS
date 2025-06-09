-- Create database
CREATE DATABASE IF NOT EXISTS db_perpustakaan;
USE db_perpustakaan;

-- Create users table
CREATE TABLE IF NOT EXISTS users (
    id INT PRIMARY KEY AUTO_INCREMENT,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,    role ENUM('admin', 'user') NOT NULL DEFAULT 'user',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Create books table
CREATE TABLE IF NOT EXISTS books (
    id INT PRIMARY KEY AUTO_INCREMENT,
    judul VARCHAR(100) NOT NULL,
    penulis VARCHAR(100) NOT NULL,
    tahun VARCHAR(4) NOT NULL,
    kategori VARCHAR(50) NOT NULL,
    stok INT NOT NULL DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Create borrows table
CREATE TABLE IF NOT EXISTS borrows (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL,
    book_id INT NOT NULL,
    tanggal_pinjam DATE NOT NULL,
    tanggal_kembali DATE NOT NULL,
    tanggal_pengembalian DATE NULL,
    status ENUM('dipinjam', 'dikembalikan') NOT NULL DEFAULT 'dipinjam',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (book_id) REFERENCES books(id) ON DELETE CASCADE
);

-- Insert default admin user
INSERT INTO users (username, password, role, created_at) VALUES 
('admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin', '2024-01-01 00:00:00');

-- Insert sample user
INSERT INTO users (username, password, role, created_at) VALUES
('testing', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'user', '2024-06-01 00:00:00');

-- Insert sample books
INSERT INTO books (judul, penulis, tahun, kategori, stok) VALUES
('Harry Potter and the Philosopher''s Stone', 'J.K. Rowling', '1997', 'Fantasy', 5),
('To Kill a Mockingbird', 'Harper Lee', '1960', 'Fiction', 3),
('The Great Gatsby', 'F. Scott Fitzgerald', '1925', 'Fiction', 4),
('1984', 'George Orwell', '1949', 'Science Fiction', 2),
('Pride and Prejudice', 'Jane Austen', '1813', 'Romance', 3);
