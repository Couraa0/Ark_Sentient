<?php
require_once '../Config/config.php';
if (session_status() === PHP_SESSION_NONE) session_start();
if (!isLoggedIn()) {
    header('Location: ../index.php');
    exit;
}
// Ambil data user dari database
$stmt = $conn->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$_SESSION['user_id']]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profil Saya - ARK Sentient</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        body { background: #f8f9fa; }
        .profile-card {
            max-width: 600px;
            margin: 2rem auto;
            border-radius: 18px;
            box-shadow: 0 4px 24px rgba(44,85,48,0.08);
            border: none;
            background: #fff;
        }
        .profile-header {
            background: linear-gradient(135deg, #AEEA00 0%, #00C853 100%);
            color: #fff;
            border-radius: 18px 18px 0 0;
            padding: 2rem 1.5rem 1.5rem 1.5rem;
            text-align: center;
        }
        .profile-avatar {
            width: 110px;
            height: 110px;
            border-radius: 50%;
            background: #e8f5e9;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 3rem;
            color: #2c5530;
            margin: -60px auto 1rem auto;
            box-shadow: 0 4px 16px rgba(44,85,48,0.08);
            border: 5px solid #fff;
        }
        .profile-body {
            padding: 2rem 2rem 1.5rem 2rem;
        }
        .profile-info-list {
            list-style: none;
            padding: 0;
            margin: 0 0 1.5rem 0;
        }
        .profile-info-list li {
            margin-bottom: 1.2rem;
            display: flex;
            align-items: center;
            gap: 1rem;
        }
        .profile-info-list .icon {
            color: #00C853;
            font-size: 1.3rem;
            width: 32px;
            text-align: center;
        }
        .profile-info-list .label {
            font-weight: 500;
            color: #388e3c;
            min-width: 110px;
        }
        .profile-info-list .value {
            color: #333;
            font-weight: 600;
        }
        .profile-actions {
            display: flex;
            gap: 1rem;
            justify-content: center;
            margin-bottom: 1rem;
        }
        .profile-actions .btn {
            min-width: 140px;
        }
        .profile-footer {
            background: #f1f8e9;
            border-radius: 0 0 18px 18px;
            padding: 1rem 1.5rem;
            text-align: center;
            color: #666;
            font-size: 0.95rem;
        }
        @media (max-width: 600px) {
            .profile-card { padding: 0; }
            .profile-body { padding: 1.2rem; }
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
                            <li><a class="dropdown-item active" href="profile.php"><i class="fas fa-user me-2"></i>Profile</a></li>
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
    <div class="profile-card shadow">
        <div class="profile-header">
            <div class="profile-avatar">
                <i class="fas fa-user"></i>
            </div>
            <h3 class="mb-1"><?php echo htmlspecialchars($user['full_name']); ?></h3>
            <div class="mb-2" style="font-size:1.1rem;"><?php echo htmlspecialchars($user['role']); ?></div>
        </div>
        <div class="profile-body">
            <ul class="profile-info-list">
                <li>
                    <span class="icon"><i class="fas fa-user"></i></span>
                    <span class="label">Username</span>
                    <span class="value"><?php echo htmlspecialchars($user['username']); ?></span>
                </li>
                <li>
                    <span class="icon"><i class="fas fa-envelope"></i></span>
                    <span class="label">Email</span>
                    <span class="value"><?php echo htmlspecialchars($user['email']); ?></span>
                </li>
                <li>
                    <span class="icon"><i class="fas fa-phone"></i></span>
                    <span class="label">Telepon</span>
                    <span class="value"><?php echo htmlspecialchars($user['phone'] ?? '-'); ?></span>
                </li>
                <li>
                    <span class="icon"><i class="fas fa-map-marker-alt"></i></span>
                    <span class="label">Alamat</span>
                    <span class="value"><?php echo htmlspecialchars($user['address'] ?? '-'); ?></span>
                </li>
                <li>
                    <span class="icon"><i class="fas fa-calendar-plus"></i></span>
                    <span class="label">Bergabung</span>
                    <span class="value"><?php echo date('d M Y', strtotime($user['created_at'])); ?></span>
                </li>
            </ul>
            <div class="profile-actions">
                <a href="#" class="btn btn-success"><i class="fas fa-edit"></i> Edit Profil</a>
                <a href="#" class="btn btn-outline-success"><i class="fas fa-key"></i> Ubah Password</a>
            </div>
        </div>
        <div class="profile-footer">
            <i class="fas fa-info-circle me-1"></i>
            Data profil Anda aman dan hanya dapat diubah oleh Anda sendiri.
        </div>
    </div>
    <div class="text-center mb-4">
        <a href="dashboard.php" class="btn btn-secondary"><i class="fas fa-arrow-left"></i> Kembali ke Marketplace</a>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>