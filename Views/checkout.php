<?php
require_once '../Config/config.php';
if (session_status() === PHP_SESSION_NONE) session_start();

if (!isLoggedIn()) {
    redirectTo('index.php');
}

// Pastikan koneksi database menggunakan variabel yang benar
// Berdasarkan payment_summary.php, mereka menggunakan $conn, bukan $pdo
$db = isset($conn) ? $conn : (isset($pdo) ? $pdo : null);
if (!$db) {
    die('Database connection not found.');
}

// --- Helper function for input sanitization ---
if (!function_exists('sanitizeInput')) {
    function sanitizeInput($data) {
        return htmlspecialchars(trim($data), ENT_QUOTES, 'UTF-8');
    }
}
// --- End helper function ---

// Get cart items
$stmt = $db->prepare("
    SELECT c.*, l.name, l.breed, l.price, l.age_months, l.weight_kg, l.location
    FROM cart c 
    JOIN livestock l ON c.livestock_id = l.id 
    WHERE c.user_id = ?
");
$stmt->execute([$_SESSION['user_id']]);
$cart_items = $stmt->fetchAll(PDO::FETCH_ASSOC);

if (empty($cart_items)) {
    redirectTo('cart.php');
}

// Calculate totals
$subtotal = 0;
foreach ($cart_items as $item) {
    $subtotal += $item['price'] * $item['quantity'];
}

$shipping_cost = 200000;
$tax_rate = 0.10; // Pajak 10%
$tax_amount = $subtotal * $tax_rate;
$total = $subtotal + $shipping_cost + $tax_amount;

// Handle form submission - PERBAIKAN LOGIC UTAMA
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['place_order'])) {
    $payment_method = sanitizeInput($_POST['payment_method']);
    $shipping_address = sanitizeInput($_POST['shipping_address']);
    $notes = sanitizeInput($_POST['notes'] ?? '');
    
    // Validasi input
    if (empty($payment_method) || empty($shipping_address)) {
        $error = 'Please fill in all required fields.';
    } else {
        try {
            $db->beginTransaction();

            // Generate order number
            $order_number = 'ORD-' . date('Ymd') . '-' . str_pad(rand(1, 9999), 4, '0', STR_PAD_LEFT);

            // Insert order - PERBAIKAN: pastikan semua field sesuai dengan database schema
            $stmt = $db->prepare("
                INSERT INTO orders (user_id, order_number, total_amount, shipping_cost, tax_amount, final_amount, payment_method, shipping_address, notes, payment_status, order_status)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, 'pending', 'pending')
            ");
            
            $order_inserted = $stmt->execute([
                $_SESSION['user_id'], 
                $order_number, 
                $subtotal, 
                $shipping_cost, 
                $tax_amount, 
                $total, 
                $payment_method, 
                $shipping_address, 
                $notes
            ]);

            if (!$order_inserted) {
                throw new Exception('Failed to create order');
            }

            $order_id = $db->lastInsertId();
            
            if (!$order_id) {
                throw new Exception('Failed to get order ID');
            }

            // Insert order items
            foreach ($cart_items as $item) {
                $stmt = $db->prepare("
                    INSERT INTO order_items (order_id, livestock_id, quantity, unit_price, total_price)
                    VALUES (?, ?, ?, ?, ?)
                ");
                
                $item_inserted = $stmt->execute([
                    $order_id, 
                    $item['livestock_id'], 
                    $item['quantity'], 
                    $item['price'], 
                    $item['price'] * $item['quantity']
                ]);
                
                if (!$item_inserted) {
                    throw new Exception('Failed to add order item for: ' . $item['name']);
                }
            }

            // Insert payment record
            $stmt = $db->prepare("
                INSERT INTO payments (order_id, payment_method, amount, payment_status)
                VALUES (?, ?, ?, 'pending')
            ");
            
            $payment_inserted = $stmt->execute([
                $order_id, 
                $payment_method, 
                $total
            ]);
            
            if (!$payment_inserted) {
                throw new Exception('Failed to create payment record');
            }

            // Clear cart setelah order berhasil dibuat
            $stmt = $db->prepare("DELETE FROM cart WHERE user_id = ?");
            $cart_cleared = $stmt->execute([$_SESSION['user_id']]);
            
            if (!$cart_cleared) {
                throw new Exception('Failed to clear cart');
            }

            // Commit semua transaksi
            $db->commit();

            // PERBAIKAN: Gunakan session flash message untuk memastikan data tersedia di halaman berikutnya
            $_SESSION['order_success'] = true;
            $_SESSION['order_number'] = $order_number;
            $_SESSION['order_total'] = $total;

            // Redirect ke payment summary dengan parameter yang aman
            header("Location: payment_summary.php?order=" . urlencode($order_number), true, 302);
            exit();

        } catch (Exception $e) {
            // Rollback jika ada error
            $db->rollBack();
            $error = 'Failed to process order: ' . $e->getMessage();
            
            // Log error untuk debugging
            error_log("Order creation failed for user " . $_SESSION['user_id'] . ": " . $e->getMessage());
        }
    }
}

// Get user data
$stmt = $db->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$_SESSION['user_id']]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

// Jika user tidak ditemukan, redirect ke login
if (!$user) {
    session_destroy();
    redirectTo('index.php');
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout - ARK Sentient</title>
    <link href="../Asset/css/checkout.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
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

    <div class="container mt-4">
        <div class="row">
            <div class="col-md-8">
                <h3 class="mb-4">Checkout</h3>
                
                <?php if (isset($error)): ?>
                    <div class="alert alert-danger"><?php echo $error; ?></div>
                <?php endif; ?>

                <form method="POST">
                    <!-- Shipping Information -->
                    <div class="checkout-step">
                        <h5 class="mb-3"><i class="fas fa-shipping-fast me-2"></i>Shipping Information</h5>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Full Name</label>
                                <input type="text" class="form-control" value="<?php echo htmlspecialchars($user['full_name']); ?>" readonly>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Phone</label>
                                <input type="text" class="form-control" value="<?php echo htmlspecialchars($user['phone'] ?? ''); ?>" readonly>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Shipping Address</label>
                            <textarea class="form-control" rows="3" name="shipping_address" required 
                                      placeholder="Enter your complete shipping address"><?php echo htmlspecialchars($user['address'] ?? ''); ?></textarea>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Notes (Optional)</label>
                            <textarea class="form-control" rows="2" name="notes" 
                                      placeholder="Special instructions or notes for delivery"></textarea>
                        </div>
                    </div>

                    <!-- Payment Method -->
                    <div class="checkout-step">
                        <h5 class="mb-3"><i class="fas fa-credit-card me-2"></i>Payment Method</h5>
                        
                        <div class="payment-method" onclick="selectPayment('qris')">
                            <input type="radio" name="payment_method" value="qris" id="qris" required>
                            <label for="qris" class="ms-2">
                                <i class="fas fa-qrcode me-2"></i>QRIS
                                <small class="text-muted d-block">Pay with QR Code</small>
                            </label>
                        </div>
                        
                        <div class="payment-method" onclick="selectPayment('bank_transfer')">
                            <input type="radio" name="payment_method" value="bank_transfer" id="bank_transfer" required>
                            <label for="bank_transfer" class="ms-2">
                                <i class="fas fa-university me-2"></i>Bank Transfer
                                <small class="text-muted d-block">Transfer to our bank account</small>
                            </label>
                        </div>
                    </div>

                    <div class="mb-4"></div>
                    <button type="submit" name="place_order" class="btn btn-place-order btn-success btn-lg w-100 mb-4" style="margin-bottom:2.5rem;">
                        <i class="fas fa-shopping-bag me-2"></i>Place Order - <?php echo formatRupiah($total); ?>
                    </button>
                </form>
            </div>
            
            <div class="col-md-4">
                <div class="order-summary">
                    <h5 class="mb-3">Order Summary</h5>
                    
                    <!-- Items -->
                    <?php foreach ($cart_items as $item): ?>
                        <div class="cart-item">
                            <div class="d-flex justify-content-between">
                                <div class="flex-grow-1">
                                    <h6 class="mb-1"><?php echo htmlspecialchars($item['name']); ?></h6>
                                    <small class="text-muted">
                                        <?php echo htmlspecialchars($item['breed']); ?> • 
                                        <?php echo $item['age_months']; ?> months • 
                                        <?php echo $item['weight_kg']; ?> kg
                                    </small>
                                    <div class="text-muted small">Qty: <?php echo $item['quantity']; ?></div>
                                </div>
                                <div class="text-end">
                                    <strong><?php echo formatRupiah($item['price'] * $item['quantity']); ?></strong>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                    
                    <!-- Price Breakdown -->
                    <div class="price-breakdown">
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
                        <div class="d-flex justify-content-between fs-5 fw-bold">
                            <span>Total</span>
                            <span><?php echo formatRupiah($total); ?></span>
                        </div>
                    </div>
                    
                    <!-- Security Badge -->
                    <div class="text-center mt-3">
                        <small class="text-muted">
                            <i class="fas fa-shield-alt me-1"></i>
                            Secure & encrypted payment
                        </small>
                    </div>
                </div>
            </div>
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

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function selectPayment(method) {
            // Remove selected class from all payment methods
            document.querySelectorAll('.payment-method').forEach(pm => {
                pm.classList.remove('selected');
            });
            
            // Add selected class to clicked method
            event.currentTarget.classList.add('selected');
            
            // Check the radio button
            document.getElementById(method).checked = true;
        }
        
        // Auto-select payment method when radio button is clicked
        document.querySelectorAll('input[name="payment_method"]').forEach(radio => {
            radio.addEventListener('change', function() {
                document.querySelectorAll('.payment-method').forEach(pm => {
                    pm.classList.remove('selected');
                });
                this.closest('.payment-method').classList.add('selected');
            });
        });
        
        // Form validation
        document.querySelector('form').addEventListener('submit', function(e) {
            const shippingAddress = document.querySelector('[name="shipping_address"]').value.trim();
            const paymentMethod = document.querySelector('input[name="payment_method"]:checked');
            
            if (!shippingAddress) {
                e.preventDefault();
                alert('Please enter your shipping address');
                return;
            }
            
            if (!paymentMethod) {
                e.preventDefault();
                alert('Please select a payment method');
                return;
            }
            
            // Show loading state
            const submitBtn = document.querySelector('[name="place_order"]');
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Processing...';
            submitBtn.disabled = true;
        });
    </script>
</body>
</html>