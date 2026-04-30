<?php
require_once '../../config/database.php';
require_once '../../includes/auth.php';

// Hanya Operator yang bisa manajemen user
require_role('Operator');

$pesan = '';
$tipe_pesan = '';

// HAPUS USER
if (isset($_GET['hapus']) && !empty($_GET['hapus'])) {
    $id = (int)$_GET['hapus'];
    
    // Jangan biarkan hapus diri sendiri
    if ($id == $_SESSION['id_user']) {
        header("Location: index.php?msg=Tidak bisa menghapus akun sendiri!&type=danger");
        exit;
    }

    $del = mysqli_query($conn, "DELETE FROM users WHERE id_user=$id");

    if ($del) {
        $pesan = "User berhasil dihapus.";
        $tipe_pesan = 'success';
    } else {
        $pesan = "Gagal menghapus user.";
        $tipe_pesan = 'danger';
    }

    header("Location: index.php?msg=" . urlencode($pesan) . "&type=$tipe_pesan");
    exit;
}

if (isset($_GET['msg'])) {
    $pesan = $_GET['msg'];
    $tipe_pesan = $_GET['type'] ?? 'info';
}

// AMBIL DATA
$result = mysqli_query($conn, "SELECT * FROM users ORDER BY id_user DESC");

$data = [];
while ($row = mysqli_fetch_assoc($result)) {
    $data[] = $row;
}
?>

<?php include_once '../../includes/header.php'; ?>
<?php include_once '../../includes/sidebar.php'; ?>

<div class="container-fluid">

    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4>Data User</h4>
        <a href="tambah.php" class="btn btn-primary">+ Tambah User</a>
    </div>

    <?php if ($pesan): ?>
        <div class="alert alert-<?= $tipe_pesan ?>">
            <?= $pesan ?>
        </div>
    <?php endif; ?>

    <div class="card">
        <div class="card-body table-responsive">

            <table class="table table-bordered table-hover">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Username</th>
                        <th>Role</th>
                        <th width="150">Aksi</th>
                    </tr>
                </thead>
                <tbody>

                <?php if(empty($data)): ?>
                    <tr><td colspan="4" class="text-center">Tidak ada data</td></tr>
                <?php else: ?>
                    <?php foreach($data as $i => $u): ?>
                    <tr>
                        <td><?= $i+1 ?></td>
                        <td><?= $u['username'] ?></td>
                        <td><?= $u['role'] ?></td>
                        <td>
                            <a style="color: white;" href="edit.php?id=<?= $u['id_user'] ?>" class="btn btn-warning btn-sm">Edit</a>

                            <?php if($u['id_user'] != $_SESSION['id_user']): ?>
                            <a href="index.php?hapus=<?= $u['id_user'] ?>"
                               class="btn btn-danger btn-sm"
                               onclick="return confirm('Yakin hapus?')">
                               Hapus
                            </a>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                <?php endif; ?>

                </tbody>
            </table>

        </div>
    </div>

</div>

<?php include_once '../../includes/footer.php'; ?>
