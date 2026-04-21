<?php
include "../../config/database.php";
include "../../includes/auth.php";
require_role(['Guru', 'Operator']);

// Pastikan form dikirim dengan method POST
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Ambil data dari form
    $kode_mapel   = $_POST['kode_mapel'];
    $jenis_nilai  = $_POST['jenis_nilai'];
    $nip          = $_POST['nip'];
    $id_riwayat_array = $_POST['id_riwayat']; // array berisi id_riwayat tiap siswa
    $nilai_array      = $_POST['nilai'];      // array berisi nilai tiap siswa

    $sukses = 0;
    $gagal  = 0;

    // Loop untuk setiap siswa
    for ($i = 0; $i < count($id_riwayat_array); $i++) {
        $id_riwayat = (int)$id_riwayat_array[$i];
        $nilai = floatval($nilai_array[$i]);

        // Cek apakah data nilai sudah ada untuk kombinasi (id_riwayat, kode_mapel, jenis_nilai)
        $cek = mysqli_query($conn, "SELECT id_nilai FROM nilai WHERE id_riwayat = $id_riwayat AND kode_mapel = '$kode_mapel' AND jenis_nilai = '$jenis_nilai'");
        
        if (mysqli_num_rows($cek) > 0) {
            // Jika sudah ada, lakukan UPDATE
            $sql = "UPDATE nilai SET nilai_angka = $nilai, nip = '$nip', waktu_input = NOW() WHERE id_riwayat = $id_riwayat AND kode_mapel = '$kode_mapel' AND jenis_nilai = '$jenis_nilai'";
        } else {
            // Jika belum ada, lakukan INSERT
            $sql = "INSERT INTO nilai (id_riwayat, kode_mapel, nip, jenis_nilai, nilai_angka) VALUES ($id_riwayat, '$kode_mapel', '$nip', '$jenis_nilai', $nilai)";
        }

        if (mysqli_query($conn, $sql)) {
            $sukses++;
        } else {
            $gagal++;
        }
    }

    // Setelah selesai, kembali ke halaman index dengan pesan
    $msg = "Berhasil menyimpan $sukses nilai, gagal $gagal.";
    header("Location: index.php?msg=" . urlencode($msg));
    exit;
} else {
    // Jika bukan method POST, redirect ke index
    header("Location: index.php");
    exit;
}
?>