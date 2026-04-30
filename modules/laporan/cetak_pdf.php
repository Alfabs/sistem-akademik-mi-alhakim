<?php
require '../../vendor/autoload.php';
use Dompdf\Dompdf;

include "../../config/database.php";

$dompdf = new Dompdf();

$tipe = $_GET['tipe'];

$html = "<h3>Laporan</h3>";

if ($tipe == "absensi") {
    $id_kelas = (int)$_GET['id_kelas'];

    $q = mysqli_query($conn,"
        SELECT s.nama_lengkap,
        SUM(a.status='Hadir') hadir,
        SUM(a.status='Alpha') alpha
        FROM riwayat_kelas r
        JOIN siswa s ON r.nisn=s.nisn
        LEFT JOIN absensi a ON r.id_riwayat=a.id_riwayat
        WHERE r.id_kelas=$id_kelas
        GROUP BY s.nisn
    ");

    $html .= "<table border='1'><tr><th>Nama</th><th>Hadir</th><th>Alpha</th></tr>";

    while($d=mysqli_fetch_assoc($q)){
        $html .= "<tr>
        <td>{$d['nama_lengkap']}</td>
        <td>{$d['hadir']}</td>
        <td>{$d['alpha']}</td>
        </tr>";
    }

    $html .= "</table>";
}

if ($tipe == "nilai") {
    $id_kelas = (int)$_GET['id_kelas'];
    $kode_mapel = $_GET['kode_mapel'];

    $q = mysqli_query($conn,"
        SELECT s.nama_lengkap,
        MAX(CASE WHEN jenis_nilai='UH' THEN nilai_angka END) UH,
        MAX(CASE WHEN jenis_nilai='UTS' THEN nilai_angka END) UTS,
        MAX(CASE WHEN jenis_nilai='UAS' THEN nilai_angka END) UAS
        FROM riwayat_kelas r
        JOIN siswa s ON r.nisn=s.nisn
        LEFT JOIN nilai n ON r.id_riwayat=n.id_riwayat AND n.kode_mapel='$kode_mapel'
        WHERE r.id_kelas=$id_kelas
        GROUP BY s.nisn
    ");

    $html .= "<table border='1'><tr><th>Nama</th><th>UH</th><th>UTS</th><th>UAS</th></tr>";

    while($d=mysqli_fetch_assoc($q)){
        $html .= "<tr>
        <td>{$d['nama_lengkap']}</td>
        <td>{$d['UH']}</td>
        <td>{$d['UTS']}</td>
        <td>{$d['UAS']}</td>
        </tr>";
    }

    $html .= "</table>";
}

$dompdf->loadHtml($html);
$dompdf->setPaper('A4','portrait');
$dompdf->render();
$dompdf->stream("laporan.pdf");