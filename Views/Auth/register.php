<?php
require_once '../../Config/config.php';

function sanitizeInput($data) {
    return htmlspecialchars(trim($data), ENT_QUOTES, 'UTF-8');
}

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = sanitizeInput($_POST['username']);
    $email = sanitizeInput($_POST['email']);
    $fullName = sanitizeInput($_POST['full_name']);
    $phone = sanitizeInput($_POST['phone']);
    $address = sanitizeInput($_POST['address']);
    $password = $_POST['password'];
    $confirmPassword = $_POST['confirm_password'];
    $role = sanitizeInput($_POST['role']);
    
    if (empty($username) || empty($email) || empty($fullName) || empty($password) || empty($confirmPassword) || empty($role)) {
        $error = 'Username, email, nama lengkap, password, dan role wajib diisi';
    } elseif ($password !== $confirmPassword) {
        $error = 'Password dan konfirmasi password tidak sama';
    } elseif (strlen($password) < 6) {
        $error = 'Password minimal 6 karakter';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Format email tidak valid';
    } else {
        // Check if username or email already exists
        $stmt = $conn->prepare("SELECT id FROM users WHERE username = ? OR email = ?");
        $stmt->execute([$username, $email]);
        $existingUser = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($existingUser) {
            $error = 'Username atau email sudah terdaftar';
        } else {
            // Hash password and insert user
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $conn->prepare("INSERT INTO users (username, email, full_name, phone, address, password, role) VALUES (?, ?, ?, ?, ?, ?, ?)");
            
            if ($stmt->execute([$username, $email, $fullName, $phone, $address, $hashedPassword, $role])) {
                $success = 'Registrasi berhasil! Silakan login.';
            } else {
                $error = 'Terjadi kesalahan saat registrasi';
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - ARK Sentient</title>
    <link href="../../Asset/css/register.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    
</head>
<body>
    <div class="register-container">
        <div class="left-section d-none d-md-flex">
            <div class="left-content">
                <i class="fas fa-cow fa-4x mb-3"></i>
                <h3>Marketplace Ternak Terpercaya</h3>
                <p class="lead">Bergabung dengan komunitas peternak Indonesia</p>
            </div>
        </div>
        <div class="right-section w-100">
            <div class="register-form-container mx-auto">
                <div class="brand-header">
                    <div class="brand-logo">
                        <div class="brand-icon">
                            <i class="fas fa-seedling"></i>
                        </div>
                        ARK Sentient
                    </div>
                    <h2 class="register-title">Register</h2>
                </div>
                
                <?php if ($error): ?>
                    <div class="alert alert-danger" role="alert">
                        <i class="fas fa-exclamation-triangle me-2"></i><?php echo $error; ?>
                    </div>
                <?php endif; ?>
                
                <?php if ($success): ?>
                    <div class="alert alert-success" role="alert">
                        <i class="fas fa-check-circle me-2"></i><?php echo $success; ?>
                    </div>
                <?php endif; ?>
                
                <form method="POST" action="" id="registerForm" class="row g-3">
                    <div class="col-md-6">
                        <label for="username" class="form-label">Username</label>
                        <input type="text" class="form-control" id="username" name="username"
                               placeholder="Username" required value="<?php echo htmlspecialchars($username ?? ''); ?>">
                    </div>
                    <div class="col-md-6">
                        <label for="email" class="form-label">Email</label>
                        <input type="email" class="form-control" id="email" name="email"
                               placeholder="Email" required value="<?php echo htmlspecialchars($email ?? ''); ?>">
                    </div>
                    <div class="col-md-6">
                        <label for="full_name" class="form-label">Nama Lengkap</label>
                        <input type="text" class="form-control" id="full_name" name="full_name"
                               placeholder="Nama Lengkap" required value="<?php echo htmlspecialchars($fullName ?? ''); ?>">
                    </div>
                    <div class="col-md-6">
                        <label for="phone" class="form-label">Nomor Telepon</label>
                        <input type="tel" class="form-control" id="phone" name="phone"
                               placeholder="08xxxxxxxxxx" value="<?php echo htmlspecialchars($phone ?? ''); ?>">
                    </div>
                    <div class="col-md-6">
                        <label for="role" class="form-label">Daftar Sebagai</label>
                        <select class="form-select" id="role" name="role" required>
                            <option value="">Pilih Role</option>
                            <option value="customer" <?php echo (isset($role) && $role == 'customer') ? 'selected' : ''; ?>>Pembeli</option>
                            <option value="farmer" <?php echo (isset($role) && $role == 'farmer') ? 'selected' : ''; ?>>Peternak</option>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label for="address" class="form-label">Alamat</label>
                        <textarea class="form-control" id="address" name="address" rows="2"
                                  placeholder="Alamat lengkap"><?php echo htmlspecialchars($address ?? ''); ?></textarea>
                    </div>
                    <div class="col-md-6">
                        <label for="password" class="form-label">Password</label>
                        <input type="password" class="form-control" id="password" name="password"
                               placeholder="Create your password" required>
                        <div class="password-strength" id="passwordStrength"></div>
                    </div>
                    <div class="col-md-6">
                        <label for="confirm_password" class="form-label">Konfirmasi Password</label>
                        <input type="password" class="form-control" id="confirm_password" name="confirm_password"
                               placeholder="Confirm password" required>
                    </div>
                    <div class="col-12">
                        <button type="submit" class="btn btn-register">
                            Register
                        </button>
                    </div>
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
                
                <div class="login-link">
                    <p>Already have an account? 
                        <a href="../../index.php">Sign In</a>
                    </p>
                </div>
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Password strength indicator
        document.getElementById('password').addEventListener('input', function() {
            const password = this.value;
            const strengthIndicator = document.getElementById('passwordStrength');
            
            if (password.length === 0) {
                strengthIndicator.textContent = '';
                return;
            }
            
            let strength = 0;
            let feedback = [];
            
            if (password.length >= 8) strength++;
            else feedback.push('minimal 8 karakter');
            
            if (/[a-z]/.test(password)) strength++;
            else feedback.push('huruf kecil');
            
            if (/[A-Z]/.test(password)) strength++;
            else feedback.push('huruf besar');
            
            if (/[0-9]/.test(password)) strength++;
            else feedback.push('angka');
            
            if (/[^A-Za-z0-9]/.test(password)) strength++;
            else feedback.push('karakter khusus');
            
            const colors = ['#dc3545', '#fd7e14', '#ffc107', '#20c997', '#198754'];
            const labels = ['Sangat Lemah', 'Lemah', 'Cukup', 'Kuat', 'Sangat Kuat'];
            
            strengthIndicator.style.color = colors[Math.min(strength, 4)];
            strengthIndicator.textContent = labels[Math.min(strength, 4)];
            
            if (feedback.length > 0 && strength < 4) {
                strengthIndicator.textContent += ' (perlu: ' + feedback.slice(0, 2).join(', ') + ')';
            }
        });
        
        // Form validation
        document.getElementById('registerForm').addEventListener('submit', function(e) {
            const username = document.getElementById('username').value;
            const email = document.getElementById('email').value;
            const fullName = document.getElementById('full_name').value;
            const password = document.getElementById('password').value;
            const confirmPassword = document.getElementById('confirm_password').value;
            const role = document.getElementById('role').value;
            
            let isValid = true;
            
            // Reset validation classes
            document.querySelectorAll('.form-control, .form-select').forEach(el => {
                el.classList.remove('is-invalid', 'is-valid');
            });
            
            // Required field validation
            if (!username.trim()) {
                document.getElementById('username').classList.add('is-invalid');
                isValid = false;
            } else {
                document.getElementById('username').classList.add('is-valid');
            }
            
            if (!email.trim()) {
                document.getElementById('email').classList.add('is-invalid');
                isValid = false;
            } else if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)) {
                document.getElementById('email').classList.add('is-invalid');
                alert('Format email tidak valid');
                isValid = false;
            } else {
                document.getElementById('email').classList.add('is-valid');
            }
            
            if (!fullName.trim()) {
                document.getElementById('full_name').classList.add('is-invalid');
                isValid = false;
            } else {
                document.getElementById('full_name').classList.add('is-valid');
            }
            
            if (!role) {
                document.getElementById('role').classList.add('is-invalid');
                isValid = false;
            } else {
                document.getElementById('role').classList.add('is-valid');
            }
            
            if (password !== confirmPassword) {
                document.getElementById('password').classList.add('is-invalid');
                document.getElementById('confirm_password').classList.add('is-invalid');
                alert('Password dan konfirmasi password tidak sama');
                isValid = false;
            }
            
            if (password.length < 6) {
                document.getElementById('password').classList.add('is-invalid');
                alert('Password minimal 6 karakter');
                isValid = false;
            }
            
            if (!isValid) {
                e.preventDefault();
                return false;
            }
        });
        
        // Phone number formatting
        document.getElementById('phone').addEventListener('input', function() {
            let value = this.value.replace(/\D/g, ''); // Remove non-digits
            if (value.startsWith('0')) {
                // Format: 08xx-xxxx-xxxx
                value = value.substring(0, 13);
                if (value.length > 4 && value.length <= 8) {
                    value = value.substring(0, 4) + '-' + value.substring(4);
                } else if (value.length > 8) {
                    value = value.substring(0, 4) + '-' + value.substring(4, 8) + '-' + value.substring(8);
                }
            }
            this.value = value;
        });
        
        // Real-time password confirmation check
        document.getElementById('confirm_password').addEventListener('input', function() {
            const password = document.getElementById('password').value;
            const confirmPassword = this.value;
            
            if (confirmPassword && password !== confirmPassword) {
                this.style.borderColor = '#dc3545';
            } else {
                this.style.borderColor = '#ddd';
            }
        });
    </script>
</body>
</html>