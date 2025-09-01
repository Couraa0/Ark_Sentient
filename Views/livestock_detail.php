<?php
if (session_status() === PHP_SESSION_NONE) session_start();
require_once '../Config/config.php';

if (!isLoggedIn()) {
    header('Location: ../index.php');
    exit;
}

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$stmt = $conn->prepare("
    SELECT l.*, c.name as category_name, u.full_name as farmer_name
    FROM livestock l
    JOIN categories c ON l.category_id = c.id
    JOIN users u ON l.farmer_id = u.id
    WHERE l.id = ?
    LIMIT 1
");
$stmt->execute([$id]);
$animal = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$animal) {
    echo "<div class='container mt-5'><div class='alert alert-danger'>Produk tidak ditemukan.</div></div>";
    exit;
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Detail Ternak - <?php echo htmlspecialchars($animal['name']); ?></title>
    <link href="../Asset/css/dashboard.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        .detail-img {
            width: 100%;
            max-height: 350px;
            object-fit: cover;
            border-radius: 12px;
            background: #f8f9fa;
        }
        .detail-card {
            box-shadow: 0 4px 24px rgba(44,85,48,0.08);
            border-radius: 16px;
            border: none;
        }
        .badge-location {
            background: #e8f5e9;
            color: #388e3c;
            font-size: 0.95em;
        }
    </style>
</head>
<body>
    <!-- Navbar -->
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
                    <!-- ...existing nav items... -->
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
                        <!-- ...existing user dropdown... -->
                    </li>
                </ul>
            </div>
        </div>
    </nav>
    <div style="height:80px"></div>
    <div class="container my-5">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="card detail-card p-4">
                    <div class="row g-4">
                        <div class="col-md-5">
                            <?php if (!empty($animal['image_url'])): ?>
                                <img src="<?php echo htmlspecialchars($animal['image_url']); ?>" alt="<?php echo htmlspecialchars($animal['name']); ?>" class="detail-img mb-3">
                            <?php else: ?>
                                <div class="d-flex align-items-center justify-content-center detail-img mb-3" style="height:220px;">
                                    <i class="fas fa-cow fa-4x text-muted"></i>
                                </div>
                            <?php endif; ?>
                        </div>
                        <div class="col-md-7">
                            <h2 class="fw-bold mb-2"><?php echo htmlspecialchars($animal['name']); ?></h2>
                            <div class="mb-2">
                                <span class="badge bg-success"><i class="fas fa-tag me-1"></i><?php echo htmlspecialchars($animal['category_name']); ?></span>
                                <span class="badge badge-location ms-2"><i class="fas fa-map-marker-alt me-1"></i><?php echo htmlspecialchars($animal['location']); ?></span>
                            </div>
                            <h4 class="text-success mb-3"><?php echo formatRupiah($animal['price']); ?></h4>
                            <ul class="list-unstyled mb-3">
                                <li><b>Umur:</b> <?php echo $animal['age_months']; ?> bulan</li>
                                <li><b>Berat:</b> <?php echo $animal['weight_kg']; ?> kg</li>
                                <li><b>Ras:</b> <?php echo htmlspecialchars($animal['breed']); ?></li>
                                <li><b>Peternak:</b> <?php echo htmlspecialchars($animal['farmer_name']); ?></li>
                            </ul>
                            <?php if (!empty($animal['description'])): ?>
                                <div class="mb-3">
                                    <b>Deskripsi:</b>
                                    <div><?php echo nl2br(htmlspecialchars($animal['description'])); ?></div>
                                </div>
                            <?php endif; ?>
                            <div class="d-flex gap-2 mt-4">
                                <a href="dashboard.php" class="btn btn-success"><i class="fas fa-arrow-left"></i> Kembali ke Marketplace</a>
                                <button class="btn btn-primary" onclick="addToCart(<?php echo $animal['id']; ?>)">
                                    <i class="fas fa-cart-plus me-1"></i>Tambah ke Keranjang
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
                <div id="addCartMsg" class="mt-3"></div>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function addToCart(livestockId) {
            fetch('../Controllers/add_to_cart.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: new URLSearchParams({ livestock_id: livestockId }).toString()
            })
            .then(async response => {
                let data;
                try { data = await response.json(); } catch (e) { data = {success:false, message:'Gagal'}; }
                return data;
            })
            .then(function(data) {
                const msg = document.getElementById('addCartMsg');
                if (data.success) {
                    msg.innerHTML = '<div class="alert alert-success">Berhasil ditambahkan ke keranjang!</div>';
                } else {
                    msg.innerHTML = '<div class="alert alert-danger">'+(data.message||'Gagal menambah ke keranjang')+'</div>';
                }
            })
            .catch(function() {
                document.getElementById('addCartMsg').innerHTML = '<div class="alert alert-danger">Terjadi kesalahan.</div>';
            });
        }
    </script>
</body>
</html>
