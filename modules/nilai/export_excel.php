<?php
include "../../config/database.php";

$id_kelas   = (int)$_GET['id_kelas'];
$kode_mapel = $_GET['kode_mapel'];

// Set header agar browser menganggapnya sebagai file Excel
header("Content-type: application/vnd-ms-excel");
header("Content-Disposition: attachment; filename=Rekap_Nilai_".$kode_mapel."_Kelas_".$id_kelas.".xls");

// Query data sama seperti di rekap.php
$sql = "
    SELECT s.nisn, s.nama_lengkap,
        MAX(CASE WHEN n.jenis_nilai = 'UH' THEN n.nilai_angka END) AS UH,
        MAX(CASE WHEN n.jenis_nilai = 'UTS' THEN n.nilai_angka END) AS UTS,
        MAX(CASE WHEN n.jenis_nilai = 'UAS' THEN n.nilai_angka END) AS UAS
    FROM riwayat_kelas r
    JOIN siswa s ON r.nisn = s.nisn
    LEFT JOIN nilai n ON r.id_riwayat = n.id_riwayat AND n.kode_mapel = '$kode_mapel'
    WHERE r.id_kelas = $id_kelas
    GROUP BY s.nisn
    ORDER BY s.nama_lengkap
";
$result = mysqli_query($conn, $sql);

// Tampilkan tabel dalam format HTML (Excel bisa membaca HTML)
echo "<h2>Rekap Nilai - Mata Pelajaran $kode_mapel - Kelas $id_kelas</h2>";
echo "<table border='1'>";
echo "<tr><th>No</th><th>NISN</th><th>Nama</th><th>UH</th><th>UTS</th><th>UAS</th><th>Rata-rata</th></tr>";

$no = 1;
while ($row = mysqli_fetch_assoc($result)) {
    $rata = round(($row['UH'] + $row['UTS'] + $row['UAS']) / 3, 2);
    echo "<tr>
            <td>{$no}</td>
            <td>{$row['nisn']}</td>
            <td>{$row['nama_lengkap']}</td>
            <td>{$row['UH']}</td>
            <td>{$row['UTS']}</td>
            <td>{$row['UAS']}</td>
            <td>{$rata}</td>
          </tr>";
    $no++;
}
echo "</table>";
?>