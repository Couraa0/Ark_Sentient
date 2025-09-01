<?php
ini_set('display_errors', 0);
ini_set('log_errors', 1);
error_reporting(E_ALL);

require_once '../Config/config.php';

if (session_status() === PHP_SESSION_NONE) session_start();
header('Content-Type: application/json');

$data = json_decode(file_get_contents('php://input'), true);

if (!isset($data['conversation_id'])) {
    echo json_encode(['success' => false, 'message' => 'conversation_id tidak ditemukan']);
    exit;
}

$conversation_id = $data['conversation_id'];
$user_id = $_SESSION['user_id'] ?? null;

if (empty($user_id)) {
    echo json_encode(['success' => false, 'message' => 'Anda harus login untuk mengakses fitur ini']);
    exit;
}

try {
    $conn = function_exists('getDbConnection') ? getDbConnection() : $conn;

    // Pastikan conversation milik user yang login
    $stmtCheck = $conn->prepare("SELECT id FROM conversations WHERE id = ? AND user_id = ?");
    $stmtCheck->execute([$conversation_id, $user_id]);
    if (!$stmtCheck->fetch()) {
        echo json_encode(['success' => false, 'message' => 'Percakapan tidak ditemukan atau bukan milik Anda']);
        exit;
    }

    // Hapus pesan (messages) dan conversation
    // Jika FOREIGN KEY ON DELETE CASCADE sudah aktif, cukup hapus conversation
    $stmtDel = $conn->prepare("DELETE FROM conversations WHERE id = ?");
    $stmtDel->execute([$conversation_id]);

    echo json_encode(['success' => true]);
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Gagal hapus: ' . $e->getMessage()]);
}
?>
