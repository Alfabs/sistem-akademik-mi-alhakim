<?php
require_once '../../config/database.php';

if (isset($_GET['id'])) {

    $id_dokumen = mysqli_real_escape_string($conn, $_GET['id']);

    $query = "SELECT nama_file, path_file FROM dokumen WHERE id_dokumen = '$id_dokumen'";
    $result = mysqli_query($conn, $query);

    if (mysqli_num_rows($result) > 0) {

        $data = mysqli_fetch_assoc($result);

        $filepath = __DIR__ . '/../../' . $data['path_file'];
        $filename = $data['nama_file'];

        if (file_exists($filepath)) {

            header('Content-Description: File Transfer');
            header('Content-Type: application/octet-stream');
            header('Content-Disposition: attachment; filename="' . basename($filename) . '"');
            header('Expires: 0');
            header('Cache-Control: must-revalidate');
            header('Pragma: public');
            header('Content-Length: ' . filesize($filepath));

            readfile($filepath);
            exit;

        } else {
            echo "<script>alert('File fisik tidak ditemukan di server!'); window.location.href='index.php';</script>";
        }

    } else {
        echo "<script>alert('Data dokumen tidak valid!'); window.location.href='index.php';</script>";
    }

} else {
    header("Location: index.php");
}
?>