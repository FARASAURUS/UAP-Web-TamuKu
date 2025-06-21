<?php
require_once 'config/database.php';
require_once 'auth/session.php';
checkLogin();

// Filter berdasarkan tanggal (default hari ini)
$tanggal = $_GET['tanggal'] ?? date('Y-m-d');
$search = $_GET['search'] ?? '';

// Query untuk mengambil data kunjungan
$where_conditions = ["DATE(t.waktu_kunjungan) = ?"];
$params = [$tanggal];
$types = 's';

if (!empty($search)) {
    $where_conditions[] = "(t.nama_tamu LIKE ? OR p.nama_petugas LIKE ? OR d.nama_departemen LIKE ? OR k.nama_keperluan LIKE ?)";
    $search_param = "%$search%";
    $params = array_merge($params, [$search_param, $search_param, $search_param, $search_param]);
    $types .= 'ssss';
}

$where_clause = implode(' AND ', $where_conditions);

$query = "SELECT t.*, k.nama_keperluan, p.nama_petugas, d.nama_departemen 
          FROM tamu t 
          LEFT JOIN keperluan k ON t.keperluan_id = k.id 
          LEFT JOIN petugas p ON t.petugas_id = p.id 
          LEFT JOIN departemen d ON t.departemen_id = d.id 
          WHERE $where_clause
          ORDER BY t.waktu_kunjungan DESC";

$result = execute_query($query, $params, $types);

// Hitung statistik hari ini
$stats_query = "SELECT 
                COUNT(*) as total_kunjungan,
                COUNT(DISTINCT t.departemen_id) as departemen_dikunjungi,
                COUNT(DISTINCT t.petugas_id) as petugas_dikunjungi
                FROM tamu t 
                WHERE DATE(t.waktu_kunjungan) = ?";
$stats_result = execute_query($stats_query, [$tanggal], 's');
$stats = $stats_result->fetch_assoc();

$title = 'Riwayat Kunjungan - TamuKu';
include 'includes/header.php';
?>

