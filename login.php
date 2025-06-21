<?php
require_once 'config/database.php';
require_once 'auth/session.php';

// Jika sudah login, redirect ke dashboard
if (isLoggedIn()) {
    header('Location: dashboard.php');
    exit();
}

$error = '';

if ($_POST) {
    $username = escape_string($_POST['username']);
    $password = $_POST['password'];
    
    if (empty($username) || empty($password)) {
        $error = 'Username dan password tidak boleh kosong!';
    } else {
        $password_hash = md5($password);
        $result = execute_query(
            "SELECT * FROM users WHERE username = ? AND password = ?",
            [$username, $password_hash],
            'ss'
        );
        
        if ($result && $result->num_rows > 0) {
            $user = $result->fetch_assoc();
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['nama'] = $user['nama'];
            
            header('Location: dashboard.php');
            exit();
        } else {
            $error = 'Username atau password salah!';
        }
    }
}

$title = 'Login - Buku Tamu Digital';
include 'includes/header.php';
?>

<div class="container-fluid vh-100">
    <div class="row h-100">
        <div class="col-md-6 d-flex align-items-center justify-content-center login-bg-primary">
            <div class="text-white text-center">
                <div class="mb-4">
                    <i class="fas fa-book display-1" style="font-size: 5rem;"></i>
                </div>
                <h1 class="display-4 mb-4">TamuKu</h1>
                <p class="lead fs-3">Website Buku Tamu Digital</p>
                <div class="mt-4">
                    <i class="fas fa-users me-3"></i>
                    <i class="fas fa-clipboard-check me-3"></i>
                    <i class="fas fa-chart-line"></i>
                </div>
            </div>
        </div>
        <div class="col-md-6 d-flex align-items-center justify-content-center" style="background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);">
            <div class="card shadow-lg" style="width: 450px; border-radius: 25px;">
                <div class="card-body p-5">
                    <div class="text-center mb-4">
                        <div class="mb-3">
                            <i class="fas fa-user-shield" style="font-size: 3rem; color: var(--primary-pink);"></i>
                        </div>
                        <h3 class="card-title" style="color: var(--primary-pink);">Login Admin</h3>
                        <p class="text-muted">Masuk ke panel administrasi</p>
                    </div>
                    
                    <?php if ($error): ?>
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <i class="fas fa-exclamation-triangle me-2"></i>
                            <?php echo htmlspecialchars($error); ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    <?php endif; ?>
                    
                    <form method="POST">
                        <div class="mb-4">
                            <label for="username" class="form-label">
                                <i class="fas fa-user me-2" style="color: var(--primary-pink);"></i>Username
                            </label>
                            <input type="text" class="form-control" id="username" name="username" 
                                   value="<?php echo htmlspecialchars($_POST['username'] ?? ''); ?>" 
                                   placeholder="Masukkan username" required>
                        </div>
                        <div class="mb-4">
                            <label for="password" class="form-label">
                                <i class="fas fa-lock me-2" style="color: var(--primary-pink);"></i>Password
                            </label>
                            <input type="password" class="form-control" id="password" name="password" 
                                   placeholder="Masukkan password" required>
                        </div>
                        <button type="submit" class="btn btn-primary w-100 py-3">
                            <i class="fas fa-sign-in-alt me-2"></i>Masuk ke Dashboard
                        </button>
                    </form>

                    <hr class="my-4">
                    
                    <div class="text-center">
                        <p class="mb-3" style="color: var(--primary-green);">
                            <i class="fas fa-users me-2"></i>Anda pengunjung?
                        </p>
                        <a href="kunjungan-tamu.php" class="btn btn-success w-100 py-3">
                            <i class="fas fa-user-edit me-2"></i>Isi Form Kunjungan Tamu
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>