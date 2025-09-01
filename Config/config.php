<?php
// File: Ark_Sentient/Config/config.php

if (session_status() === PHP_SESSION_NONE) session_start();

$host = 'localhost';
$db   = 'e11'; 
$user = 'root';           
$pass = '';               
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];

try {
    // KONEKSI DATABASE UTAMA YANG SUDAH ADA
    $conn = new PDO($dsn, $user, $pass, $options);
} catch (\PDOException $e) {
    // Tambahkan logging error untuk debugging di lingkungan production
    error_log("Database Connection Error: " . $e->getMessage());
    throw new \PDOException($e->getMessage(), (int)$e->getCode());
}

// --- Helper functions ---
if (!function_exists('isLoggedIn')) {
    function isLoggedIn() {
        return isset($_SESSION['user_id']);
    }
}
if (!function_exists('redirectTo')) {
    function redirectTo($url) {
        header("Location: $url");
        exit;
    }
}
if (!function_exists('formatRupiah')) {
    function formatRupiah($angka) {
        return 'Rp ' . number_format($angka, 0, ',', '.');
    }
}

// --- TAMBAHAN KODE UNTUK FITUR RIWAYAT CHAT ---
// Fungsi untuk mendapatkan koneksi database
// Ini akan digunakan oleh file API (misal: api/chat.php)
// Fungsi ini mengembalikan objek $conn yang sudah dibuat di atas
if (!function_exists('getDbConnection')) {
    function getDbConnection() {
        global $conn; // Mengakses variabel $conn yang didefinisikan secara global di awal file
        return $conn;
    }
}
// --- AKHIR TAMBAHAN KODE ---
?>