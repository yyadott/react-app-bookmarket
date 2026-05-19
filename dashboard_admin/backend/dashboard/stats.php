<?php
require '../config/koneksi.php';
error_reporting(0);

// Default nilai awal
$response = [
    'total_produk' => 0,
    'total_user' => 0,
    'total_transaksi' => 0 
];

// 1. Hitung total Produk
$queryProduk = mysqli_query($conn, "SELECT COUNT(*) as total FROM produk");
if($row = mysqli_fetch_assoc($queryProduk)) {
    $response['total_produk'] = $row['total'];
}

// 2. Hitung total User
// Memastikan query tidak error jika tabel user belum dibuat
$queryUser = mysqli_query($conn, "SELECT COUNT(*) as total FROM user");
if($queryUser && $row = mysqli_fetch_assoc($queryUser)) {
    $response['total_user'] = $row['total'];
}

// 3. Hitung total Transaksi 
// (Akan bernilai 0 sementara jika tabel transaksi belum ada)
$queryTransaksi = mysqli_query($conn, "SELECT COUNT(*) as total FROM transaksi");
if($queryTransaksi && $row = mysqli_fetch_assoc($queryTransaksi)) {
    $response['total_transaksi'] = $row['total'];
}

// Kirim data dalam format JSON
echo json_encode($response);
?>