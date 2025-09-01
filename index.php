<?php
session_start();
require_once 'Config/config.php'; // Pastikan path dan koneksi benar

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $password = $_POST['password'];

    // Query user dari database
    $stmt = $conn->prepare("SELECT * FROM users WHERE username = ?");
    $stmt->execute([$username]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user && password_verify($password, $user['password'])) {
        // Login berhasil
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['full_name'] = $user['full_name'];
        $_SESSION['role'] = $user['role'];
        $_SESSION['email'] = $user['email'];
        // ...tambahkan data lain jika perlu...
        header('Location: Views/home.php');
        exit;
    } else {
        $error = 'Username atau password salah';
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ARK Sentient - Login</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="Asset/css/auth.css" rel="stylesheet">
</head>
<body>
    <div class="login-container">
        <!-- Left side - Farm image -->
        <div class="left-section">
            <div class="left-content">
                <i class="fas fa-cow fa-4x mb-3"></i>
                <h3>Marketplace Ternak Terpercaya</h3>
                <p class="lead">Menghubungkan peternak dengan pembeli</p>
            </div>
        </div>
        
        <!-- Right side - Login form -->
        <div class="right-section">
            <div class="login-form-container">
                <div class="brand-header text-center">
                    <div class="brand-logo d-flex align-items-center justify-content-center mb-2">
                        <img src="Asset/icon/logoweb.png" alt="Logo" style="height:40px;width:40px;background:none;object-fit:contain;margin-right:12px;">
                        <span class="fw-bold fs-4" style="color:#388e3c;">ARK Sentient</span>
                    </div>
                    <h2 class="login-title mt-3">Login</h2>
                </div>
                <?php if ($error): ?>
                    <div class="alert alert-danger mt-2"><?= htmlspecialchars($error) ?></div>
                <?php endif; ?>
                <form method="post" id="loginForm">
                    <div class="form-group">
                        <label for="username" class="form-label">Username</label>
                        <input type="text" class="form-control" id="username" name="username"
                               placeholder="Enter your username" required>
                    </div>
                    <div class="form-group">
                        <label for="password" class="form-label">Password</label>
                        <input type="password" class="form-control" id="password" name="password"
                               placeholder="Enter your password" required>
                    </div>
                    <button type="submit" class="btn btn-login">
                        Login
                    </button>
                </form>
                
                <div class="social-buttons">
                    <button class="btn btn-social">
                        <i class="fab fa-google google-icon"></i>
                        Continue With Gmail
                    </button>
                    <button class="btn btn-social">
                        <i class="fab fa-facebook facebook-icon"></i>
                        Continue With Facebook
                    </button>
                </div>
                
                <div class="signup-link">
                    <p>Don't you have an account? 
                        <a href="views/Auth/register.php">Sign Up</a>
                    </p>
                </div>
                
                <div class="demo-info">
                    <div class="text-center">
                        <small>
                            <strong>Demo Login:</strong><br>
                            <strong>Username:</strong> admin | <strong>Password</strong> 123<br>
                            <strong>Username:</strong> farmer1 | <strong>Password:</strong> password<br>
                            <strong>Username:</strong> customer1 | <strong>Password:</strong> password
                        </small>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>