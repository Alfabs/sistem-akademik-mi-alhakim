<?php
include "../../config/database.php";

// Ambil data dari form
$id_riwayat = $_POST['id_riwayat'];
$nilai      = $_POST['nilai'];
$kode_mapel = $_POST['kode_mapel'];
$jenis      = $_POST['jenis_nilai'];
$nip        = $_POST['nip'];

// Loop simpan data
for ($i = 0; $i < count($id_riwayat); $i++) {

    $id = (int)$id_riwayat[$i];
    $n  = (float)$nilai[$i];

    // Cek apakah sudah ada
    $cek = mysqli_query($conn, "
        SELECT id_nilai FROM nilai
        WHERE id_riwayat=$id
        AND kode_mapel='$kode_mapel'
        AND jenis_nilai='$jenis'
    ");

    if (mysqli_num_rows($cek) > 0) {
        // UPDATE
        mysqli_query($conn, "
            UPDATE nilai SET nilai_angka=$n
            WHERE id_riwayat=$id
            AND kode_mapel='$kode_mapel'
            AND jenis_nilai='$jenis'
        ");
    } else {
        // INSERT
        mysqli_query($conn, "
            INSERT INTO nilai (id_riwayat, kode_mapel, nip, jenis_nilai, nilai_angka)
            VALUES ($id, '$kode_mapel', '$nip', '$jenis', $n)
        ");
    }
}

// Redirect balik
header("Location: index.php?msg=Data berhasil disimpan");
exit;