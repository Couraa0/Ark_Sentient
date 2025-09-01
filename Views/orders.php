<?php
require_once '../Config/config.php';
if (session_status() === PHP_SESSION_NONE) session_start();
if (!isLoggedIn()) {
    header('Location: ../index.php');
    exit;
}

// Ambil data pesanan user
$stmt = $conn->prepare("SELECT * FROM orders WHERE user_id = ? ORDER BY created_at DESC");
$stmt->execute([$_SESSION['user_id']]);
$orders = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Fitur pencarian sederhana
$search = isset($_GET['search']) ? trim($_GET['search']) : '';
if ($search !== '') {
    $stmt = $conn->prepare("SELECT * FROM orders WHERE user_id = ? AND order_number LIKE ? ORDER BY created_at DESC");
    $stmt->execute([$_SESSION['user_id'], "%$search%"]);
    $orders = $stmt->fetchAll(PDO::FETCH_ASSOC);
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pesanan Saya - ARK Sentient</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body { background: #f8f9fa; }
        .order-card {
            border-radius: 18px;
            box-shadow: 0 4px 24px rgba(44,85,48,0.08);
            border: none;
            margin-bottom: 2rem;
        }
        .order-status-badge {
            font-size: 0.95em;
            padding: 0.4em 1em;
        }
        .order-header {
            background: linear-gradient(135deg, #AEEA00 0%, #00C853 100%);
            color: #fff;
            border-radius: 18px 18px 0 0;
            padding: 1.2rem 1.5rem;
        }
        .order-body {
            padding: 1.5rem;
        }
        .order-items-list {
            margin-bottom: 0.5rem;
        }
        .order-items-list li {
            margin-bottom: 0.3rem;
        }
        .order-footer {
            background: #f1f8e9;
            border-radius: 0 0 18px 18px;
            padding: 1rem 1.5rem;
        }
        .search-bar {
            max-width: 350px;
            margin-bottom: 2rem;
        }
        .no-orders {
            text-align: center;
            color: #888;
            margin: 3rem 0;
        }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg fixed-top" style="background: linear-gradient(135deg, #AEEA00 0%, #00C853 100%);">
        <div class="container">
            <a class="navbar-brand text-success d-flex align-items-center" href="home.php">
                <img src="../asset/img/Logo.png" alt="Logo" style="height:32px;width:auto;">
                ARK Sentient
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item"><a class="nav-link" href="dashboard.php"><i class="fas fa-store me-1"></i>Marketplace</a></li>
                    <li class="nav-item"><a class="nav-link" href="priksaternak.php"><i class="fas fa-stethoscope me-1"></i>Pemeriksaan</a></li>
                    <li class="nav-item"><a class="nav-link" href="smartasis.php"><i class="fas fa-robot me-1"></i>Smart Assistant</a></li>
                    <li class="nav-item"><a class="nav-link" href="about.php"><i class="fas fa-info-circle me-1"></i>About</a></li>
                </ul>
                <ul class="navbar-nav">
                    <li class="nav-item">
                        <a class="nav-link position-relative" href="cart.php" id="navbarCartLink">
                            <i class="fas fa-shopping-cart me-1"></i>
                            Cart
                            <?php
                            $cart_count = 0;
                            if (isset($_SESSION['user_id'])) {
                                $stmtCart = $conn->prepare("SELECT SUM(quantity) FROM cart WHERE user_id = ?");
                                $stmtCart->execute([$_SESSION['user_id']]);
                                $cart_count = (int)$stmtCart->fetchColumn();
                            }
                            if ($cart_count > 0): ?>
                                <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
                                    <?php echo $cart_count; ?>
                                </span>
                            <?php endif; ?>
                        </a>
                    </li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown">
                            <i class="fas fa-user-circle me-1"></i>
                            <?php
                            $user_display = isset($_SESSION['full_name']) && $_SESSION['full_name'] !== ''
                                ? htmlspecialchars($_SESSION['full_name'])
                                : (isset($_SESSION['username']) ? htmlspecialchars($_SESSION['username']) : 'User');
                            echo $user_display;
                            ?>
                        </a>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="profile.php"><i class="fas fa-user me-2"></i>Profile</a></li>
                            <li><a class="dropdown-item active" href="orders.php"><i class="fas fa-list me-2"></i>My Orders</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item" href="logout.php"><i class="fas fa-sign-out-alt me-2"></i>Logout</a></li>
                        </ul>
                    </li>
                </ul>
            </div>
        </div>
    </nav>
    <div style="height:80px"></div>
    <div class="container">
        <h2 class="mb-4 mt-3">Pesanan Saya</h2>
        <form class="d-flex search-bar mb-4" method="get" action="">
            <input class="form-control me-2" type="search" name="search" placeholder="Cari No. Order..." value="<?php echo htmlspecialchars($search); ?>">
            <button class="btn btn-success" type="submit"><i class="fas fa-search"></i></button>
        </form>
        <?php if (empty($orders)): ?>
            <div class="no-orders">
                <i class="fas fa-box-open fa-3x mb-3"></i>
                <h5>Belum ada pesanan.</h5>
                <a href="dashboard.php" class="btn btn-primary mt-3"><i class="fas fa-store me-1"></i>Belanja Sekarang</a>
            </div>
        <?php else: ?>
            <?php foreach ($orders as $order): ?>
                <div class="card order-card">
                    <div class="order-header d-flex justify-content-between align-items-center">
                        <div>
                            <span class="fw-bold">No. Order:</span> <?php echo htmlspecialchars($order['order_number']); ?>
                        </div>
                        <span class="order-status-badge badge 
                            <?php
                                if ($order['payment_status'] == 'paid' || $order['payment_status'] == 'completed') echo 'bg-success';
                                elseif ($order['payment_status'] == 'pending') echo 'bg-warning text-dark';
                                else echo 'bg-danger';
                            ?>">
                            <?php echo ucfirst($order['payment_status']); ?>
                        </span>
                    </div>
                    <div class="order-body">
                        <div class="mb-2"><b>Tanggal:</b> <?php echo date('d M Y H:i', strtotime($order['created_at'])); ?></div>
                        <div class="mb-2"><b>Status Pesanan:</b> <?php echo ucfirst($order['order_status']); ?></div>
                        <div class="mb-2"><b>Total:</b> <?php echo formatRupiah($order['final_amount']); ?></div>
                        <div class="mb-2"><b>Metode Pembayaran:</b> <?php echo htmlspecialchars($order['payment_method']); ?></div>
                        <div class="mb-2"><b>Alamat Pengiriman:</b> <?php echo htmlspecialchars($order['shipping_address']); ?></div>
                        <div class="mb-2"><b>Catatan:</b> <?php echo htmlspecialchars($order['notes']); ?></div>
                        <div class="mb-2"><b>Item:</b>
                            <ul class="order-items-list">
                                <?php
                                $stmtItems = $conn->prepare("SELECT oi.*, l.name FROM order_items oi JOIN livestock l ON oi.livestock_id = l.id WHERE oi.order_id = ?");
                                $stmtItems->execute([$order['id']]);
                                $items = $stmtItems->fetchAll(PDO::FETCH_ASSOC);
                                foreach ($items as $item):
                                ?>
                                    <li>
                                        <?php echo htmlspecialchars($item['name']); ?> (<?php echo $item['quantity']; ?>x) - <?php echo formatRupiah($item['total_price']); ?>
                                    </li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    </div>
                    <div class="order-footer d-flex justify-content-between align-items-center">
                        <span><i class="fas fa-clock me-1"></i>Terakhir update: <?php echo date('d M Y H:i', strtotime($order['updated_at'])); ?></span>
                        <a href="order_summary.php?order_id=<?php echo urlencode($order['order_number']); ?>" class="btn btn-outline-success btn-sm">
                            <i class="fas fa-eye"></i> Lihat Detail
                        </a>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
        <div class="text-center mt-4">
            <a href="dashboard.php" class="btn btn-secondary"><i class="fas fa-arrow-left"></i> Kembali ke Marketplace</a>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>