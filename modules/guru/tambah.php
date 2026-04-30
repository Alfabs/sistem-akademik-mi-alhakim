<?php
include "../../config/database.php";

if (isset($_POST['simpan'])) {

    $nip   = mysqli_real_escape_string($conn, $_POST['nip']);
    $nama  = mysqli_real_escape_string($conn, $_POST['nama']);
    $hp    = mysqli_real_escape_string($conn, $_POST['no_hp']);

    if ($nip == '' || $nama == '') {
        $error = "NIP dan Nama wajib diisi";
    } else {

        mysqli_query($conn, "
            INSERT INTO guru (nip, nama_lengkap, no_hp)
            VALUES ('$nip', '$nama', '$hp')
        ");

        header("Location: index.php");
        exit;
    }
}

include "../../includes/header.php";
include "../../includes/sidebar.php";
?>

<div class="container-fluid">

    <h2 class="fw-bold mb-3">
        <i class="fas fa-plus"></i> Tambah Guru
    </h2>

    <?php if (!empty($error)): ?>
        <div class="alert alert-danger"><?= $error ?></div>
    <?php endif; ?>

    <div class="card">
        <div class="card-body">

            <form method="POST">

                <div class="mb-3">
                    <label>NIP</label>
                    <input type="text" name="nip" class="form-control" required>
                </div>

                <div class="mb-3">
                    <label>Nama Lengkap</label>
                    <input type="text" name="nama" class="form-control" required>
                </div>

                <div class="mb-3">
                    <label>No HP</label>
                    <input type="text" name="no_hp" class="form-control">
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