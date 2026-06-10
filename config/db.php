<?php
/**
 * Panggonan Resto — Koneksi Database Terpusat (PDO)
 * File ini harus di-require di setiap halaman PHP yang membutuhkan akses database.
 */

// ========================================================
// PENGATURAN DATABASE (PRODUCTION DEPLOYMENT GUIDELINES)
// ========================================================
// Jika Anda mengunggah website ini ke hosting (seperti cPanel/VPS):
// 1. Buat database baru di cPanel (MySQL Database Wizard).
// 2. Buat user database baru dan hubungkan ke database tersebut dengan hak akses penuh (All Privileges).
// 3. Impor berkas "database.sql" ke phpMyAdmin hosting Anda.
// 4. Ubah baris konfigurasi di bawah ini sesuai kredensial hosting Anda!
// ========================================================
define('DB_HOST', 'localhost');      // Biasanya tetap 'localhost' di sebagian besar hosting cPanel
define('DB_USER', 'root');           // GANTI dengan nama user database hosting Anda
define('DB_PASS', '');               // GANTI dengan kata sandi user database hosting Anda
define('DB_NAME', 'panggonan_db');   // GANTI dengan nama database hosting Anda

try {
    $pdo = new PDO(
        "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4",
        DB_USER,
        DB_PASS,
        [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
        ]
    );
} catch (PDOException $e) {
    die("Koneksi database gagal: " . $e->getMessage());
}
