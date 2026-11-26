<?php
// FILE: index.php (root) — Halaman Login
session_start();
if (isset($_SESSION['admin_id'])) {
    header('Location: admin/app/page/dashboard/index.php');
    exit;
}

require_once 'config/koneksi.php';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $uname = mysqli_real_escape_string($koneksi, trim($_POST['username'] ?? ''));
    $pass = mysqli_real_escape_string($koneksi, trim($_POST['password'] ?? ''));
    $res = mysqli_query($koneksi, "SELECT * FROM data_admin WHERE username='$uname' AND password='$pass'");
    if ($row = mysqli_fetch_assoc($res)) {
        $_SESSION['admin_id'] = $row['id_admin'];
        $_SESSION['admin_nama'] = $row['nama'];
        header('Location: admin/app/page/dashboard/index.php');
        exit;
    } else {
        $error = 'Username atau password salah.';
    }
}
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>Login — Smart Healthy Checkup</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700;800&display=swap');

        *,
        *::before,
        *::after {
            box-sizing: border-box;
            margin: 0;
            padding: 0
        }

        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background: linear-gradient(135deg, #1A1B4B 0%, #3B5BDB 50%, #1971C2 100%)
        }

        .login-card {
            background: #fff;
            border-radius: 24px;
            padding: 44px 40px;
            width: 100%;
            max-width: 420px;
            box-shadow: 0 24px 64px rgba(0, 0, 0, .25)
        }

        .logo {
            text-align: center;
            margin-bottom: 28px
        }

        .logo i {
            font-size: 2.6rem;
            color: #3B5BDB
        }

        .logo h1 {
            font-size: 1.35rem;
            font-weight: 800;
            color: #1A1B2E;
            margin: 10px 0 4px
        }

        .logo p {
            font-size: .83rem;
            color: #868E96
        }

        .fg {
            margin-bottom: 18px
        }

        .fg label {
            display: block;
            font-size: .78rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: .4px;
            color: #495057;
            margin-bottom: 7px
        }

        .input-wrap {
            position: relative
        }

        .input-wrap i {
            position: absolute;
            left: 14px;
            top: 50%;
            transform: translateY(-50%);
            color: #ADB5BD;
            font-size: 1rem
        }

        .fg input {
            width: 100%;
            padding: 12px 14px 12px 40px;
            border: 2px solid #E9ECEF;
            border-radius: 12px;
            font-family: inherit;
            font-size: .9rem;
            outline: none;
            transition: border-color .15s;
            color: #1A1B2E
        }

        .fg input:focus {
            border-color: #3B5BDB
        }

        .btn-login {
            width: 100%;
            padding: 13px;
            background: linear-gradient(135deg, #3B5BDB, #1971C2);
            color: #fff;
            border: none;
            border-radius: 12px;
            font-family: inherit;
            font-size: .95rem;
            font-weight: 700;
            cursor: pointer;
            transition: filter .2s
        }

        .btn-login:hover {
            filter: brightness(1.1)
        }

        .error {
            background: #FFF5F5;
            border: 1px solid #FFC9C9;
            color: #C92A2A;
            padding: 10px 14px;
            border-radius: 10px;
            font-size: .84rem;
            margin-bottom: 16px;
            display: flex;
            align-items: center;
            gap: 8px
        }

        .footer-txt {
            text-align: center;
            margin-top: 20px;
            font-size: .78rem;
            color: #ADB5BD
        }
    </style>
</head>

<body>
    <div class="login-card">
        <div class="logo">
            <i class="bi bi-heart-pulse-fill"></i>
            <h1>Smart Healthy Checkup</h1>
            <p>Sistem Monitoring Kesehatan Mahasiswa IoT</p>
        </div>

        <?php if ($error): ?>
            <div class="error"><i class="bi bi-exclamation-triangle-fill"></i> <?= $error ?></div>
        <?php endif; ?>

        <form method="POST">
            <div class="fg">
                <label>Username</label>
                <div class="input-wrap">
                    <i class="bi bi-person-fill"></i>
                    <input type="text" name="username" placeholder="Masukkan username" required autocomplete="username">
                </div>
            </div>
            <div class="fg">
                <label>Password</label>
                <div class="input-wrap">
                    <i class="bi bi-lock-fill"></i>
                    <input type="password" name="password" placeholder="Masukkan password" required
                        autocomplete="current-password">
                </div>
            </div>
            <button type="submit" class="btn-login"><i class="bi bi-box-arrow-in-right"></i> Masuk</button>
        </form>
        <p class="footer-txt">© <?= date('Y') ?> Smart Healthy Checkup IoT</p>
    </div>
</body>

</html>