<?php
session_start();
require_once 'config/database.php';

if (isset($_SESSION['status_login']) && $_SESSION['status_login'] === true) {
    header("Location: modules/dashboard/index.php");
    exit;
}

$error = "";

if (isset($_POST['login'])) {
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $password = $_POST['password'];

    $query = "SELECT * FROM users WHERE username = '$username'";
    $result = mysqli_query($conn, $query);

    if (mysqli_num_rows($result) === 1) {
        $user = mysqli_fetch_assoc($result);
        
        // Verifikasi password (menggunakan password_verify jika dihash, atau plain jika belum)
        // Karena ini sistem baru, kita asumsikan password_verify. 
        // Jika gagal, coba plain text (untuk user awal jika belum dihash)
        if (password_verify($password, $user['password']) || $password === $user['password']) {
            
            $_SESSION['status_login'] = true;
            $_SESSION['id_user']      = $user['id_user'];
            $_SESSION['username']     = $user['username'];
            $_SESSION['role']         = $user['role'];
            
            // Ambil nama asli jika role adalah Guru
            if ($user['role'] === 'Guru') {
                $q_guru = mysqli_query($conn, "SELECT nama_lengkap, nip FROM guru WHERE id_user = " . $user['id_user']);
                if ($g = mysqli_fetch_assoc($q_guru)) {
                    $_SESSION['nama_user'] = $g['nama_lengkap'];
                    $_SESSION['nip'] = $g['nip'];
                } else {
                    $_SESSION['nama_user'] = $user['username'];
                }
            } else {
                $_SESSION['nama_user'] = $user['username'];
            }

            header("Location: modules/dashboard/index.php");
            exit;
        } else {
            $error = "Password salah!";
        }
    } else {
        $error = "Username tidak ditemukan!";
    }
}
?>

<!doctype html>
<html lang="id">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Login | MI AL-HAKIM</title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;600&display=swap" rel="stylesheet" />
    <link rel="stylesheet" href="assets/css/style.css" />
    <style>
        body.login-page {
            background: linear-gradient(135deg, #198754 0%, #0d6efd 100%);
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .login-card {
            background: white;
            padding: 2rem;
            border-radius: 15px;
            box-shadow: 0 10px 25px rgba(0,0,0,0.2);
            width: 100%;
            max-width: 400px;
            text-align: center;
        }
        .login-card img.logo {
            width: 80px;
            margin-bottom: 1rem;
        }
        .form-control {
            border-radius: 10px;
            padding: 0.75rem 1rem;
            margin-bottom: 1rem;
        }
        .btn-login {
            width: 100%;
            padding: 0.75rem;
            border-radius: 10px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
    </style>
  </head>
  
  <body class="login-page">
    <div class="login-card">
      <img src="assets/logo.png" alt="Logo Sekolah" class="logo" onerror="this.src='https://cdn-icons-png.flaticon.com/512/2859/2859731.png'" />
      
      <h3 class="fw-bold">MI AL-HAKIM</h3>
      <p class="text-muted">Sistem Informasi Akademik</p>

      <?php if ($error): ?>
        <div class="alert alert-danger py-2" role="alert">
            <?= $error ?>
        </div>
      <?php endif; ?>

      <form action="" method="POST">
        <div class="text-start mb-3">
            <label class="form-label small fw-bold">Username</label>
            <input type="text" name="username" class="form-control" placeholder="Masukkan Username" required autofocus />
        </div>
        
        <div class="text-start mb-4">
            <label class="form-label small fw-bold">Password</label>
            <input type="password" name="password" class="form-control" placeholder="Masukkan Password" required />
        </div>

        <button type="submit" name="login" class="btn btn-primary btn-login">Login</button>
      </form>
      
      <div class="mt-4 small text-muted">
        &copy; <?= date('Y') ?> MI AL-HAKIM
      </div>
    </div>
  </body>
</html>
