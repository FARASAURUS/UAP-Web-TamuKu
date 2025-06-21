<?php
require_once 'config/database.php';

$message = '';
$error = '';

// Proses form kunjungan tamu
if ($_POST) {
    $nama_tamu = mysqli_real_escape_string($conn, trim($_POST['nama_tamu']));
    $no_telepon = mysqli_real_escape_string($conn, trim($_POST['no_telepon']));
    $alamat = mysqli_real_escape_string($conn, trim($_POST['alamat']));
    $keperluan_id = (int)$_POST['keperluan_id'];
    $petugas_id = (int)$_POST['petugas_id'];
    $departemen_id = (int)$_POST['departemen_id'];
    
    // Validasi input
    if (empty($nama_tamu) || $keperluan_id == 0 || $departemen_id == 0 || $petugas_id == 0) {
        $error = 'Mohon lengkapi data yang wajib diisi!';
    } else {
        $query = "INSERT INTO tamu (nama_tamu, no_telepon, alamat, keperluan_id, petugas_id, departemen_id, waktu_kunjungan) 
                  VALUES ('$nama_tamu', '$no_telepon', '$alamat', '$keperluan_id', '$petugas_id', '$departemen_id', NOW())";
        
        if ($conn->query($query)) {
            $message = 'Terima kasih! Data kunjungan Anda telah berhasil dicatat.';
            // Reset form setelah berhasil
            $_POST = array();
        } else {
            $error = 'Terjadi kesalahan saat menyimpan data. Silakan coba lagi.';
        }
    }
}

