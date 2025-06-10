# 📚 Sistem Manajemen Perpustakaan Mini

![PHP](https://img.shields.io/badge/PHP-8.0+-777BB4?style=for-the-badge&logo=php&logoColor=white)
![MySQL](https://img.shields.io/badge/MySQL-8.0+-4479A1?style=for-the-badge&logo=mysql&logoColor=white)
![HTML5](https://img.shields.io/badge/HTML5-E34F26?style=for-the-badge&logo=html5&logoColor=white)
![CSS3](https://img.shields.io/badge/CSS3-1572B6?style=for-the-badge&logo=css3&logoColor=white)
![JavaScript](https://img.shields.io/badge/JavaScript-F7DF1E?style=for-the-badge&logo=javascript&logoColor=black)

Sistem manajemen perpustakaan berbasis web yang dibangun menggunakan PHP, MySQL, HTML, CSS, dan JavaScript. Aplikasi ini memungkinkan pengelolaan buku, anggota, peminjaman, dan pengembalian buku dengan antarmuka yang modern dan responsif.

## 🚀 Fitur Utama

### 👤 Manajemen Pengguna

- **Autentikasi**: Sistem login dan registrasi yang aman
- **Role-based Access**: Pembedaan akses untuk admin dan user
- **Profile Management**: Pengelolaan profil pengguna
- **Password Security**: Enkripsi password dengan bcrypt

### 📖 Manajemen Buku

- **CRUD Operations**: Create, Read, Update, Delete buku
- **Kategori Buku**: Pengelompokan buku berdasarkan kategori
- **Stock Management**: Pengelolaan stok buku
- **Search & Filter**: Pencarian dan filter buku

### 👥 Manajemen Anggota (Admin Only)

- **Member Registration**: Pendaftaran anggota baru
- **Member Management**: Pengelolaan data anggota
- **Borrowing History**: Riwayat peminjaman per anggota

### 📋 Sistem Peminjaman

- **Book Borrowing**: Peminjaman buku dengan validasi stok
- **Return Management**: Pengembalian buku
- **Due Date Tracking**: Pelacakan tanggal jatuh tempo
- **Status Monitoring**: Monitoring status peminjaman

### 🎨 UI/UX Modern

- **Responsive Design**: Tampilan yang adaptif di semua perangkat
- **Material Design**: Menggunakan Material Icons
- **Glass Morphism**: Efek kaca modern
- **Smooth Animations**: Transisi dan animasi yang halus
- **Dark Theme**: Tema gelap yang elegan

## 🛠️ Teknologi yang Digunakan

### Backend

- **PHP 8.0+**: Server-side scripting
- **MySQL 8.0+**: Database management
- **PDO/MySQLi**: Database connectivity

### Frontend

- **HTML5**: Markup structure
- **CSS3**: Styling dengan flexbox dan grid
- **JavaScript**: Interaktivitas dan validasi
- **Material Icons**: Icon library

### Tools & Environment

- **XAMPP**: Local development server
- **phpMyAdmin**: Database administration

## 📋 Persyaratan Sistem

### Minimum Requirements

- **PHP**: Version 8.0 atau lebih tinggi
- **MySQL**: Version 8.0 atau lebih tinggi
- **Web Server**: Apache 2.4+ (XAMPP/WAMP/LAMP)
- **Browser**: Chrome 90+, Firefox 88+, Safari 14+

### Recommended

- **RAM**: 2GB atau lebih
- **Storage**: 100MB free space
- **PHP Extensions**: mysqli, pdo, session

## 🚀 Instalasi

### 1. Clone Repository

```bash
git clone <repository-url>
cd perpustakaan-mini
```

### 2. Setup XAMPP

1. Download dan install [XAMPP](https://www.apachefriends.org/)
2. Start Apache dan MySQL services
3. Copy folder project ke `C:\xampp\htdocs\UAS`

### 3. Database Setup

1. Buka [phpMyAdmin](http://localhost/phpmyadmin)
2. Import file `db_perpustakaan.sql`
3. Database akan otomatis terbuat dengan sample data

### 4. Konfigurasi Database

Edit file `db.php` jika diperlukan:

```php
<?php
$host = 'localhost';
$username = 'root';
$password = '';
$database = 'db_perpustakaan';
?>
```

### 5. Akses Aplikasi

Buka browser dan akses: `http://localhost/UAS`

## 👤 Default Login

### Administrator

- **Username**: `admin`
- **Password**: `password`

### User Testing

- **Username**: `testing`
- **Password**: `password`

## 📁 Struktur File

```
UAS/
├── 📄 index.php              # Entry point (redirect ke login)
├── 🔐 auth.php               # Authentication handler
├── 🔐 login.php              # Halaman login
├── 🔐 register.php           # Halaman registrasi
├── 🔐 logout.php             # Logout handler
├── 🏠 dashboard.php          # Dashboard utama
├── 📚 books.php              # Management buku
├── ➕ books_add.php          # Tambah buku
├── ✏️ books_edit.php         # Edit buku
├── 📖 borrow.php             # Peminjaman buku
├── 🔄 return.php             # Pengembalian buku
├── 👥 members.php            # Management anggota (admin)
├── ➕ member_add.php         # Tambah anggota
├── 👤 profile.php            # Profil pengguna
├── 🔧 profile_update.php     # Update profil
├── 🗄️ db.php                # Database connection
├── 🗄️ db_perpustakaan.sql   # Database schema
├── 🎨 style.css              # Main stylesheet
├── ⚡ script.js              # JavaScript functions
└── 📖 README.md              # Dokumentasi
```

## 🎨 Fitur UI/UX

### Design System

- **Color Palette**: Dark theme dengan accent gold
- **Typography**: Responsive font scaling
- **Layout**: Flexbox dan CSS Grid
- **Components**: Reusable UI components

### Responsive Breakpoints

- **Mobile**: < 768px
- **Tablet**: 768px - 1024px
- **Desktop**: 1024px - 1440px
- **Large**: > 1440px

### Interactive Elements

- **Hover Effects**: Smooth transitions
- **Form Validation**: Real-time validation
- **Modal Dialogs**: Confirmation dialogs
- **Loading States**: User feedback

## 🔧 Konfigurasi Lanjutan

### Custom CSS Variables

```css
:root {
  --primary: #d4af37;
  --secondary: #1a1a1a;
  --success: #10b981;
  --error: #ef4444;
  --warning: #f59e0b;
}
```

### JavaScript Configuration

```javascript
// Global settings
const CONFIG = {
  API_URL: "api/",
  TIMEOUT: 5000,
  DEBUG: false,
};
```

## 📊 Database Schema

### Tables Overview

1. **users**: Manajemen pengguna dan autentikasi
2. **books**: Master data buku
3. **borrows**: Transaksi peminjaman dan pengembalian

### Relationships

- `borrows.user_id` → `users.id` (Many-to-One)
- `borrows.book_id` → `books.id` (Many-to-One)

## 🛡️ Security Features

### Authentication

- **Password Hashing**: bcrypt algorithm
- **Session Management**: Secure session handling
- **CSRF Protection**: Form token validation
- **Input Sanitization**: XSS prevention

### Authorization

- **Role-based Access**: Admin vs User permissions
- **Route Protection**: Middleware for protected routes
- **Data Validation**: Server-side validation

## 🐛 Debugging & Troubleshooting

### Common Issues

#### Database Connection Error

```php
// Check db.php configuration
$host = 'localhost';
$username = 'root';
$password = '';  // Check if password is needed
```

#### Permission Issues

```bash
# Set proper permissions (Linux/Mac)
chmod 755 /path/to/project
chmod 644 *.php
```

#### Session Issues

```php
// Clear session data
session_start();
session_destroy();
```

## 📈 Performance Optimization

### Database

- **Indexing**: Proper database indexes
- **Query Optimization**: Efficient SQL queries
- **Connection Pooling**: Reuse database connections

### Frontend

- **CSS Minification**: Optimized stylesheet
- **Image Optimization**: Compressed assets
- **Caching**: Browser caching headers

## 🚀 Deployment

### Production Setup

1. **Web Server**: Apache/Nginx configuration
2. **PHP Configuration**: Production php.ini settings
3. **Database**: MySQL optimization
4. **SSL Certificate**: HTTPS implementation
5. **Backup Strategy**: Regular database backups

### Environment Variables

```php
// config/production.php
define('DB_HOST', 'your-db-host');
define('DB_USER', 'your-db-user');
define('DB_PASS', 'your-db-password');
define('DB_NAME', 'your-db-name');
```

## 🤝 Contributing

### Development Workflow

1. Fork the repository
2. Create feature branch (`git checkout -b feature/amazing-feature`)
3. Commit changes (`git commit -m 'Add amazing feature'`)
4. Push to branch (`git push origin feature/amazing-feature`)
5. Open Pull Request

### Code Standards

- **PHP**: PSR-12 coding standard
- **JavaScript**: ES6+ features
- **CSS**: BEM methodology
- **Documentation**: Inline comments

## 📝 Changelog

### Version 2.0.0 (June 2025)

- ✅ Enhanced UI/UX with modern design
- ✅ Responsive layout improvements
- ✅ Password visibility toggles
- ✅ Form validation enhancements
- ✅ Better error handling
- ✅ Performance optimizations

### Version 1.0.0 (Initial)

- ✅ Basic CRUD operations
- ✅ User authentication
- ✅ Book management
- ✅ Borrowing system

## 📞 Support

### Documentation

- **Installation Guide**: Detailed setup instructions
- **User Manual**: End-user documentation
- **API Reference**: Developer documentation

### Community

- **Issues**: Report bugs and request features
- **Discussions**: Community support and questions
- **Wiki**: Additional documentation and tutorials

## 📄 License

This project is licensed under the MIT License - see the [LICENSE](LICENSE) file for details.

## 👨‍💻 Author

**Library Management System**

- Developed with ❤️ using modern web technologies
- Built for educational and practical purposes
- Designed with user experience in mind

---

### 🌟 Features Highlight

| Feature              | Description                   | Status      |
| -------------------- | ----------------------------- | ----------- |
| 🔐 Authentication    | Secure login/register system  | ✅ Complete |
| 📚 Book Management   | CRUD operations for books     | ✅ Complete |
| 👥 User Management   | Admin panel for users         | ✅ Complete |
| 📋 Borrowing System  | Loan and return tracking      | ✅ Complete |
| 📱 Responsive Design | Mobile-friendly interface     | ✅ Complete |
| 🎨 Modern UI         | Glass morphism design         | ✅ Complete |
| 🔍 Search & Filter   | Advanced search functionality | ✅ Complete |
| 📊 Dashboard         | Analytics and overview        | ✅ Complete |

---

**Made with 💖 for the community**
