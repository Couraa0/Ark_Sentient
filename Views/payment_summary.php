<?php
require_once '../Config/config.php';
if (session_status() === PHP_SESSION_NONE) session_start();

if (!isLoggedIn()) {
    redirectTo('index.php');
}

$order_number = isset($_GET['order']) ? $_GET['order'] : '';
if (!$order_number) {
    header('Location: dashboard.php');
    exit;
}

// Get order data
$stmt = $conn->prepare("SELECT * FROM orders WHERE order_number = ? AND user_id = ?");
$stmt->execute([$order_number, $_SESSION['user_id']]);
$order = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$order) {
    echo "<div class='container mt-5'><div class='alert alert-danger'>Order tidak ditemukan.</div></div>";
    exit;
}

// Get order items
$stmt = $conn->prepare("
    SELECT oi.*, l.name 
    FROM order_items oi
    JOIN livestock l ON oi.livestock_id = l.id
    WHERE oi.order_id = ?
");
$stmt->execute([$order['id']]);
$order_items = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Ringkasan Pembayaran - ARK Sentient</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="../Asset/css/checkout.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        /* ...copy navbar/footer style from checkout.php for consistency... */
        :root {
            --primary-green: #2c5530;
            --secondary-green: #1e3a21;
            --accent-lime: #AEEA00;
            --accent-green: #00C853;
            --light-gray: #f8f9fa;
            --text-dark: #333333;
            --text-muted: #666666;
            --white: #ffffff;
            --shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
            --transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }
        .navbar {
            background: linear-gradient(135deg, var(--accent-lime) 0%, var(--accent-green) 100%) !important;
            backdrop-filter: blur(20px);
            box-shadow: var(--shadow);
            padding: 1rem 0;
            transition: var(--transition);
        }
        .navbar-brand {
            font-weight: 700;
            color: var(--primary-green) !important;
            font-size: 1.5rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        .navbar-brand img {
            height: 32px;
            width: auto;
            transition: var(--transition);
        }
        .footer {
            background: linear-gradient(135deg, var(--primary-green) 0%, var(--secondary-green) 100%);
            color: var(--white);
            padding: 3rem 0 1.5rem;
        }
        .footer h5, .footer h6 {
            color: var(--white);
            font-weight: 600;
            margin-bottom: 1rem;
        }
        .footer .list-unstyled li {
            margin-bottom: 0.5rem;
        }
        .footer .list-unstyled a {
            color: rgba(255, 255, 255, 0.8);
            text-decoration: none;
            transition: var(--transition);
        }
        .footer .list-unstyled a:hover {
            color: var(--accent-lime);
            transform: translateX(5px);
        }
        .social-links a {
            color: rgba(255, 255, 255, 0.8);
            font-size: 1.5rem;
            margin-right: 1rem;
            transition: var(--transition);
        }
        .social-links a:hover {
            color: var(--accent-lime);
            transform: translateY(-3px);
        }
        .footer hr {
            border-top: 1px solid rgba(174, 234, 0, 0.3);
            margin: 2rem 0;
        }
    </style>
</head>
<body>
    <!-- Navbar -->
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
            <h3 class="mb-2">Pembayaran Berhasil!</h3>
            <p>Terima kasih telah melakukan pembayaran di <strong>ARK Sentient</strong>.</p>
        </div>
        <div class="card p-4 mb-4">
            <h4 class="mb-3">Ringkasan Pembayaran</h4>
            <ul class="list-unstyled mb-3">
                <li><strong>No. Order:</strong> <?php echo htmlspecialchars($order['order_number']); ?></li>
                <li><strong>Total:</strong> <?php echo formatRupiah($order['final_amount']); ?></li>
                <li><strong>Metode Pembayaran:</strong> <?php echo htmlspecialchars($order['payment_method']); ?></li>
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
    <!-- Footer -->
    <footer class="footer">
        <div class="container">
            <div class="row">
                <div class="col-lg-4 col-md-6 mb-4">
                    <h5>
                        <i class="fas fa-leaf me-2"></i>
                        ARK Sentient
                    </h5>
                    <p class="mb-3">Solusi pintar untuk peternakan modern Indonesia dengan teknologi AI terdepan.</p>
                    <div class="social-links">
                        <a href="#" aria-label="Facebook"><i class="fab fa-facebook-f"></i></a>
                        <a href="#" aria-label="Twitter"><i class="fab fa-twitter"></i></a>
                        <a href="#" aria-label="Instagram"><i class="fab fa-instagram"></i></a>
                        <a href="#" aria-label="LinkedIn"><i class="fab fa-linkedin-in"></i></a>
                    </div>
                </div>
                <div class="col-lg-2 col-md-6 mb-4">
                    <h6>Menu</h6>
                    <ul class="list-unstyled">
                        <li><a href="home.php">Beranda</a></li>
                        <li><a href="#features">Fitur</a></li>
                        <li><a href="about.php">Tentang</a></li>
                        <li><a href="http://wa.me/6287871310560">Kontak</a></li>
                    </ul>
                </div>
                <div class="col-lg-3 col-md-6 mb-4">
                    <h6>Layanan</h6>
                    <ul class="list-unstyled">
                        <li><a href="dashboard.php">Marketplace Ternak</a></li>
                        <li><a href="priksaternak.php">Pemeriksaan Ternak</a></li>
                        <li><a href="smartasis.php">Smart Assistant</a></li>
                    </ul>
                </div>
                <div class="col-lg-3 col-md-6 mb-4">
                    <h6>Dukungan</h6>
                    <ul class="list-unstyled">
                        <li><a href="#help">Pusat Bantuan</a></li>
                        <li><a href="#faq">FAQ</a></li>
                        <li><a href="#support">Support 24/7</a></li>
                        <li><a href="#documentation">Dokumentasi</a></li>
                    </ul>
                </div>
            </div>
            <hr>
            <div class="row align-items-center">
                <div class="col-md-6">
                    <p class="mb-0">&copy; 2025 ARK Sentient. Semua hak dilindungi.</p>
                </div>
                <div class="col-md-6 text-md-end">
                    <p class="mb-0">
                        Dibuat dengan <i class="fas fa-heart text-white"></i> untuk peternak Indonesia
                    </p>
                </div>
            </div>
        </div>
    </footer>
</body>
</html>
