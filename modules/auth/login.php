<?php

session_start();

if (isset($_SESSION['status_login']) && $_SESSION['status_login'] === true) {
    header("Location: ../laporan/dashboard.php");
    exit;
}

$error = "";

if (isset($_POST['login'])) {
    $username = $_POST['username'];
    $password = $_POST['password'];

    if ($username === 'admin' && $password === 'admin123') {
        
        // Daftarkan data user ke Session
        $_SESSION['status_login'] = true;
        $_SESSION['username'] = $username;
        $_SESSION['nama_user'] = 'Budi Setiawan';
        $_SESSION['role'] = 'Administrator';

        header("Location: ../laporan/dashboard.php");
        exit;

    } else {
        $error = "Username atau Password salah!";
    }
}
?>

<!doctype html>
<html lang="id">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Login Sistem Sekolah</title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
    
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;600&display=swap" rel="stylesheet" />
    
    <link rel="stylesheet" href="../../assets/css/style.css" />
  </head>
  
  <body class="login-page">
    <div class="login-card">
      <img src="../../assets/logo.png" alt="Logo Sekolah" class="logo" />
      
      <h2>Selamat Datang</h2>
      <p>Masukkan akun Anda untuk melanjutkan</p>

      <form action="" method="POST">
        <input
          type="text"
          name="username" 
          class="form-control"
          placeholder="Masukkan Username"
          required
        />
        
        <input
          type="password"
          name="password"
          class="form-control"
          placeholder="Masukkan Password"
          required
        />

        <button type="submit" name="login" class="btn btn-success mt-2">Login</button>
      </form>
    </div>

  </body>
</html>