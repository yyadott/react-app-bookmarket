<?php
session_start();
require_once 'koneksi.php';

// Jika user sudah login, langsung lempar ke halaman utama/panel
if (isset($_SESSION['user'])) {
    header("Location: index.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Minta Atur Ulang Kata Sandi - Book Market</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <style>
        body, html {
            height: 100%;
            background-color: #f3f4f6;
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif;
        }
        .wrapper {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 1.5rem;
        }
        .card-custom {
            background-color: #ffffff;
            border-radius: 16px;
            box-shadow: 0 4px 16px rgba(0, 0, 0, 0.04);
            width: 100%;
            max-width: 440px;
            padding: 2.5rem 2.2rem;
        }
        .btn-custom {
            background-color: #5a67d8;
            color: #ffffff;
            font-weight: 600;
            border-radius: 10px;
            border: none;
            width: 100%;
            padding: 0.75rem;
            transition: background-color 0.2s;
        }
        .btn-custom:hover {
            background-color: #434f9a;
        }
        .form-label-custom {
            font-size: 0.85rem;
            font-weight: 600;
            color: #374151;
            margin-bottom: 0.4rem;
        }
        .input-group-custom {
            border: 1px solid #e5e7eb;
            border-radius: 10px;
            display: flex;
            align-items: center;
            overflow: hidden;
            margin-bottom: 1.25rem;
        }
        .input-icon-span {
            padding-left: 1rem;
            color: #9ca3af;
        }
        .form-input-custom {
            border: none !important;
            padding: 0.75rem 1rem;
            font-size: 0.95rem;
            width: 100%;
            outline: none;
        }
    </style>
</head>

<body>

    <div class="wrapper">
        <div class="card-custom">
            
            <div class="mb-4">
                <h3 class="fw-bold text-dark mb-1">Reset Password</h3>
                <p class="text-secondary small">Masukkan email akun Anda untuk mengatur ulang kata sandi.</p>
            </div>

            <form method="POST">
                <div class="mb-3">
                    <label class="form-label-custom">Alamat Email</label>
                    <div class="input-group-custom">
                        <span class="input-icon-span"><i class="bi bi-envelope"></i></span>
                        <input type="email" name="email" class="form-input-custom" placeholder="woilah@gmail.com" required>
                    </div>
                </div>

                <button type="submit" name="kirim_reset" class="btn-custom shadow-sm mb-3">
                    Konfirmasi
                </button>
            </form>

            <div class="text-center">
                <a href="login.php" class="text-decoration-none small fw-semibold" style="color: #5a67d8;">
                    <i class="bi bi-arrow-left me-1"></i> Kembali ke Login
                </a>
            </div>

            <?php
            if (isset($_POST['kirim_reset'])) {
                $email = mysqli_real_escape_string($koneksi, $_POST['email']);

                // 1. Cek apakah email terdaftar di tabel users
                $query = mysqli_query($koneksi, "SELECT * FROM users WHERE email='$email'");
                $cek = mysqli_num_rows($query);

                if ($cek > 0) {
                    $row = mysqli_fetch_assoc($query);
                    
                    // Alur Simulasi Nyata (Tanpa Mail Server SMTP eksternal):
                    // Kita melemparkan id user langsung lewat URL query string ke halaman pembuatan password baru.
                    echo "
                        <script>
                            alert('Email terdaftar! Menuju halaman konfigurasi kata sandi baru.');
                            window.location='gantipassword.php?id=" . $row['id'] . "';
                        </script>
                    ";
                } else {
                    echo "
                        <div class='alert alert-danger border-0 mt-3 py-2 small text-center text-danger bg-danger-subtle rounded-3 fw-medium'>
                            Maaf, alamat email tidak ditemukan/tidak terdaftar.
                        </div>
                    ";
                }
            }
            ?>

        </div>
    </div>

</body>
</html>