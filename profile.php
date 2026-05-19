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

// UPDATE PROFILE
if (isset($_POST['simpan'])) {

    $nama = mysqli_real_escape_string(
        $koneksi,
        $_POST['nama']
    );

    $email = mysqli_real_escape_string(
        $koneksi,
        $_POST['email']
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

    $password = $_POST['password'];

    // CEK EMAIL
    $cek = mysqli_query($koneksi, "
        SELECT *
        FROM users
        WHERE email='$email'
        AND id != '$iduser'
    ");

    if (mysqli_num_rows($cek) > 0) {

        echo "
            <script>
                alert('Email sudah digunakan');
            </script>
        ";
    } else {

        // UPDATE TANPA PASSWORD
        if (empty($password)) {

            mysqli_query($koneksi, "
                UPDATE users SET
                    nama='$nama',
                    email='$email',
                    jeniskelamin='$jeniskelamin',
                    nohp='$nohp',
                    alamat='$alamat'
                WHERE id='$iduser'
            ");
        } else {

            $passwordbaru = password_hash(
                $password,
                PASSWORD_DEFAULT
            );

            mysqli_query($koneksi, "
                UPDATE users SET
                    nama='$nama',
                    email='$email',
                    password='$passwordbaru',
                    jeniskelamin='$jeniskelamin',
                    nohp='$nohp',
                    alamat='$alamat'
                WHERE id='$iduser'
            ");
        }

        // UPDATE SESSION
        $_SESSION['user'] = mysqli_fetch_assoc(mysqli_query($koneksi, "
            SELECT *
            FROM users
            WHERE id='$iduser'
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

<div class="container py-4 mb-5">

    <!-- HEADER -->
    <div class="d-flex align-items-center mb-4">

        <a href="akun.php"
            class="btn btn-light rounded-circle shadow-sm me-3">

            <i class="bi bi-arrow-left"></i>

        </a>

        <div>

            <h4 class="fw-bold mb-0">
                Profile Saya
            </h4>

            <small class="text-muted">
                Kelola data akun Anda
            </small>

        </div>

    </div>

    <!-- PROFILE CARD -->
    <div class="card border-0 shadow-sm rounded-4 mb-4">

        <div class="card-body p-4 text-center">

            <div class="bg-primary text-white rounded-circle d-inline-flex align-items-center justify-content-center mb-3"
                style="width:90px; height:90px; font-size:35px;">

                <?= strtoupper(substr($user['nama'], 0, 1)) ?>

            </div>

            <h5 class="fw-bold mb-1">
                <?= $user['nama'] ?>
            </h5>

            <p class="text-muted mb-0">
                <?= $user['email'] ?>
            </p>

        </div>

    </div>

    <!-- FORM -->
    <div class="card border-0 shadow-sm rounded-4">

        <div class="card-body p-4">

            <form method="POST">

                <div class="mb-3">

                    <label class="form-label fw-semibold">
                        Nama Lengkap
                    </label>

                    <input type="text"
                        name="nama"
                        class="form-control rounded-3"
                        value="<?= $user['nama'] ?>"
                        required>

                </div>

                <div class="mb-3">

                    <label class="form-label fw-semibold">
                        Email
                    </label>

                    <input type="email"
                        name="email"
                        class="form-control rounded-3"
                        value="<?= $user['email'] ?>"
                        required>

                </div>

                <div class="mb-3">

                    <label class="form-label fw-semibold">
                        Jenis Kelamin
                    </label>

                    <select name="jeniskelamin"
                        class="form-select rounded-3"
                        required>

                        <option value="">
                            -- Pilih --
                        </option>

                        <option value="Laki-laki"
                            <?= $user['jeniskelamin'] == 'Laki-laki' ? 'selected' : '' ?>>

                            Laki-laki
                        </option>

                        <option value="Perempuan"
                            <?= $user['jeniskelamin'] == 'Perempuan' ? 'selected' : '' ?>>

                            Perempuan
                        </option>

                    </select>

                </div>

                <div class="mb-3">

                    <label class="form-label fw-semibold">
                        No HP
                    </label>

                    <input type="text"
                        name="nohp"
                        class="form-control rounded-3"
                        value="<?= $user['nohp'] ?>"
                        required>

                </div>

                <div class="mb-3">

                    <label class="form-label fw-semibold">
                        Alamat
                    </label>

                    <textarea
                        name="alamat"
                        class="form-control rounded-3"
                        rows="4"
                        required><?= $user['alamat'] ?></textarea>

                </div>

                <div class="mb-4">

                    <label class="form-label fw-semibold">
                        Password Baru
                    </label>

                    <input type="password"
                        name="password"
                        class="form-control rounded-3"
                        placeholder="Kosongkan jika tidak ingin mengubah password">

                    <small class="text-muted">
                        Isi hanya jika ingin mengganti password
                    </small>

                </div>

                <button type="submit"
                    name="simpan"
                    class="btn btn-primary w-100 rounded-pill py-3 fw-bold">

                    <i class="bi bi-check-circle me-1"></i>
                    Simpan Perubahan

                </button>

            </form>

        </div>

    </div>

</div>

<?php include 'footer.php'; ?>