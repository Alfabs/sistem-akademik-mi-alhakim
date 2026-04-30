<?php
include "../../config/database.php";

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// Ambil data
$data = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM kelas WHERE id_kelas=$id"));

if (!$data) {
    die("Data tidak ditemukan");
}

// Update
if (isset($_POST['update'])) {

    $nama = mysqli_real_escape_string($conn, $_POST['nama_kelas']);

    if ($nama == '') {
        $error = "Nama kelas wajib diisi";
    } else {
        mysqli_query($conn, "UPDATE kelas SET nama_kelas='$nama' WHERE id_kelas=$id");
        header("Location: index.php");
        exit;
    }
}

include "../../includes/header.php";
include "../../includes/sidebar.php";
?>

<div class="container-fluid">

    <h2 class="fw-bold mb-3">
        <i class="fas fa-edit"></i> Edit Kelas
    </h2>

    <?php if (!empty($error)): ?>
        <div class="alert alert-danger"><?= $error ?></div>
    <?php endif; ?>

    <div class="card">
        <div class="card-body">

            <form method="POST">

                <div class="mb-3">
                    <label>Nama Kelas</label>
                    <input type="text" name="nama_kelas" class="form-control"
                        value="<?= $data['nama_kelas'] ?>" required>
                </div>

                <button name="update" class="btn btn-success">
                    <i class="fas fa-save"></i> Update
                </button>

                <a href="index.php" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Kembali
                </a>

            </form>

        </div>
    </div>

</div>

<?php include "../../includes/footer.php"; ?>