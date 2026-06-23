<?php include 'header.php'; ?>

<?php
if (!isset($_SESSION['user'])) {
    echo "
        <script>
            alert('Silakan login terlebih dahulu');
            location='login.php';
        </script>
    ";
    exit;
}

$iduser = $_SESSION['user']['id'];
$user = mysqli_fetch_assoc(mysqli_query($koneksi, "
    SELECT *
    FROM users
    WHERE id='$iduser'
"));

if (!$user) {
    session_destroy();
    echo "
        <script>
            alert('User tidak ditemukan');
            location='login.php';
        </script>
    ";
    exit;
}

// UPDATE PROFILE PROCESS
if (isset($_POST['simpan'])) {
    $nama = mysqli_real_escape_string($koneksi, $_POST['nama']);
    $email = mysqli_real_escape_string($koneksi, $_POST['email']);
    $jeniskelamin = mysqli_real_escape_string($koneksi, $_POST['jeniskelamin']);
    $nohp = mysqli_real_escape_string($koneksi, $_POST['nohp']);
    $alamat = mysqli_real_escape_string($koneksi, $_POST['alamat']);
    $kota = mysqli_real_escape_string($koneksi, $_POST['kota'] ?? '');
    $provinsi = mysqli_real_escape_string($koneksi, $_POST['provinsi'] ?? '');
    $password = $_POST['password'];

    $cek = mysqli_query($koneksi, "
        SELECT * FROM users WHERE email='$email' AND id != '$iduser'
    ");

    if (mysqli_num_rows($cek) > 0) {
        echo "<script>alert('Email sudah digunakan');</script>";
    } else {
        if (empty($password)) {
            mysqli_query($koneksi, "
                UPDATE users SET
                    nama='$nama',
                    email='$email',
                    jeniskelamin='$jeniskelamin',
                    nohp='$nohp',
                    alamat='$alamat',
                    kota='$kota',
                    provinsi='$provinsi'
                WHERE id='$iduser'
            ");
        } else {
            $passwordbaru = password_hash($password, PASSWORD_DEFAULT);
            mysqli_query($koneksi, "
                UPDATE users SET
                    nama='$nama',
                    email='$email',
                    password='$passwordbaru',
                    jeniskelamin='$jeniskelamin',
                    nohp='$nohp',
                    alamat='$alamat',
                    kota='$kota',
                    provinsi='$provinsi'
                WHERE id='$iduser'
            ");
        }

        $_SESSION['user'] = mysqli_fetch_assoc(mysqli_query($koneksi, "
            SELECT * FROM users WHERE id='$iduser'
        "));

        echo "
            <script>
                alert('Profile berhasil diperbarui');
                location='profile.php';
            </script>
        ";
    }
}
?>

<style>
    body {
        background-color: #f4f6f9;
        font-family: 'Segoe UI', Arial, sans-serif;
    }

    /* Hero Banner Sesuai Gambar mockup */
    .hero-account-banner {
        background: linear-gradient(135deg, #4f5b93 0%, #6372b0 100%);
        border-radius: 24px;
        padding: 4rem 2rem;
        text-align: center;
        color: #ffffff;
        position: relative;
        overflow: hidden;
        box-shadow: 0 10px 25px rgba(79, 91, 147, 0.15);
    }
    .hero-account-banner h2 {
        font-size: 2.25rem;
        font-weight: 700;
        letter-spacing: 0.5px;
        margin: 0;
        z-index: 2;
        position: relative;
    }

    /* Sub-title Kategori Form */
    .form-category-title {
        font-size: 1.2rem;
        font-weight: 700;
        color: #3a3a3a;
        margin-top: 2.5rem;
        margin-bottom: 1.25rem;
    }

    /* Wrapper Foto Profil Kiri */
    .avatar-upload-box {
        background-color: #e9ecef;
        border-radius: 20px;
        padding: 2.5rem 1.5rem;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        text-align: center;
        border: 1px solid #dee2e6;
    }
    .avatar-placeholder-circle {
        width: 110px;
        height: 110px;
        background-color: #adb5bd;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-bottom: 1.5rem;
        box-shadow: 0 4px 10px rgba(0,0,0,0.06);
    }
    .btn-blueprint-upload {
        background-color: #2151a1;
        color: #ffffff;
        font-size: 0.75rem;
        font-weight: 600;
        padding: 0.55rem 1.25rem;
        border-radius: 20px;
        border: none;
        transition: background-color 0.2s;
    }
    .btn-blueprint-upload:hover {
        background-color: #173b77;
        color: #ffffff;
    }

    /* Desain Elemen Input Sesuai Gambar */
    .custom-profile-label {
        font-size: 0.75rem;
        color: #8e8e8e;
        font-weight: 500;
        margin-bottom: 0.25rem;
    }
    .custom-input-field {
        background-color: #ffffff !important;
        border: 1px solid #dcdcdc !important;
        border-radius: 4px !important;
        padding: 0.6rem 0.75rem !important;
        font-size: 0.85rem !important;
        color: #2b2b2b !important;
        transition: border-color 0.2s;
    }
    .custom-input-field:focus {
        border-color: #4f5b93 !important;
        box-shadow: none !important;
    }

    /* Navigasi Link Bawah */
    .logout-action-trigger {
        color: #7a7a7a;
        font-size: 0.85rem;
        text-decoration: none;
        font-weight: 500;
    }
    .logout-action-trigger:hover {
        color: #dc3545;
    }
</style>

<div class="container py-4 mb-5" style="max-width: 1000px;">
    
    <div class="hero-account-banner mb-4">
        <h2>Akun</h2>
    </div>

    <form method="POST">
        
        <h5 class="form-category-title">Personal Informasi</h5>
        <div class="row g-4">
            <div class="col-md-3">
                <div class="avatar-upload-box">
                    <div class="avatar-placeholder-circle">
                        <i class="bi bi-person-fill" style="font-size: 3.5rem; color: #f8f9fa;"></i>
                    </div>
                    <button type="button" class="btn btn-blueprint-upload shadow-sm">Ganti Foto Profil</button>
                </div>
            </div>
            
            <div class="col-md-9 d-flex flex-column justify-content-between">
                <div class="mb-2">
                    <label class="custom-profile-label">Nama</label>
                    <input type="text" name="nama" class="form-control custom-input-field" value="<?= htmlspecialchars($user['nama']) ?>" required>
                </div>
                <div class="mb-2">
                    <label class="custom-profile-label">Jenis Kelamin</label>
                    <select name="jeniskelamin" class="form-select custom-input-field" required>
                        <option value="Laki-laki" <?= $user['jeniskelamin'] == 'Laki-laki' ? 'selected' : '' ?>>Laki-Laki</option>
                        <option value="Perempuan" <?= $user['jeniskelamin'] == 'Perempuan' ? 'selected' : '' ?>>Perempuan</option>
                    </select>
                </div>
                <div class="mb-0">
                    <label class="custom-profile-label">Tanggal Lahir</label>
                    <input type="text" name="tanggal_lahir" class="form-control custom-input-field" value="<?= htmlspecialchars($user['tanggal_lahir'] ?? '19 Agustus 1999') ?>">
                </div>
            </div>
        </div>

        <h5 class="form-category-title">Alamat</h5>
        <div class="row g-3">
            <div class="col-12">
                <label class="custom-profile-label">Alamat Rumah</label>
                <input type="text" name="alamat" class="form-control custom-input-field" value="<?= htmlspecialchars($user['alamat']) ?>" required>
            </div>
            <div class="col-12">
                <label class="custom-profile-label">Kota</label>
                <input type="text" name="kota" class="form-control custom-input-field" value="<?= htmlspecialchars($user['kota'] ?? 'Cimahi') ?>">
            </div>
            <div class="col-12">
                <label class="custom-profile-label">Provinsi</label>
                <input type="text" name="provinsi" class="form-control custom-input-field" value="<?= htmlspecialchars($user['provinsi'] ?? 'West Java') ?>">
            </div>
        </div>

        <h5 class="form-category-title">Detail Kontak</h5>
        <div class="row g-3">
            <div class="col-12">
                <label class="custom-profile-label">Nomor Handphone</label>
                <input type="text" name="nohp" class="form-control custom-input-field" value="<?= htmlspecialchars($user['nohp']) ?>" required>
            </div>
            <div class="col-12">
                <label class="custom-profile-label">Email</label>
                <input type="email" name="email" class="form-control custom-input-field" value="<?= htmlspecialchars($user['email']) ?>" required>
            </div>
            <div class="col-12">
                <label class="custom-profile-label">Password Baru (Kosongkan jika tidak diubah)</label>
                <input type="password" name="password" class="form-control custom-input-field" placeholder="Masukkan password baru jika ingin mengganti">
            </div>
        </div>

        <div class="mt-4 pt-2">
            <button type="submit" name="simpan" class="btn btn-blueprint-upload rounded-2 px-4 py-2 fw-bold">
                <i class="bi bi-save me-1"></i> Simpan Perubahan
            </button>
        </div>
    </form>

    <h5 class="form-category-title">Lainnya</h5>
    <div class="ps-1">
        <a href="logout.php" onclick="return confirm('Yakin ingin logout dari sistem?')" class="logout-action-trigger">
            <i class="bi bi-box-arrow-left me-1"></i> Logout
        </a>
    </div>

</div>

<?php include 'footer.php'; ?>