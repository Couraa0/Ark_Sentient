<?php
require_once '../Config/config.php';
if (session_status() === PHP_SESSION_NONE) session_start();

$order_id = isset($_GET['order_id']) ? $_GET['order_id'] : '';
if (!$order_id) {
    echo "<div class='container mt-5'><div class='alert alert-danger'>Order ID tidak ditemukan.</div></div>";
    exit;
}

// Ambil order berdasarkan order_number
$stmt = $conn->prepare("SELECT * FROM orders WHERE order_number = ? AND user_id = ?");
$stmt->execute([$order_id, $_SESSION['user_id']]);
$order = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$order) {
    echo "<div class='container mt-5'><div class='alert alert-danger'>Order tidak ditemukan.</div></div>";
    exit;
}

// Ambil order items
$stmt = $conn->prepare("SELECT oi.*, l.name FROM order_items oi JOIN livestock l ON oi.livestock_id = l.id WHERE oi.order_id = ?");
$stmt->execute([$order['id']]);
$order_items = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Ambil status pembayaran
$stmt = $conn->prepare("SELECT * FROM payments WHERE order_id = ?");
$stmt->execute([$order['id']]);
$payment = $stmt->fetch(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Order Summary - ARK Sentient</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="../Asset/css/checkout.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
</head>
<body>
    <nav class="navbar navbar-expand-lg fixed-top">
        <div class="container">
            <a class="navbar-brand text-success d-flex align-items-center" href="home.php">
                <img src="../asset/img/Logo.png" alt="Logo" style="height:32px;width:auto;">
                ARK Sentient
            </a>
        </div>
    </nav>
    <div style="height:80px"></div>
    <div class="container mt-5">
        <div class="alert alert-success text-center">
            <i class="fas fa-check-circle fa-3x text-success mb-3"></i>
            <h3 class="mb-2">Pembayaran <?php echo ($payment && $payment['payment_status'] == 'completed') ? 'Berhasil' : 'Pending'; ?>!</h3>
            <p>Terima kasih telah melakukan pembayaran di <strong>ARK Sentient</strong>.</p>
        </div>
        <div class="card p-4 mb-4">
            <h4 class="mb-3">Ringkasan Order</h4>
            <ul class="list-unstyled mb-3">
                <li><strong>No. Order:</strong> <?php echo htmlspecialchars($order['order_number']); ?></li>
                <li><strong>Total:</strong> <?php echo formatRupiah($order['final_amount']); ?></li>
                <li><strong>Status Pembayaran:</strong> <?php echo htmlspecialchars($payment['payment_status']); ?></li>
                <li><strong>Metode Pembayaran:</strong> <?php echo htmlspecialchars($payment['payment_method']); ?></li>
                <li><strong>Alamat Pengiriman:</strong> <?php echo htmlspecialchars($order['shipping_address']); ?></li>
                <?php if (!empty($order['notes'])): ?>
                    <li><strong>Catatan:</strong> <?php echo htmlspecialchars($order['notes']); ?></li>
                <?php endif; ?>
            </ul>
            <h5>Barang yang Dibeli</h5>
            <ul class="list-group mb-3">
                <?php foreach ($order_items as $item): ?>
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        <span>
                            <?php echo htmlspecialchars($item['name']); ?> (<?php echo $item['quantity']; ?>x)
                        </span>
                        <span><?php echo formatRupiah($item['total_price']); ?></span>
                    </li>
                <?php endforeach; ?>
            </ul>
        </div>
        <div class="text-center">
            <a href="dashboard.php" class="btn btn-success me-2"><i class="fas fa-home me-1"></i> Kembali ke Marketplace</a>
            <a href="orders.php" class="btn btn-outline-success"><i class="fas fa-list me-1"></i> Lihat Pesanan Saya</a>
        </div>
    </div>
</body>
</html>
