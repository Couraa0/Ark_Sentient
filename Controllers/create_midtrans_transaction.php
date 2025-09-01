<?php
require_once '../Config/config.php';
if (session_status() === PHP_SESSION_NONE) session_start();

header('Content-Type: application/json');

// Cek login
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['error' => 'Unauthorized']);
    exit;
}

// Ambil data cart user
try {
    $stmt = $conn->prepare("
        SELECT c.*, l.name, l.price
        FROM cart c
        JOIN livestock l ON c.livestock_id = l.id
        WHERE c.user_id = ?
    ");
    $stmt->execute([$_SESSION['user_id']]);
    $cart_items = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if (!$cart_items) {
        echo json_encode(['error' => 'Cart is empty']);
        exit;
    }
} catch (Exception $e) {
    echo json_encode(['error' => 'Database error: ' . $e->getMessage()]);
    exit;
}

// Hitung total
$subtotal = 0;
foreach ($cart_items as $item) {
    $subtotal += $item['price'] * $item['quantity'];
}
$shipping_cost = 200000;
$tax_amount = $subtotal * 0.10;
$total = $subtotal + $shipping_cost + $tax_amount;

// Validasi minimum amount untuk Midtrans
if ($total < 1000) {
    echo json_encode(['error' => 'Minimum transaction amount is Rp 1.000']);
    exit;
}

// Buat order_number unik dengan timestamp untuk menghindari duplikasi
$order_number = 'ORD-' . date('YmdHis') . '-' . str_pad(mt_rand(1, 9999), 4, '0', STR_PAD_LEFT);

// Simpan order ke database (status pending)
$conn->beginTransaction();
try {
    // Insert order
    $stmt = $conn->prepare("
        INSERT INTO orders (
            user_id, order_number, total_amount, shipping_cost, 
            tax_amount, final_amount, payment_method, 
            payment_status, order_status, shipping_address, notes, created_at
        ) VALUES (?, ?, ?, ?, ?, ?, 'qris', 'pending', 'pending', '', '', NOW())
    ");
    $stmt->execute([
        $_SESSION['user_id'],
        $order_number,
        $subtotal,
        $shipping_cost,
        $tax_amount,
        $total
    ]);
    $order_id = $conn->lastInsertId();

    // Validasi order_id
    if (!$order_id) {
        throw new Exception('Failed to create order');
    }

    // Simpan order_items
    foreach ($cart_items as $item) {
        $stmt = $conn->prepare("
            INSERT INTO order_items (order_id, livestock_id, quantity, unit_price, total_price) 
            VALUES (?, ?, ?, ?, ?)
        ");
        $stmt->execute([
            $order_id,
            $item['livestock_id'],
            $item['quantity'],
            $item['price'],
            $item['price'] * $item['quantity']
        ]);
    }

    // Simpan ke tabel payments (status pending)
    $stmt = $conn->prepare("
        INSERT INTO payments (order_id, payment_method, amount, payment_status, created_at) 
        VALUES (?, 'qris', ?, 'pending', NOW())
    ");
    $stmt->execute([$order_id, $total]);

    $conn->commit();
} catch (Exception $e) {
    $conn->rollBack();
    echo json_encode(['error' => 'Database transaction failed: ' . $e->getMessage()]);
    exit;
}

// Load .env jika belum (simple loader)
$env_path = dirname(__DIR__) . '/.env';
if (file_exists($env_path)) {
    $lines = file($env_path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        if (strpos(trim($line), '#') === 0 || strpos(trim($line), '=') === false) continue;
        list($name, $value) = array_map('trim', explode('=', $line, 2));
        $value = trim($value, "\"'");
        if (!getenv($name)) putenv("$name=$value");
    }
}

// Konfigurasi Midtrans
$autoload_path = '../vendor/autoload.php';
if (!file_exists($autoload_path)) {
    echo json_encode(['error' => 'Midtrans library not found. Please run: composer require midtrans/midtrans-php']);
    exit;
}

require_once($autoload_path);

use Midtrans\Config as MidtransConfig;
use Midtrans\Snap as MidtransSnap;

// Ambil konfigurasi dari env
$midtransServerKey = getenv('MIDTRANS_SERVER_KEY');
$midtransClientKey = getenv('MIDTRANS_CLIENT_KEY');
$midtransIsProduction = getenv('MIDTRANS_IS_PRODUCTION') === 'true' ? true : false;

MidtransConfig::$serverKey = $midtransServerKey;
MidtransConfig::$clientKey = $midtransClientKey;
MidtransConfig::$isProduction = $midtransIsProduction;
MidtransConfig::$isSanitized = true;
MidtransConfig::$is3ds = true;

// Validasi konfigurasi
if (empty(MidtransConfig::$serverKey)) {
    echo json_encode(['error' => 'Midtrans server key not configured']);
    exit;
}

// Data customer yang lebih lengkap
$customer_details = [
    'first_name' => $_SESSION['full_name'] ?? 'Customer',
    'email' => $_SESSION['email'] ?? 'customer@example.com',
];

// Tambahkan phone jika tersedia
if (isset($_SESSION['phone'])) {
    $customer_details['phone'] = $_SESSION['phone'];
}

// Data transaksi untuk Snap
$params = [
    'transaction_details' => [
        'order_id' => $order_number,
        'gross_amount' => (int)$total,
    ],
    'customer_details' => $customer_details,
    'item_details' => [],
    'callbacks' => [
        'finish' => 'http://yoursite.com/payment/finish', // Ganti dengan URL finish page
    ]
];

// Tambahkan item details
foreach ($cart_items as $item) {
    // Validasi data item
    if (empty($item['name']) || $item['price'] <= 0 || $item['quantity'] <= 0) {
        echo json_encode(['error' => 'Invalid item data']);
        exit;
    }

    $params['item_details'][] = [
        'id' => 'livestock_' . $item['livestock_id'],
        'price' => (int)$item['price'],
        'quantity' => (int)$item['quantity'],
        'name' => substr($item['name'], 0, 50), // Batasi panjang nama
    ];
}

// Tambahkan shipping cost jika > 0
if ($shipping_cost > 0) {
    $params['item_details'][] = [
        'id' => 'shipping',
        'price' => (int)$shipping_cost,
        'quantity' => 1,
        'name' => 'Shipping Cost'
    ];
}

// Tambahkan tax jika > 0
if ($tax_amount > 0) {
    $params['item_details'][] = [
        'id' => 'tax',
        'price' => (int)$tax_amount,
        'quantity' => 1,
        'name' => 'Tax (10%)'
    ];
}

// Validasi total item details sama dengan gross amount
$total_items = 0;
foreach ($params['item_details'] as $item) {
    $total_items += $item['price'] * $item['quantity'];
}

if ($total_items != $total) {
    echo json_encode(['error' => 'Item total mismatch with gross amount']);
    exit;
}

try {
    $snapToken = MidtransSnap::getSnapToken($params);

    // Update order dan payments dengan snap token
    $stmt = $conn->prepare("UPDATE orders SET snap_token = ? WHERE id = ?");
    $stmt->execute([$snapToken, $order_id]);
    $stmt = $conn->prepare("UPDATE payments SET snap_token = ? WHERE order_id = ?");
    $stmt->execute([$snapToken, $order_id]);

    // Hapus cart setelah berhasil membuat transaksi
    $stmt = $conn->prepare("DELETE FROM cart WHERE user_id = ?");
    $stmt->execute([$_SESSION['user_id']]);

    echo json_encode([
        'success' => true,
        'token' => $snapToken,
        'order_id' => $order_number,
        'order_db_id' => $order_id,
        'total_amount' => $total
    ]);

} catch (Exception $e) {
    // Log error untuk debugging
    error_log('Midtrans Error: ' . $e->getMessage());
    
    // Rollback order jika gagal membuat snap token
    try {
        $stmt = $conn->prepare("UPDATE orders SET order_status = 'failed' WHERE id = ?");
        $stmt->execute([$order_id]);
    } catch (Exception $rollback_error) {
        error_log('Rollback Error: ' . $rollback_error->getMessage());
    }
    
    echo json_encode(['error' => 'Payment gateway error: ' . $e->getMessage()]);
}
?>