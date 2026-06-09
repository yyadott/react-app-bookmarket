<?php include 'header.php'; ?>

<?php
// Proteksi halaman: Jika belum login, paksa lempar ke halaman login
if (!isset($_SESSION['user'])) {
    echo "<script>location='login.php';</script>";
    exit;
}

$user = $_SESSION['user'];
?>

<style>
    body {
        background-color: #f8fafc;
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    }
    
    /* HERO BANNER TOP (Sesuai Gambar UI Atas) */
    .bg-profile-blue {
        background: linear-gradient(180deg, #5a67d8 0%, #4c51bf 100%);
        border-radius: 0 0 30px 30px;
    }
    .inner-profile-card {
        background: rgba(255, 255, 255, 0.1);
        backdrop-filter: blur(10px);
        -webkit-backdrop-filter: blur(10px);
        border: 1px solid rgba(255, 255, 255, 0.2);
        border-radius: 20px;
        padding: 3.5rem 2rem;
    }

    /* SUB-HEADER TITLE */
    .section-title-group {
        font-size: 1.15rem;
        font-weight: 700;
        color: #2d3748;
        margin-top: 2rem;
        margin-bottom: 1.25rem;
    }

    /* GAYA FOTO PROFIL LINGKARAN & TOMBOL */
    .avatar-wrapper-card {
        background: #e2e8f0;
        border-radius: 24px;
        padding: 2.5rem 1.5rem;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        height: 100%;
        min-height: 250px;
    }
    .avatar-circle-grey {
        width: 120px;
        height: 120px;
        background-color: #cbd5e0;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #718096;
        font-size: 3.5rem;
        margin-bottom: 1.5rem;
        box-shadow: inset 0 2px 8px rgba(0,0,0,0.05);
    }
    .btn-change-photo {
        background-color: #2b6cb0;
        color: #ffffff;
        font-size: 0.8rem;
        font-weight: 600;
        padding: 0.5rem 1.5rem;
        border-radius: 20px;
        border: none;
        box-shadow: 0 4px 6px rgba(43, 108, 176, 0.2);
        transition: background-color 0.2s;
    }
    .btn-change-photo:hover {
        background-color: #2c5282;
        color: #ffffff;
    }

    /* INPUT FIELD FORM STYLE (READONLY MINIMALIS) */
    .profile-label-text {
        font-size: 0.8rem;
        color: #a0aec0;
        font-weight: 500;
        margin-bottom: 0.35rem;
    }
    .form-control-profile {
        background-color: #ffffff !important;
        border: 1px solid #e2e8f0 !important;
        border-radius: 6px;
        padding: 0.65rem 1rem;
        font-size: 0.9rem;
        color: #2d3748;
        font-weight: 500;
        width: 100%;
        box-shadow: 0 2px 4px rgba(0,0,0,0.01) !important;
    }

    /* ACTION LOGOUT */
    .link-logout-action {
        color: #718096;
        font-weight: 600;
        font-size: 0.9rem;
        text-decoration: none;
        transition: color 0.2s;
        display: inline-flex;
        align-items: center;
    }
    .link-logout-action:hover {
        color: #e53e3e;
    }
</style>

<div class="w-100 bg-profile-blue py-5 mb-5 text-center text-white">
    <div class="container px-4">
        <div class="inner-profile-card mx-auto" style="max-width: 960px;">
            <h1 class="fw-bold mb-0 display-6" style="letter-spacing: -0.5px;">Akun</h1>
        </div>
    </div>
</div>

<div class="container pb-5" style="max-width: 960px;">
    
    <h5 class="section-title-group">Personal Informasi</h5>
    <div class="row g-4 mb-4">
        <div class="col-md-4">
            <div class="avatar-wrapper-card">
                <div class="avatar-circle-grey">
                    <i class="bi bi-person-fill" style="font-size: 4rem; color: #a0aec0;"></i>
                </div>
                <button type="button" class="btn btn-change-photo shadow-sm">Ganti Foto Profil</button>
            </div>

        </div>
        
        <div class="col-md-8 d-flex flex-column justify-content-center gap-3">
            <div>
                <label class="profile-label-text">Nama</label>
                <input type="text" class="form-control-profile" value="<?= htmlspecialchars($user['nama']) ?>" readonly>
            </div>
            <div>
                <label class="profile-label-text">Jenis Kelamin</label>
                <input type="text" class="form-control-profile" value="<?= htmlspecialchars($user['jeniskelamin'] ?? 'Laki-Laki') ?>" readonly>
            </div>
            <div>
                <label class="profile-label-text">Tanggal Lahir</label>
                <input type="text" class="form-control-profile" value="<?= htmlspecialchars($user['tanggal_lahir'] ?? '12 Agustus 1998') ?>" readonly>
            </div>
        </div>
    </div>

    <h5 class="section-title-group">Alamat</h5>
    <div class="d-flex flex-column gap-3 mb-4">
        <div>
            <label class="profile-label-text">Alamat Rumah</label>
            <input type="text" class="form-control-profile" value="<?= htmlspecialchars($user['alamat']) ?>" readonly>
        </div>
        <div>
            <label class="profile-label-text">Kota</label>
            <input type="text" class="form-control-profile" value="<?= htmlspecialchars($user['kota'] ?? 'Jakarta') ?>" readonly>
        </div>
        <div>
            <label class="profile-label-text">Provinsi</label>
            <input type="text" class="form-control-profile" value="<?= htmlspecialchars($user['provinsi'] ?? 'DKI Jakarta') ?>" readonly>
        </div>
    </div>

    <h5 class="section-title-group">Detail Kontak</h5>
    <div class="d-flex flex-column gap-3 mb-4">
        <div>
            <label class="profile-label-text">Nomor Handphone</label>
            <input type="text" class="form-control-profile" value="<?= htmlspecialchars($user['nohp']) ?>" readonly>
        </div>
        <div>
            <label class="profile-label-text">Email</label>
            <input type="email" class="form-control-profile" value="<?= htmlspecialchars($user['email']) ?>" readonly>
        </div>
    </div>

    <h5 class="section-title-group">Lainnya</h5>
    <div class="pt-1">
        <a href="logout.php" onclick="return confirm('Yakin ingin logout dari sistem?')" class="link-logout-action">
            <i class="bi bi-box-arrow-left me-2 fs-5 text-danger"></i> Logout
        </a>
    </div>

</div>

<?php include 'footer.php'; ?>