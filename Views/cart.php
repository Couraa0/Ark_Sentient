<?php
require_once '../Config/config.php';
if (session_status() === PHP_SESSION_NONE) session_start();

if (!isLoggedIn()) {
    redirectTo('index.php');
}

// Get cart items
$stmt = $conn->prepare("
    SELECT c.*, l.name, l.breed, l.price, l.age_months, l.weight_kg, l.location, l.image_url
    FROM cart c 
    JOIN livestock l ON c.livestock_id = l.id 
    WHERE c.user_id = ?
    ORDER BY c.added_at DESC
");
$stmt->execute([$_SESSION['user_id']]);
$cart_items = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Calculate totals
$subtotal = 0;
foreach ($cart_items as $item) {
    $subtotal += $item['price'] * $item['quantity'];
}

$shipping_cost = 200000; // Fixed shipping cost
$tax_rate = 0.10; // 10% tax
$tax_amount = $subtotal * $tax_rate;
$total = $subtotal + $shipping_cost + $tax_amount;

// Handle cart updates
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['update_quantity'])) {
        $cart_id = (int)$_POST['cart_id'];
        $new_quantity = (int)$_POST['quantity'];
        
        if ($new_quantity > 0) {
            $stmt = $conn->prepare("UPDATE cart SET quantity = ? WHERE id = ? AND user_id = ?");
            $stmt->execute([$new_quantity, $cart_id, $_SESSION['user_id']]);
        } else {
            $stmt = $conn->prepare("DELETE FROM cart WHERE id = ? AND user_id = ?");
            $stmt->execute([$cart_id, $_SESSION['user_id']]);
        }
        
        redirectTo('cart.php');
    }
    
    if (isset($_POST['remove_item'])) {
        $cart_id = (int)$_POST['cart_id'];
        $stmt = $conn->prepare("DELETE FROM cart WHERE id = ? AND user_id = ?");
        $stmt->execute([$cart_id, $_SESSION['user_id']]);
        
        redirectTo('cart.php');
    }
}

