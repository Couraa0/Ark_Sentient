<?php
if (session_status() === PHP_SESSION_NONE) session_start();
require_once '../Config/config.php';

if (!isLoggedIn()) {
    header('Location: ../index.php');
    exit;
}

// Tambahkan logic pencarian sebelum query livestock
$search = '';
if (isset($_GET['search'])) {
    $search = trim($_GET['search']);
}

// Get livestock data
if ($search !== '') {
    $stmt = $conn->prepare("
        SELECT l.*, c.name as category_name, u.full_name as farmer_name 
        FROM livestock l 
        JOIN categories c ON l.category_id = c.id 
        JOIN users u ON l.farmer_id = u.id 
        WHERE l.status = 'available'
        AND (
            l.name LIKE ? OR
            l.breed LIKE ? OR
            l.location LIKE ? OR
            c.name LIKE ?
        )
        ORDER BY l.created_at DESC
        LIMIT 12
    ");
    $searchParam = "%$search%";
    $stmt->execute([$searchParam, $searchParam, $searchParam, $searchParam]);
} else {
    $stmt = $conn->prepare("
        SELECT l.*, c.name as category_name, u.full_name as farmer_name 
        FROM livestock l 
        JOIN categories c ON l.category_id = c.id 
        JOIN users u ON l.farmer_id = u.id 
        WHERE l.status = 'available'
        ORDER BY l.created_at DESC
        LIMIT 12
    ");
    $stmt->execute();
}
$livestock = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Get categories
$stmt = $conn->query("SELECT * FROM categories ORDER BY name");
$categories = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Get cart count
$cart_count = 0;
if (isLoggedIn()) {
    $stmt = $conn->prepare("SELECT COUNT(*) as count FROM cart WHERE user_id = ?");
    $stmt->execute([$_SESSION['user_id']]);
    $cart_count = $stmt->fetchColumn();
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ARK Sentient - Marketplace Ternak</title>
    <link href="../Asset/css/dashboard.css" rel="stylesheet">
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
     
        .hero-section {
            background: linear-gradient(135deg, var(--accent-lime) 0%, var(--accent-green) 100%);
            color: var(--white);
            padding: 3rem 0 2rem 0;
            border-radius: 0 0 32px 32px;
            box-shadow: 0 4px 24px rgba(44,85,48,0.08);
            margin-bottom: 2.5rem;
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
<body>
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg fixed-top">
        <div class="container-fluid">
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
                            <?php if ($cart_count > 0): ?>
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
    <!-- Toast for notifications -->
    <div class="toast-container position-fixed bottom-0 end-0 p-3">
        <div id="cartToast" class="toast" role="alert" aria-live="assertive" aria-atomic="true">
            <div class="toast-header">
                <i class="fas fa-shopping-cart text-success me-2"></i>
                <strong class="me-auto">Cart</strong>
                <button type="button" class="btn-close" data-bs-dismiss="toast"></button>
            </div>
            <div class="toast-body">
                Item berhasil ditambahkan ke keranjang!
            </div>
        </div>
    </div>

    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <div class="col-md-2 sidebar" style="position:fixed; top:80px; left:0; bottom:0; height:calc(100vh - 80px); z-index:1030; background:#fff; border-right:1px solid #e0e0e0; overflow-y:auto;">
                <div class="px-3">
                    <h6 class="text-muted mb-3">Marketplace Farm</h6>
                    <nav class="nav flex-column">
                        <?php
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
                        <a class="nav-link" href="../Views/cart.php">
                            <i class="fas fa-shopping-cart"></i>Cart
                        </a>
                    </nav>
                </div>
            </div>

            <!-- Main Content -->
            <div class="col-md-10" style="margin-left:16.6667%;">
                <!-- Hero Section -->
                <div class="hero-section">
                    <div class="container">
                        <div class="row align-items-center">
                            <div class="col-md-8">
                                <h1 class="display-4 fw-bold mb-3">ARK Sentient</h1>
                                <p class="lead">Aplikasi pintar berbasis AI yang dirancang khusus untuk membantu peternak memelihara kesehatan ternak, mengelola data ternak secara digital, serta menyediakan marketplace dalam satu sistem yang terintegrasi.</p>
                            </div>
                            <div class="col-md-4 text-center">
                                <i class="fas fa-cow fa-5x opacity-75"></i>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Search Bar -->
                <div class="container mb-4">
                    <div class="row justify-content-center">
                        <div class="col-md-8">
                            <form class="input-group" method="get" action="">
                                <input type="text" name="search" class="form-control form-control-lg" placeholder="Search livestock..." value="<?php echo htmlspecialchars($search); ?>">
                                <button class="btn btn-primary" type="submit">
                                    <i class="fas fa-search"></i>
                                </button>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- Livestock Grid -->
                <div class="container">
                    <h3 class="mb-4">Available Livestock</h3>
                    <div class="row">
                        <?php foreach ($livestock as $animal): ?>
                            <div class="col-lg-3 col-md-4 col-sm-6 mb-4">
                                <div class="card livestock-card">
                                    <?php if (!empty($animal['image_url'])): ?>
                                        <img src="<?php echo htmlspecialchars($animal['image_url']); ?>" alt="<?php echo htmlspecialchars($animal['name']); ?>" class="card-img-top livestock-image" style="object-fit:cover;height:180px;">
                                    <?php else: ?>
                                        <div class="livestock-image card-img-top d-flex align-items-center justify-content-center" style="height:180px;background:#f8f9fa;">
                                            <i class="fas fa-cow fa-3x text-muted"></i>
                                        </div>
                                    <?php endif; ?>
                                    <div class="card-body">
                                        <h5 class="card-title"><?php echo htmlspecialchars($animal['name']); ?></h5>
                                        <p class="text-muted mb-2">
                                            <small>Age: <?php echo $animal['age_months']; ?> months</small><br>
                                            <small>Weight: <?php echo $animal['weight_kg']; ?> kg</small><br>
                                            <small>Breed: <?php echo htmlspecialchars($animal['breed']); ?></small>
                                        </p>
                                        <p class="mb-2">
                                            <span class="badge badge-location">
                                                <i class="fas fa-map-marker-alt me-1"></i><?php echo htmlspecialchars($animal['location']); ?>
                                            </span>
                                        </p>
                                        <div class="d-flex justify-content-between align-items-center">
                                            <span class="price-tag"><?php echo formatRupiah($animal['price']); ?></span>
                                        </div>
                                        <div class="mt-3">
                                            <button class="btn btn-primary btn-sm me-2" onclick="addToCart(<?php echo $animal['id']; ?>)">
                                                <i class="fas fa-cart-plus me-1"></i>Add to Cart
                                            </button>
                                            <a href="livestock_detail.php?id=<?php echo $animal['id']; ?>" class="btn btn-outline-success btn-sm">
                                                See More...
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function addToCart(livestockId) {
            fetch('../Controllers/add_to_cart.php', { 
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: new URLSearchParams({ livestock_id: livestockId }).toString()
            })
            .then(async response => {
                if (!response.ok) {
                    throw new Error('Network response was not ok');
                }
                let data;
                try {
                    data = await response.json();
                } catch (e) {
                    throw new Error('Invalid JSON response');
                }
                return data;
            })
            .then(function(data) {
                if (data.success) {
                    // Show toast notification
                    const toastElement = document.getElementById('cartToast');
                    const toast = new bootstrap.Toast(toastElement);
                    toast.show();
                    
                    // Update cart count badge
                    updateCartBadge(data.cart_count);
                } else {
                    alert(data.message || 'Gagal menambahkan ke keranjang');
                }
            })
            .catch(function(error) {
                console.error('Error:', error);
                alert('Terjadi kesalahan saat menambahkan ke keranjang: ' + error.message);
            });
        }

        function updateCartBadge(count) {
            const cartLink = document.getElementById('navbarCartLink');
            let badge = cartLink.querySelector('.badge');
            if (count > 0) {
                if (!badge) {
                    badge = document.createElement('span');
                    badge.className = "position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger";
                    cartLink.appendChild(badge);
                }
                badge.textContent = count;
                badge.style.display = 'inline-block';
            } else {
                if (badge) {
                    badge.style.display = 'none';
                }
            }
        }
    </script>

</body>
</html>