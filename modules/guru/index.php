<?php
include "../../config/database.php";

// Hapus data guru
if (isset($_GET['hapus'])) {
    $id = mysqli_real_escape_string($conn, $_GET['hapus']);

    mysqli_query($conn, "DELETE FROM guru WHERE nip='$id'");
    header("Location: index.php");
    exit;
}

// Ambil data guru + user (opsional)
$query = mysqli_query($conn, "
    SELECT g.*, u.username, u.role
    FROM guru g
    LEFT JOIN users u ON g.id_user = u.id_user
    ORDER BY g.nama_lengkap
");

include "../../includes/header.php";
include "../../includes/sidebar.php";
?>

<div class="container-fluid">

    <!-- HEADER -->
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h2 class="fw-bold mb-0">
             Data Guru
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
                        <th>No</th>
                        <th>NIP</th>
                        <th>Nama</th>
                        <th>No HP</th>
                        <th width="20%">Aksi</th>
                    </tr>
                </thead>

                <tbody>
                <?php $no = 1; while ($g = mysqli_fetch_assoc($query)): ?>
                    <tr>
                        <td><?= $no++ ?></td>
                        <td><?= $g['nip'] ?></td>
                        <td><?= $g['nama_lengkap'] ?></td>
                        <td><?= $g['no_hp'] ?: '-' ?></td>
                        <td>
                            <a style="color: white;" href="edit.php?nip=<?= $g['nip'] ?>" class="btn btn-warning btn-sm">
                                <i class="fas fa-edit"></i>
                            </a>

                            <a href="?hapus=<?= $g['nip'] ?>"
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