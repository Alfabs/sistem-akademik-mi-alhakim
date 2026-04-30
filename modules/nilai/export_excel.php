<?php
include "../../config/database.php";

// Ambil parameter
$id_kelas   = isset($_GET['id_kelas']) ? (int)$_GET['id_kelas'] : 0;
$kode_mapel = isset($_GET['kode_mapel']) ? mysqli_real_escape_string($conn, $_GET['kode_mapel']) : '';

// Validasi sederhana
if (!$id_kelas || !$kode_mapel) {
    die("Parameter tidak lengkap");
}

// Header Excel
header("Content-type: application/vnd-ms-excel");
header("Content-Disposition: attachment; filename=Rekap_Nilai.xls");

// Query
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

// Output
echo "<h3>Rekap Nilai</h3>";
echo "<table border='1'>";
echo "<tr><th>No</th><th>NISN</th><th>Nama</th><th>UH</th><th>UTS</th><th>UAS</th><th>Rata-rata</th></tr>";

$no = 1;
while ($row = mysqli_fetch_assoc($result)) {

    $uh  = $row['UH'] ?? 0;
    $uts = $row['UTS'] ?? 0;
    $uas = $row['UAS'] ?? 0;

    $rata = ($uh + $uts + $uas) / 3;

    echo "<tr>
        <td>{$no}</td>
        <td>{$row['nisn']}</td>
        <td>{$row['nama_lengkap']}</td>
        <td>{$row['UH']}</td>
        <td>{$row['UTS']}</td>
        <td>{$row['UAS']}</td>
        <td>".round($rata,2)."</td>
    </tr>";

    $no++;
}

echo "</table>";