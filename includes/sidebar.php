<div class="col-md-3 col-lg-2 px-0">
    <div class="sidebar text-white p-3">
        <h4 class="mb-4"><i class="fas fa-book"></i> TamuKu</h4>
        <ul class="nav flex-column">
            <li class="nav-item mb-2">
                <a href="dashboard.php" class="nav-link text-white <?php echo basename($_SERVER['PHP_SELF']) == 'dashboard.php' ? 'active' : ''; ?>">
                    <i class="fas fa-tachometer-alt me-2"></i> Dashboard
                </a>
            </li>
            <li class="nav-item mb-2">
                <a href="tamu.php" class="nav-link text-white <?php echo basename($_SERVER['PHP_SELF']) == 'tamu.php' ? 'active' : ''; ?>">
                    <i class="fas fa-users me-2"></i> Data Tamu
                </a>
            </li>
            <li class="nav-item mb-2">
                <a href="keperluan.php" class="nav-link text-white <?php echo basename($_SERVER['PHP_SELF']) == 'keperluan.php' ? 'active' : ''; ?>">
                    <i class="fas fa-clipboard-list me-2"></i> Keperluan
                </a>
            </li>
            <li class="nav-item mb-2">
                <a href="petugas.php" class="nav-link text-white <?php echo basename($_SERVER['PHP_SELF']) == 'petugas.php' ? 'active' : ''; ?>">
                    <i class="fas fa-user-tie me-2"></i> Petugas
                </a>
            </li>
            <li class="nav-item mb-2">
                <a href="departemen.php" class="nav-link text-white <?php echo basename($_SERVER['PHP_SELF']) == 'departemen.php' ? 'active' : ''; ?>">
                    <i class="fas fa-building me-2"></i> Departemen
                </a>
            </li>
            <li class="nav-item mb-2">
                <a href="users.php" class="nav-link text-white <?php echo basename($_SERVER['PHP_SELF']) == 'users.php' ? 'active' : ''; ?>">
                    <i class="fas fa-user-cog me-2"></i> Pengguna
                </a>
            </li>
            <hr class="my-3">
            <li class="nav-item mb-2">
                <a href="kunjungan-tamu.php" class="nav-link text-white" target="_blank">
                    <i class="fas fa-external-link-alt me-2"></i> Form Tamu
                </a>
            </li>
            <li class="nav-item mb-2">
                <a href="riwayat-kunjungan.php" class="nav-link text-white <?php echo basename($_SERVER['PHP_SELF']) == 'riwayat-kunjungan.php' ? 'active' : ''; ?>">
                    <i class="fas fa-history me-2"></i> Riwayat Kunjungan
                </a>
            </li>
            <li class="nav-item mt-4">
                <a href="logout.php" class="nav-link text-white">
                    <i class="fas fa-sign-out-alt me-2"></i> Logout
                </a>
            </li>
        </ul>
    </div>
</div>
