<?php

$id = intval($_GET['id']);

$data = mysqli_query($koneksi, "SELECT * FROM kategori WHERE id='$id'");
$row = mysqli_fetch_assoc($data);

if (!$row) {
    echo "<script>alert('Data kategori tidak ditemukan');</script>";
    echo "<script>location='index.php?page=kategori';</script>";
    exit;
}

if (isset($_POST['update'])) {

    $nama  = mysqli_real_escape_string($koneksi, $_POST['nama']);

    mysqli_query($koneksi, "UPDATE kategori SET 
        namakategori='$nama'
        WHERE id='$id'
        ");

    echo "<script>alert('Data kategori berhasil diupdate');</script>";
    echo "<script>location='index.php?page=kategori';</script>";
}
?>

<div class="row page-titles mx-0">
    <div class="col">
        <h4>Edit Kategori</h4>
    </div>
</div>

<div class="container-fluid">
    <div class="card">
        <div class="card-body">

            <form method="POST">

                <div class="form-group">
                    <label>Nama Kategori</label>
                    <input type="text" name="nama" class="form-control" value="<?= $row['namakategori'] ?>" required>
                </div>

                <button type="submit" name="update" class="btn btn-primary">
                    Update
                </button>

                <a href="index.php?page=kategori" class="btn btn-secondary">
                    Kembali
                </a>

            </form>

        </div>
    </div>
</div>