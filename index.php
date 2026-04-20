<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Login Page</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    body {
      min-height: 100vh;
      margin: 0;
      display: flex;
      align-items: center;
      justify-content: center;
      background: linear-gradient(160deg,#63c89d 0%,#2f8c67 55%,#20553f 100%);
      position: relative;
      overflow: hidden;
      font-family: Arial, sans-serif;
    }
    body::before {
      content: "";
      position: absolute;
      inset: 0;
      background: radial-gradient(circle at top left, rgba(255,255,255,.10), transparent 35%),
                  radial-gradient(circle at bottom right, rgba(255,255,255,.08), transparent 30%);
    }
    .login-card {
      position: relative;
      z-index: 1;
      width: 100%;
      max-width: 380px;
      background: rgba(255,255,255,.95);
      border-radius: 14px;
      padding: 2rem;
      box-shadow: 0 18px 40px rgba(0,0,0,.18);
    }
    .login-title {
      font-size: 1.7rem;
      font-weight: 700;
      margin-bottom: .25rem;
    }
    .login-subtitle {
      font-size: .9rem;
      color: #666;
      margin-bottom: 1.5rem;
    }
    .form-control {
      border-radius: 10px;
      padding: .75rem .95rem;
    }
    .btn-login {
      border-radius: 10px;
      padding: .7rem;
      background: #19a56b;
      border: none;
      font-weight: 600;
    }
    .btn-login:hover { background: #148b59; }
    @media (max-width: 576px) {
      .login-card {
        max-width: 92%;
        padding: 1.5rem;
      }
      .login-title { font-size: 1.45rem; }
    }
  </style>
</head>
<body>
  <div class="login-card text-center">
    <h1 class="login-title">Selamat Datang</h1>
    <p class="login-subtitle">Masukkan akun Anda untuk melanjutkan</p>

    <form>
      <div class="mb-3 text-start">
        <input type="text" name="username" class="form-control" placeholder="Masukkan Username">
      </div>
      <div class="mb-4 text-start">
        <input type="password" name="password" class="form-control" placeholder="Masukkan Password">
      </div>
      <button type="submit" class="btn btn-login text-white w-100">Login</button>
    </form>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
