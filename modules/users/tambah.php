<?php

require_once '../../config/database.php';

$error = [];

// ambil data guru yang belum punya akun
$guru_list = mysqli_query($conn, "
    SELECT nip, nama_lengkap 
    FROM guru 
    WHERE id_user IS NULL
");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';
    $role     = $_POST['role'] ?? '';
    $nip      = $_POST['nip'] ?? '';

    // VALIDASI
    if (empty($username)) $error[] = "Username wajib diisi.";
    if (empty($password)) $error[] = "Password wajib diisi.";
    
    if (!in_array($role, ['Operator','Guru','TU','Kepsek'])) {
        $error[] = "Role tidak valid.";
    }

    // validasi khusus guru
    if ($role === 'Guru' && empty($nip)) {
        $error[] = "Pilih guru terlebih dahulu.";
    }

    // CEK DUPLIKAT USERNAME
    if (empty($error)) {
        $cek = mysqli_query($conn, "SELECT id_user FROM users WHERE username='$username'");
        if (mysqli_num_rows($cek) > 0) {
            $error[] = "Username sudah digunakan.";
        }
    }

    if (empty($error)) {

        $hash = password_hash($password, PASSWORD_DEFAULT);

        mysqli_begin_transaction($conn);

        try {

            // INSERT USER
            $sql = "INSERT INTO users (username, password, role)
                    VALUES ('$username', '$hash', '$role')";

            mysqli_query($conn, $sql);

            $id_user = mysqli_insert_id($conn);

            // JIKA ROLE GURU → UPDATE TABEL GURU
            if ($role === 'Guru') {
                $update = "UPDATE guru 
                           SET id_user = '$id_user' 
                           WHERE nip = '$nip'";

                mysqli_query($conn, $update);
            }

            mysqli_commit($conn);

            header("Location: index.php?msg=" . urlencode("User berhasil ditambahkan.") . "&type=success");
            exit;

        } catch (Exception $e) {
            mysqli_rollback($conn);
            $error[] = "Gagal menyimpan data.";
        }
    }
}
?>

<?php include_once '../../includes/header.php'; ?>
<?php include_once '../../includes/sidebar.php'; ?>

<div class="container-fluid">

    <h4 class="mb-4">Tambah User</h4>

    <?php if (!empty($error)): ?>
        <div class="alert alert-danger">
            <ul>
                <?php foreach($error as $e): ?>
                    <li><?= $e ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <div class="card">
        <div class="card-body">

            <form method="POST">

                <div class="mb-3">
                    <label>Username</label>
                    <input type="text" name="username" class="form-control">
                </div>

                <div class="mb-3">
                    <label>Password</label>
                    <input type="password" name="password" class="form-control">
                </div>

                <div class="mb-3">
                    <label>Role</label>
                    <select name="role" id="role" class="form-select">
                        <option value="">Pilih</option>
                        <option value="Operator">Operator</option>
                        <option value="Guru">Guru</option>
                        <option value="TU">TU</option>
                        <option value="Kepsek">Kepsek</option>
                    </select>
                </div>

                <!-- DROPDOWN GURU -->
                <div class="mb-3" id="guruField" style="display:none;">
                    <label>Pilih Guru</label>
                    <select name="nip" class="form-select">
                        <option value="">-- Pilih Guru --</option>
                        <?php while($g = mysqli_fetch_assoc($guru_list)): ?>
                            <option value="<?= $g['nip'] ?>">
                                <?= $g['nama_lengkap'] ?> (<?= $g['nip'] ?>)
                            </option>
                        <?php endwhile; ?>
                    </select>
                </div>

                <button class="btn btn-success">Simpan</button>
                <a href="index.php" class="btn btn-secondary">Batal</a>

            </form>

        </div>
    </div>

</div>

<script>
// tampilkan dropdown guru hanya jika role = Guru
document.getElementById('role').addEventListener('change', function() {
    let guruField = document.getElementById('guruField');
    if (this.value === 'Guru') {
        guruField.style.display = 'block';
    } else {
        guruField.style.display = 'none';
    }
});
</script>

<?php include_once '../../includes/footer.php'; ?>