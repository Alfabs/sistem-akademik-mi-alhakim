<?php
include "../../config/database.php";

$nip = mysqli_real_escape_string($conn, $_GET['nip']);

// Ambil data
$data = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM guru WHERE nip='$nip'"));

if (!$data) {
    die("Data tidak ditemukan");
}

// Update
if (isset($_POST['update'])) {

    $nama = mysqli_real_escape_string($conn, $_POST['nama']);
    $hp   = mysqli_real_escape_string($conn, $_POST['no_hp']);

    mysqli_query($conn, "
        UPDATE guru 
        SET nama_lengkap='$nama', no_hp='$hp'
        WHERE nip='$nip'
    ");

    header("Location: index.php");
    exit;
}

include "../../includes/header.php";
include "../../includes/sidebar.php";
?>

<div class="container-fluid">

    <h2 class="fw-bold mb-3">
        <i class="fas fa-edit"></i> Edit Guru
    </h2>

    <div class="card">
        <div class="card-body">

            <form method="POST">

                <div class="mb-3">
                    <label>NIP</label>
                    <input type="text" class="form-control" value="<?= $data['nip'] ?>" readonly>
                </div>

                <div class="mb-3">
                    <label>Nama Lengkap</label>
                    <input type="text" name="nama" class="form-control"
                        value="<?= $data['nama_lengkap'] ?>" required>
                </div>

                <div class="mb-3">
                    <label>No HP</label>
                    <input type="text" name="no_hp" class="form-control"
                        value="<?= $data['no_hp'] ?>">
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