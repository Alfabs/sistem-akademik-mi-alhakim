<?php
require_once '../../config/database.php';

if (isset($_GET['id'])) {

    $id_dokumen = mysqli_real_escape_string($conn, $_GET['id']);

    $query = "SELECT path_file FROM dokumen WHERE id_dokumen = '$id_dokumen'";
    $result = mysqli_query($conn, $query);

    if (mysqli_num_rows($result) > 0) {

        $data = mysqli_fetch_assoc($result);

        $filepath = __DIR__ . '/../../' . $data['path_file'];

        // Hapus file fisik
        if (file_exists($filepath)) {
            if (!unlink($filepath)) {
                echo "<script>alert('Gagal menghapus file dari server!'); window.location.href='index.php';</script>";
                exit;
            }
        }

        // Hapus dari database
        $hapus_query = "DELETE FROM dokumen WHERE id_dokumen = '$id_dokumen'";
        if (mysqli_query($conn, $hapus_query)) {
            echo "<script>alert('Dokumen berhasil dihapus permanen!'); window.location.href='index.php';</script>";
        } else {
            echo "<script>alert('Gagal menghapus data dari database.'); window.location.href='index.php';</script>";
        }

    } else {
        echo "<script>alert('Dokumen tidak ditemukan.'); window.location.href='index.php';</script>";
    }

} else {
    header("Location: index.php");
}
?>