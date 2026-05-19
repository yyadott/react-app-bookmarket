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

    <link rel="stylesheet"
        href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
</head>

<body class="bg-white">

    <div class="container py-5">

        <div class="row justify-content-center">

            <div class="col-12 col-md-6 col-lg-5 text-center">

                <!-- LOGO -->
                <div class="d-flex align-items-center justify-content-center mb-4">

                    <!-- <img src="assets/foto/logo.jpeg"
                        width="40"
                        height="40"
                        class="rounded-circle me-2"> -->

                    <h4 class="fw-bold mb-0"
                        style="color:#A65D37;">
                        Book Market
                    </h4>

                </div>

                <!-- ILLUSTRASI -->
                <!--
                <img src="assets/foto/register.png"
                    class="img-fluid mb-4">
                -->

                <h2 class="fw-bold mb-2">
                    Register
                </h2>

                <p class="text-muted small mb-4">
                    Buat akun untuk menggunakan layanan catering
                </p>

                <!-- FORM -->
                <form method="POST" class="text-start">

                    <!-- NAMA -->
                    <div class="mb-3">

                        <label class="form-label fw-semibold text-secondary small">
                            Nama Lengkap
                        </label>

                        <input
                            type="text"
                            name="nama"
                            class="form-control form-control-lg border-light-subtle"
                            placeholder="Masukkan nama lengkap"
                            required
                            style="background:#fcfcfc;font-size:0.9rem;">

                    </div>

                    <!-- EMAIL -->
                    <div class="mb-3">

                        <label class="form-label fw-semibold text-secondary small">
                            Email
                        </label>

                        <input
                            type="email"
                            name="email"
                            class="form-control form-control-lg border-light-subtle"
                            placeholder="Masukkan email"
                            required
                            style="background:#fcfcfc;font-size:0.9rem;">

                    </div>

                    <!-- PASSWORD -->
                    <div class="mb-3">

                        <label class="form-label fw-semibold text-secondary small">
                            Password
                        </label>

                        <div class="input-group">

                            <input
                                type="password"
                                name="password"
                                id="password"
                                class="form-control form-control-lg border-light-subtle border-end-0"
                                placeholder="Masukkan password"
                                required
                                style="background:#fcfcfc;font-size:0.9rem;">

                            <span
                                class="input-group-text bg-transparent border-light-subtle border-start-0 text-muted"
                                id="togglePassword"
                                style="cursor:pointer;">

                                <i class="bi bi-eye-slash"></i>

                            </span>

                        </div>

                    </div>

                    <!-- JENIS KELAMIN -->
                    <div class="mb-3">

                        <label class="form-label fw-semibold text-secondary small">
                            Jenis Kelamin
                        </label>

                        <select
                            name="jeniskelamin"
                            class="form-control form-control-lg border-light-subtle"
                            required
                            style="background:#fcfcfc;font-size:0.9rem;">

                            <option value="">
                                -- Pilih --
                            </option>

                            <option value="Laki-laki">
                                Laki-laki
                            </option>

                            <option value="Perempuan">
                                Perempuan
                            </option>

                        </select>

                    </div>

                    <!-- NO HP -->
                    <div class="mb-3">

                        <label class="form-label fw-semibold text-secondary small">
                            No Handphone
                        </label>

                        <input
                            type="text"
                            name="nohp"
                            class="form-control form-control-lg border-light-subtle"
                            placeholder="Masukkan no handphone"
                            required
                            style="background:#fcfcfc;font-size:0.9rem;">

                    </div>

                    <!-- ALAMAT -->
                    <div class="mb-4">

                        <label class="form-label fw-semibold text-secondary small">
                            Alamat
                        </label>

                        <textarea
                            name="alamat"
                            class="form-control border-light-subtle"
                            rows="3"
                            placeholder="Masukkan alamat"
                            required
                            style="background:#fcfcfc;font-size:0.9rem;"></textarea>

                    </div>

                    <!-- BUTTON -->
                    <button
                        type="submit"
                        name="register"
                        class="btn btn-lg w-100 text-white fw-bold mb-3"
                        style="background-color:#A65D37;border-radius:10px;">

                        Daftar

                    </button>

                </form>

                <!-- LOGIN -->
                <p class="small">

                    Sudah punya akun?

                    <a href="login.php"
                        class="text-decoration-none fw-bold"
                        style="color:#A65D37;">

                        Login

                    </a>

                </p>

                <?php

                if (isset($_POST['register'])) {

                    $nama  = mysqli_real_escape_string(
                        $koneksi,
                        $_POST['nama']
                    );

                    $email = mysqli_real_escape_string(
                        $koneksi,
                        $_POST['email']
                    );

                    $password = password_hash(
                        $_POST['password'],
                        PASSWORD_DEFAULT
                    );

                    $jeniskelamin = mysqli_real_escape_string(
                        $koneksi,
                        $_POST['jeniskelamin']
                    );

                    $nohp = mysqli_real_escape_string(
                        $koneksi,
                        $_POST['nohp']
                    );

                    $alamat = mysqli_real_escape_string(
                        $koneksi,
                        $_POST['alamat']
                    );

                    $role = "User";

                    // CEK EMAIL
                    $cek = mysqli_query(
                        $koneksi,
                        "SELECT * FROM users WHERE email='$email'"
                    );

                    if (mysqli_num_rows($cek) > 0) {

                        echo "
                            <div class='alert alert-danger mt-3 py-2 small'>
                                Email sudah digunakan
                            </div>
                        ";
                    } else {

                        mysqli_query($koneksi, "
                            INSERT INTO users
                            (
                                nama,
                                email,
                                password,
                                jeniskelamin,
                                nohp,
                                alamat,
                                role
                            )
                            VALUES
                            (
                                '$nama',
                                '$email',
                                '$password',
                                '$jeniskelamin',
                                '$nohp',
                                '$alamat',
                                '$role'
                            )
                        ");

                        echo "
                            <script>
                                alert('Registrasi berhasil');

                                location='login.php';
                            </script>
                        ";
                    }
                }

                ?>

            </div>

        </div>

    </div>

    <script>
        // SHOW HIDE PASSWORD
        const togglePassword =
            document.getElementById('togglePassword');

        const password =
            document.getElementById('password');

        togglePassword.addEventListener('click', function() {

            const type =
                password.getAttribute('type') === 'password' ?
                'text' :
                'password';

            password.setAttribute('type', type);

            this.innerHTML =
                type === 'password' ?
                '<i class="bi bi-eye-slash"></i>' :
                '<i class="bi bi-eye"></i>';
        });
    </script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

</body>

</html>