$title = 'Form Kunjungan Tamu';
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $title; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        :root {
            --primary-pink: #e91e63;
            --primary-pink-dark: #c2185b;
            --primary-green: #4caf50;
            --primary-green-dark: #388e3c;
            --gradient-mixed: linear-gradient(135deg, #e91e63 0%, #4caf50 100%);
        }

        body {
            background: var(--gradient-mixed);
            min-height: 100vh;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        .main-container {
            padding: 20px 0;
        }
        
        .form-card {
            background: white;
            border-radius: 25px;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.15);
            overflow: hidden;
            backdrop-filter: blur(10px);
        }
        
        .header-section {
            background: var(--gradient-mixed);
            color: white;
            padding: 40px 30px;
            text-align: center;
            position: relative;
        }
        
        .header-section::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><defs><pattern id="grain" width="100" height="100" patternUnits="userSpaceOnUse"><circle cx="25" cy="25" r="1" fill="rgba(255,255,255,0.1)"/><circle cx="75" cy="75" r="1" fill="rgba(255,255,255,0.1)"/><circle cx="50" cy="10" r="0.5" fill="rgba(255,255,255,0.1)"/></pattern></defs><rect width="100" height="100" fill="url(%23grain)"/></svg>');
        }
        
        .header-section > * {
            position: relative;
            z-index: 1;
        }
        
        .form-section {
            padding: 40px 30px;
        }
        
        .form-control {
            border-radius: 15px;
            border: 2px solid #e9ecef;
            padding: 15px 20px;
            font-size: 16px;
            transition: all 0.3s ease;
        }
        
        .form-control:focus {
            border-color: var(--primary-pink);
            box-shadow: 0 0 0 0.2rem rgba(233, 30, 99, 0.25);
            transform: translateY(-2px);
        }
        
        .form-label {
            font-weight: 600;
            color: #495057;
            margin-bottom: 8px;
        }
        
        .btn-submit {
            background: var(--gradient-mixed);
            border: none;
            border-radius: 25px;
            padding: 15px 40px;
            font-weight: bold;
            color: white;
            font-size: 18px;
            transition: all 0.3s ease;
        }
        
        .btn-submit:hover {
            color: white;
            transform: translateY(-3px);
            box-shadow: 0 10px 25px rgba(233, 30, 99, 0.4);
        }
        
        .required {
            color: var(--primary-pink);
            font-weight: bold;
        }
        
        .admin-link {
            position: fixed;
            top: 20px;
            right: 20px;
            z-index: 1000;
        }
        
        .welcome-text {
            background: rgba(255, 255, 255, 0.15);
            padding: 20px;
            border-radius: 15px;
            margin-top: 20px;
            backdrop-filter: blur(10px);
        }
        
        .info-card {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-radius: 20px;
            border: none;
        }
        
        .alert {
            border-radius: 15px;
            border: none;
            padding: 20px;
        }
        
        .alert-success {
            background: linear-gradient(135deg, #c8e6c9 0%, #a5d6a7 100%);
            color: var(--primary-green-dark);
        }
        
        .alert-danger {
            background: linear-gradient(135deg, #ffcdd2 0%, #ef9a9a 100%);
            color: #c62828;
        }
        
        /* Animasi */
        @keyframes slideInUp {
            from {
                opacity: 0;
                transform: translateY(50px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        .form-card {
            animation: slideInUp 0.6s ease-out;
        }
        
        .form-control:hover {
            border-color: var(--primary-green);
        }
        
        select.form-control {
            cursor: pointer;
        }
        
        /* Icon styling */
        .form-label i {
            margin-right: 8px;
            width: 20px;
            text-align: center;
        }
        
        /* Responsive */
        @media (max-width: 768px) {
            .form-section {
                padding: 30px 20px;
            }
            
            .header-section {
                padding: 30px 20px;
            }
            
            .admin-link {
                position: relative;
                top: auto;
                right: auto;
                text-align: center;
                margin-bottom: 20px;
            }
        }
    </style>
</head>
<body>
    <!-- Link ke Admin -->
    <div class="admin-link">    
        <a href="login.php" class="btn btn-light btn-lg shadow">
            <i class="fas fa-user-shield"></i> Admin Login
        </a>
    </div>

    <div class="container main-container">
        <div class="row justify-content-center">
            <div class="col-md-8 col-lg-7">
                <div class="form-card">
                    <!-- Header -->
                    <div class="header-section">
                        <div class="mb-3">
                            <i class="fas fa-book-open" style="font-size: 4rem;"></i>
                        </div>
                        <h1 class="display-5 mb-0">TamuKu</h1>
                        <div class="welcome-text">
                            <h4><i class="fas fa-hand-wave me-2"></i>Selamat Datang!</h4>
                            <p class="mb-0 fs-5">Silakan isi form di bawah ini untuk mencatat kunjungan Anda</p>
                        </div>
                    </div>

                    <!-- Form Section -->
                    <div class="form-section">
                        <?php if ($message): ?>
                            <div class="alert alert-success alert-dismissible fade show" role="alert">
                                <i class="fas fa-check-circle me-2"></i> <?php echo $message; ?>
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        <?php endif; ?>

                        <?php if ($error): ?>
                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                <i class="fas fa-exclamation-triangle me-2"></i> <?php echo $error; ?>
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        <?php endif; ?>

                        <form method="POST" id="formKunjungan">
                            <div class="row">
                                <div class="col-md-12 mb-4">
                                    <label for="nama_tamu" class="form-label">
                                        <i class="fas fa-user" style="color: var(--primary-pink);"></i>Nama Lengkap <span class="required">*</span>
                                    </label>
                                    <input type="text" class="form-control" name="nama_tamu" id="nama_tamu" 
                                           value="<?php echo $_POST['nama_tamu'] ?? ''; ?>" 
                                           placeholder="Masukkan nama lengkap Anda" required>
                                </div>

                                <div class="col-md-6 mb-4">
                                    <label for="no_telepon" class="form-label">
                                        <i class="fas fa-phone" style="color: var(--primary-green);"></i>No. Telepon
                                    </label>
                                    <input type="text" class="form-control" name="no_telepon" id="no_telepon" 
                                           value="<?php echo $_POST['no_telepon'] ?? ''; ?>" 
                                           placeholder="08xxxxxxxxxx">
                                </div>

                                <div class="col-md-6 mb-4">
                                    <label for="keperluan_id" class="form-label">
                                        <i class="fas fa-clipboard-list" style="color: var(--primary-pink);"></i>Keperluan <span class="required">*</span>
                                    </label>
                                    <select class="form-control" name="keperluan_id" id="keperluan_id" required>
                                        <option value="">Pilih Keperluan</option>
                                        <?php
                                        $keperluan = $conn->query("SELECT * FROM keperluan ORDER BY nama_keperluan");
                                        while ($k = $keperluan->fetch_assoc()):
                                        ?>
                                            <option value="<?php echo $k['id']; ?>" 
                                                    <?php echo ($_POST['keperluan_id'] ?? '') == $k['id'] ? 'selected' : ''; ?>>
                                                <?php echo $k['nama_keperluan']; ?>
                                            </option>
                                        <?php endwhile; ?>
                                    </select>
                                </div>

                                <div class="col-md-12 mb-4">
                                    <label for="alamat" class="form-label">
                                        <i class="fas fa-map-marker-alt" style="color: var(--primary-green);"></i>Alamat
                                    </label>
                                    <textarea class="form-control" name="alamat" id="alamat" rows="3" 
                                              placeholder="Masukkan alamat lengkap Anda"><?php echo $_POST['alamat'] ?? ''; ?></textarea>
                                </div>

                                <div class="col-md-6 mb-4">
                                    <label for="departemen_id" class="form-label">
                                        <i class="fas fa-building" style="color: var(--primary-pink);"></i>Departemen Tujuan <span class="required">*</span>
                                    </label>
                                    <select class="form-control" name="departemen_id" id="departemen_id" required>
                                        <option value="">Pilih Departemen</option>
                                        <?php
                                        $departemen = $conn->query("SELECT * FROM departemen ORDER BY nama_departemen");
                                        while ($d = $departemen->fetch_assoc()):
                                        ?>
                                            <option value="<?php echo $d['id']; ?>" 
                                                    <?php echo ($_POST['departemen_id'] ?? '') == $d['id'] ? 'selected' : ''; ?>>
                                                <?php echo $d['nama_departemen']; ?>
                                            </option>
                                        <?php endwhile; ?>
                                    </select>
                                </div>

                                <div class="col-md-6 mb-4">
                                    <label for="petugas_id" class="form-label">
                                        <i class="fas fa-user-tie" style="color: var(--primary-green);"></i>Petugas yang Dituju <span class="required">*</span>
                                    </label>
                                    <select class="form-control" name="petugas_id" id="petugas_id" required>
                                        <option value="">Pilih Petugas</option>
                                        <?php
                                        $petugas = $conn->query("SELECT p.*, d.nama_departemen FROM petugas p LEFT JOIN departemen d ON p.departemen_id = d.id ORDER BY p.nama_petugas");
                                        while ($p = $petugas->fetch_assoc()):
                                        ?>
                                            <option value="<?php echo $p['id']; ?>" 
                                                    data-departemen="<?php echo $p['departemen_id']; ?>"
                                                    <?php echo ($_POST['petugas_id'] ?? '') == $p['id'] ? 'selected' : ''; ?>>
                                                <?php echo $p['nama_petugas'] . ' (' . $p['nama_departemen'] . ')'; ?>
                                            </option>
                                        <?php endwhile; ?>
                                    </select>
                                </div>
                            </div>

                            <div class="text-center mt-4">
                                <button type="submit" class="btn btn-submit btn-lg px-5">
                                    <i class="fas fa-paper-plane me-2"></i>Kirim Data Kunjungan
                                </button>
                            </div>

                            <div class="text-center mt-4">
                                <div class="alert alert-info">
                                    <i class="fas fa-info-circle me-2"></i> 
                                    Data yang bertanda <span class="required">*</span> wajib diisi
                                </div>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Info Card -->
                <div class="card info-card mt-4 shadow">
                    <div class="card-body text-center py-4">
                        <h5 style="color: var(--primary-pink);">
                            <i class="fas fa-clock me-2"></i>Jam Operasional
                        </h5>
                        <div class="row mt-3">
                            <div class="col-md-6">
                                <p class="mb-1"><strong>Senin - Jumat</strong></p>
                                <p class="text-muted">08:00 - 17:00 WIB</p>
                            </div>
                            <div class="col-md-6">
                                <p class="mb-1"><strong>Sabtu</strong></p>
                                <p class="text-muted">08:00 - 12:00 WIB</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Filter petugas berdasarkan departemen
        document.getElementById('departemen_id').addEventListener('change', function() {
            var departemenId = this.value;
            var petugasSelect = document.getElementById('petugas_id');
            var petugasOptions = petugasSelect.querySelectorAll('option');
            
            // Reset petugas selection
            petugasSelect.value = '';
            
            // Show/hide petugas based on departemen
            petugasOptions.forEach(function(option) {
                if (option.value === '') {
                    option.style.display = 'block';
                } else {
                    var optionDepartemen = option.getAttribute('data-departemen');
                    if (departemenId === '' || optionDepartemen === departemenId) {
                        option.style.display = 'block';
                    } else {
                        option.style.display = 'none';
                    }
                }
            });
        });

        // Auto hide alerts
        setTimeout(function() {
            var alerts = document.querySelectorAll('.alert');
            alerts.forEach(function(alert) {
                if (alert.classList.contains('show')) {
                    var bsAlert = new bootstrap.Alert(alert);
                    bsAlert.close();
                }
            });
        }, 5000);

        // Form validation
        document.getElementById('formKunjungan').addEventListener('submit', function(e) {
            var nama = document.getElementById('nama_tamu').value.trim();
            var keperluan = document.getElementById('keperluan_id').value;
            var departemen = document.getElementById('departemen_id').value;
            var petugas = document.getElementById('petugas_id').value;
            
            if (!nama || !keperluan || !departemen || !petugas) {
                e.preventDefault();
                alert('Mohon lengkapi semua data yang wajib diisi!');
                return false;
            }
        });
    </script>
</body>
</html>
