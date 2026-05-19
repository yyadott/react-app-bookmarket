<?php

$id = intval($_GET['id']);

$data = mysqli_query($koneksi, "SELECT * FROM produk WHERE id='$id'");
$row = mysqli_fetch_assoc($data);

if (!$row) {
    echo "<script>alert('Data produk tidak ditemukan');</script>";
    echo "<script>location='index.php?page=produk';</script>";
    exit;
}

if (isset($_POST['update'])) {

    $namaproduk = mysqli_real_escape_string($koneksi, $_POST['namaproduk']);
    $deskripsi   = mysqli_real_escape_string($koneksi, $_POST['deskripsi']);
    $harga       = mysqli_real_escape_string($koneksi, $_POST['harga']);
    $kategori_id = mysqli_real_escape_string($koneksi, $_POST['kategori_id']);
    $stok      = mysqli_real_escape_string($koneksi, $_POST['stok']);

    $folder = "../assets/uploads/produk/";

    if (!empty($_FILES['foto']['name'])) {

        $foto = $_FILES['foto']['name'];
        $tmp  = $_FILES['foto']['tmp_name'];

        $allowed = ['jpg', 'jpeg', 'png', 'webp'];
        $ext = strtolower(pathinfo($foto, PATHINFO_EXTENSION));

        if (!in_array($ext, $allowed)) {
            echo "<script>alert('Format foto harus JPG/JPEG/PNG');</script>";
        } else {

            $nama_foto = time() . "_" . basename($foto);

            move_uploaded_file($tmp, $folder . $nama_foto);

            if (!empty($row['foto']) && file_exists($folder . $row['foto'])) {
                unlink($folder . $row['foto']);
            }

            mysqli_query($koneksi, "UPDATE produk SET 
                namaproduk='$namaproduk',
                deskripsi='$deskripsi',
                harga='$harga',
                kategori_id='$kategori_id',
                stok='$stok',
                foto='$nama_foto'
                WHERE id='$id'");

            echo "<script>alert('Data produk berhasil diupdate');</script>";
            echo "<script>location='index.php?page=produk';</script>";
        }
    } else {

        // update tanpa ganti foto
        mysqli_query($koneksi, "UPDATE produk SET 
            namaproduk='$namaproduk',
            deskripsi='$deskripsi',
            kategori_id='$kategori_id',
            stok='$stok',
            harga='$harga'
            WHERE id='$id'");

        echo "<script>alert('Data produk berhasil diupdate');</script>";
        echo "<script>location='index.php?page=produk';</script>";
    }
}
?>

<div class="row page-titles mx-0">
    <div class="col">
        <h4>Edit Produk</h4>
    </div>
</div>

<div class="container-fluid">
    <div class="card">
        <div class="card-body">

            <form method="POST" enctype="multipart/form-data">

                <div class="form-group">
                    <label>Nama Produk</label>
                    <input type="text" name="namaproduk" class="form-control"
                        value="<?= $row['namaproduk'] ?>" required>
                </div>

                <div class="form-group">
                    <label>Nama Kategori</label>
                    <select name="kategori_id" class="form-control" required>
                        <option value="" selected disabled>-- Pilih --</option>
                        <?php
                        $kategori = mysqli_query($koneksi, "SELECT * FROM kategori ORDER BY namakategori ASC");
                        while ($p = mysqli_fetch_assoc($kategori)) {
                            $selected = ($p['id'] == $row['kategori_id']) ? 'selected' : '';
                            echo "<option value='{$p['id']}' $selected>{$p['namakategori']}</option>";
                        }
                        ?>
                    </select>
                </div>

                <div class="form-group">
                    <label>Deskripsi</label>
                    <textarea name="deskripsi" class="form-control" required><?= $row['deskripsi'] ?></textarea>
                </div>

                <div class="form-group">
                    <label>Foto Saat Ini</label><br>
                    <?php if ($row['foto']) { ?>
                        <img src="../assets/uploads/produk/<?= $row['foto'] ?>" width="120" style="border-radius:10px;">
                    <?php } else { ?>
                        <span class="text-muted">Tidak ada foto</span>
                    <?php } ?>
                </div>

                <div class="form-group">
                    <label>Harga</label>
                    <input type="number" name="harga" class="form-control" value="<?= $row['harga'] ?>" required>
                </div>

                <div class="form-group">
                    <label>Stok</label>
                    <input type="number" name="stok" class="form-control" value="<?= $row['stok'] ?>" required>
                </div>

                <div class="form-group">
                    <label>Ganti Foto (opsional)</label>
                    <input type="file" name="foto" class="form-control" accept="image/*">
                </div>

                <button type="submit" name="update" class="btn btn-primary">
                    Update
                </button>

                <a href="index.php?page=layanan" class="btn btn-secondary">
                    Kembali
                </a>

            </form>

        </div>
    </div>
</div>