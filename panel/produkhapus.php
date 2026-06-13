<?php

$id = intval($_GET['id']);

$data = mysqli_query($koneksi, "SELECT * FROM produk WHERE id='$id'");
$row = mysqli_fetch_assoc($data);

if (!$row) {
    echo "<script>alert('Data produk tidak ditemukan');</script>";
    echo "<script>location='index.php?page=produk';</script>";
    exit;
}

$folder = "../assets/uploads/produk/";

if (!empty($row['foto']) && file_exists($folder . $row['foto'])) {
    unlink($folder . $row['foto']);
}

mysqli_query($koneksi, "DELETE FROM produk WHERE id='$id'");

echo "<script>alert('Data produk berhasil dihapus');</script>";
echo "<script>location='index.php?page=produk';</script>";
