<?php
include "../../config/database.php";

if (isset($_POST['simpan'])) {
    $tanggal = $_POST['tanggal'];
    $status_array = $_POST['status']; // Menangkap array status
    $ket_array = $_POST['keterangan']; // Menangkap array keterangan

    $sukses = 0;
    $gagal = 0;

    foreach ($status_array as $id_riwayat => $status) {
        $keterangan = mysqli_real_escape_string($conn, $ket_array[$id_riwayat]);
        
        // Gunakan ON DUPLICATE KEY UPDATE supaya kalau absen di hari yang sama, datanya terupdate (tidak error)
        $sql = "INSERT INTO absensi (id_riwayat, tanggal, status, keterangan) 
                VALUES ('$id_riwayat', '$tanggal', '$status', '$keterangan')
                ON DUPLICATE KEY UPDATE 
                status = VALUES(status), 
                keterangan = VALUES(keterangan)";
        
        if (mysqli_query($conn, $sql)) {
            $sukses++;
        } else {
            $gagal++;
        }
    }

    if ($sukses > 0) {
        echo "<script>alert('Berhasil menyimpan $sukses data absensi!'); window.location='rekap.php';</script>";
    } else {
        echo "<script>alert('Gagal menyimpan data!'); window.location='index.php';</script>";
    }
} else {
    header("Location: index.php");
}
?>