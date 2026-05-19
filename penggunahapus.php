<?php

if (isset($_GET['id'])) {
    $id = $_GET['id'];

    $cek = mysqli_query($koneksi, "SELECT * FROM users WHERE id='$id' AND role='User'");
    $data = mysqli_num_rows($cek);

    if ($data > 0) {

        mysqli_query($koneksi, "DELETE FROM users WHERE id='$id' AND role='User'");

        echo "<script>alert('Data berhasil dihapus');</script>";
        echo "<script>location='index.php?page=pengguna';</script>";
    } else {

        echo "<script>alert('Data tidak ditemukan');</script>";
        echo "<script>location='index.php?page=pengguna';</script>";
    }
} else {

    echo "<script>alert('ID tidak valid');</script>";
    echo "<script>location='index.php?page=pengguna';</script>";
}