// Tambahkan Midtrans Snap JS
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Shopping Cart - ARK Sentient</title>
    <link href="../Asset/css/cart.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <script src="https://app.sandbox.midtrans.com/snap/snap.js" data-client-key="SB-Mid-client-vVJ0R4QSenoK7QLI"></script>
    <style>
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
        .navbar.scrolled {
            padding: 0.5rem 0;
            background: rgba(255, 255, 255, 0.95) !important;
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
        .navbar-nav .nav-link {
            color: var(--primary-green) !important;
            font-weight: 500;
            padding: 0.5rem 1rem !important;
            border-radius: 8px;
            transition: var(--transition);
            position: relative;
        }
        .navbar-nav .nav-link:hover {
            background: rgba(44, 85, 48, 0.1);
            transform: translateY(-1px);
        }
        .navbar-nav .nav-link::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 50%;
            width: 0;
            height: 2px;
            background: var(--primary-green);
            transition: var(--transition);
            transform: translateX(-50%);
        }
        .navbar-nav .nav-link:hover::after {
            width: 80%;
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
        @media (max-width: 768px) {
            .navbar-brand {
                font-size: 1.1rem;
            }
            .footer {
                padding: 2rem 0 1rem;
            }
        }
    </style>
</head>
<body class="bg-light">
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg fixed-top">
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
                    <li class="nav-item">
                        <a class="nav-link" href="dashboard.php">
                            <i class="fas fa-store me-1"></i>
                            Marketplace
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="../Views/priksaternak.php">
                            <i class="fas fa-stethoscope me-1"></i>
                            Pemeriksaan
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="../Views/smartasis.php">
                            <i class="fas fa-robot me-1"></i>
                            Smart Assistant
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="../Views/about.php">
                            <i class="fas fa-info-circle me-1"></i>
                            About
                        </a>
                    </li>
                </ul>
                <ul class="navbar-nav">
                    <li class="nav-item">
                        <a class="nav-link position-relative" href="cart.php" id="navbarCartLink">
                            <i class="fas fa-shopping-cart me-1"></i>
                            Cart
                            <?php
                            // Hitung jumlah item di cart
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
                            <li><a class="dropdown-item" href="orders.php"><i class="fas fa-list me-2"></i>My Orders</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item" href="logout.php"><i class="fas fa-sign-out-alt me-2"></i>Logout</a></li>
                        </ul>
                    </li>
                </ul>
            </div>
        </div>
    </nav>
    <div style="height:80px"></div>

    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <div class="col-md-2 sidebar">
                <div class="px-3">
                    <h6 class="text-muted mb-3">Marketplace Farm</h6>
                    <nav class="nav flex-column">
                        <?php
                        // Ambil kategori dari database
                        $stmt = $conn->query("SELECT * FROM categories ORDER BY name");
                        $categories = $stmt->fetchAll(PDO::FETCH_ASSOC);

                        // Mapping kategori ke icon cart.php
                        $iconMap = [
                            'cow' => 'fa-cow',
                            'goat' => 'fa-horse',
                            'chicken' => 'fa-kiwi-bird',
                            'seafood' => 'fa-fish'
                        ];
                        foreach ($categories as $category):
                            // Tentukan icon berdasarkan nama kategori (case-insensitive)
                            $icon = 'fa-cow'; // default
                            $name = strtolower($category['name']);
                            if (strpos($name, 'goat') !== false) $icon = 'fa-horse';
                            elseif (strpos($name, 'chicken') !== false) $icon = 'fa-kiwi-bird';
                            elseif (strpos($name, 'seafood') !== false || strpos($name, 'ikan') !== false) $icon = 'fa-fish';
                        ?>
                            <a class="nav-link" href="category.php?id=<?php echo $category['id']; ?>">
                                <i class="fas <?php echo $icon; ?>"></i><?php echo $category['name']; ?>
                            </a>
                        <?php endforeach; ?>
                        <a class="nav-link active" href="cart.php">
                            <i class="fas fa-shopping-cart"></i>Cart
                        </a>
                    </nav>
                </div>
            </div>

            <!-- Main Content -->
            <div class="col-md-10">
                <div class="container mt-4">
                    <div class="row">
                        <div class="col-md-8">
                            <h3 class="mb-4">Shopping Cart</h3>
                            
                            <?php if (empty($cart_items)): ?>
                                <div class="text-center py-5">
                                    <i class="fas fa-shopping-cart fa-3x text-muted mb-3"></i>
                                    <h5 class="text-muted">Your cart is empty</h5>
                                    <a href="dashboard.php" class="btn btn-primary mt-3">Continue Shopping</a>
                                </div>
                            <?php else: ?>
                                <?php foreach ($cart_items as $item): ?>
                                    <div class="cart-item">
                                        <div class="row align-items-center">
                                            <div class="col-md-2">
                                                <?php if (!empty($item['image_url'])): ?>
                                                    <img src="<?php echo htmlspecialchars($item['image_url']); ?>" alt="<?php echo htmlspecialchars($item['name']); ?>" class="img-fluid rounded" style="width:100px;height:100px;object-fit:cover;">
                                                <?php else: ?>
                                                    <div class="bg-light d-flex align-items-center justify-content-center" 
                                                         style="width: 100px; height: 100px; border-radius: 8px;">
                                                        <i class="fas fa-cow fa-2x text-muted"></i>
                                                    </div>
                                                <?php endif; ?>
                                            </div>
                                            <div class="col-md-4">
                                                <h5 class="mb-1"><?php echo htmlspecialchars($item['name']); ?></h5>
                                                <p class="text-muted mb-1">
                                                    <small>Breed: <?php echo htmlspecialchars($item['breed']); ?></small><br>
                                                    <small>Age: <?php echo $item['age_months']; ?> months</small><br>
                                                    <small>Weight: <?php echo $item['weight_kg']; ?> kg</small><br>
                                                    <small>Location: <?php echo htmlspecialchars($item['location']); ?></small>
                                                </p>
                                                <p class="mb-0">
                                                    <strong class="text-success"><?php echo formatRupiah($item['price']); ?></strong>
                                                </p>
                                            </div>
                                            <div class="col-md-3">
                                                <div class="quantity-control">
                                                    <form method="POST" class="d-inline">
                                                        <input type="hidden" name="cart_id" value="<?php echo $item['id']; ?>">
                                                        <input type="hidden" name="quantity" value="<?php echo $item['quantity'] - 1; ?>">
                                                        <button type="submit" name="update_quantity" class="quantity-btn">
                                                            <i class="fas fa-minus"></i>
                                                        </button>
                                                    </form>
                                                    <span class="mx-3 fw-bold"><?php echo $item['quantity']; ?></span>
                                                    <form method="POST" class="d-inline">
                                                        <input type="hidden" name="cart_id" value="<?php echo $item['id']; ?>">
                                                        <input type="hidden" name="quantity" value="<?php echo $item['quantity'] + 1; ?>">
                                                        <button type="submit" name="update_quantity" class="quantity-btn">
                                                            <i class="fas fa-plus"></i>
                                                        </button>
                                                    </form>
                                                </div>
                                            </div>
                                            <div class="col-md-2">
                                                <p class="mb-2 fw-bold"><?php echo formatRupiah($item['price'] * $item['quantity']); ?></p>
                                                <form method="POST" class="d-inline">
                                                    <input type="hidden" name="cart_id" value="<?php echo $item['id']; ?>">
                                                    <button type="submit" name="remove_item" class="btn btn-sm btn-outline-danger">
                                                        <i class="fas fa-trash"></i>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </div>
                        
                        <?php if (!empty($cart_items)): ?>
                            <div class="col-md-4">
                                <div class="summary-card">
                                    <h5 class="mb-3">Order Summary</h5>
                                    
                                    <div class="d-flex justify-content-between mb-2">
                                        <span>Subtotal</span>
                                        <span><?php echo formatRupiah($subtotal); ?></span>
                                    </div>
                                    
                                    <div class="d-flex justify-content-between mb-2">
                                        <span>Shipping</span>
                                        <span><?php echo formatRupiah($shipping_cost); ?></span>
                                    </div>
                                    
                                    <div class="d-flex justify-content-between mb-2">
                                        <span>Taxes (10%)</span>
                                        <span><?php echo formatRupiah($tax_amount); ?></span>
                                    </div>
                                    
                                    <hr>
                                    
                                    <div class="d-flex justify-content-between mb-3">
                                        <strong>Total</strong>
                                        <strong class="text-success"><?php echo formatRupiah($total); ?></strong>
                                    </div>
                                    
                                    <button id="pay-button" class="btn btn-checkout btn-success w-100">
                                        Proceed to Payment
                                    </button>
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Midtrans Snap integration
        document.addEventListener('DOMContentLoaded', function() {
            var payBtn = document.getElementById('pay-button');
            if (payBtn) {
                payBtn.addEventListener('click', function(e) {
                    e.preventDefault();
                    fetch('../Controllers/create_midtrans_transaction.php', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json' }
                    })
                    .then(response => response.json())
                    .then(function(data) {
                        if (data.token) {
                            // Selalu gunakan token yang baru dari response, jangan cache token lama
                            snap.pay(data.token, {
                                onSuccess: function(result){
                                    window.location.href = 'order_summary.php?order_id=' + encodeURIComponent(data.order_id);
                                },
                                onPending: function(result){
                                    window.location.href = 'order_summary.php?order_id=' + encodeURIComponent(data.order_id);
                                },
                                onError: function(result){
                                    alert('Pembayaran gagal atau dibatalkan.');
                                }
                            });
                        } else {
                            alert(data.error || 'Gagal membuat transaksi pembayaran.');
                        }
                    })
                    .catch(function(error) {
                        alert('Gagal menghubungi server pembayaran.');
                    });
                });
            }
        });
    </script>

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