<?php

require_once '../../config/database.php';

$id = (int)($_GET['id'] ?? 0);

if ($id === 0) {
    header("Location: index.php");
    exit;
}

// AMBIL DATA
$res = mysqli_query($conn, "SELECT * FROM users WHERE id_user=$id");
if (mysqli_num_rows($res) === 0) {
    header("Location: index.php");
    exit;
}
$user = mysqli_fetch_assoc($res);

$error = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';
    $role     = $_POST['role'] ?? '';

    if (empty($username)) $error[] = "Username wajib diisi.";
    if (!in_array($role, ['Operator','Guru','TU','Kepsek'])) {
        $error[] = "Role tidak valid.";
    }

    if (empty($error)) {

        // jika password diisi → update
        if (!empty($password)) {
            $hash = password_hash($password, PASSWORD_DEFAULT);
            $sql = "UPDATE users SET 
                        username='$username',
                        password='$hash',
                        role='$role'
                    WHERE id_user=$id";
        } else {
            $sql = "UPDATE users SET 
                        username='$username',
                        role='$role'
                    WHERE id_user=$id";
        }

        if (mysqli_query($conn, $sql)) {
            header("Location: index.php?msg=" . urlencode("User berhasil diupdate.") . "&type=success");
            exit;
        } else {
            $error[] = "Gagal update.";
        }
    }

    $user['username'] = $username;
    $user['role']     = $role;
}
?>

<?php include_once '../../includes/header.php'; ?>
<?php include_once '../../includes/sidebar.php'; ?>

<div class="container-fluid">

    <h4 class="mb-4">Edit User</h4>

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
                    <input type="text" name="username"
                           class="form-control"
                           value="<?= $user['username'] ?>">
                </div>

                <div class="mb-3">
                    <label>Password (kosongkan jika tidak diubah)</label>
                    <input type="password" name="password" class="form-control">
                </div>

                <div class="mb-3">
                    <label>Role</label>
                    <select name="role" class="form-select">
                        <option value="Operator" <?= $user['role']=='Operator'?'selected':'' ?>>Operator</option>
                        <option value="Guru" <?= $user['role']=='Guru'?'selected':'' ?>>Guru</option>
                        <option value="TU" <?= $user['role']=='TU'?'selected':'' ?>>TU</option>
                        <option value="Kepsek" <?= $user['role']=='Kepsek'?'selected':'' ?>>Kepsek</option>
                    </select>
                </div>

                <button class="btn btn-success">Simpan</button>
                <a href="index.php" class="btn btn-secondary">Batal</a>

            </form>

        </div>
    </div>

</div>

<?php include_once '../../includes/footer.php'; ?>