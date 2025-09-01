<?php
if (session_status() === PHP_SESSION_NONE) session_start();

// Inisialisasi cart_count
$cart_count = 0;
if (isset($_SESSION['user_id'])) {
    require_once '../Config/config.php';
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
    <title>ARK Sentient - Home</title>
    <meta name="description" content="ARK Sentient adalah aplikasi pintar berbasis AI untuk membantu peternak memantau kesehatan ternak, mengelola pakan otomatis, dan meningkatkan produktivitas peternakan modern.">
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="../Asset/css/home.css" rel="stylesheet">
    
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

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
            line-height: 1.6;
            color: var(--text-dark);
            overflow-x: hidden;
        }

        /* Navigation Styles */
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

        /* Hero Section */
        .hero-section {
            background: linear-gradient(135deg, var(--primary-green) 0%, var(--secondary-green) 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            position: relative;
            overflow: hidden;
        }

        .hero-section::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1000 1000"><circle cx="200" cy="200" r="100" fill="rgba(174,234,0,0.1)"/><circle cx="800" cy="300" r="150" fill="rgba(0,200,83,0.1)"/><circle cx="300" cy="700" r="80" fill="rgba(174,234,0,0.15)"/><circle cx="700" cy="800" r="120" fill="rgba(0,200,83,0.1)"/></svg>');
            animation: float 20s ease-in-out infinite;
        }

        @keyframes float {
            0%, 100% { transform: translateY(0px) rotate(0deg); }
            33% { transform: translateY(-20px) rotate(1deg); }
            66% { transform: translateY(10px) rotate(-1deg); }
        }

        .hero-content {
            color: var(--white);
            z-index: 2;
            position: relative;
        }

        .hero-title {
            font-size: clamp(2.5rem, 6vw, 4.5rem);
            font-weight: 700;
            margin-bottom: 1.5rem;
            text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.3);
            line-height: 1.2;
        }

        .hero-subtitle {
            font-size: 1.5rem;
            font-weight: 300;
            margin-bottom: 1rem;
            color: var(--accent-lime);
        }

        .hero-description {
            font-size: 1.1rem;
            line-height: 1.8;
            margin-bottom: 2.5rem;
            text-shadow: 1px 1px 2px rgba(0, 0, 0, 0.3);
            max-width: 600px;
        }

        .btn-custom {
            background: linear-gradient(135deg, var(--accent-lime) 0%, var(--accent-green) 100%);
            color: var(--primary-green);
            border: none;
            padding: 1rem 2rem;
            border-radius: 50px;
            font-weight: 600;
            font-size: 1.1rem;
            transition: var(--transition);
            box-shadow: 0 4px 15px rgba(174, 234, 0, 0.3);
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
        }

        .btn-custom:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 25px rgba(174, 234, 0, 0.4);
            color: var(--primary-green);
        }

        .btn-outline-custom {
            background: transparent;
            color: var(--white);
            border: 2px solid var(--white);
            padding: 1rem 2rem;
            border-radius: 50px;
            font-weight: 600;
            font-size: 1.1rem;
            transition: var(--transition);
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
        }

        .btn-outline-custom:hover {
            background: var(--white);
            color: var(--primary-green);
            transform: translateY(-3px);
        }

        /* Features Section */
        .features-section {
            padding: 6rem 0;
            background: var(--light-gray);
            position: relative;
        }

        .section-title {
            text-align: center;
            font-size: 3rem;
            font-weight: 700;
            color: var(--primary-green);
            margin-bottom: 1rem;
        }

        .section-subtitle {
            text-align: center;
            font-size: 1.2rem;
            color: var(--text-muted);
            margin-bottom: 4rem;
            max-width: 600px;
            margin-left: auto;
            margin-right: auto;
        }

        .feature-card {
            background: var(--white);
            border-radius: 20px;
            padding: 2.5rem;
            margin: 1rem 0;
            text-align: center;
            transition: var(--transition);
            border: none;
            height: 350px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            position: relative;
            overflow: hidden;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
        }

        .feature-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: linear-gradient(135deg, var(--accent-lime) 0%, var(--accent-green) 100%);
        }

        .feature-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.15);
        }

        .feature-icon {
            font-size: 3.5rem;
            background: linear-gradient(135deg, var(--accent-lime) 0%, var(--accent-green) 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            margin-bottom: 1.5rem;
        }

        .feature-title {
            font-size: 1.5rem;
            font-weight: 600;
            color: var(--primary-green);
            margin-bottom: 1rem;
        }

        .feature-description {
            color: var(--text-muted);
            font-size: 1rem;
            line-height: 1.6;
        }

        /* Stats Section */
        .stats-section {
            background: var(--primary-green);
            color: var(--white);
            padding: 4rem 0;
            position: relative;
        }

        .stats-section::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1000 200"><path d="M0,100 C150,150 350,50 500,100 C650,150 850,50 1000,100 L1000,200 L0,200 Z" fill="rgba(174,234,0,0.1)"/></svg>');
        }

        .stat-item {
            text-align: center;
            margin-bottom: 2rem;
            position: relative;
            z-index: 2;
        }

        .stat-number {
            font-size: 3.5rem;
            font-weight: 700;
            margin-bottom: 0.5rem;
            background: linear-gradient(135deg, var(--accent-lime) 0%, var(--white) 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .stat-label {
            font-size: 1.1rem;
            font-weight: 400;
            opacity: 0.9;
        }

        /* Footer */
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

        /* Responsive Design */
        @media (max-width: 768px) {
            .hero-title {
                font-size: 2.5rem;
            }
            
            .hero-description {
                font-size: 1rem;
            }
            
            .section-title {
                font-size: 2rem;
            }
            
            .feature-card {
                height: auto;
                padding: 2rem;
            }
            
            .stat-number {
                font-size: 2.5rem;
            }
            
            .btn-custom, .btn-outline-custom {
                padding: 0.8rem 1.5rem;
                font-size: 1rem;
            }
        }

        /* Animations */
        .fade-in {
            opacity: 0;
            transform: translateY(30px);
            transition: opacity 0.6s ease, transform 0.6s ease;
        }

        .fade-in.visible {
            opacity: 1;
            transform: translateY(0);
        }

        /* Loading Animation */
        .loading {
            display: inline-block;
            width: 20px;
            height: 20px;
            border: 3px solid rgba(255,255,255,.3);
            border-radius: 50%;
            border-top-color: #fff;
            animation: spin 1s ease-in-out infinite;
        }

        @keyframes spin {
            to { transform: rotate(360deg); }
        }
    </style>
</head>
<body>
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
                        <a class="nav-link" href="../Views/dashboard.php">
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

    <!-- Hero Section -->
    <section class="hero-section" id="home">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-8">
                    <div class="hero-content">
                        <h1 class="hero-title">ARK Sentient</h1>
                        <p class="hero-subtitle">Smart Livestock Management Solution</p>
                        <p class="hero-description">
                            Solusi pintar berbasis AI yang dirancang khusus untuk membantu peternak modern memantau kesehatan ternak, mengelola pakan secara otomatis, mendeteksi penyakit lebih awal dengan teknologi computer vision, dan memberikan rekomendasi berbasis data untuk meningkatkan produktivitas peternakan.
                        </p>
                        <div class="d-flex flex-wrap gap-3">
                            <a href="../Views/dashboard.php" class="btn-custom">
                                <i class="fas fa-rocket"></i>
                                Mulai Sekarang
                            </a>
                            <a href="#demo" class="btn-outline-custom">
                                <i class="fas fa-play"></i>
                                Lihat Demo
                            </a>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4 d-none d-lg-block">
                    <div class="text-center">
                        <i class="fas fa-cow" style="font-size: 15rem; color: rgba(255,255,255,0.1);"></i>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Features Section -->
    <section class="features-section" id="features">
        <div class="container">
            <h2 class="section-title fade-in">Fitur Utama</h2>
            <p class="section-subtitle fade-in">Teknologi terdepan untuk peternakan modern Indonesia</p>
            <div class="row">
                <div class="col-lg-4 col-md-6">
                    <div class="feature-card fade-in">
                        <div class="feature-icon">
                            <i class="fas fa-store"></i>
                        </div>
                        <h3 class="feature-title">Marketplace Ternak</h3>
                        <p class="feature-description">
                            Platform jual beli ternak yang aman dan terpercaya dengan sistem verifikasi lengkap dan jaminan kualitas untuk setiap transaksi.
                        </p>
                    </div>
                </div>
                <div class="col-lg-4 col-md-6">
                    <div class="feature-card fade-in">
                        <div class="feature-icon">
                            <i class="fas fa-stethoscope"></i>
                        </div>
                        <h3 class="feature-title">Pemeriksaan Ternak</h3>
                        <p class="feature-description">
                            Sistem AI canggih untuk mendeteksi penyakit dan kondisi kesehatan ternak secara real-time dengan akurasi tinggi menggunakan computer vision.
                        </p>
                    </div>
                </div>
                <div class="col-lg-4 col-md-6">
                    <div class="feature-card fade-in">
                        <div class="feature-icon">
                            <i class="fas fa-robot"></i>
                        </div>
                        <h3 class="feature-title">Smart Assistant</h3>
                        <p class="feature-description">
                            Asisten pintar berbasis AI untuk mengatur jadwal pemberian pakan optimal, monitoring kesehatan, dan rekomendasi perawatan.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Stats Section -->
    <section class="stats-section">
        <div class="container">
            <div class="row text-center">
                <div class="col-md-3 col-sm-6">
                    <div class="stat-item fade-in">
                        <div class="stat-number" data-count="1000">0</div>
                        <div class="stat-label">Peternak Aktif</div>
                    </div>
                </div>
                <div class="col-md-3 col-sm-6">
                    <div class="stat-item fade-in">
                        <div class="stat-number" data-count="50000">0</div>
                        <div class="stat-label">Ternak Terpantau</div>
                    </div>
                </div>
                <div class="col-md-3 col-sm-6">
                    <div class="stat-item fade-in">
                        <div class="stat-number" data-count="95">0</div>
                        <div class="stat-label">Akurasi AI</div>
                    </div>
                </div>
                <div class="col-md-3 col-sm-6">
                    <div class="stat-item fade-in">
                        <div class="stat-number">24/7</div>
                        <div class="stat-label">Monitoring</div>
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

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        // Smooth scrolling for navigation links
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                e.preventDefault();
                const target = document.querySelector(this.getAttribute('href'));
                if (target) {
                    const offsetTop = target.offsetTop - 80;
                    window.scrollTo({
                        top: offsetTop,
                        behavior: 'smooth'
                    });
                }
            });
        });

        // Navbar scroll effect
        window.addEventListener('scroll', function() {
            const navbar = document.querySelector('.navbar');
            if (window.scrollY > 50) {
                navbar.classList.add('scrolled');
            } else {
                navbar.classList.remove('scrolled');
            }
        });

        // Intersection Observer for animations
        const observerOptions = {
            threshold: 0.1,
            rootMargin: '0px 0px -50px 0px'
        };

        const observer = new IntersectionObserver(function(entries) {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('visible');
                }
            });
        }, observerOptions);

        // Observe elements for animation
        document.querySelectorAll('.fade-in').forEach(el => {
            observer.observe(el);
        });

        // Counter animation for stats
        function animateCounter(element, target) {
            let current = 0;
            const increment = target / 100;
            const timer = setInterval(() => {
                current += increment;
                if (current >= target) {
                    current = target;
                    clearInterval(timer);
                }
                
                if (element.dataset.count === '50000') {
                    element.textContent = Math.floor(current / 1000) + 'k+';
                } else if (element.dataset.count === '95') {
                    element.textContent = Math.floor(current) + '%';
                } else if (element.dataset.count === '1000') {
                    element.textContent = Math.floor(current) + '+';
                }
            }, 20);
        }

        // Start counter animation when stats section is visible
        const statsObserver = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    const counters = entry.target.querySelectorAll('[data-count]');
                    counters.forEach(counter => {
                        const target = parseInt(counter.dataset.count);
                        animateCounter(counter, target);
                    });
                    statsObserver.unobserve(entry.target);
                }
            });
        });

        const statsSection = document.querySelector('.stats-section');
        if (statsSection) {
            statsObserver.observe(statsSection);
        }

        // Add loading state to buttons
        document.querySelectorAll('.btn-custom, .btn-outline-custom').forEach(btn => {
            btn.addEventListener('click', function(e) {
                if (this.href && this.href.includes('#')) return;
                
                const originalText = this.innerHTML;
                this.innerHTML = '<span class="loading"></span> Loading...';
                this.disabled = true;
                
                setTimeout(() => {
                    this.innerHTML = originalText;
                    this.disabled = false;
                }, 2000);
            });
        });

        // Add parallax effect to hero background
        window.addEventListener('scroll', () => {
            const scrolled = window.pageYOffset;
            const parallax = document.querySelector('.hero-section::before');
            const speed = scrolled * 0.5;
            
            if (parallax) {
                parallax.style.transform = `translateY(${speed}px)`;
            }
        });

        // Performance optimization: Debounce scroll events
        function debounce(func, wait) {
            let timeout;
            return function executedFunction(...args) {
                const later = () => {
                    clearTimeout(timeout);
                    func(...args);
                };
                clearTimeout(timeout);
                timeout = setTimeout(later, wait);
            };
        }

        // Apply debouncing to scroll handler
        window.addEventListener('scroll', debounce(() => {
            // Your scroll handlers here
        }, 10));
    </script>
</body>
</html>