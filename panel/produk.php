<?php

if (isset($_POST['tambah'])) {

    $namaproduk = mysqli_real_escape_string($koneksi, $_POST['namaproduk']);
    $deskripsi   = mysqli_real_escape_string($koneksi, $_POST['deskripsi']);
    $harga       = mysqli_real_escape_string($koneksi, $_POST['harga']);
    $kategori_id = mysqli_real_escape_string($koneksi, $_POST['kategori_id']);
    $stok       = mysqli_real_escape_string($koneksi, $_POST['stok']);

    $foto = $_FILES['foto']['name'];
    $tmp  = $_FILES['foto']['tmp_name'];

    $folder = "../assets/uploads/produk/";

    if (!is_dir($folder)) {
        mkdir($folder, 0777, true);
    }

    $nama_foto = time() . "_" . basename($foto);

    move_uploaded_file($tmp, $folder . $nama_foto);

    mysqli_query($koneksi, "INSERT INTO produk 
        (namaproduk,  deskripsi, harga, stok, foto, kategori_id) 
        VALUES 
        ('$namaproduk','$deskripsi','$harga','$stok','$nama_foto','$kategori_id')");

    echo "<script>alert('Data produk berhasil ditambahkan');</script>";
    echo "<script>location='index.php?page=produk';</script>";
}
?>

<div class="row page-titles mx-0">
    <div class="col">
        <h4>Data Produk</h4>
    </div>
</div>

<div class="container-fluid">
    <div class="card">
        <div class="card-body">

            <div class="d-flex justify-content-between mb-3">
                <h5 class="mb-0">Daftar Produk</h5>

                <button class="btn btn-primary btn-sm" data-toggle="modal" data-target="#modalTambah">
                    + Tambah Produk
                </button>
            </div>

            <div class="table-responsive">
                <table class="table table-bordered table-striped" id="datatable">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Nama Produk</th>
                            <th>Nama Kategori</th>
                            <th>Deskripsi</th>
                            <th>Harga</th>
                            <th>Stok</th>
                            <th>Foto</th>
                            <th width="150">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>

                        <?php
                        $no = 1;
                        $data = mysqli_query($koneksi, "SELECT produk.*, kategori.namakategori FROM produk LEFT JOIN kategori ON produk.kategori_id = kategori.id ORDER BY produk.id DESC");

                        while ($row = mysqli_fetch_assoc($data)) {
                        ?>
                            <tr>
                                <td><?= $no++ ?></td>
                                <td><?= $row['namaproduk'] ?></td>
                                <td><?= $row['namakategori'] ?? '-' ?></td>
                                <td><?= $row['deskripsi'] ?></td>
                                <td>Rp. <?= number_format($row['harga']) ?></td>
                                <td><?= $row['stok'] ?></td>
                                <td>
                                    <?php if ($row['foto']) { ?>
                                        <img src="../assets/uploads/produk/<?= $row['foto'] ?>" width="80" style="border-radius:8px;">
                                    <?php } else { ?>
                                        <span class="text-muted">Tidak ada</span>
                                    <?php } ?>
                                </td>
                                <td>

                                    <a href="index.php?page=produkedit&id=<?= $row['id'] ?>"
                                        class="btn btn-warning btn-sm">
                                        Edit
                                    </a>

                                    <a href="index.php?page=produkhapus&id=<?= $row['id'] ?>"
                                        class="btn btn-danger btn-sm"
                                        onclick="return confirm('Yakin hapus produk ini?')">
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

            <form method="POST" enctype="multipart/form-data">
                <div class="modal-header">
                    <h5 class="modal-title">Tambah Produk</h5>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>

                <div class="modal-body">

                    <div class="form-group">
                        <label>Nama Produk</label>
                        <input type="text" name="namaproduk" class="form-control" required>
                    </div>

                    <div class="form-group">
                        <label>Kategori</label>
                        <select name="kategori_id" class="form-control" required>
                            <option value="" selected disabled>-- Pilih Kategori --</option>
                            <?php
                            $kategoriData = mysqli_query($koneksi, "SELECT * FROM kategori ORDER BY namakategori ASC");
                            while ($kategori = mysqli_fetch_assoc($kategoriData)) {
                                echo "<option value='" . $kategori['id'] . "'>" . $kategori['namakategori'] . "</option>";
                            }
                            ?>
                        </select>
                    </div>

                    <div class="form-group">
                        <label>Deskripsi</label>
                        <textarea name="deskripsi" class="form-control" required></textarea>
                    </div>

                    <div class="form-group">
                        <label>Harga</label>
                        <input type="number" name="harga" class="form-control" required>
                    </div>

                    <div class="form-group">
                        <label>Stok</label>
                        <input type="number" name="stok" class="form-control" required>
                    </div>

                    <div class="form-group">
                        <label>Foto</label>
                        <input type="file" name="foto" class="form-control" accept="image/*" required>
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