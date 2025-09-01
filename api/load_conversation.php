<?php
// File: Ark_Sentient/api/load_conversation.php

// Memuat koneksi database dari config.php
require_once __DIR__ . '/../Config/config.php';

// Mengatur header untuk respons JSON
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: ' . ($_SERVER['HTTP_ORIGIN'] ?? '*')); // Aman untuk development
header('Access-Control-Allow-Methods: GET'); // Ini adalah GET request
header('Access-Control-Allow-Headers: Content-Type, X-Requested-With');

// Periksa apakah conversation_id diberikan
$conversation_id = $_GET['conversation_id'] ?? null;

if (empty($conversation_id)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'ID percakapan tidak diberikan.']);
    exit();
}

try {
    $conn = getDbConnection(); // Dapatkan koneksi database

    // Pastikan percakapan ini milik user yang benar jika ada sistem login
    // <<-- Placeholder user ID. GANTI ini dengan ID user yang login jika ada sistem login -->>
    // Contoh: $user_id = $_SESSION['user_id'] ?? null;
    $user_id = $_SESSION['user_id'] ?? null; // Ambil user_id dari sesi login

    // Tambahkan pengecekan jika user_id tidak ditemukan (user belum login atau sesi sudah habis)
    if (empty($user_id)) {
        error_log("ERROR (chat.php): Akses tanpa otentikasi. User ID kosong.");
        http_response_code(401); // Unauthorized
        echo json_encode(['success' => false, 'message' => 'Anda harus login untuk mengakses fitur ini.']);
        exit();
    }

    // Ambil semua pesan untuk conversation_id tertentu
    $stmt = $conn->prepare("SELECT sender, text FROM messages WHERE conversation_id = ? ORDER BY created_at ASC");
    $stmt->execute([$conversation_id]);
    $messages = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Validasi conversation milik user yang login
    $stmt_check = $conn->prepare("SELECT id FROM conversations WHERE id = ? AND user_id = ?");
    $stmt_check->execute([$conversation_id, $user_id]);
    if (!$stmt_check->fetch()) {
        http_response_code(403); // Forbidden
        echo json_encode(['success' => false, 'message' => 'Anda tidak memiliki akses ke percakapan ini.']);
        exit();
    }


    echo json_encode(['success' => true, 'messages' => $messages, 'conversation_id' => $conversation_id]);

} catch (PDOException $e) {
    error_log("Database Error (load_conversation.php): " . $e->getMessage());
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Gagal memuat percakapan.']);
} catch (Exception $e) {
    error_log("General Error (load_conversation.php): " . $e->getMessage());
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Terjadi kesalahan tidak terduga.']);
}
?>