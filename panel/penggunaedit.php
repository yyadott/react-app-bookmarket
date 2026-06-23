<?php

$id = intval($_GET['id']);

$data = mysqli_query($koneksi, "SELECT * FROM users WHERE id='$id' AND role='User'");
$row = mysqli_fetch_assoc($data);

if (!$row) {
    echo "<script>alert('Data pengguna tidak ditemukan');</script>";
    echo "<script>location='index.php?page=pengguna';</script>";
    exit;
}

if (isset($_POST['update'])) {

    $nama  = mysqli_real_escape_string($koneksi, $_POST['nama']);
    $email = mysqli_real_escape_string($koneksi, $_POST['email']);
    $jeniskelamin = mysqli_real_escape_string($koneksi, $_POST['jeniskelamin']);
    $nohp = mysqli_real_escape_string($koneksi, $_POST['nohp']);
    $alamat = mysqli_real_escape_string($koneksi, $_POST['alamat']);

    // CEK EMAIL (biar ga double kecuali email dia sendiri)
    $cek = mysqli_query($koneksi, "SELECT * FROM users WHERE email='$email' AND id!='$id'");

    if (mysqli_num_rows($cek) > 0) {
        echo "<script>alert('Email sudah digunakan!');</script>";
    } else {

        // CEK PASSWORD DIISI ATAU TIDAK
        if (!empty($_POST['password'])) {
            $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

            mysqli_query($koneksi, "UPDATE users SET 
                nama='$nama',
                email='$email',
                password='$password',
                jeniskelamin='$jeniskelamin',
                nohp='$nohp',
                alamat='$alamat'
                WHERE id='$id'");
        } else {
            mysqli_query($koneksi, "UPDATE users SET 
                nama='$nama',
                email='$email',
                jeniskelamin='$jeniskelamin',
                nohp='$nohp',
                alamat='$alamat'
                WHERE id='$id'");
        }

        echo "<script>alert('Data pengguna berhasil diupdate');</script>";
        echo "<script>location='index.php?page=pengguna';</script>";
    }
}
?>

<div class="row page-titles mx-0">
    <div class="col">
        <h4>Edit Pengguna</h4>
    </div>
</div>

<div class="container-fluid">
    <div class="card">
        <div class="card-body">

            <form method="POST">

                <div class="form-group">
                    <label>Nama Pengguna</label>
                    <input type="text" name="nama" class="form-control" value="<?= $row['nama'] ?>" required>
                </div>

                <div class="form-group">
                    <label>Email</label>
                    <input type="email" name="email" class="form-control" value="<?= $row['email'] ?>" required>
                </div>

                <div class="form-group">
                    <label>Password (kosongkan jika tidak diubah)</label>
                    <input type="password" name="password" class="form-control">
                </div>

                <div class="form-group">
                    <label>Jenis Kelamin</label>
                    <select name="jeniskelamin" class="form-control" required>
                        <option value="">-- Pilih --</option>
                        <option value="Laki-laki" <?= ($row['jeniskelamin'] == 'Laki-laki') ? 'selected' : '' ?>>Laki-laki</option>
                        <option value="Perempuan" <?= ($row['jeniskelamin'] == 'Perempuan') ? 'selected' : '' ?>>Perempuan</option>
                    </select>
                </div>

                <div class="form-group">
                    <label>No HP</label>
                    <input type="text" name="nohp" class="form-control" value="<?= $row['nohp'] ?>" required>
                </div>

                <div class="form-group">
                    <label>Alamat</label>
                    <textarea name="alamat" class="form-control" required><?= $row['alamat'] ?></textarea>
                </div>

                <button type="submit" name="update" class="btn btn-primary">
                    Update
                </button>

                <a href="index.php?page=pengguna" class="btn btn-secondary">
                    Kembali
                </a>

            </form>

        </div>
    </div>
</div>