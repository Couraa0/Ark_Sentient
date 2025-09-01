<?php
// File: Ark_Sentient/api/list_conversations.php

// Memuat koneksi database dari config.php
require_once __DIR__ . '/../Config/config.php';

// Mengatur header untuk respons JSON
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: ' . ($_SERVER['HTTP_ORIGIN'] ?? '*')); // Aman untuk development
header('Access-Control-Allow-Methods: GET'); // Ini adalah GET request
header('Access-Control-Allow-Headers: Content-Type, X-Requested-With');

try {
    $conn = getDbConnection(); // Dapatkan koneksi database

    // <<-- Placeholder user ID. GANTI ini dengan ID user yang login jika ada sistem login -->>
    // Jika Anda memiliki sistem login, ganti ini dengan $_SESSION['user_id']
    // Contoh: $user_id = $_SESSION['user_id'] ?? null;
    // Untuk saat ini, kita pakai ID 1 seperti di api/chat.php
    $user_id = $_SESSION['user_id'] ?? null; // Ambil user_id dari sesi login

    // Tambahkan pengecekan jika user_id tidak ditemukan (user belum login atau sesi sudah habis)
    if (empty($user_id)) {
        error_log("ERROR (chat.php): Akses tanpa otentikasi. User ID kosong.");
        http_response_code(401); // Unauthorized
        echo json_encode(['success' => false, 'message' => 'Anda harus login untuk mengakses fitur ini.']);
        exit();
    }
    // Ambil daftar percakapan terbaru untuk user ini
    $stmt = $conn->prepare("SELECT id, title FROM conversations WHERE user_id = ? ORDER BY created_at DESC LIMIT 10"); // Ambil 10 percakapan terbaru
    $stmt->execute([$user_id]);
    $conversations = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Sudah mengambil daftar percakapan (id, title) milik user yang login dari tabel conversations

    echo json_encode(['success' => true, 'conversations' => $conversations]);

} catch (PDOException $e) {
    error_log("Database Error (list_conversations.php): " . $e->getMessage());
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Gagal mengambil daftar percakapan.']);
} catch (Exception $e) {
    error_log("General Error (list_conversations.php): " . $e->getMessage());
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Terjadi kesalahan tidak terduga.']);
}
?>