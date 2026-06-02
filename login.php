<?php
session_start();
require_once 'koneksi.php';

if (isset($_SESSION['user'])) {
    header("Location: panel");
    exit;
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Login - Book Market</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    
    <style>
        body, html {
            height: 100%;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            
            /* 1. SATU BACKGROUND UTAMA UNTUK KESELURUHAN HALAMAN */
            background-image: url('assets/bg/bg1.jpg');
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
            background-attachment: fixed; /* Menjaga background tetap tenang saat form di-scroll */
            
            overflow-x: hidden;
        }
        
        /* NAVBAR UTAMA */
        .navbar-custom {
            background-color: #5a67d8;
            padding: 0.8rem 2rem;
            z-index: 1000;
        }
        .navbar-custom .navbar-brand {
            color: #ffffff;
            font-weight: 700;
            font-size: 1.25rem;
            letter-spacing: 0.5px;
        }
        .navbar-custom .nav-link {
            color: rgba(255, 255, 255, 0.8);
            font-size: 0.9rem;
            font-weight: 500;
        }
        .navbar-custom .nav-link:hover, .navbar-custom .nav-link.active {
            color: #ffffff;
        }
        .btn-daftar {
            background-color: #ffffff;
            color: #222222;
            font-weight: 700;
            font-size: 0.9rem;
            padding: 0.5rem 1.5rem;
            border-radius: 6px;
            border: none;
        }

        /* LAYOUT DUA SISI */
        .split-container {
            min-height: calc(100vh - 62px);
        }
        
        /* SISI KIRI: BANNER INFORMASI (BACKGROUND DIUBAH TRANSPARAN) */
        .left-banner {
            background: transparent; /* Menghapus warna & gambar lokal agar tembus ke body */
            display: flex;
            flex-direction: column;
            justify-content: center;
            padding: 4rem;
            position: relative;
        }
        .banner-title {
            font-size: 3.5rem;
            font-weight: 900;
            color: #5a67d8;
            line-height: 1.1;
            margin-bottom: 1.5rem;
            letter-spacing: -1px;
        }
        .banner-subtitle {
            color: #5a67d8;
            font-size: 1.1rem;
            line-height: 1.6;
            max-width: 480px;
        }

        /* SISI KANAN: FORMULIR MASUK (BACKGROUND DIUBAH TRANSPARAN) */
        .right-form-side {
            background: transparent; /* Menghapus gradasi & gambar lokal agar tembus ke body */
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 2rem;
        }
        
        /* KARTU LOGIN TRANSPARAN (FROSTED GLASS EFFECT) */
        .login-box {
            background: rgba(222, 221, 221, 0.75);
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
            border-radius: 24px;
            padding: 3.5rem 3rem;
            width: 100%;
            max-width: 520px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.05);
        }
        .form-control-custom {
            background-color: #f7fafc !important;
            border: 1px solid #e2e8f0 !important;
            border-radius: 8px;
            padding: 0.75rem 1rem;
            font-size: 0.95rem;
        }
        .form-control-custom:focus {
            box-shadow: 0 0 0 3px rgba(90, 103, 216, 0.2) !important;
            border-color: #5a67d8 !important;
        }
        .btn-login-custom {
            background-color: #5a67d8 !important;
            color: #ffffff !important;
            font-weight: 600;
            font-size: 1rem;
            padding: 0.75rem;
            border-radius: 8px;
            border: none;
            transition: background-color 0.2s;
        }
        .btn-login-custom:hover {
            background-color: #434f9a !important;
        }
        .text-link-danger {
            color: #e53e3e !important;
            font-weight: 600;
        }
    </style>
</head>

