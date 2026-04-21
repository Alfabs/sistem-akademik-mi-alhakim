<?php
include "../../config/database.php";

// Supaya filenya otomatis kedownload jadi format Excel
header("Content-type: application/vnd-ms-excel");
header("Content-Disposition: attachment; filename=Rekap_Absensi_Siswa.xls");

$id_kelas_pilihan = isset($_GET['id_kelas']) ? $_GET['id_kelas'] : '';
$where_clause = $id_kelas_pilihan ? "WHERE r.id_kelas = '$id_kelas_pilihan'" : "";
?>

<h2>REKAP ABSENSI SISWA</h2>
<table border="1">
    <thead>
        <tr style="background-color: #eee;">
            <th>No</th>
            <th>Nama Siswa</th>
            <th>Hadir</th>
            <th>Izin</th>
            <th>Sakit</th>
            <th>Alpha</th>
            <th>Total Pertemuan</th>
        </tr>
    </thead>
    <tbody>
        <?php
        $no = 1;
        $sql = "SELECT s.nama_lengkap, 
                SUM(CASE WHEN a.status = 'Hadir' THEN 1 ELSE 0 END) as h,
                SUM(CASE WHEN a.status = 'Izin' THEN 1 ELSE 0 END) as i,
                SUM(CASE WHEN a.status = 'Sakit' THEN 1 ELSE 0 END) as s,
                SUM(CASE WHEN a.status = 'Alpha' THEN 1 ELSE 0 END) as a,
                COUNT(a.status) as total
                FROM siswa s
                JOIN riwayat_kelas r ON s.nisn = r.nisn
                LEFT JOIN absensi a ON r.id_riwayat = a.id_riwayat
                $where_clause
                GROUP BY s.nisn
                ORDER BY s.nama_lengkap ASC";
        
        $query = mysqli_query($conn, $sql);
        while($d = mysqli_fetch_array($query)){
            echo "<tr>
                <td>".$no++."</td>
                <td>".$d['nama_lengkap']."</td>
                <td>".$d['h']."</td>
                <td>".$d['i']."</td>
                <td>".$d['s']."</td>
                <td>".$d['a']."</td>
                <td>".$d['total']."</td>
            </tr>";
        }
        ?>
    </tbody>
</table>