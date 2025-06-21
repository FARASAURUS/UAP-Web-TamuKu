<?php
require_once 'config/database.php';
require_once 'auth/session.php';
checkLogin();

$message = '';

// Proses tambah/edit data
if ($_POST) {
    $nama_tamu = $_POST['nama_tamu'];
    $no_telepon = $_POST['no_telepon'];
    $alamat = $_POST['alamat'];
    $keperluan_id = $_POST['keperluan_id'];
    $petugas_id = $_POST['petugas_id'];
    $departemen_id = $_POST['departemen_id'];
    
    if (isset($_POST['id']) && $_POST['id']) {
        // Update
        $id = $_POST['id'];
        $query = "UPDATE tamu SET nama_tamu='$nama_tamu', no_telepon='$no_telepon', alamat='$alamat', 
                  keperluan_id='$keperluan_id', petugas_id='$petugas_id', departemen_id='$departemen_id' 
                  WHERE id='$id'";
        $message = 'Data tamu berhasil diupdate!';
    } else {
        // Insert
        $query = "INSERT INTO tamu (nama_tamu, no_telepon, alamat, keperluan_id, petugas_id, departemen_id) 
                  VALUES ('$nama_tamu', '$no_telepon', '$alamat', '$keperluan_id', '$petugas_id', '$departemen_id')";
        $message = 'Data tamu berhasil ditambahkan!';
    }
    
    $conn->query($query);
}

// Proses hapus data
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    $conn->query("DELETE FROM tamu WHERE id='$id'");
    $message = 'Data tamu berhasil dihapus!';
}

// Ambil data untuk edit
$edit_data = null;
if (isset($_GET['edit'])) {
    $id = $_GET['edit'];
    $edit_data = $conn->query("SELECT * FROM tamu WHERE id='$id'")->fetch_assoc();
}

$title = 'Data Tamu - Buku Tamu Digital';
include 'includes/header.php';
?>

<div class="container-fluid">
    <div class="row">
        <?php include 'includes/sidebar.php'; ?>
        
        <div class="col-md-9 col-lg-10 px-4">
            <div class="d-flex justify-content-between align-items-center py-3 border-bottom">
                <h2>Data Tamu</h2>
                <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#tamuModal">
                    <i class="fas fa-plus"></i> Tambah Tamu
                </button>
            </div>
            
            <?php if ($message): ?>
                <div class="alert alert-success mt-3"><?php echo $message; ?></div>
            <?php endif; ?>
            
            <div class="card mt-4">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Nama Tamu</th>
                                    <th>No. Telepon</th>
                                    <th>Alamat</th>
                                    <th>Keperluan</th>
                                    <th>Petugas</th>
                                    <th>Departemen</th>
                                    <th>Waktu Kunjungan</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $query = "SELECT t.*, k.nama_keperluan, p.nama_petugas, d.nama_departemen 
                                         FROM tamu t 
                                         LEFT JOIN keperluan k ON t.keperluan_id = k.id 
                                         LEFT JOIN petugas p ON t.petugas_id = p.id 
                                         LEFT JOIN departemen d ON t.departemen_id = d.id 
                                         ORDER BY t.waktu_kunjungan DESC";
                                $result = $conn->query($query);
                                $no = 1;
                                
                                while ($row = $result->fetch_assoc()):
                                ?>
                                    <tr>
                                        <td><?php echo $no++; ?></td>
                                        <td><?php echo $row['nama_tamu']; ?></td>
                                        <td><?php echo $row['no_telepon']; ?></td>
                                        <td><?php echo $row['alamat']; ?></td>
                                        <td><?php echo $row['nama_keperluan']; ?></td>
                                        <td><?php echo $row['nama_petugas']; ?></td>
                                        <td><?php echo $row['nama_departemen']; ?></td>
                                        <td><?php echo date('d/m/Y H:i', strtotime($row['waktu_kunjungan'])); ?></td>
                                        <td>
                                            <a href="?edit=<?php echo $row['id']; ?>" class="btn btn-sm btn-warning">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <a href="?delete=<?php echo $row['id']; ?>" class="btn btn-sm btn-danger" onclick="return confirmDelete()">
                                                <i class="fas fa-trash"></i>
                                            </a>
                                        </td>
                                    </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Tambah/Edit Tamu -->