<body>
    <div class="container-fluid p-0">
        <div class="row g-0 split-container">
            
            <div class="col-md-6 left-banner d-none d-md-flex">
                <h1 class="banner-title">
                    Masuk ke<br>Akun Anda
                </h1>
                <p class="banner-subtitle">
                    Silakan login untuk melanjutkan belanja buku favorit, melihat riwayat pesanan, dan mendapatkan promo eksklusif dari Book Market.
                </p>
            </div>
            
            <div class="col-md-6 right-form-side">
                <div class="login-box">
                    
                    <h2 class="fw-bold text-dark mb-2">Masuk</h2>
                    <p class="text-secondary small mb-4" style="line-height: 1.4;">
                        Silahkan Masukkan Email dan Password yang benar
                    </p>
                    
                    <form method="POST">
                        <div class="mb-3">
                            <label class="form-label text-secondary small fw-medium mb-1">
                                <i class="bi bi-briefcase text-warning me-1"></i> Email
                            </label>
                            <input type="email" name="email" class="form-control form-control-custom" 
                                   placeholder="Masukkan email Anda" required
                                   value="<?= $_GET['search'] ?? '' /* Tetap menjaga value jika ada lemparan */ ?>">
                        </div>

                        <div class="mb-3">
    <div class="d-flex justify-content-between">
        <label class="form-label text-secondary small fw-medium mb-1">
            <i class="bi bi-lock text-warning me-1"></i> Password
        </label>
    </div>
    
    <div class="input-group">
        <input type="password" name="password" id="password" class="form-control form-custom form-control-custom" 
               placeholder="Masukkan password Anda" required>
        
        <button class="btn btn-outline-secondary" type="button" id="togglePassword" style="border-top-right-radius: 0.375rem; border-bottom-right-radius: 0.375rem;">
            <i class="bi bi-eye" id="eyeIcon"></i>
        </button>
    </div>
</div>

                       <div class="d-flex justify-content-between align-items-center mb-4 small">
                        <a href="lupapassword.php" class="text-decoration-none text-muted small">Lupa Kata Sandi</a>
                        </div>

                        <button type="submit" name="login" class="btn btn-login-custom w-100 mb-4 shadow-sm">
                            Login
                        </button>
                    </form>

                    <p class="small text-center text-secondary mb-0">
                        Belum punya akun? <a href="register.php" class="text-decoration-none text-link-danger">Daftar</a>
                    </p>

                    <?php
                    if (isset($_POST['login'])) {
                        $email = mysqli_real_escape_string($koneksi, $_POST['email']);
                        $password = $_POST['password'];

                        $user = mysqli_query($koneksi, "SELECT * FROM users WHERE email='$email'");
                        $cek = mysqli_num_rows($user);

                        if ($cek > 0) {
                            $row = mysqli_fetch_assoc($user);
                            if (password_verify($password, $row['password'])) {
                                $_SESSION['user'] = $row;
                                $target = ($row['role'] != 'User') ? 'panel/index.php' : 'index.php';
                                echo "<script>alert('Login Berhasil'); window.location='$target';</script>";
                            } else {
                                echo "<div class='alert alert-danger mt-3 py-2 small border-0 text-center rounded-3'>Email atau Password salah</div>";
                            }
                        } else {
                            echo "<div class='alert alert-danger mt-3 py-2 small border-0 text-center rounded-3'>Email atau Password salah</div>";
                        }
                    }
                    ?>
                </div>
            </div>

        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <script>
    const togglePassword = document.querySelector('#togglePassword');
    const password = document.querySelector('#password');
    const eyeIcon = document.querySelector('#eyeIcon');

    togglePassword.addEventListener('click', function () {
        // Alihkan tipe input antara password dan text
        const type = password.getAttribute('type') === 'password' ? 'text' : 'password';
        password.setAttribute('type', type);
        
        // Alihkan ikon antara bi-eye (mata terbuka) dan bi-eye-slash (mata dicoret)
        if (type === 'text') {
            eyeIcon.classList.remove('bi-eye');
            eyeIcon.classList.add('bi-eye-slash');
        } else {
            eyeIcon.classList.remove('bi-eye-slash');
            eyeIcon.classList.add('bi-eye');
        }
    });
</script>
</body>

</html>