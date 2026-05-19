<?php

if (isset($_POST['tambah'])) {
    $nama  = mysqli_real_escape_string($koneksi, $_POST['nama']);
    $jeniskelamin = mysqli_real_escape_string($koneksi, $_POST['jeniskelamin']);
    $nohp = mysqli_real_escape_string($koneksi, $_POST['nohp']);

    mysqli_query($koneksi, "INSERT INTO kategori 
        (namakategori) 
        VALUES 
        ('$nama')");

    echo "<script>alert('Data kategori berhasil ditambahkan');</script>";
    echo "<script>location='index.php?page=kategori';</script>";
}
?>

<div class="row page-titles mx-0">
    <div class="col">
        <h4>Data Kategori</h4>
    </div>
</div>

<div class="container-fluid">
    <div class="card">
        <div class="card-body">

            <div class="d-flex justify-content-between mb-3">
                <h5 class="mb-0">Daftar Kategori</h5>

                <button class="btn btn-primary btn-sm" data-toggle="modal" data-target="#modalTambah">
                    + Tambah Kategori
                </button>
            </div>

            <div class="table-responsive">
                <table class="table table-bordered table-striped" id="datatable">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Nama Kategori</th>
                            <th width="150">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>

                        <?php
                        $no = 1;
                        $data = mysqli_query($koneksi, "SELECT * FROM kategori ORDER BY id DESC");

                        while ($row = mysqli_fetch_assoc($data)) {
                        ?>
                            <tr>
                                <td><?= $no++ ?></td>
                                <td><?= $row['namakategori'] ?></td>
                                <td>

                                    <!-- EDIT -->
                                    <a href="index.php?page=kategoriedit&id=<?= $row['id'] ?>"
                                        class="btn btn-warning btn-sm">
                                        Edit
                                    </a>

                                    <!-- HAPUS -->
                                    <a href="index.php?page=kategorihapus&id=<?= $row['id'] ?>"
                                        class="btn btn-danger btn-sm"
                                        onclick="return confirm('Yakin hapus kategori ini?')">
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
                    <h5 class="modal-title">Tambah Kategori</h5>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>

                <div class="modal-body">

                    <div class="form-group">
                        <label>Nama Kategori</label>
                        <input type="text" name="nama" class="form-control" required>
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