<div class="container-fluid">
    <div class="row">
        <?php include 'includes/sidebar.php'; ?>
        
        <div class="col-md-9 col-lg-10 px-4">
            <div class="d-flex justify-content-between align-items-center py-4 border-bottom">
                <div>
                    <h1 class="mb-1" style="color: var(--primary-pink);">
                        <i class="fas fa-history me-3"></i>Riwayat Kunjungan
                    </h1>
                    <p class="text-muted mb-0">Data kunjungan tamu pada <?php echo date('l, d F Y', strtotime($tanggal)); ?></p>
                </div>
                <div class="text-end">
                    <div class="badge bg-success fs-6 px-3 py-2">
                        <i class="fas fa-calendar-day me-2"></i>
                        <?php echo date('d F Y', strtotime($tanggal)); ?>
                    </div>
                </div>
            </div>
            
            <!-- Statistics Cards -->
            <div class="row mt-4">
                <div class="col-md-3 mb-4">
                    <div class="card text-white bg-primary h-100">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h2 class="mb-1"><?php echo $stats['total_kunjungan']; ?></h2>
                                    <p class="mb-0 fs-6">Total Kunjungan</p>
                                </div>
                                <div class="align-self-center">
                                    <i class="fas fa-users fa-3x opacity-75"></i>
                                </div>
                            </div>
                        </div>
                        <div class="card-footer bg-transparent border-0">
                            <small><i class="fas fa-calendar-day me-1"></i><?php echo date('d M Y', strtotime($tanggal)); ?></small>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-3 mb-4">
                    <div class="card text-white bg-success h-100">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h2 class="mb-1"><?php echo $stats['total_kunjungan']; ?></h2>
                                    <p class="mb-0 fs-6">Tamu Aktif</p>
                                </div>
                                <div class="align-self-center">
                                    <i class="fas fa-user-check fa-3x opacity-75"></i>
                                </div>
                            </div>
                        </div>
                        <div class="card-footer bg-transparent border-0">
                            <small><i class="fas fa-clock me-1"></i>Hari ini</small>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-3 mb-4">
                    <div class="card text-white bg-info h-100">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h2 class="mb-1"><?php echo $stats['departemen_dikunjungi']; ?></h2>
                                    <p class="mb-0 fs-6">Departemen</p>
                                </div>
                                <div class="align-self-center">
                                    <i class="fas fa-building fa-3x opacity-75"></i>
                                </div>
                            </div>
                        </div>
                        <div class="card-footer bg-transparent border-0">
                            <small><i class="fas fa-sitemap me-1"></i>Dikunjungi</small>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-3 mb-4">
                    <div class="card text-white bg-warning h-100">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h2 class="mb-1"><?php echo $stats['petugas_dikunjungi']; ?></h2>
                                    <p class="mb-0 fs-6">Petugas</p>
                                </div>
                                <div class="align-self-center">
                                    <i class="fas fa-user-tie fa-3x opacity-75"></i>
                                </div>
                            </div>
                        </div>
                        <div class="card-footer bg-transparent border-0">
                            <small><i class="fas fa-users-cog me-1"></i>Ditemui</small>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Filter Section -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="fas fa-filter me-2"></i>Filter & Pencarian
                    </h5>
                </div>
                <div class="card-body">
                    <form method="GET" class="row g-3">
                        <div class="col-md-3">
                            <label for="tanggal" class="form-label">Tanggal Kunjungan</label>
                            <input type="date" class="form-control" id="tanggal" name="tanggal" 
                                   value="<?php echo $tanggal; ?>">
                        </div>
                        <div class="col-md-6">
                            <label for="search" class="form-label">Cari Tamu/Petugas/Departemen</label>
                            <input type="text" class="form-control" id="search" name="search" 
                                   value="<?php echo htmlspecialchars($search); ?>" 
                                   placeholder="Masukkan kata kunci pencarian...">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">&nbsp;</label>
                            <div class="d-flex gap-2">
                                <button type="submit" class="btn btn-primary flex-fill">
                                    <i class="fas fa-search me-2"></i>Cari
                                </button>
                                <a href="riwayat-kunjungan.php" class="btn btn-outline-secondary">
                                    <i class="fas fa-refresh"></i>
                                </a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Data Table -->
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">
                        <i class="fas fa-list me-2"></i>Daftar Kunjungan Tamu
                    </h5>
                    <div class="d-flex gap-2">
                        <button class="btn btn-sm btn-success" onclick="window.print()">
                            <i class="fas fa-print me-1"></i>Print
                        </button>
                        <a href="kunjungan-tamu.php" class="btn btn-sm btn-outline-light" target="_blank">
                            <i class="fas fa-plus me-1"></i>Tambah Kunjungan
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <?php if ($result && $result->num_rows > 0): ?>
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th><i class="fas fa-user me-2"></i>Nama Tamu</th>
                                        <th><i class="fas fa-clipboard-list me-2"></i>Keperluan</th>
                                        <th><i class="fas fa-user-tie me-2"></i>Petugas</th>
                                        <th><i class="fas fa-building me-2"></i>Departemen</th>
                                        <th><i class="fas fa-clock me-2"></i>Waktu Kunjungan</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php 
                                    $no = 1;
                                    while ($row = $result->fetch_assoc()): 
                                    ?>
                                        <tr>
                                            <td><?php echo $no++; ?></td>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <div class="avatar-sm bg-primary rounded-circle d-flex align-items-center justify-content-center me-3">
                                                        <i class="fas fa-user text-white"></i>
                                                    </div>
                                                    <div>
                                                        <div class="fw-bold"><?php echo htmlspecialchars($row['nama_tamu']); ?></div>
                                                        <?php if (!empty($row['no_telepon'])): ?>
                                                            <small class="text-muted">
                                                                <i class="fas fa-phone me-1"></i>
                                                                <?php echo htmlspecialchars($row['no_telepon']); ?>
                                                            </small>
                                                        <?php endif; ?>
                                                    </div>
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
                                                <div class="fw-semibold"><?php echo date('H:i', strtotime($row['waktu_kunjungan'])); ?></div>
                                                <small class="text-muted">
                                                    <?php echo date('d/m/Y', strtotime($row['waktu_kunjungan'])); ?>
                                                </small>
                                            </td>
                                        </tr>
                                    <?php endwhile; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php else: ?>
                        <div class="text-center py-5">
                            <div class="text-muted">
                                <i class="fas fa-inbox fa-4x mb-3"></i>
                                <h5>Tidak ada data kunjungan</h5>
                                <p class="mb-3">
                                    <?php if (!empty($search)): ?>
                                        Tidak ditemukan hasil untuk pencarian "<?php echo htmlspecialchars($search); ?>"
                                    <?php else: ?>
                                        Belum ada kunjungan pada tanggal <?php echo date('d F Y', strtotime($tanggal)); ?>
                                    <?php endif; ?>
                                </p>
                                <a href="kunjungan-tamu.php" class="btn btn-primary" target="_blank">
                                    <i class="fas fa-plus me-2"></i>Daftar Kunjungan Baru
                                </a>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="row mt-4">
                <div class="col-md-6">
                    <div class="card bg-light">
                        <div class="card-body text-center">
                            <i class="fas fa-calendar-day fa-2x text-primary mb-3"></i>
                            <h6>Lihat Hari Ini</h6>
                            <a href="?tanggal=<?php echo date('Y-m-d'); ?>" class="btn btn-sm btn-primary">
                                <i class="fas fa-eye me-1"></i>Hari Ini
                            </a>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card bg-light">
                        <div class="card-body text-center">
                            <i class="fas fa-calendar-minus fa-2x text-success mb-3"></i>
                            <h6>Lihat Kemarin</h6>
                            <a href="?tanggal=<?php echo date('Y-m-d', strtotime('-1 day')); ?>" class="btn btn-sm btn-success">
                                <i class="fas fa-eye me-1"></i>Kemarin
                            </a>
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

@media print {
    .sidebar, .btn, .card-header .btn, .d-print-none {
        display: none !important;
    }
    
    .main-content {
        margin-left: 0 !important;
    }
    
    .card {
        border: none !important;
        box-shadow: none !important;
    }
    
    .table {
        font-size: 12px;
    }
}
</style>

<script>
// Set tanggal hari ini sebagai default
document.addEventListener('DOMContentLoaded', function() {
    const dateInput = document.getElementById('tanggal');
    if (!dateInput.value) {
        dateInput.value = new Date().toISOString().split('T')[0];
    }
});

// Auto refresh setiap 1 menit jika tanggal adalah hari ini
<?php if ($tanggal === date('Y-m-d')): ?>
setInterval(function() {
    if (!document.hidden) {
        location.reload();
    }
}, 60000);
<?php endif; ?>
</script>

<?php include 'includes/footer.php'; ?>
