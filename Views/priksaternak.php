<?php
if (session_status() === PHP_SESSION_NONE) session_start();
require_once __DIR__ . '/../Config/config.php';

if (!isLoggedIn()) {
    header('Location: ../../index.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pemeriksaan Ternak - ARK Sentient</title>
    <link href="../../Asset/css/dashboard.css" rel="stylesheet">
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
                    <li class="nav-item">
                        <a class="nav-link" href="dashboard.php">
                            <i class="fas fa-store me-1"></i>
                            Marketplace
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="priksaternak.php">
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
    <!-- End Navbar -->

    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="card shadow">
                    <div class="card-body">
                        <h4 class="fw-bold mb-4">Periksa Kesehatan Hewanmu</h4>
                        <div class="mb-2 text-muted">Upload Photo/Video</div>
                        <div class="p-4 mb-4 border rounded text-center pemeriksaan-upload-bg">
                            <div class="fw-semibold mb-2">Upload a photo or video of your animal</div>
                            <div class="mb-3 pemeriksaan-desc">
                                Our system will automatically detect and highlight the face, nose, and eyes of the animal in the image or video.
                            </div>
                            <input type="file" id="mediaInput" accept="image/*,video/*" hidden>
                            <button class="btn btn-success" id="uploadMediaBtn">Upload Media</button>
                            <div id="mediaPreview" class="mt-3"></div>
                            <!-- Form input akan muncul di sini setelah upload -->
                            <div id="dynamicTextFormContainer"></div>
                        </div>
                        <div class="p-4 border rounded text-center pemeriksaan-upload-bg">
                            <div class="mb-3 text-muted">Or, use your camera to capture a new image or video</div>
                            <button class="btn btn-light p-0 border-0 pemeriksaan-cam-btn" onclick="openCamera()" id="openCameraBtn">
                                <img src="../Asset/icon/Cam.png" alt="camera" class="pemeriksaan-cam-img"/>
                            </button>
                            <div id="cameraPreview" class="mt-3"></div>
                        </div>
                    </div>
                </div>
                <!-- Tambahkan tombol kembali ke home dengan margin -->
                <div class="d-flex justify-content-end mt-4 mb-3">
                    <a href="home.php" class="btn btn-success">
                        <i class="fas fa-arrow-left"></i> Kembali ke Home
                    </a>
                </div>
            </div>
        </div>
    </div>

    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function openCamera() {
            // Sembunyikan tombol kamera saat diklik
            const camBtn = document.getElementById('openCameraBtn');
            if (camBtn) camBtn.style.display = 'none';

            const preview = document.getElementById('cameraPreview');
            preview.innerHTML = '';

            // Create video element
            const video = document.createElement('video');
            video.autoplay = true;
            video.className = 'camera-video-preview mb-2';
            preview.appendChild(video);

            // Create capture button
            const captureBtn = document.createElement('button');
            captureBtn.textContent = 'Capture Photo';
            captureBtn.className = 'btn btn-success mb-2';
            preview.appendChild(captureBtn);

            // Access webcam
            navigator.mediaDevices.getUserMedia({ video: true })
                .then(function(stream) {
                    video.srcObject = stream;

                    captureBtn.onclick = function() {
                        // Create canvas only in memory, not appended to DOM
                        const canvas = document.createElement('canvas');
                        canvas.width = video.videoWidth;
                        canvas.height = video.videoHeight;
                        canvas.getContext('2d').drawImage(video, 0, 0, canvas.width, canvas.height);

                        // Stop video stream
                        stream.getTracks().forEach(track => track.stop());
                        video.style.display = 'none';
                        captureBtn.style.display = 'none';

                        // Show captured image (only once)
                        const img = document.createElement('img');
                        img.src = canvas.toDataURL('image/png');
                        img.className = 'img-fluid rounded mb-2 pemeriksaan-preview-img';
                        preview.appendChild(img);

                        // Kirim hasil foto ke AI (seperti upload media)
                        const loading = document.createElement('div');
                        loading.textContent = 'Ark Sentient Proses...';
                        loading.className = 'text-info my-2 pemeriksaan-loading';
                        preview.appendChild(loading);

                        // Convert base64 to Blob
                        function dataURLtoBlob(dataurl) {
                            var arr = dataurl.split(','), mime = arr[0].match(/:(.*?);/)[1],
                                bstr = atob(arr[1]), n = bstr.length, u8arr = new Uint8Array(n);
                            while(n--){
                                u8arr[n] = bstr.charCodeAt(n);
                            }
                            return new Blob([u8arr], {type:mime});
                        }
                        const blob = dataURLtoBlob(img.src);
                        const file = new File([blob], "capture.png", { type: "image/png" });

                        const formData = new FormData();
                        formData.append('media', file);

                        fetch('../api/ai_cheack.php', {
                            method: 'POST',
                            body: formData
                        })
                        .then(response => response.json())
                        .then(function(data) {
                            loading.remove();
                            if (data && data.success) {
                                const resultDiv = document.createElement('div');
                                resultDiv.className = 'alert alert-success mt-2';
                                resultDiv.textContent = 'Hasil Ark Sentient: ' + data.result;
                                preview.appendChild(resultDiv);
                            } else if (data && data.message) {
                                const errorDiv = document.createElement('div');
                                errorDiv.className = 'alert alert-danger mt-2';
                                errorDiv.textContent = 'Gagal memproses gambar dengan AI: ' + data.message;
                                preview.appendChild(errorDiv);
                            } else {
                                const errorDiv = document.createElement('div');
                                errorDiv.className = 'alert alert-danger mt-2';
                                errorDiv.textContent = 'Gagal memproses gambar dengan AI.';
                                preview.appendChild(errorDiv);
                            }
                        })
                        .catch(function(error) {
                            loading.remove();
                            // ...optional error handling...
                        });
                    };
                })
                .catch(function(err) {
                    preview.innerHTML = '<div class="alert alert-danger">Tidak dapat mengakses kamera: ' + err.message + '</div>';
                });
        }
        document.getElementById('uploadMediaBtn').onclick = function() {
            document.getElementById('mediaInput').click();
        };

        document.getElementById('mediaInput').addEventListener('change', function(e) {
            const file = e.target.files[0];
            const preview = document.getElementById('mediaPreview');
            preview.innerHTML = '';
            if (!file) {
                alert('Silakan pilih file gambar atau video terlebih dahulu.');
                return;
            }
            // Preview image/video
            if (file.type.startsWith('image/')) {
                const img = document.createElement('img');
                img.src = URL.createObjectURL(file);
                img.className = 'img-fluid rounded mb-2 pemeriksaan-preview-img';
                preview.appendChild(img);
            } else if (file.type.startsWith('video/')) {
                const video = document.createElement('video');
                video.src = URL.createObjectURL(file);
                video.controls = true;
                video.className = 'img-fluid rounded mb-2 pemeriksaan-preview-video';
                preview.appendChild(video);
            } else {
                alert('File harus berupa gambar atau video.');
                return;
            }

            // Tampilkan loading
            const loading = document.createElement('div');
            loading.textContent = 'Ark Sentient Proses...';
            loading.className = 'text-info my-2 pemeriksaan-loading';
            preview.appendChild(loading);

            // Kirim ke backend untuk AI detection
            const formData = new FormData();
            formData.append('media', file);
            fetch('../api/ai_cheack.php', { // <-- ubah path ke api
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(function(data) {
                loading.remove();
                if (data && data.success) {
                    // Tampilkan hasil AI di bawah preview
                    const resultDiv = document.createElement('div');
                    resultDiv.className = 'alert alert-success mt-2';
                    resultDiv.textContent = 'Hasil Ark Sentient : ' + data.result;
                    preview.appendChild(resultDiv);
                } else if (data && data.message) {
                    const errorDiv = document.createElement('div');
                    errorDiv.className = 'alert alert-danger mt-2';
                    errorDiv.textContent = 'Gagal memproses gambar/video dengan AI: ' + data.message;
                    preview.appendChild(errorDiv);
                } else {
                    const errorDiv = document.createElement('div');
                    errorDiv.className = 'alert alert-danger mt-2';
                    errorDiv.textContent = 'Gagal memproses gambar/video dengan AI.';
                    preview.appendChild(errorDiv);
                }
            })
            .catch(function(error) {
                loading.remove();
                // Hapus notifikasi error: 
                // const errorDiv = document.createElement('div');
                // errorDiv.className = 'alert alert-danger mt-2';
                // errorDiv.textContent = 'Terjadi kesalahan saat mengirim ke AI.';
                // preview.appendChild(errorDiv);
            });

            // Tampilkan form input di bawah gambar/video yang diupload
            showDynamicTextForm();
        });

        function showDynamicTextForm() {
            const container = document.getElementById('dynamicTextFormContainer');
            if (!container.innerHTML) {
                container.innerHTML = `
                    <form class="d-flex justify-content-center mt-3" id="navPriksaForm" onsubmit="return handleNavPriksaSubmit(event)">
                        <input type="text" class="form-control form-control-lg" id="navPriksaInput" placeholder="Isi Keluhan Hewan Ternak Anda Disini" required style="width: 100%; max-width: 600px;">
                        <button type="submit" class="btn btn-success btn-lg ms-3">Kirim</button>
                    </form>
                `;
            }
        }

        // Hapus form input jika upload baru
        document.getElementById('uploadMediaBtn').addEventListener('click', function() {
            document.getElementById('dynamicTextFormContainer').innerHTML = '';
        });

        function handleNavPriksaSubmit(e) {
            e.preventDefault();
            const input = document.getElementById('navPriksaInput');
            if (!input.value.trim()) {
                input.focus();
                input.classList.add('is-invalid');
                return false;
            }
            const container = document.getElementById('dynamicTextFormContainer');
            let oldResult = container.querySelector('.ai-answer');
            if (oldResult) oldResult.remove();

            const loading = document.createElement('div');
            loading.textContent = 'Ark Sentient Proses...';
            loading.className = 'text-info my-2 pemeriksaan-loading';
            container.appendChild(loading);

            const formData = new FormData();
            formData.append('keluhan', input.value);

            fetch('../api/ai_cheack.php', { // <-- perbaiki path
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(function(data) {
                loading.remove();
                const resultDiv = document.createElement('div');
                resultDiv.className = 'ai-answer alert mt-2 ' + (data.success ? 'alert-success' : 'alert-danger');
                resultDiv.textContent = data.success ? 'Jawaban Ark Sentient: ' + data.result : 'Gagal mendapatkan jawaban Ark Sentient: ' + (data.message || '');
                container.appendChild(resultDiv);
            })
            .catch(function(error) {
                loading.remove();
                const errorDiv = document.createElement('div');
                errorDiv.className = 'ai-answer alert alert-danger mt-2';
                errorDiv.textContent = 'Terjadi kesalahan saat menghubungi AI.';
                container.appendChild(errorDiv);
            });

            input.value = '';
            input.classList.remove('is-invalid');
            return false;
        }

        // Tambahkan handler untuk form kamera
        function handleCamPriksaSubmit(e) {
            e.preventDefault();
            const input = document.getElementById('camPriksaInput');
            if (!input.value.trim()) {
                input.focus();
                input.classList.add('is-invalid');
                return false;
            }
            const formDiv = document.getElementById('camPriksaFormContainer');
            let oldResult = formDiv.querySelector('.ai-answer');
            if (oldResult) oldResult.remove();

            const loading = document.createElement('div');
            loading.textContent = 'Ark Sentient Proses...';
            loading.className = 'text-info my-2 pemeriksaan-loading';
            formDiv.appendChild(loading);

            const formData = new FormData();
            formData.append('keluhan', input.value);

            fetch('../api/ai_cheack.php', { // <-- perbaiki path
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(function(data) {
                loading.remove();
                const resultDiv = document.createElement('div');
                resultDiv.className = 'ai-answer alert mt-2 ' + (data.success ? 'alert-success' : 'alert-danger');
                resultDiv.textContent = data.success ? 'Jawaban Ark Sentient: ' + data.result : 'Gagal mendapatkan jawaban AI: ' + (data.message || '');
                formDiv.appendChild(resultDiv);
            })
            .catch(function(error) {
                loading.remove();
                const errorDiv = document.createElement('div');
                errorDiv.className = 'ai-answer alert alert-danger mt-2';
                errorDiv.textContent = 'Terjadi kesalahan saat menghubungi AI.';
                formDiv.appendChild(errorDiv);
            });

            input.value = '';
            input.classList.remove('is-invalid');
            return false;
        }
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
