<?php
// config/database.php

$host     = "localhost";
$username = "root";
$password = "";
$database = "manajemen_sekolah";

// Koneksi database
$conn = mysqli_connect($host, $username, $password, $database);

// Cek koneksi
if (!$conn) {
    die("Koneksi database gagal: " . mysqli_connect_error());
}

// Optional charset
mysqli_set_charset($conn, "utf8");
?>