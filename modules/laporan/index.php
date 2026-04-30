<?php
include "../../includes/header.php";
include "../../includes/sidebar.php";
?>

<div class="container-fluid">

    <h2 class="fw-bold mb-4">
         Modul Laporan
    </h2>

    <div class="row">

        <!-- LAPORAN KEHADIRAN -->
        <div class="col-md-6">
            <div class="card shadow-sm mb-3">
                <div class="card-body text-center">
                    <i class="fas fa-user-check fa-3x mb-3 text-primary"></i>
                    <h5>Laporan Kehadiran</h5>
                    <p class="text-muted">Rekap absensi siswa</p>
                    <a href="laporan_kehadiran.php" class="btn btn-primary">
                        <i class="fas fa-eye"></i> Lihat
                    </a>
                </div>
            </div>
        </div>

        <!-- LAPORAN NILAI -->
        <div class="col-md-6">
            <div class="card shadow-sm mb-3">
                <div class="card-body text-center">
                    <i class="fas fa-chart-line fa-3x mb-3 text-success"></i>
                    <h5>Laporan Nilai</h5>
                    <p class="text-muted">Rekap nilai siswa</p>
                    <a href="laporan_nilai.php" class="btn btn-success">
                        <i class="fas fa-eye"></i> Lihat
                    </a>
                </div>
            </div>
        </div>

    </div>

</div>

<?php include "../../includes/footer.php"; ?>