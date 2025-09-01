<?php
ob_start();
ini_set('display_errors', 0);
ini_set('log_errors', 1);
error_reporting(E_ALL);

include '../Config/config.php';

// Pastikan session aktif sebelum akses $_SESSION
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

header('Content-Type: application/json');

$data = json_decode(file_get_contents('php://input'), true);

if (!isset($data['conversation_id']) || !isset($data['new_title'])) {
    ob_end_clean();
    echo json_encode(['success' => false, 'message' => 'conversation_id atau new_title tidak ditemukan']);
    exit;
}

$id = $data['conversation_id'];
$newTitle = $data['new_title'];
$user_id = $_SESSION['user_id'] ?? null;

if (empty($user_id)) {
    ob_end_clean();
    echo json_encode(['success' => false, 'message' => 'Anda harus login untuk mengakses fitur ini']);
    exit;
}

// Pastikan koneksi database $conn tersedia
if (!isset($conn)) {
    if (function_exists('getDbConnection')) {
        $conn = getDbConnection();
    } else {
        ob_end_clean();
        echo json_encode(['success' => false, 'message' => 'Koneksi database tidak tersedia']);
        exit;
    }
}

try {
    // Pastikan conversation milik user yang login
    $stmtCheck = $conn->prepare("SELECT id FROM conversations WHERE id = ? AND user_id = ?");
    $stmtCheck->execute([$id, $user_id]);
    if (!$stmtCheck->fetch()) {
        ob_end_clean();
        echo json_encode(['success' => false, 'message' => 'Percakapan tidak ditemukan atau bukan milik Anda']);
        exit;
    }

    $stmt = $conn->prepare("UPDATE conversations SET title = ? WHERE id = ?");
    $stmt->execute([$newTitle, $id]);

    ob_end_clean();
    echo json_encode(['success' => true]);
} catch (PDOException $e) {
    ob_end_clean();
    echo json_encode(['success' => false, 'message' => 'Gagal rename: ' . $e->getMessage()]);
}
?>
