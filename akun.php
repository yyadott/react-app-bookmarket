<?php 
include 'header.php'; 

// Proteksi halaman: Jika belum login, paksa lempar ke halaman login
if (!isset($_SESSION['user'])) {
    echo "<script>location='login.php';</script>";
    exit;
}

$user_id = $_SESSION['user']['id'];

// PROSES UPDATE DATABASE SAAT TOMBOL SIMPAN DIKLIK
if (isset($_POST['update_profile'])) {
    $nama          = mysqli_real_escape_string($koneksi, $_POST['nama']);
    $jeniskelamin  = mysqli_real_escape_string($koneksi, $_POST['jeniskelamin']);
    $tanggal_lahir = mysqli_real_escape_string($koneksi, $_POST['tanggal_lahir']);
    $alamat        = mysqli_real_escape_string($koneksi, $_POST['alamat']);
    $kota          = mysqli_real_escape_string($koneksi, $_POST['kota']);
    $provinsi      = mysqli_real_escape_string($koneksi, $_POST['provinsi']);
    $nohp          = mysqli_real_escape_string($koneksi, $_POST['nohp']);
    $email         = mysqli_real_escape_string($koneksi, $_POST['email']);

    // LOGIKA PROSES UNGGAH FOTO PROFIL BARU
    $foto_lama = $_SESSION['user']['foto'] ?? '';
    $foto_final = $foto_lama; 

    if (isset($_FILES['foto_profile']) && $_FILES['foto_profile']['error'] === UPLOAD_ERR_OK) {
        $folder_tujuan = "assets/uploads/profile/";
        
        if (!is_dir($folder_tujuan)) {
            mkdir($folder_tujuan, 0777, true);
        }

        $file_extension = pathinfo($_FILES['foto_profile']['name'], PATHINFO_EXTENSION);
        $nama_file_baru = "AVATAR_" . time() . "_" . bin2hex(random_bytes(4)) . "." . $file_extension;

        if (move_uploaded_file($_FILES['foto_profile']['tmp_name'], $folder_tujuan . $nama_file_baru)) {
            $foto_final = $nama_file_baru;
            if (!empty($foto_lama) && file_exists($folder_tujuan . $foto_lama)) {
                unlink($folder_tujuan . $foto_lama);
            }
        }
    }

    // Jalankan Query Update data ke tabel users
    $query = "UPDATE users SET 
                nama = '$nama', 
                jeniskelamin = '$jeniskelamin', 
                tanggal_lahir = '$tanggal_lahir', 
                alamat = '$alamat', 
                kota = '$kota', 
                provinsi = '$provinsi', 
                nohp = '$nohp', 
                email = '$email',
                foto = '$foto_final' 
              WHERE id = '$user_id'";
              
    if (mysqli_query($koneksi, $query)) {
        // Ambil data terbaru untuk disinkronkan ke dalam session internal
        $ambil = mysqli_query($koneksi, "SELECT * FROM users WHERE id = '$user_id'");
        $_SESSION['user'] = mysqli_fetch_assoc($ambil);
        
        // PERBAIKAN: Diarahkan langsung kembali ke akun.php agar tampilan tetap di sini
        echo "<script>alert('Profil dan foto berhasil diperbarui!'); location='akun.php';</script>";
        exit;
    } else {
        echo "<script>alert('Gagal memperbarui profil: " . mysqli_error($koneksi) . "');</script>";
    }
}

$user = $_SESSION['user'];
?>

