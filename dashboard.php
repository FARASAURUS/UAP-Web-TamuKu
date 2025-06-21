<?php
require_once 'config/database.php';
require_once 'auth/session.php';
checkLogin();

// Hitung statistik
$total_tamu = $conn->query("SELECT COUNT(*) as total FROM tamu")->fetch_assoc()['total'];
$total_departemen = $conn->query("SELECT COUNT(*) as total FROM departemen")->fetch_assoc()['total'];
$total_petugas = $conn->query("SELECT COUNT(*) as total FROM petugas")->fetch_assoc()['total'];
$total_keperluan = $conn->query("SELECT COUNT(*) as total FROM keperluan")->fetch_assoc()['total'];

// Tamu hari ini
$tamu_hari_ini = $conn->query("SELECT COUNT(*) as total FROM tamu WHERE DATE(waktu_kunjungan) = CURDATE()")->fetch_assoc()['total'];

$title = 'Dashboard - TamuKu';
include 'includes/header.php';
?>

<div class="container-fluid">
    <div class="row">
        <?php include 'includes/sidebar.php'; ?>
        
        <div class="col-md-9 col-lg-10 px-4">
            <div class="d-flex justify-content-between align-items-center py-4 border-bottom">
                <div>
                    <h1 class="mb-1" style="color: var(--primary-pink);">
                        <i class="fas fa-tachometer-alt me-3"></i>Dashboard
                    </h1>
                    <p class="text-muted mb-0">Selamat datang kembali, <?php echo $_SESSION['nama']; ?>!</p>
                </div>
                <div class="text-end">
                    <div class="badge bg-success fs-6 px-3 py-2">
                        <i class="fas fa-calendar-day me-2"></i>
                        <?php echo date('d F Y'); ?>
                    </div>
                </div>
            </div>
            
            <div class="row mt-4">
                <div class="col-md-3 mb-4">
                    <div class="card text-white bg-primary h-100">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h2 class="mb-1"><?php echo $total_tamu; ?></h2>
                                    <p class="mb-0 fs-6">Total Tamu</p>
                                </div>
                                <div class="align-self-center">
                                    <i class="fas fa-users fa-3x opacity-75"></i>
                                </div>
                            </div>
                        </div>
                        <div class="card-footer bg-transparent border-0">
                            <small><i class="fas fa-chart-line me-1"></i>Semua waktu</small>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-3 mb-4">
                    <div class="card text-white bg-success h-100">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h2 class="mb-1"><?php echo $tamu_hari_ini; ?></h2>
                                    <p class="mb-0 fs-6">Tamu Hari Ini</p>
                                </div>
                                <div class="align-self-center">
                                    <i class="fas fa-calendar-day fa-3x opacity-75"></i>
                                </div>
                            </div>
                        </div>
                        <div class="card-footer bg-transparent border-0">
                            <small><i class="fas fa-clock me-1"></i><?php echo date('d M Y'); ?></small>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-3 mb-4">
                    <div class="card text-white bg-info h-100">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h2 class="mb-1"><?php echo $total_departemen; ?></h2>
                                    <p class="mb-0 fs-6">Departemen</p>
                                </div>
                                <div class="align-self-center">
                                    <i class="fas fa-building fa-3x opacity-75"></i>
                                </div>
                            </div>
                        </div>
                        <div class="card-footer bg-transparent border-0">
                            <small><i class="fas fa-sitemap me-1"></i>Aktif</small>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-3 mb-4">
                    <div class="card text-white bg-warning h-100">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h2 class="mb-1"><?php echo $total_petugas; ?></h2>
                                    <p class="mb-0 fs-6">Petugas</p>
                                </div>
                                <div class="align-self-center">
                                    <i class="fas fa-user-tie fa-3x opacity-75"></i>
                                </div>
                            </div>
                        </div>
                        <div class="card-footer bg-transparent border-0">
                            <small><i class="fas fa-users-cog me-1"></i>Terdaftar</small>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="row">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h5 class="mb-0">
                                <i class="fas fa-history me-2"></i>Kunjungan Terbaru
                            </h5>
                            <a href="tamu.php" class="btn btn-sm btn-outline-light">
                                <i class="fas fa-eye me-1"></i>Lihat Semua
                            </a>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th><i class="fas fa-user me-2"></i>Nama Tamu</th>
                                            <th><i class="fas fa-clipboard-list me-2"></i>Keperluan</th>
                                            <th><i class="fas fa-user-tie me-2"></i>Petugas</th>
                                            <th><i class="fas fa-building me-2"></i>Departemen</th>
                                            <th><i class="fas fa-clock me-2"></i>Waktu Kunjungan</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        $query = "SELECT t.nama_tamu, k.nama_keperluan, p.nama_petugas, d.nama_departemen, t.waktu_kunjungan 
                                                 FROM tamu t 
                                                 LEFT JOIN keperluan k ON t.keperluan_id = k.id 
                                                 LEFT JOIN petugas p ON t.petugas_id = p.id 
                                                 LEFT JOIN departemen d ON t.departemen_id = d.id 
                                                 ORDER BY t.waktu_kunjungan DESC LIMIT 10";
                                        $result = $conn->query($query);
                                        
                                        if ($result->num_rows > 0):
                                            while ($row = $result->fetch_assoc()):
                                        ?>
                                            <tr>
                                                <td>
                                                    <div class="d-flex align-items-center">
                                                        <div class="avatar-sm bg-primary rounded-circle d-flex align-items-center justify-content-center me-3">
                                                            <i class="fas fa-user text-white"></i>
                                                        </div>
                                                        <strong><?php echo htmlspecialchars($row['nama_tamu']); ?></strong>
                                                    </div>
                                                </td>
                                                <td>
                                                    <span class="badge bg-info"><?php echo htmlspecialchars($row['nama_keperluan']); ?></span>
                                                </td>
                                                <td><?php echo htmlspecialchars($row['nama_petugas']); ?></td>
                                                <td>
                                                    <span class="badge bg-success"><?php echo htmlspecialchars($row['nama_departemen']); ?></span>
                                                </td>
                                                <td>
                                                    <small class="text-muted">
                                                        <?php echo date('d/m/Y H:i', strtotime($row['waktu_kunjungan'])); ?>
                                                    </small>
                                                </td>
                                            </tr>
                                        <?php 
                                            endwhile;
                                        else:
                                        ?>
                                            <tr>
                                                <td colspan="5" class="text-center py-4">
                                                    <div class="text-muted">
                                                        <i class="fas fa-inbox fa-3x mb-3"></i>
                                                        <p>Belum ada data kunjungan</p>
                                                    </div>
                                                </td>
                                            </tr>
                                        <?php endif; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.avatar-sm {
    width: 40px;
    height: 40px;
}
</style>

<?php include 'includes/footer.php'; ?>
