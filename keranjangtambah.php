<?php
session_start();
include 'koneksi.php';

$id  = intval($_GET['id']);
$qty = isset($_GET['qty']) ? intval($_GET['qty']) : 1;

if ($qty < 1) {
    $qty = 1;
}

// stok produk
$produk = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT * FROM produk WHERE id='$id'"));
if ($produk['stok'] < $qty) {
    echo "
        <script>
            alert('Stok produk tidak mencukupi');
            location='produkdetail.php?id=$id';
        </script>
    ";
    exit;
}

if (!isset($_SESSION['keranjang'])) {
    $_SESSION['keranjang'] = [];
}

// JIKA PRODUK SUDAH ADA
if (isset($_SESSION['keranjang'][$id])) {

    $_SESSION['keranjang'][$id] += $qty;
} else {

    $_SESSION['keranjang'][$id] = $qty;
}

echo "
    <script>
        alert('Produk berhasil ditambahkan ke keranjang');
        location='keranjang.php';
    </script>
";
