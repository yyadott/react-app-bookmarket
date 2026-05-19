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
</head>

<body class="bg-white">

    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-12 col-md-5 col-lg-4 text-center">

                <div class="d-flex align-items-center justify-content-center mb-4">
                    <!-- <img src="assets/foto/logo.jpeg" alt="Logo" width="40" height="40" class="rounded-circle me-2"> -->
                    <h4 class="fw-bold mb-0" style="color: #A65D37;">Book Market</h4>
                </div>

                <img src="assets/foto/login.png" class="img-fluid mb-4" width="100" alt="Catering Delivery">

                <h2 class="fw-bold mb-2">Login</h2>
                <p class="text-muted small mb-4">Selamat datang di E Catering, silahkan login untuk memesan catering</p>

                <form method="POST" class="text-start">
                    <div class="mb-3">
                        <label class="form-label fw-semibold text-secondary small">Email</label>
                        <input type="email" name="email" class="form-control form-control-lg border-light-subtle"
                            placeholder="Masukkan email Anda" required
                            style="background-color: #fcfcfc; font-size: 0.9rem;">
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold text-secondary small">Password</label>
                        <div class="input-group">
                            <input type="password" name="password" id="password" class="form-control form-control-lg border-light-subtle border-end-0"
                                placeholder="Masukkan password Anda" required
                                style="background-color: #fcfcfc; font-size: 0.9rem;">
                            <span class="input-group-text bg-transparent border-light-subtle border-start-0 text-muted">
                                <i class="bi bi-eye-slash"></i>
                            </span>
                        </div>
                    </div>

                    <button type="submit" name="login" class="btn btn-lg w-100 text-white fw-bold mb-3"
                        style="background-color: #A65D37; border-radius: 10px;">
                        Login
                    </button>

                    <!-- <div class="d-flex justify-content-between align-items-center mb-4 small">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="simpan">
                            <label class="form-check-label text-secondary" for="simpan">Simpan Login</label>
                        </div>
                        <a href="#" class="text-decoration-none" style="color: #2D4373;">Lupa Password?</a>
                    </div> -->
                </form>

                <p class="small">Belum punya akun? <a href="register.php" class="text-decoration-none fw-bold" style="color: #A65D37;">Buat Akun</a></p>

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
                            echo "<div class='alert alert-danger mt-3 py-2 small'>Email atau Password salah</div>";
                        }
                    } else {
                        echo "<div class='alert alert-danger mt-3 py-2 small'>Email atau Password salah</div>";
                    }
                }
                ?>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>