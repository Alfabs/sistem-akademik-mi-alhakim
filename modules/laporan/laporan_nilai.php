<?php
include "../../config/database.php";

$id_kelas = $_GET['id_kelas'] ?? 0;
$kode_mapel = $_GET['kode_mapel'] ?? '';

$query_kelas = mysqli_query($conn, "SELECT * FROM kelas");
$query_mapel = mysqli_query($conn, "SELECT * FROM mapel");

$data = [];

if ($id_kelas && $kode_mapel) {
    $sql = "
        SELECT s.nama_lengkap,
        MAX(CASE WHEN jenis_nilai='UH' THEN nilai_angka END) AS UH,
        MAX(CASE WHEN jenis_nilai='UTS' THEN nilai_angka END) AS UTS,
        MAX(CASE WHEN jenis_nilai='UAS' THEN nilai_angka END) AS UAS
        FROM riwayat_kelas r
        JOIN siswa s ON r.nisn=s.nisn
        LEFT JOIN nilai n ON r.id_riwayat=n.id_riwayat AND n.kode_mapel='$kode_mapel'
        WHERE r.id_kelas=$id_kelas
        GROUP BY s.nisn
    ";

    $res = mysqli_query($conn, $sql);
    while ($row = mysqli_fetch_assoc($res)) {
        $data[] = $row;
    }
}

include "../../includes/header.php";
include "../../includes/sidebar.php";
?>

<div class="container-fluid">
    <h2>Laporan Nilai</h2>

    <form method="GET" class="mb-3">
        <select name="id_kelas" class="form-select w-25 d-inline">
            <option value="">Kelas</option>
            <?php while ($k = mysqli_fetch_assoc($query_kelas)): ?>
                <option value="<?= $k['id_kelas'] ?>" <?= ($id_kelas == $k['id_kelas']) ? 'selected' : '' ?>>
                    <?= $k['nama_kelas'] ?>
                </option>
            <?php endwhile; ?>
        </select>

        <select name="kode_mapel" class="form-select w-25 d-inline">
            <option value="">Mapel</option>
            <?php while ($m = mysqli_fetch_assoc($query_mapel)): ?>
                <option value="<?= $m['kode_mapel'] ?>" <?= ($kode_mapel == $m['kode_mapel']) ? 'selected' : '' ?>>
                    <?= $m['nama_mapel'] ?>
                </option>
            <?php endwhile; ?>
        </select>

        <button class="btn btn-primary"><i class="fas fa-search"></i></button>
    </form>

    <?php if ($data): ?>
        <a href="cetak_pdf.php?tipe=nilai&id_kelas=<?= $id_kelas ?>&kode_mapel=<?= $kode_mapel ?>" class="btn btn-danger mb-2">
            <i class="fas fa-file-pdf"></i> Cetak PDF
        </a>

        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Nama</th>
                    <th>UH</th>
                    <th>UTS</th>
                    <th>UAS</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($data as $d): ?>
                    <tr>
                        <td><?= $d['nama_lengkap'] ?></td>
                        <td><?= $d['UH'] ?></td>
                        <td><?= $d['UTS'] ?></td>
                        <td><?= $d['UAS'] ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</div>

<?php include "../../includes/footer.php"; ?>