<div class="modal fade" id="tamuModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><?php echo $edit_data ? 'Edit' : 'Tambah'; ?> Tamu</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST">
                <div class="modal-body">
                    <?php if ($edit_data): ?>
                        <input type="hidden" name="id" value="<?php echo $edit_data['id']; ?>">
                    <?php endif; ?>
                    
                    <div class="mb-3">
                        <label for="nama_tamu" class="form-label">Nama Tamu</label>
                        <input type="text" class="form-control" name="nama_tamu" 
                               value="<?php echo $edit_data['nama_tamu'] ?? ''; ?>" required>
                    </div>
                    
                    <div class="mb-3">
                        <label for="no_telepon" class="form-label">No. Telepon</label>
                        <input type="text" class="form-control" name="no_telepon" 
                               value="<?php echo $edit_data['no_telepon'] ?? ''; ?>">
                    </div>
                    
                    <div class="mb-3">
                        <label for="alamat" class="form-label">Alamat</label>
                        <textarea class="form-control" name="alamat" rows="3"><?php echo $edit_data['alamat'] ?? ''; ?></textarea>
                    </div>
                    
                    <div class="mb-3">
                        <label for="keperluan_id" class="form-label">Keperluan</label>
                        <select class="form-control" name="keperluan_id" required>
                            <option value="">Pilih Keperluan</option>
                            <?php
                            $keperluan = $conn->query("SELECT * FROM keperluan ORDER BY nama_keperluan");
                            while ($k = $keperluan->fetch_assoc()):
                            ?>
                                <option value="<?php echo $k['id']; ?>" 
                                        <?php echo ($edit_data['keperluan_id'] ?? '') == $k['id'] ? 'selected' : ''; ?>>
                                    <?php echo $k['nama_keperluan']; ?>
                                </option>
                            <?php endwhile; ?>
                        </select>
                    </div>
                    
                    <div class="mb-3">
                        <label for="departemen_id" class="form-label">Departemen</label>
                        <select class="form-control" name="departemen_id" required>
                            <option value="">Pilih Departemen</option>
                            <?php
                            $departemen = $conn->query("SELECT * FROM departemen ORDER BY nama_departemen");
                            while ($d = $departemen->fetch_assoc()):
                            ?>
                                <option value="<?php echo $d['id']; ?>" 
                                        <?php echo ($edit_data['departemen_id'] ?? '') == $d['id'] ? 'selected' : ''; ?>>
                                    <?php echo $d['nama_departemen']; ?>
                                </option>
                            <?php endwhile; ?>
                        </select>
                    </div>
                    
                    <div class="mb-3">
                        <label for="petugas_id" class="form-label">Petugas</label>
                        <select class="form-control" name="petugas_id" required>
                            <option value="">Pilih Petugas</option>
                            <?php
                            $petugas = $conn->query("SELECT p.*, d.nama_departemen FROM petugas p LEFT JOIN departemen d ON p.departemen_id = d.id ORDER BY p.nama_petugas");
                            while ($p = $petugas->fetch_assoc()):
                            ?>
                                <option value="<?php echo $p['id']; ?>" 
                                        <?php echo ($edit_data['petugas_id'] ?? '') == $p['id'] ? 'selected' : ''; ?>>
                                    <?php echo $p['nama_petugas'] . ' (' . $p['nama_departemen'] . ')'; ?>
                                </option>
                            <?php endwhile; ?>
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

<?php if ($edit_data): ?>
<script>
    // Tampilkan modal jika ada data edit
    var modal = new bootstrap.Modal(document.getElementById('tamuModal'));
    modal.show();
</script>
<?php endif; ?>

<?php include 'includes/footer.php'; ?>
