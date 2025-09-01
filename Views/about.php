<?php
if (session_status() === PHP_SESSION_NONE) session_start();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>About Us - ARK Sentient</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
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
        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
            color: var(--text-dark);
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
        .navbar-nav .nav-link:hover,
        .navbar-nav .nav-link.active {
            background: rgba(44, 85, 48, 0.1);
            transform: translateY(-1px);
            color: var(--accent-green) !important;
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
        .navbar-nav .nav-link:hover::after,
        .navbar-nav .nav-link.active::after {
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
        .about-hero {
            background: linear-gradient(135deg, #e8f5e9 0%, #fffde7 100%);
            padding: 3rem 0 2rem 0;
            border-radius: 0 0 32px 32px;
            box-shadow: 0 4px 24px rgba(44,85,48,0.08);
            margin-bottom: 2.5rem;
        }
        .about-title {
            color: #2c5530;
            font-weight: 800;
            font-size: 2.5rem;
        }
        .about-desc {
            color: #388e3c;
            font-size: 1.2rem;
            max-width: 700px;
            margin: 1.5rem auto 0 auto;
        }
        .team-section {
            padding: 2rem 0 3rem 0;
        }
        .team-title {
            color: #2c5530;
            font-weight: 700;
            margin-bottom: 2rem;
            text-align: center;
        }
        .team-card {
            border: none;
            border-radius: 18px;
            box-shadow: 0 4px 24px rgba(44,85,48,0.08);
            transition: transform 0.2s;
            background: #fff;
        }
        .team-card:hover {
            transform: translateY(-8px) scale(1.03);
            box-shadow: 0 8px 32px rgba(44,85,48,0.13);
        }
        .team-avatar {
            width: 110px;
            height: 110px;
            object-fit: cover;
            border-radius: 50%;
            margin: 0 auto 1rem auto;
            background: #e8f5e9;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 3rem;
            color: #2c5530;
        }
        .team-name {
            font-weight: 600;
            color: #2c5530;
            font-size: 1.2rem;
            margin-bottom: 0.3rem;
        }
        .team-role {
            color: #00C853;
            font-size: 1rem;
            margin-bottom: 0.5rem;
        }
        .team-social a {
            color: #388e3c;
            margin: 0 0.5rem;
            font-size: 1.3rem;
            transition: color 0.2s;
        }
        .team-social a:hover {
            color: #AEEA00;
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
    <!-- Navbar -->
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
                    <li class="nav-item"><a class="nav-link" href="dashboard.php"><i class="fas fa-store me-1"></i>Marketplace</a></li>
                    <li class="nav-item"><a class="nav-link" href="priksaternak.php"><i class="fas fa-stethoscope me-1"></i>Pemeriksaan</a></li>
                    <li class="nav-item"><a class="nav-link" href="smartasis.php"><i class="fas fa-robot me-1"></i>Smart Assistant</a></li>
                    <li class="nav-item"><a class="nav-link active" href="about.php"><i class="fas fa-info-circle me-1"></i>About</a></li>
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
    <!-- About Hero -->
    <section class="about-hero text-center">
        <div class="container">
            <h1 class="about-title mb-3">Tentang ARK Sentient</h1>
            <p class="about-desc mx-auto">
                ARK Sentient adalah aplikasi pintar berbasis AI yang dirancang untuk membantu peternak Indonesia dalam memantau kesehatan ternak, mengelola data ternak secara digital, serta menyediakan marketplace dan asisten cerdas dalam satu sistem terintegrasi. Dengan teknologi terkini, ARK Sentient hadir sebagai solusi modern untuk meningkatkan produktivitas, efisiensi, dan kesejahteraan peternakan di Indonesia.
            </p>
        </div>
    </section>
    <!-- Team Section -->
    <section class="team-section">
        <div class="container">
            <h2 class="team-title mb-4">Tim Pengembang ARK Sentient</h2>
            <div class="row justify-content-center">
                <div class="col-md-4 mb-4">
                    <div class="card team-card text-center p-4">
                        <div class="team-avatar">
                            <i class="fas fa-user"></i>
                        </div>
                        <div class="team-name">Muhammad Rakha Syamputra</div>
                        <div class="team-role">Founder Ark Sentient</div>
                        <div class="team-social">
                            <a href="https://github.com/couraa0" target="_blank" title="GitHub"><i class="fab fa-github"></i></a>
                            <a href="mailto:muhammadrakhasyamputra@gmail.com" title="Email"><i class="fas fa-envelope"></i></a>
                        </div>
                    </div>
                </div>
                <div class="col-md-4 mb-4">
                    <div class="card team-card text-center p-4">
                        <div class="team-avatar">
                            <i class="fas fa-user"></i>
                        </div>
                        <div class="team-name">Muhammad Rafly Dwi Gunawan</div>
                        <div class="team-role">Founder Ark Sentient</div>
                        <div class="team-social">
                            <a href="https://github.com/MuhammadRafly23100" target="_blank" title="GitHub"><i class="fab fa-github"></i></a>
                            <a href="mailto:mraflyyydwiii@gmail.com" title="Email"><i class="fas fa-envelope"></i></a>
                        </div>
                    </div>
                </div>
                <div class="col-md-4 mb-4">
                    <div class="card team-card text-center p-4">
                        <div class="team-avatar">
                            <i class="fas fa-user"></i>
                        </div>
                        <div class="team-name">Arya Chakra Ramadhan</div>
                        <div class="team-role">Founder Ark Sentient</div>
                        <div class="team-social">
                            <a href="https://github.com/ayraa34" target="_blank" title="GitHub"><i class="fab fa-github"></i></a>
                            <a href="mailto:aryachakra@gmail.com" title="Email"><i class="fas fa-envelope"></i></a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
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
