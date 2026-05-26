<?php
include 'header.php';
require_once 'koneksi.php';

// Proteksi halaman
if (!isset($_SESSION['user'])) {
    echo "<script>location='login.php';</script>";
    exit;
}

// Ambil email user yang sedang login
$email = $_SESSION['user']['email'];

// Ambil data terbaru dari database
$query = mysqli_query($koneksi, "SELECT * FROM users WHERE email='$email'");
$user = mysqli_fetch_assoc($query);

// Jika user tidak ditemukan
if (!$user) {
    session_destroy();
    echo "<script>alert('Data pengguna tidak ditemukan'); location='login.php';</script>";
    exit;
}
?>

<style>
body {
    background-color: #f8fafc;
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
}

/* HEADER */
.bg-profile-blue {
    background: linear-gradient(180deg, #5a67d8 0%, #4c51bf 100%);
    border-radius: 0 0 30px 30px;
}

.inner-profile-card {
    background: rgba(255,255,255,0.1);
    backdrop-filter: blur(10px);
    border:1px solid rgba(255,255,255,0.2);
    border-radius:20px;
    padding:3rem;
}

.section-title-group{
    font-size:1.15rem;
    font-weight:700;
    color:#2d3748;
    margin-top:2rem;
    margin-bottom:1.2rem;
}

/* FOTO */
.avatar-wrapper-card{
    background:#e2e8f0;
    border-radius:24px;
    padding:2.5rem;
    display:flex;
    flex-direction:column;
    align-items:center;
    justify-content:center;
    min-height:260px;
}

.avatar-circle-grey{
    width:120px;
    height:120px;
    background:#cbd5e0;
    border-radius:50%;
    display:flex;
    align-items:center;
    justify-content:center;
    margin-bottom:20px;
}

.profile-label-text{
    font-size:.8rem;
    color:#718096;
    margin-bottom:5px;
    font-weight:600;
}

.form-control-profile{
    width:100%;
    padding:.75rem 1rem;
    border:1px solid #e2e8f0;
    border-radius:8px;
    background:white;
}

.link-logout-action{
    text-decoration:none;
    font-weight:600;
    color:#718096;
}

.link-logout-action:hover{
    color:red;
}
</style>

<div class="w-100 bg-profile-blue py-5 mb-5 text-center text-white">
    <div class="container">
        <div class="inner-profile-card mx-auto" style="max-width:900px">
            <h1 class="fw-bold">Akun Pelanggan</h1>
        </div>
    </div>
</div>

<div class="container pb-5" style="max-width:960px">

    <h5 class="section-title-group">
        Personal Informasi
    </h5>

    <div class="row g-4">

        <div class="col-md-4">

            <div class="avatar-wrapper-card">

                <div class="avatar-circle-grey">
                    <i class="bi bi-person-fill"
                    style="font-size:70px;color:#a0aec0;">
                    </i>
                </div>

                <h5 class="fw-bold">
                    <?= htmlspecialchars($user['nama']) ?>
                </h5>

                <small class="text-muted">
                    Pelanggan Book Market
                </small>

            </div>

        </div>

        <div class="col-md-8">

            <div class="mb-3">
                <label class="profile-label-text">
                    Nama Lengkap
                </label>

                <input type="text"
                class="form-control-profile"
                value="<?= htmlspecialchars($user['nama']) ?>"
                readonly>
            </div>

            <div class="mb-3">
                <label class="profile-label-text">
                    Jenis Kelamin
                </label>

                <input type="text"
                class="form-control-profile"
                value="<?= htmlspecialchars($user['jeniskelamin']) ?>"
                readonly>
            </div>

        </div>

    </div>


    <h5 class="section-title-group">
        Alamat
    </h5>

    <div class="mb-3">

        <label class="profile-label-text">
            Alamat Rumah
        </label>

        <textarea class="form-control-profile"
        rows="3"
        readonly><?= htmlspecialchars($user['alamat']) ?></textarea>

    </div>


    <h5 class="section-title-group">
        Detail Kontak
    </h5>

    <div class="mb-3">

        <label class="profile-label-text">
            Nomor Handphone
        </label>

        <input type="text"
        class="form-control-profile"
        value="<?= htmlspecialchars($user['nohp']) ?>"
        readonly>

    </div>

    <div class="mb-4">

        <label class="profile-label-text">
            Email
        </label>

        <input type="email"
        class="form-control-profile"
        value="<?= htmlspecialchars($user['email']) ?>"
        readonly>

    </div>

    <h5 class="section-title-group">
        Lainnya
    </h5>

    <a href="logout.php"
    onclick="return confirm('Yakin ingin logout?')"
    class="link-logout-action">

        <i class="bi bi-box-arrow-left me-2 text-danger"></i>
        Logout

    </a>

</div>

<?php include 'footer.php'; ?>