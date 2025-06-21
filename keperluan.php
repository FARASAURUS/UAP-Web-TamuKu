<?php
require_once 'config/database.php';
require_once 'auth/session.php';
checkLogin();

$message = '';
$error = '';

// Proses tambah/edit data
if ($_POST) {
    $nama_keperluan = escape_string($_POST['nama_keperluan']);
    
    if (empty($nama_keperluan)) {
        $error = 'Nama keperluan tidak boleh kosong!';
    } else {
        if (isset($_POST['id']) && !empty($_POST['id'])) {
            // Update
            $id = (int)$_POST['id'];
            $result = execute_query(
                "UPDATE keperluan SET nama_keperluan = ? WHERE id = ?",
                [$nama_keperluan, $id],
                'si'
            );
            $message = $result ? 'Data keperluan berhasil diupdate!' : 'Gagal mengupdate data!';
        } else {
            // Insert
            $result = execute_query(
                "INSERT INTO keperluan (nama_keperluan) VALUES (?)",
                [$nama_keperluan],
                's'
            );
            $message = $result ? 'Data keperluan berhasil ditambahkan!' : 'Gagal menambahkan data!';
        }
    }
}

// Proses hapus data
if (isset($_GET['delete']) && !empty($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    $result = execute_query("DELETE FROM keperluan WHERE id = ?", [$id], 'i');
    $message = $result ? 'Data keperluan berhasil dihapus!' : 'Gagal menghapus data!';
    
    header('Location: keperluan.php?msg=' . urlencode($message));
    exit();
}

// Handle message dari redirect
if (isset($_GET['msg'])) {
    $message = $_GET['msg'];
}

$title = 'Data Keperluan - Buku Tamu Digital';
include 'includes/header.php';
?>

<div class="container-fluid">
    <div class="row">
        <?php include 'includes/sidebar.php'; ?>
        
        <div class="col-md-9 col-lg-10 px-4">
            <div class="d-flex justify-content-between align-items-center py-3 border-bottom">
                <h2>Data Keperluan</h2>
                <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#keperluanModal" onclick="resetForm()">
                    <i class="fas fa-plus"></i> Tambah Keperluan
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
                                    <th>Nama Keperluan</th>
                                    <th>Tanggal Dibuat</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $query = "SELECT * FROM keperluan ORDER BY nama_keperluan";
                                $result = $conn->query($query);
                                $no = 1;
                                
                                if ($result && $result->num_rows > 0):
                                    while ($row = $result->fetch_assoc()):
                                ?>
                                    <tr>
                                        <td><?php echo $no++; ?></td>
                                        <td><?php echo htmlspecialchars($row['nama_keperluan']); ?></td>
                                        <td><?php echo date('d/m/Y H:i', strtotime($row['created_at'])); ?></td>
                                        <td>
                                            <button class="btn btn-sm btn-warning" onclick="editData(<?php echo $row['id']; ?>, '<?php echo htmlspecialchars($row['nama_keperluan'], ENT_QUOTES); ?>')">
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
                                        <td colspan="4" class="text-center">Belum ada data keperluan</td>
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

<!-- Modal Tambah/Edit Keperluan -->
<div class="modal fade" id="keperluanModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalTitle">Tambah Keperluan</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" id="keperluanForm">
                <div class="modal-body">
                    <input type="hidden" name="id" id="keperluan_id">
                    
                    <div class="mb-3">
                        <label for="nama_keperluan" class="form-label">Nama Keperluan</label>
                        <input type="text" class="form-control" name="nama_keperluan" id="nama_keperluan" required>
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
    document.getElementById('keperluanForm').reset();
    document.getElementById('keperluan_id').value = '';
    document.getElementById('modalTitle').textContent = 'Tambah Keperluan';
}

function editData(id, nama) {
    document.getElementById('keperluan_id').value = id;
    document.getElementById('nama_keperluan').value = nama;
    document.getElementById('modalTitle').textContent = 'Edit Keperluan';
    
    var modal = new bootstrap.Modal(document.getElementById('keperluanModal'));
    modal.show();
}

function confirmDelete() {
    return confirm('Apakah Anda yakin ingin menghapus data ini?');
}
</script>

<?php include 'includes/footer.php'; ?>
