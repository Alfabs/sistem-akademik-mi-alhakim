<?php
include "../../config/database.php";

$id_kelas = isset($_GET['id_kelas']) ? (int)$_GET['id_kelas'] : 0;

// dropdown
$query_kelas = mysqli_query($conn, "SELECT * FROM kelas");

$data = [];

if ($id_kelas) {
    $sql = "
        SELECT s.nisn, s.nama_lengkap,
            SUM(CASE WHEN a.status='Hadir' THEN 1 ELSE 0 END) AS hadir,
            SUM(CASE WHEN a.status='Izin' THEN 1 ELSE 0 END) AS izin,
            SUM(CASE WHEN a.status='Sakit' THEN 1 ELSE 0 END) AS sakit,
            SUM(CASE WHEN a.status='Alpha' THEN 1 ELSE 0 END) AS alpha
        FROM riwayat_kelas r
        JOIN siswa s ON r.nisn = s.nisn
        LEFT JOIN absensi a ON r.id_riwayat = a.id_riwayat
        WHERE r.id_kelas = $id_kelas
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
    <h2 class="fw-bold mb-3">
        Laporan Kehadiran
    </h2>

    <form method="GET" class="mb-3">
        <select name="id_kelas" class="form-select w-25 d-inline">
            <option value="">-- Pilih Kelas --</option>
            <?php while($k = mysqli_fetch_assoc($query_kelas)): ?>
                <option value="<?= $k['id_kelas'] ?>" <?= $id_kelas==$k['id_kelas']?'selected':'' ?>>
                    <?= $k['nama_kelas'] ?>
                </option>
            <?php endwhile; ?>
        </select>
        <button class="btn btn-primary">
            <i class="fas fa-search"></i>
        </button>
    </form>

    <?php if ($data): ?>
    <a href="cetak_pdf.php?tipe=absensi&id_kelas=<?= $id_kelas ?>" class="btn btn-danger mb-2">
        <i class="fas fa-file-pdf"></i> Cetak PDF
    </a>

    <table class="table table-bordered">
        <tr>
            <th>Nama</th>
            <th>Hadir</th>
            <th>Izin</th>
            <th>Sakit</th>
            <th>Alpha</th>
        </tr>

        <?php foreach($data as $d): ?>
        <tr>
            <td><?= $d['nama_lengkap'] ?></td>
            <td><?= $d['hadir'] ?></td>
            <td><?= $d['izin'] ?></td>
            <td><?= $d['sakit'] ?></td>
            <td><?= $d['alpha'] ?></td>
        </tr>
        <?php endforeach; ?>
    </table>
    <?php endif; ?>

</div>

<?php include "../../includes/footer.php"; ?>