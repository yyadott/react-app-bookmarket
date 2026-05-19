<?php

if (isset($_POST['tambah'])) {
    $nama  = mysqli_real_escape_string($koneksi, $_POST['nama']);
    $email = mysqli_real_escape_string($koneksi, $_POST['email']);
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $jeniskelamin = mysqli_real_escape_string($koneksi, $_POST['jeniskelamin']);
    $nohp = mysqli_real_escape_string($koneksi, $_POST['nohp']);
    $alamat = mysqli_real_escape_string($koneksi, $_POST['alamat']);
    $role = "User";

    $cek = mysqli_query($koneksi, "SELECT * FROM users WHERE email='$email'");

    if (mysqli_num_rows($cek) > 0) {
        echo "<script>alert('Email sudah digunakan!');</script>";
    } else {
        mysqli_query($koneksi, "INSERT INTO users 
            (nama, email, password, jeniskelamin, nohp, alamat, role) 
            VALUES 
            ('$nama','$email','$password','$jeniskelamin','$nohp','$alamat','$role')");

        echo "<script>alert('Data pengguna berhasil ditambahkan');</script>";
        echo "<script>location='index.php?page=pengguna';</script>";
    }
}
?>

<div class="row page-titles mx-0">
    <div class="col">
        <h4>Data Pengguna</h4>
    </div>
</div>

<div class="container-fluid">
    <div class="card">
        <div class="card-body">

            <div class="d-flex justify-content-between mb-3">
                <h5 class="mb-0">Daftar Pengguna</h5>

                <button class="btn btn-primary btn-sm" data-toggle="modal" data-target="#modalTambah">
                    + Tambah Pengguna
                </button>
            </div>

            <div class="table-responsive">
                <table class="table table-bordered table-striped" id="datatable">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Nama</th>
                            <th>Email</th>
                            <th>Jenis Kelamin</th>
                            <th>No HP</th>
                            <th>Alamat</th>
                            <th width="150">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>

                        <?php
                        $no = 1;
                        $data = mysqli_query($koneksi, "SELECT * FROM users WHERE role='User' ORDER BY id DESC");

                        while ($row = mysqli_fetch_assoc($data)) {
                        ?>
                            <tr>
                                <td><?= $no++ ?></td>
                                <td><?= $row['nama'] ?></td>
                                <td><?= $row['email'] ?></td>
                                <td><?= $row['jeniskelamin'] ?></td>
                                <td><?= $row['nohp'] ?></td>
                                <td><?= $row['alamat'] ?></td>
                                <td>

                                    <!-- EDIT -->
                                    <a href="index.php?page=penggunaedit&id=<?= $row['id'] ?>"
                                        class="btn btn-warning btn-sm">
                                        Edit
                                    </a>

                                    <!-- HAPUS -->
                                    <a href="index.php?page=penggunahapus&id=<?= $row['id'] ?>"
                                        class="btn btn-danger btn-sm"
                                        onclick="return confirm('Yakin hapus pengguna ini?')">
                                        Hapus
                                    </a>

                                </td>
                            </tr>
                        <?php } ?>

                    </tbody>
                </table>
            </div>

        </div>
    </div>
</div>

<div class="modal fade" id="modalTambah">
    <div class="modal-dialog">
        <div class="modal-content">

            <form method="POST">
                <div class="modal-header">
                    <h5 class="modal-title">Tambah Pengguna</h5>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>

                <div class="modal-body">

                    <div class="form-group">
                        <label>Nama Pengguna</label>
                        <input type="text" name="nama" class="form-control" required>
                    </div>

                    <div class="form-group">
                        <label>Email</label>
                        <input type="email" name="email" class="form-control" required>
                    </div>

                    <div class="form-group">
                        <label>Password</label>
                        <input type="password" name="password" class="form-control" required>
                    </div>

                    <div class="form-group">
                        <label>Jenis Kelamin</label>
                        <select name="jeniskelamin" class="form-control" required>
                            <option value="">-- Pilih --</option>
                            <option value="Laki-laki">Laki-laki</option>
                            <option value="Perempuan">Perempuan</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label>No HP</label>
                        <input type="text" name="nohp" class="form-control" required>
                    </div>

                    <div class="form-group">
                        <label>Alamat</label>
                        <textarea name="alamat" class="form-control" required></textarea>
                    </div>

                </div>

                <div class="modal-footer">
                    <button type="submit" name="tambah" class="btn btn-primary">
                        Simpan
                    </button>
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">
                        Batal
                    </button>
                </div>
            </form>

        </div>
    </div>
</div>