<?php
include "../../config/database.php";

if (isset($_POST['simpan'])) {

    $nama = mysqli_real_escape_string($conn, $_POST['nama_kelas']);

    if ($nama == '') {
        $error = "Nama kelas wajib diisi";
    } else {
        mysqli_query($conn, "INSERT INTO kelas (nama_kelas) VALUES ('$nama')");
        header("Location: index.php");
        exit;
    }
}

include "../../includes/header.php";
include "../../includes/sidebar.php";
?>

<div class="container-fluid">

    <h2 class="fw-bold mb-3">
        <i class="fas fa-plus"></i> Tambah Kelas
    </h2>

    <?php if (!empty($error)): ?>
        <div class="alert alert-danger"><?= $error ?></div>
    <?php endif; ?>

    <div class="card">
        <div class="card-body">

            <form method="POST">

                <div class="mb-3">
                    <label>Nama Kelas</label>
                    <input type="text" name="nama_kelas" class="form-control" placeholder="Contoh: 1A" required>
                </div>

                <button name="simpan" class="btn btn-success">
                    <i class="fas fa-save"></i> Simpan
                </button>

                <a href="index.php" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Kembali
                </a>

            </form>

        </div>
    </div>

</div>

<?php include "../../includes/footer.php"; ?>