<?php
include "../../config/database.php";

// Ambil parameter filter
$id_kelas   = isset($_GET['id_kelas']) ? (int)$_GET['id_kelas'] : 0;
$kode_mapel = isset($_GET['kode_mapel']) ? mysqli_real_escape_string($conn, $_GET['kode_mapel']) : '';

// Ambil data dropdown
$query_kelas = mysqli_query($conn, "SELECT id_kelas, nama_kelas FROM kelas ORDER BY nama_kelas");
$query_mapel = mysqli_query($conn, "SELECT kode_mapel, nama_mapel FROM mapel ORDER BY nama_mapel");

$data_siswa = [];

// Jika filter dipilih, ambil data nilai
if ($id_kelas && $kode_mapel) {
    $sql = "
        SELECT s.nisn, s.nama_lengkap,
            MAX(CASE WHEN n.jenis_nilai = 'UH' THEN n.nilai_angka END) AS UH,
            MAX(CASE WHEN n.jenis_nilai = 'UTS' THEN n.nilai_angka END) AS UTS,
            MAX(CASE WHEN n.jenis_nilai = 'UAS' THEN n.nilai_angka END) AS UAS
        FROM riwayat_kelas r
        JOIN siswa s ON r.nisn = s.nisn
        LEFT JOIN nilai n 
            ON r.id_riwayat = n.id_riwayat 
            AND n.kode_mapel = '$kode_mapel'
        WHERE r.id_kelas = $id_kelas
        GROUP BY s.nisn
        ORDER BY s.nama_lengkap
    ";

    $result = mysqli_query($conn, $sql);

    while ($row = mysqli_fetch_assoc($result)) {
        $data_siswa[] = $row;
    }
}

include "../../includes/header.php";
include "../../includes/sidebar.php";
?>

<div class="container-fluid">
    <h2 class="fw-bold mb-3">
        <i class="fas fa-chart-bar"></i> Rekap Nilai
    </h2>

    <!-- FILTER -->
    <div class="card mb-3">
        <div class="card-body">
            <form method="GET" class="row g-3">

                <div class="col-md-4">
                    <label>Kelas</label>
                    <select name="id_kelas" class="form-select">
                        <option value="">-- Pilih Kelas --</option>
                        <?php while($kelas = mysqli_fetch_assoc($query_kelas)): ?>
                            <option value="<?= $kelas['id_kelas'] ?>" <?= $id_kelas == $kelas['id_kelas'] ? 'selected' : '' ?>>
                                <?= $kelas['nama_kelas'] ?>
                            </option>
                        <?php endwhile; ?>
                    </select>
                </div>

                <div class="col-md-4">
                    <label>Mata Pelajaran</label>
                    <select name="kode_mapel" class="form-select">
                        <option value="">-- Pilih Mapel --</option>
                        <?php 
                        mysqli_data_seek($query_mapel, 0);
                        while($mapel = mysqli_fetch_assoc($query_mapel)): ?>
                            <option value="<?= $mapel['kode_mapel'] ?>" <?= $kode_mapel == $mapel['kode_mapel'] ? 'selected' : '' ?>>
                                <?= $mapel['nama_mapel'] ?>
                            </option>
                        <?php endwhile; ?>
                    </select>
                </div>

                <div class="col-md-4 d-flex align-items-end gap-2">
                    <button class="btn btn-primary">
                        <i class="fas fa-search"></i> Tampilkan
                    </button>

                    <?php if ($id_kelas && $kode_mapel): ?>
                        <a href="export_excel.php?id_kelas=<?= $id_kelas ?>&kode_mapel=<?= $kode_mapel ?>" 
                           class="btn btn-success">
                           <i class="fas fa-file-excel"></i> Export
                        </a>
                    <?php endif; ?>
                </div>

            </form>
        </div>
    </div>

    <!-- TABEL -->
    <?php if (!empty($data_siswa)): ?>
    <div class="card">
        <div class="card-body table-responsive">
            <table class="table table-bordered table-hover">
                <thead class="table-light">
                    <tr>
                        <th>No</th>
                        <th>NISN</th>
                        <th>Nama</th>
                        <th>UH</th>
                        <th>UTS</th>
                        <th>UAS</th>
                        <th>Rata-rata</th>
                    </tr>
                </thead>
                <tbody>

                <?php $no = 1; foreach($data_siswa as $siswa): 

                    $uh  = $siswa['UH'] ?? 0;
                    $uts = $siswa['UTS'] ?? 0;
                    $uas = $siswa['UAS'] ?? 0;

                    $rata = ($uh + $uts + $uas) / 3;
                ?>

                <tr>
                    <td><?= $no++ ?></td>
                    <td><?= $siswa['nisn'] ?></td>
                    <td><?= $siswa['nama_lengkap'] ?></td>
                    <td><?= $siswa['UH'] ?? '-' ?></td>
                    <td><?= $siswa['UTS'] ?? '-' ?></td>
                    <td><?= $siswa['UAS'] ?? '-' ?></td>
                    <td><?= ($uh || $uts || $uas) ? round($rata,2) : '-' ?></td>
                </tr>

                <?php endforeach; ?>

                </tbody>
            </table>
        </div>
    </div>

    <?php elseif ($id_kelas && $kode_mapel): ?>
        <div class="alert alert-warning">
            <i class="fas fa-exclamation-circle"></i> Belum ada data nilai
        </div>
    <?php endif; ?>

</div>

<?php include "../../includes/footer.php"; ?>