<style>
    body {
        background-color: #f8fafc;
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    }
    
    /* HERO BANNER TOP */
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
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-bottom: 1.5rem;
        box-shadow: 0 4px 10px rgba(0,0,0,0.08);
        overflow: hidden;
        background-color: #cbd5e0;
    }
    .avatar-img-render {
        width: 100%;
        height: 100%;
        object-fit: cover;
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

    /* INPUT FIELD FORM STYLE */
    .profile-label-text {
        font-size: 0.8rem;
        color: #a0aec0;
        font-weight: 500;
        margin-bottom: 0.35rem;
    }
    .form-control-profile {
        background-color: #f1f5f9 !important; 
        border: 1px solid #e2e8f0 !important;
        border-radius: 6px;
        padding: 0.65rem 1rem;
        font-size: 0.9rem;
        color: #2d3748;
        font-weight: 500;
        width: 100%;
        box-shadow: 0 2px 4px rgba(0,0,0,0.01) !important;
    }
    .form-control-profile:not([readonly]), .form-control-profile:not([disabled]) {
        background-color: #ffffff !important; 
        border-color: #5a67d8 !important;
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
    
    <form method="POST" enctype="multipart/form-data">
        <div class="d-flex justify-content-between align-items-center mb-2">
            <h5 class="section-title-group my-0">Personal Informasi</h5>
            <div class="d-flex gap-2">
                <a href="riwayat.php" class="btn btn-sm btn-outline-secondary text-dark px-3 rounded-pill fw-bold bg-white shadow-sm text-decoration-none d-inline-flex align-items-center">
                    <i class="bi bi-clock-history me-1"></i> Riwayat Belanja
                </a>
                <button type="button" id="btn-edit-mode" class="btn btn-sm btn-primary px-3 rounded-pill fw-bold shadow-sm" onclick="enableEditMode()">
                    <i class="bi bi-pencil-square me-1"></i> Edit Profil
                </button>
            </div>
        </div>

        <div class="row g-4 mb-4">
            <div class="col-md-4">
                <div class="avatar-wrapper-card">
                    <div class="avatar-circle-grey">
                        <?php if (!empty($user['foto']) && file_exists("assets/uploads/profile/" . $user['foto'])): ?>
                            <img src="assets/uploads/profile/<?= $user['foto'] ?>" id="avatar-preview" class="avatar-img-render" alt="Foto Profil">
                        <?php else: ?>
                            <img src="" id="avatar-preview" class="avatar-img-render d-none" alt="Foto Profil">
                            <i id="avatar-icon-default" class="bi bi-person-fill" style="font-size: 4rem; color: #a0aec0;"></i>
                        <?php endif; ?>
                    </div>
                    
                    <input type="file" name="foto_profile" id="foto_profile" class="d-none" accept="image/*" onchange="previewImage(this)">
                    <button type="button" class="btn btn-change-photo shadow-sm" onclick="pemicuKlikFile()">Ganti Foto Profil</button>
                </div>
            </div>
            
            <div class="col-md-8 d-flex flex-column justify-content-center gap-3">
                <div>
                    <label class="profile-label-text">Nama</label>
                    <input type="text" name="nama" class="form-control-profile" value="<?= htmlspecialchars($user['nama'] ?? '') ?>" readonly required>
                </div>
                <div>
                    <label class="profile-label-text">Jenis Kelamin</label>
                    <select name="jeniskelamin" class="form-control-profile form-select" disabled required>
                        <option value="Laki-Laki" <?= (($user['jeniskelamin'] ?? '') == 'Laki-Laki') ? 'selected' : '' ?>>Laki-Laki</option>
                        <option value="Perempuan" <?= (($user['jeniskelamin'] ?? '') == 'Perempuan') ? 'selected' : '' ?>>Perempuan</option>
                    </select>
                </div>
                <div>
                    <label class="profile-label-text">Tanggal Lahir</label>
                    <input type="date" name="tanggal_lahir" class="form-control-profile" value="<?= htmlspecialchars($user['tanggal_lahir'] ?? '') ?>" readonly required>
                </div>
            </div>
        </div>

        <h5 class="section-title-group">Alamat</h5>
        <div class="d-flex flex-column gap-3 mb-4">
            <div>
                <label class="profile-label-text">Alamat Rumah</label>
                <input type="text" name="alamat" class="form-control-profile" value="<?= htmlspecialchars($user['alamat'] ?? '') ?>" readonly required>
            </div>
            <div>
                <label class="profile-label-text">Kota</label>
                <input type="text" name="kota" class="form-control-profile" value="<?= htmlspecialchars($user['kota'] ?? '') ?>" readonly required>
            </div>
            <div>
                <label class="profile-label-text">Provinsi</label>
                <input type="text" name="provinsi" class="form-control-profile" value="<?= htmlspecialchars($user['provinsi'] ?? '') ?>" readonly required>
            </div>
        </div>

        <h5 class="section-title-group">Detail Kontak</h5>
        <div class="d-flex flex-column gap-3 mb-4">
            <div>
                <label class="profile-label-text">Nomor Handphone</label>
                <input type="text" name="nohp" class="form-control-profile" value="<?= htmlspecialchars($user['nohp'] ?? '') ?>" readonly required>
            </div>
            <div>
                <label class="profile-label-text">Email</label>
                <input type="email" name="email" class="form-control-profile" value="<?= htmlspecialchars($user['email'] ?? '') ?>" readonly required>
            </div>
        </div>

        <div id="save-action-area" class="text-end pt-2 mb-5 d-none">
            <button type="button" class="btn btn-light border px-4 me-2 fw-semibold rounded-3" onclick="window.location.reload()">Batal</button>
            <button type="submit" name="update_profile" class="btn btn-success px-4 fw-semibold rounded-3 shadow-sm">
                <i class="bi bi-check-circle me-1"></i> Simpan Perubahan
            </button>
        </div>
    </form>

    <div id="logout-area" class="pt-1">
        <h5 class="section-title-group">Lainnya</h5>
        <a href="logout.php" onclick="return confirm('Yakin ingin logout dari sistem?')" class="link-logout-action">
            <i class="bi bi-box-arrow-left me-2 fs-5 text-danger"></i> Logout
        </a>
    </div>

</div>

<script>
    function enableEditMode() {
        const inputs = document.querySelectorAll('.form-control-profile');
        inputs.forEach(input => {
            input.removeAttribute('readonly');
        });
        
        document.querySelector('select[name="jeniskelamin"]').removeAttribute('disabled');
        document.getElementById('save-action-area').classList.remove('d-none');
        document.getElementById('btn-edit-mode').classList.add('d-none');
        document.getElementById('logout-area').classList.add('d-none');
    }

    function pemicuKlikFile() {
        document.getElementById('foto_profile').click();
    }

    function previewImage(input) {
        const preview = document.getElementById('avatar-preview');
        const iconDefault = document.getElementById('avatar-icon-default');
        
        if (input.files && input.files[0]) {
            const reader = new FileReader();
            
            reader.onload = function(e) {
                preview.src = e.target.result;
                preview.classList.remove('d-none');
                if (iconDefault) {
                    iconDefault.classList.add('d-none');
                }
            }
            
            reader.readAsDataURL(input.files[0]);
        }
    }
</script>

<?php include 'footer.php'; ?>