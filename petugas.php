<?php
require_once 'config/database.php';
require_once 'auth/session.php';
checkLogin();

$message = '';
$error = '';

// Proses tambah/edit data
if ($_POST) {
    $nama_petugas = escape_string($_POST['nama_petugas']);
    $departemen_id = (int)$_POST['departemen_id'];
    
    if (empty($nama_petugas) || $departemen_id == 0) {
        $error = 'Nama petugas dan departemen tidak boleh kosong!';
    } else {
        if (isset($_POST['id']) && !empty($_POST['id'])) {
            // Update
            $id = (int)$_POST['id'];
            $result = execute_query(
                "UPDATE petugas SET nama_petugas = ?, departemen_id = ? WHERE id = ?",
                [$nama_petugas, $departemen_id, $id],
                'sii'
            );
            $message = $result ? 'Data petugas berhasil diupdate!' : 'Gagal mengupdate data!';
        } else {
            // Insert
            $result = execute_query(
                "INSERT INTO petugas (nama_petugas, departemen_id) VALUES (?, ?)",
                [$nama_petugas, $departemen_id],
                'si'
            );
            $message = $result ? 'Data petugas berhasil ditambahkan!' : 'Gagal menambahkan data!';
        }
    }
}

// Proses hapus data
if (isset($_GET['delete']) && !empty($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    $result = execute_query("DELETE FROM petugas WHERE id = ?", [$id], 'i');
    $message = $result ? 'Data petugas berhasil dihapus!' : 'Gagal menghapus data!';
    
    header('Location: petugas.php?msg=' . urlencode($message));
    exit();
}

// Handle message dari redirect
if (isset($_GET['msg'])) {
    $message = $_GET['msg'];
}

// Ambil data untuk edit
$edit_data = null;
if (isset($_GET['edit'])) {
    $id = $_GET['edit'];
    $edit_data = $conn->query("SELECT * FROM petugas WHERE id='$id'")->fetch_assoc();
}

$title = 'Data Petugas - Buku Tamu Digital';
include 'includes/header.php';
?>

<div class="container-fluid">
    <div class="row">
        <?php include 'includes/sidebar.php'; ?>
        
        <div class="col-md-9 col-lg-10 px-4">
            <div class="d-flex justify-content-between align-items-center py-3 border-bottom">
                <h2>Data Petugas</h2>
                <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#petugasModal" onclick="resetForm()">
                    <i class="fas fa-plus"></i> Tambah Petugas
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
                                    <th>Nama Petugas</th>
                                    <th>Departemen</th>
                                    <th>Tanggal Dibuat</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $query = "SELECT p.*, d.nama_departemen 
                                         FROM petugas p 
                                         LEFT JOIN departemen d ON p.departemen_id = d.id 
                                         ORDER BY p.nama_petugas";
                                $result = $conn->query($query);
                                $no = 1;
                                
                                if ($result && $result->num_rows > 0):
                                    while ($row = $result->fetch_assoc()):
                                ?>
                                    <tr>
                                        <td><?php echo $no++; ?></td>
                                        <td><?php echo htmlspecialchars($row['nama_petugas']); ?></td>
                                        <td><?php echo htmlspecialchars($row['nama_departemen']); ?></td>
                                        <td><?php echo date('d/m/Y H:i', strtotime($row['created_at'])); ?></td>
                                        <td>
                                            <button class="btn btn-sm btn-warning" onclick="editData(<?php echo $row['id']; ?>, '<?php echo htmlspecialchars($row['nama_petugas'], ENT_QUOTES); ?>', <?php echo $row['departemen_id']; ?>)">
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
                                        <td colspan="5" class="text-center">Belum ada data petugas</td>
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

<!-- Modal Tambah/Edit Petugas -->
<div class="modal fade" id="petugasModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalTitle">Tambah Petugas</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" id="petugasForm">
                <div class="modal-body">
                    <input type="hidden" name="id" id="petugas_id">
                    
                    <div class="mb-3">
                        <label for="nama_petugas" class="form-label">Nama Petugas</label>
                        <input type="text" class="form-control" name="nama_petugas" id="nama_petugas" required>
                    </div>
                    
                    <div class="mb-3">
                        <label for="departemen_id" class="form-label">Departemen</label>
                        <select class="form-control" name="departemen_id" id="departemen_id" required>
                            <option value="">Pilih Departemen</option>
                            <?php
                            $departemen = $conn->query("SELECT * FROM departemen ORDER BY nama_departemen");
                            if ($departemen):
                                while ($d = $departemen->fetch_assoc()):
                            ?>
                                <option value="<?php echo $d['id']; ?>">
                                    <?php echo htmlspecialchars($d['nama_departemen']); ?>
                                </option>
                            <?php 
                                endwhile;
                            endif;
                            ?>
                        </select>
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
    document.getElementById('petugasForm').reset();
    document.getElementById('petugas_id').value = '';
    document.getElementById('modalTitle').textContent = 'Tambah Petugas';
}

function editData(id, nama, departemenId) {
    document.getElementById('petugas_id').value = id;
    document.getElementById('nama_petugas').value = nama;
    document.getElementById('departemen_id').value = departemenId;
    document.getElementById('modalTitle').textContent = 'Edit Petugas';
    
    var modal = new bootstrap.Modal(document.getElementById('petugasModal'));
    modal.show();
}

function confirmDelete() {
    return confirm('Apakah Anda yakin ingin menghapus data ini?');
}
</script>

<?php include 'includes/footer.php'; ?>
