<?php

if (!isset($_SESSION['user'])) {
    echo "<script>alert('Silakan login');location='login.php';</script>";
    exit;
}

$id = intval($_GET['id']);

$pembayaran = mysqli_query($koneksi, "SELECT * FROM pembayaran WHERE transaksi_id='$id'");

while ($p = mysqli_fetch_assoc($pembayaran)) {

    if (!empty($p['buktibayar'])) {
        $file = "../assets/uploads/bukti/" . $p['buktibayar'];

        if (file_exists($file)) {
            unlink($file);
        }
    }
}

mysqli_query($koneksi, "DELETE FROM pembayaran WHERE transaksi_id='$id'");

mysqli_query($koneksi, "DELETE FROM transaksidetail WHERE transaksi_id='$id'");

mysqli_query($koneksi, "DELETE FROM transaksi WHERE id='$id'");

echo "<script>alert('Transaksi berhasil dihapus');</script>";
echo "<script>location='index.php?page=transaksi';</script>";
