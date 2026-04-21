<!-- modules/siswa/detail.php -->

<?php
require_once '../../config/database.php';

$nisn = $_GET['nisn'] ?? '';

if (!$nisn) {
    header("Location:index.php");
    exit;
}

$nisn = mysqli_real_escape_string($conn, $nisn);

$query = mysqli_query($conn,"
SELECT 
    s.*,
    k.nama_kelas,
    ta.tahun,
    ta.semester
FROM siswa s
LEFT JOIN riwayat_kelas rk ON rk.nisn = s.nisn
LEFT JOIN kelas k ON k.id_kelas = rk.id_kelas
LEFT JOIN tahun_ajaran ta ON ta.id_ta = rk.id_ta
WHERE s.nisn='$nisn'
ORDER BY rk.id_riwayat DESC
LIMIT 1
");

if(mysqli_num_rows($query)==0){
    header("Location:index.php");
    exit;
}

$data = mysqli_fetch_assoc($query);

include_once '../../includes/header.php';
include_once '../../includes/sidebar.php';
?>

<div class="container-fluid">

    <!-- TITLE -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="fw-bold mb-0">Detail Siswa</h2>

        <a href="index.php" class="btn btn-outline-secondary btn-sm">
            ← Kembali
        </a>
    </div>

    <div class="card border-0 shadow-sm rounded-4">
        <div class="card-body p-4">

            <div class="row g-4">

                <div class="col-md-6">
                    <label class="text-muted small">NISN</label>
                    <div class="fw-semibold"><?= $data['nisn'] ?></div>
                </div>

                <div class="col-md-6">
                    <label class="text-muted small">Nama Lengkap</label>
                    <div class="fw-semibold"><?= $data['nama_lengkap'] ?></div>
                </div>

                <div class="col-md-6">
                    <label class="text-muted small">Jenis Kelamin</label>
                    <div><?= $data['jenis_kelamin'] ?></div>
                </div>

                <div class="col-md-6">
                    <label class="text-muted small">Tempat, Tanggal Lahir</label>
                    <div>
                        <?= $data['tempat_lahir'] ?>,
                        <?= date('d/m/Y', strtotime($data['tgl_lahir'])) ?>
                    </div>
                </div>

                <div class="col-md-6">
                    <label class="text-muted small">Asal Sekolah</label>
                    <div><?= $data['asal_sekolah'] ?></div>
                </div>

                <div class="col-md-6">
                    <label class="text-muted small">Kelas</label>
                    <div><?= $data['nama_kelas'] ?></div>
                </div>

                <div class="col-md-6">
                    <label class="text-muted small">Tahun Ajaran</label>
                    <div><?= $data['tahun'] ?> (<?= $data['semester'] ?>)</div>
                </div>

                <div class="col-12">
                    <label class="text-muted small">Alamat</label>
                    <div><?= $data['alamat'] ?></div>
                </div>

            </div>

        </div>
    </div>

</div>

<?php include_once '../../includes/footer.php'; ?>