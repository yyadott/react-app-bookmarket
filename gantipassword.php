<?php
session_start();
require_once 'koneksi.php';

// Ambil ID user dari parameter URL aman
if (!isset($_GET['id']) || empty($_GET['id'])) {
    header("Location: login.php");
    exit;
}

$id_user = intval($_GET['id']);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Kata Sandi Baru - Book Market</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <style>
        body, html { height: 100%; background-color: #f3f4f6; font-family: sans-serif; }
        .wrapper { min-height: 100vh; display: flex; align-items: center; justify-content: center; padding: 1.5rem; }
        .card-custom { background-color: #ffffff; border-radius: 16px; box-shadow: 0 4px 16px rgba(0,0,0,0.04); width: 100%; max-width: 440px; padding: 2.5rem 2.2rem; }
        .btn-custom { background-color: #5a67d8; color: #ffffff; font-weight: 600; border-radius: 10px; border: none; width: 100%; padding: 0.75rem; }
        .btn-custom:hover { background-color: #434f9a; }
        .form-label-custom { font-size: 0.85rem; font-weight: 600; color: #374151; margin-bottom: 0.4rem; }
        .input-group-custom { border: 1px solid #e5e7eb; border-radius: 10px; display: flex; align-items: center; overflow: hidden; margin-bottom: 1.25rem; }
        .form-input-custom { border: none !important; padding: 0.75rem 1rem; font-size: 0.95rem; width: 100%; outline: none; }
    </style>
</head>
<body>

    <div class="wrapper">
        <div class="card-custom">
            
            <div class="mb-4">
                <h3 class="fw-bold text-dark mb-1">Kata Sandi Baru</h3>
                <p class="text-secondary small">Silakan masukkan kata sandi baru Anda (8-16 karakter).</p>
            </div>

            <form method="POST">
                <div class="mb-3">
                    <label class="form-label-custom">Password Baru</label>
                    <div class="input-group-custom">
                        <input type="password" name="password_baru" class="form-input-custom" placeholder="Buat password baru" minlength="8" maxlength="16" required>
                    </div>
                </div>

                <button type="submit" name="update_password" class="btn-custom shadow-sm">
                    Simpan Kata Sandi
                </button>
            </form>

            <?php
            if (isset($_POST['update_password'])) {
                $pw_raw = $_POST['password_baru'];

                // Validasi panjang karakter
                if (strlen($pw_raw) < 8 || strlen($pw_raw) > 16) {
                    echo "<div class='alert alert-danger border-0 mt-3 py-2 small text-center rounded-3'>Gagal: Password harus 8-16 karakter!</div>";
                } else {
                    // Enkripsi password baru
                    $password_hashed = password_hash($pw_raw, PASSWORD_DEFAULT);

                    // Jalankan perintah UPDATE ke database
                    $update = mysqli_query($koneksi, "UPDATE users SET password='$password_hashed' WHERE id='$id_user'");

                    if ($update) {
                        echo "
                            <script>
                                alert('Kata sandi berhasil diperbarui! Silakan login kembali.');
                                window.location='login.php';
                            </script>
                        ";
                    } else {
                        echo "<div class='alert alert-danger border-0 mt-3 py-2 small text-center rounded-3'>Gagal memperbarui database.</div>";
                    }
                }
            }
            ?>

        </div>
    </div>

</body>
</html>