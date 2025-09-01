<?php
require_once '../Config/config.php';
if (session_status() === PHP_SESSION_NONE) session_start();

header('Content-Type: application/json');

if (!isLoggedIn()) {
    echo json_encode(['success' => false, 'message' => 'Please login first']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_POST['livestock_id'])) {
    echo json_encode(['success' => false, 'message' => 'Invalid request']);
    exit;
}

$livestock_id = (int)$_POST['livestock_id'];
$user_id = $_SESSION['user_id'];

try {
    // Check if livestock exists and is available
    $stmt = $conn->prepare("SELECT id, name, status FROM livestock WHERE id = ?");
    $stmt->execute([$livestock_id]);
    $livestock = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$livestock) {
        echo json_encode(['success' => false, 'message' => 'Livestock not found']);
        exit;
    }
    
    if ($livestock['status'] !== 'available') {
        echo json_encode(['success' => false, 'message' => 'Livestock is not available']);
        exit;
    }
    
    // Check if item already in cart
    $stmt = $conn->prepare("SELECT id, quantity FROM cart WHERE user_id = ? AND livestock_id = ?");
    $stmt->execute([$user_id, $livestock_id]);
    $existing = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($existing) {
        // Update quantity
        $stmt = $conn->prepare("UPDATE cart SET quantity = quantity + 1 WHERE id = ?");
        $stmt->execute([$existing['id']]);
    } else {
        // Insert new item
        $stmt = $conn->prepare("INSERT INTO cart (user_id, livestock_id, quantity) VALUES (?, ?, 1)");
        $stmt->execute([$user_id, $livestock_id]);
    }
    
    // Get updated cart count
    $stmt = $conn->prepare("SELECT COUNT(*) as count FROM cart WHERE user_id = ?");
    $stmt->execute([$user_id]);
    $cart_count = $stmt->fetchColumn();
    
    echo json_encode([
        'success' => true, 
        'message' => 'Item added to cart successfully',
        'cart_count' => $cart_count
    ]);
    
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
}
?>