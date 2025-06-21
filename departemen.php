<?php
require_once 'config/database.php';
require_once 'auth/session.php';
checkLogin();

$message = '';
$error = '';

// Proses tambah/edit data
if ($_POST) {
    $nama_departemen = escape_string($_POST['nama_departemen']);
    
    if (empty($nama_departemen)) {
        $error = 'Nama departemen tidak boleh kosong!';
    } else {
        if (isset($_POST['id']) && !empty($_POST['id'])) {
            // Update
            $id = (int)$_POST['id'];
            $result = execute_query(
                "UPDATE departemen SET nama_departemen = ? WHERE id = ?",
                [$nama_departemen, $id],
                'si'
            );
            $message = $result ? 'Data departemen berhasil diupdate!' : 'Gagal mengupdate data!';
        } else {
            // Insert
            $result = execute_query(
                "INSERT INTO departemen (nama_departemen) VALUES (?)",
                [$nama_departemen],
                's'
            );
            $message = $result ? 'Data departemen berhasil ditambahkan!' : 'Gagal menambahkan data!';
        }
    }
}

// Proses hapus data
if (isset($_GET['delete']) && !empty($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    
    // Cek apakah ada petugas yang terkait dengan departemen ini
    $check_petugas = execute_query("SELECT COUNT(*) as count FROM petugas WHERE departemen_id = ?", [$id], 'i');
    $petugas_count = $check_petugas->fetch_assoc()['count'];
    
    // Cek apakah ada tamu yang terkait dengan departemen ini
    $check_tamu = execute_query("SELECT COUNT(*) as count FROM tamu WHERE departemen_id = ?", [$id], 'i');
    $tamu_count = $check_tamu->fetch_assoc()['count'];
    
    if ($petugas_count > 0 || $tamu_count > 0) {
        $error = "Tidak dapat menghapus departemen ini karena masih ada " . 
                ($petugas_count > 0 ? $petugas_count . " petugas" : "") .
                ($petugas_count > 0 && $tamu_count > 0 ? " dan " : "") .
                ($tamu_count > 0 ? $tamu_count . " data tamu" : "") . 
                " yang terkait dengan departemen ini.";
    } else {
        $result = execute_query("DELETE FROM departemen WHERE id = ?", [$id], 'i');
        $message = $result ? 'Data departemen berhasil dihapus!' : 'Gagal menghapus data!';
    }
    
    // Redirect untuk menghindari refresh delete
    $redirect_params = [];
    if ($message) $redirect_params[] = 'msg=' . urlencode($message);
    if ($error) $redirect_params[] = 'error=' . urlencode($error);
    
    header('Location: departemen.php?' . implode('&', $redirect_params));
    exit();
}

// Handle message dari redirect
if (isset($_GET['msg'])) {
    $message = $_GET['msg'];
}
if (isset($_GET['error'])) {
    $error = $_GET['error'];
}

// Ambil data untuk edit
$edit_data = null;
if (isset($_GET['edit']) && !empty($_GET['edit'])) {
    $id = (int)$_GET['edit'];
    $result = execute_query("SELECT * FROM departemen WHERE id = ?", [$id], 'i');
    if ($result && $result->num_rows > 0) {
        $edit_data = $result->fetch_assoc();
    }
}

$title = 'Data Departemen - Buku Tamu Digital';
include 'includes/header.php';
?>

<div class="container-fluid">
    <div class="row">
        <?php include 'includes/sidebar.php'; ?>
        
        <div class="col-md-9 col-lg-10 px-4">
            <div class="d-flex justify-content-between align-items-center py-3 border-bottom">
                <h2>Data Departemen</h2>
                <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#departemenModal" onclick="resetForm()">
                    <i class="fas fa-plus"></i> Tambah Departemen
                </button>
            </div>
            
            <?php if ($message): ?>
                <div class="alert alert-success alert-dismissible fade show mt-3" role="alert">
                    <?php echo htmlspecialchars($message); ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>
            
            <?php if ($error): ?>
                <div class="alert alert-danger alert-dismissible fade show mt-3" role="alert">
                    <?php echo htmlspecialchars($error); ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>
            
            <div class="card mt-4">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Nama Departemen</th>
                                    <th>Jumlah Petugas</th>
                                    <th>Tanggal Dibuat</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $query = "SELECT d.*, COUNT(p.id) as jumlah_petugas 
                                         FROM departemen d 
                                         LEFT JOIN petugas p ON d.id = p.departemen_id 
                                         GROUP BY d.id 
                                         ORDER BY d.nama_departemen";
                                $result = $conn->query($query);
                                $no = 1;
                                
                                if ($result && $result->num_rows > 0):
                                    while ($row = $result->fetch_assoc()):
                                ?>
                                    <tr>
                                        <td><?php echo $no++; ?></td>
                                        <td><?php echo htmlspecialchars($row['nama_departemen']); ?></td>
                                        <td><?php echo $row['jumlah_petugas']; ?> orang</td>
                                        <td><?php echo date('d/m/Y H:i', strtotime($row['created_at'])); ?></td>
                                        <td>
                                            <button class="btn btn-sm btn-warning" onclick="editData(<?php echo $row['id']; ?>, '<?php echo htmlspecialchars($row['nama_departemen'], ENT_QUOTES); ?>')">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                            <a href="?delete=<?php echo $row['id']; ?>" class="btn btn-sm btn-danger" onclick="return confirmDelete()">
                                                <i class="fas fa-trash"></i>
                                            </a>
                                        </td>
                                    </tr>
                                <?php 
                                    endwhile;
                                else:
                                ?>
                                    <tr>
                                        <td colspan="5" class="text-center">Belum ada data departemen</td>
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

<!-- Modal Tambah/Edit Departemen -->
<div class="modal fade" id="departemenModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalTitle">Tambah Departemen</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" id="departemenForm">
                <div class="modal-body">
                    <input type="hidden" name="id" id="departemen_id">
                    
                    <div class="mb-3">
                        <label for="nama_departemen" class="form-label">Nama Departemen</label>
                        <input type="text" class="form-control" name="nama_departemen" id="nama_departemen" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function resetForm() {
    document.getElementById('departemenForm').reset();
    document.getElementById('departemen_id').value = '';
    document.getElementById('modalTitle').textContent = 'Tambah Departemen';
}

function editData(id, nama) {
    document.getElementById('departemen_id').value = id;
    document.getElementById('nama_departemen').value = nama;
    document.getElementById('modalTitle').textContent = 'Edit Departemen';
    
    var modal = new bootstrap.Modal(document.getElementById('departemenModal'));
    modal.show();
}

function confirmDelete() {
    return confirm('Apakah Anda yakin ingin menghapus data ini?');
}
</script>

<?php include 'includes/footer.php'; ?>
