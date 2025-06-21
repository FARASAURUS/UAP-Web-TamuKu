<?php
require_once 'config/database.php';
require_once 'auth/session.php';
checkLogin();

$message = '';
$error = '';

// Proses tambah/edit data
if ($_POST) {
    $username = escape_string($_POST['username']);
    $nama = escape_string($_POST['nama']);
    $password = $_POST['password'];
    
    if (empty($username) || empty($nama)) {
        $error = 'Username dan nama tidak boleh kosong!';
    } else {
        if (isset($_POST['id']) && !empty($_POST['id'])) {
            // Update
            $id = (int)$_POST['id'];
            if (!empty($password)) {
                $password_hash = md5($password);
                $result = execute_query(
                    "UPDATE users SET username = ?, nama = ?, password = ? WHERE id = ?",
                    [$username, $nama, $password_hash, $id],
                    'sssi'
                );
            } else {
                $result = execute_query(
                    "UPDATE users SET username = ?, nama = ? WHERE id = ?",
                    [$username, $nama, $id],
                    'ssi'
                );
            }
            $message = $result ? 'Data pengguna berhasil diupdate!' : 'Gagal mengupdate data!';
        } else {
            // Insert
            if (empty($password)) {
                $error = 'Password tidak boleh kosong untuk pengguna baru!';
            } else {
                $password_hash = md5($password);
                $result = execute_query(
                    "INSERT INTO users (username, nama, password) VALUES (?, ?, ?)",
                    [$username, $nama, $password_hash],
                    'sss'
                );
                $message = $result ? 'Data pengguna berhasil ditambahkan!' : 'Gagal menambahkan data!';
            }
        }
    }
}

// Proses hapus data
if (isset($_GET['delete']) && !empty($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    
    // Cek apakah user yang akan dihapus adalah user yang sedang login
    if ($id == $_SESSION['user_id']) {
        $error = 'Anda tidak dapat menghapus akun yang sedang digunakan!';
    } else {
        $result = execute_query("DELETE FROM users WHERE id = ?", [$id], 'i');
        $message = $result ? 'Data pengguna berhasil dihapus!' : 'Gagal menghapus data!';
    }
    
    header('Location: users.php?msg=' . urlencode($message) . '&error=' . urlencode($error));
    exit();
}

// Handle message dari redirect
if (isset($_GET['msg'])) {
    $message = $_GET['msg'];
}
if (isset($_GET['error'])) {
    $error = $_GET['error'];
}

$title = 'Data Pengguna - Buku Tamu Digital';
include 'includes/header.php';
?>

<div class="container-fluid">
    <div class="row">
        <?php include 'includes/sidebar.php'; ?>
        
        <div class="col-md-9 col-lg-10 px-4">
            <div class="d-flex justify-content-between align-items-center py-3 border-bottom">
                <h2>Data Pengguna</h2>
                <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#userModal" onclick="resetForm()">
                    <i class="fas fa-plus"></i> Tambah Pengguna
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
                                    <th>Username</th>
                                    <th>Nama</th>
                                    <th>Tanggal Dibuat</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $query = "SELECT * FROM users ORDER BY nama";
                                $result = $conn->query($query);
                                $no = 1;
                                
                                if ($result && $result->num_rows > 0):
                                    while ($row = $result->fetch_assoc()):
                                ?>
                                    <tr>
                                        <td><?php echo $no++; ?></td>
                                        <td><?php echo htmlspecialchars($row['username']); ?></td>
                                        <td><?php echo htmlspecialchars($row['nama']); ?></td>
                                        <td><?php echo date('d/m/Y H:i', strtotime($row['created_at'])); ?></td>
                                        <td>
                                            <button class="btn btn-sm btn-warning" onclick="editData(<?php echo $row['id']; ?>, '<?php echo htmlspecialchars($row['username'], ENT_QUOTES); ?>', '<?php echo htmlspecialchars($row['nama'], ENT_QUOTES); ?>')">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                            <?php if ($row['id'] != $_SESSION['user_id']): ?>
                                                <a href="?delete=<?php echo $row['id']; ?>" class="btn btn-sm btn-danger" onclick="return confirmDelete()">
                                                    <i class="fas fa-trash"></i>
                                                </a>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                <?php 
                                    endwhile;
                                else:
                                ?>
                                    <tr>
                                        <td colspan="5" class="text-center">Belum ada data pengguna</td>
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

<!-- Modal Tambah/Edit User -->
<div class="modal fade" id="userModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalTitle">Tambah Pengguna</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" id="userForm">
                <div class="modal-body">
                    <input type="hidden" name="id" id="user_id">
                    
                    <div class="mb-3">
                        <label for="username" class="form-label">Username</label>
                        <input type="text" class="form-control" name="username" id="username" required>
                    </div>
                    
                    <div class="mb-3">
                        <label for="nama" class="form-label">Nama Lengkap</label>
                        <input type="text" class="form-control" name="nama" id="nama" required>
                    </div>
                    
                    <div class="mb-3">
                        <label for="password" class="form-label">Password <span id="passwordNote"></span></label>
                        <input type="password" class="form-control" name="password" id="password">
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
    document.getElementById('userForm').reset();
    document.getElementById('user_id').value = '';
    document.getElementById('modalTitle').textContent = 'Tambah Pengguna';
    document.getElementById('passwordNote').textContent = '';
    document.getElementById('password').required = true;
}

function editData(id, username, nama) {
    document.getElementById('user_id').value = id;
    document.getElementById('username').value = username;
    document.getElementById('nama').value = nama;
    document.getElementById('modalTitle').textContent = 'Edit Pengguna';
    document.getElementById('passwordNote').textContent = '(kosongkan jika tidak diubah)';
    document.getElementById('password').required = false;
    
    var modal = new bootstrap.Modal(document.getElementById('userModal'));
    modal.show();
}

function confirmDelete() {
    return confirm('Apakah Anda yakin ingin menghapus data ini?');
}
</script>

<?php include 'includes/footer.php'; ?>
