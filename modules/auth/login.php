<?php
session_start();

if (isset($_POST['btn_login'])) {
    
    $username = $_POST['username'];
    $password = $_POST['password'];

    if ($username === 'admin' && $password === 'admin123') {
        
        $_SESSION['status_login'] = true;
        $_SESSION['username'] = $username;
        $_SESSION['nama_user'] = 'Budi Setiawan';
        $_SESSION['role'] = 'Administrator';
        
        header("Location: ../laporan/dashboard.php");
        exit;

    } else {
        
        header("Location: ../../index.php?pesan=gagal");
        exit;
    }
} else {
    
    header("Location: ../../index.php");
    exit;
}
?>