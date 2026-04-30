<?php
include "../../config/database.php";

// Hapus data
if (isset($_GET['hapus'])) {
    $id = (int)$_GET['hapus'];
    mysqli_query($conn, "DELETE FROM kelas WHERE id_kelas=$id");
    header("Location: index.php");
    exit;
}

// Ambil data
$query = mysqli_query($conn, "SELECT * FROM kelas ORDER BY nama_kelas");

include "../../includes/header.php";
include "../../includes/sidebar.php";
?>

<div class="container-fluid">

    <!-- HEADER -->
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h2 class="fw-bold mb-0">
            Data Kelas
        </h2>

        <a href="tambah.php" class="btn btn-primary">
            <i class="fas fa-plus"></i> Tambah
        </a>
    </div>

    <!-- TABLE -->
    <div class="card">
        <div class="card-body table-responsive">
            <table class="table table-bordered table-hover">
                <thead class="table-light">
                    <tr>
                        <th width="5%">No</th>
                        <th>Nama Kelas</th>
                        <th width="15%">Aksi</th>
                    </tr>
                </thead>
                <tbody>

                <?php $no=1; while($k = mysqli_fetch_assoc($query)): ?>
                <tr>
                    <td><?= $no++ ?></td>
                    <td><?= $k['nama_kelas'] ?></td>
                    <td>
                        <a href="edit.php?id=<?= $k['id_kelas'] ?>" class="btn btn-warning btn-sm">
                            <i class="fas fa-edit"></i>
                        </a>

                        <a href="?hapus=<?= $k['id_kelas'] ?>" 
                           onclick="return confirm('Hapus data ini?')" 
                           class="btn btn-danger btn-sm">
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

<?php include "../../includes/footer.php"; ?>