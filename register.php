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
    <title>Register - Book Market</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
   
    <style>
    body, html {
        height: 100%;
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        
        /* 1. MENGUNCI SATU BACKGROUND UTAMA UNTUK KEDUA SISI */
        background-image: url('assets/bg/bg1.jpg');
        background-size: cover;
        background-position: center;
        background-repeat: no-repeat;
        background-attachment: fixed; /* Membuat background tetap tenang saat form di-scroll */
        
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
    .btn-masuk-nav {
        color: #ffffff;
        font-weight: 500;
        font-size: 0.9rem;
        text-decoration: none;
    }
    .btn-daftar-nav {
        background-color: #ffffff;
        color: #ffffff;
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
    
    /* SISI KIRI: BANNER INFORMASI (BACKGROUND DIHAPUS) */
    .left-banner {
        background: transparent; /* Diubah menjadi transparan agar tembus ke background body */
        display: flex;
        flex-direction: column;
        justify-content: center;
        padding: 5rem;
        padding-bottom: 20rem;
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

    /* SISI KANAN: FORMULIR PENDAFTARAN (BACKGROUND DIHAPUS) */
    .right-form-side {
        background: transparent; /* Diubah menjadi transparan agar tembus ke background body */
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 3rem 2rem;
    }
    
    /* KARTU REGISTRASI TRANSPARAN (FROSTED GLASS EFFECT) */
    .register-box {
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
        padding: 0.6rem 1rem;
        font-size: 0.9rem;
    }
    .form-control-custom:focus {
        box-shadow: 0 0 0 3px rgba(90, 103, 216, 0.2) !important;
        border-color: #5a67d8 !important;
    }
    .btn-register-custom {
        background-color: #5a67d8 !important;
        color: #ffffff !important;
        font-weight: 600;
        font-size: 1rem;
        padding: 0.75rem;
        border-radius: 8px;
        border: none;
        transition: background-color 0.2s;
    }
    .btn-register-custom:hover {
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
            
            <div class="col-md-5 left-banner d-none d-md-flex">
                <h1 class="banner-title">
                    Buat Akun<br>Anda
                </h1>
                <p class="banner-subtitle">
                    Silakan daftar untuk mulai berbelanja buku favorit, menyimpan riwayat pesanan, dan mendapatkan promo eksklusif dari Book Market.
                </p>
            </div>
            
            <div class="col-md-7 right-form-side">
                <div class="register-box">
                    
                    <h2 class="fw-bold text-dark mb-1" style="font-size: 1.8rem;">Registrasi</h2>
                    <p class="text-secondary small mb-4">
                        Silakan lengkapi data diri Anda untuk membuat akun baru
                    </p>
                    
                    <form method="POST">
                        
                        <div class="mb-2">
                            <label class="form-label text-dark small fw-medium mb-1">Nama</label>
                            <input type="text" name="nama" class="form-control form-control-custom" placeholder="Masukkan nama lengkap"  required>
                        </div>

                        <div class="mb-2">
                            <label class="form-label text-dark small fw-medium mb-1">Email</label>
                            <input type="email" name="email" class="form-control form-control-custom" placeholder="Masukkan alamat email Anda" required>
                        </div>

                        <div class="mb-2">
                            <label class="form-label text-dark small fw-medium mb-1">No. HP</label>
                            <input type="text" name="nohp" class="form-control form-control-custom" placeholder="Masukkan nomor handphone" required>
                        </div>

                        <div class="mb-2">
                            <label class="form-label text-dark small fw-medium mb-1">Jenis Kelamin</label>
                            <select name="jeniskelamin" class="form-control form-control-custom form-select" required>
                                <option value="">-- Pilih Jenis Kelamin --</option>
                                <option value="Laki-laki">Laki-laki</option>
                                <option value="Perempuan">Perempuan</option>
                            </select>
                        </div>

                        <div class="mb-2">
                            <label class="form-label text-dark small fw-medium mb-1">Alamat</label>
                            <textarea name="alamat" class="form-control form-control-custom" rows="2" placeholder="Masukkan alamat rumah lengkap" required></textarea>
                        </div>

                        <div class="mb-4">
                        <label class="form-label text-dark small fw-medium mb-1">Password</label>
                        <div class="input-group">
                            <input type="password" 
                                name="password" 
                                id="password" 
                                class="form-control form-control-custom border-end-0" 
                                placeholder="Buat kata sandi baru (8-16 karakter)" 
                                minlength="8" 
                                maxlength="16" 
                                required>
                                
                            <span class="input-group-text bg-transparent border-light-subtle border-start-0 text-muted" id="togglePassword" style="cursor:pointer; background-color: #f7fafc !important; border: 1px solid #e2e8f0 !important; border-left: none !important; border-top-right-radius: 8px; border-bottom-right-radius: 8px;">
                                <i class="bi bi-eye-slash"></i>
                            </span>
                        </div>
                    </div>

                        <button type="submit" name="register" class="btn btn-register-custom w-100 mb-3 shadow-sm">
                            Daftar
                        </button>
                    </form>

                    <p class="small text-center text-secondary mb-0">
                        Sudah punya akun? <a href="login.php" class="text-decoration-none text-link-danger">Login</a>
                    </p>

                    <?php
if (isset($_POST['register'])) {
    $nama  = mysqli_real_escape_string($koneksi, $_POST['nama']);
    $email = mysqli_real_escape_string($koneksi, $_POST['email']);
    
    // 1. Ambil password asli sebelum di-hash untuk dicek panjangnya
    $password_raw = $_POST['password']; 
    
    $jeniskelamin = mysqli_real_escape_string($koneksi, $_POST['jeniskelamin']);
    $nohp = mysqli_real_escape_string($koneksi, $_POST['nohp']);
    $alamat = mysqli_real_escape_string($koneksi, $_POST['alamat']);
    $role = "User";

    // 2. LOGIKA VALIDASI BATAS MINIMUM 8 DAN MAKSIMUM 16 KARAKTER
    if (strlen($password_raw) < 8 || strlen($password_raw) > 16) {
        echo "<div class='alert alert-danger mt-3 py-2 small border-0 text-center rounded-3'>Gagal: Password harus berukuran antara 8 sampai 16 karakter!</div>";
    } else {
        // Jika lolos validasi, baru lakukan Hashing Password
        $password = password_hash($password_raw, PASSWORD_DEFAULT);

        // Cek duplikasi email
        $cek = mysqli_query($koneksi, "SELECT * FROM users WHERE email='$email'");

        if (mysqli_num_rows($cek) > 0) {
            echo "<div class='alert alert-danger mt-3 py-2 small border-0 text-center rounded-3'>Email sudah digunakan</div>";
        } else {
            mysqli_query($koneksi, "INSERT INTO users (nama, email, password, jeniskelamin, nohp, alamat, role) VALUES ('$nama', '$email', '$password', '$jeniskelamin', '$nohp', '$alamat', '$role')");
            echo "<script>alert('Registrasi berhasil'); location='login.php';</script>";
        }
    }
}
?>
                </div>
            </div>

        </div>
    </div>

    <script>
        const togglePassword = document.getElementById('togglePassword');
        const password = document.getElementById('password');

        togglePassword.addEventListener('click', function() {
            const type = password.getAttribute('type') === 'password' ? 'text' : 'password';
            password.setAttribute('type', type);
            this.innerHTML = type === 'password' ? '<i class="bi bi-eye-slash"></i>' : '<i class="bi bi-eye"></i>';
        });
    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>