<?php
require '../config/koneksi.php';

// Ambil data produk, urutkan dari yang terbaru (ID terbesar di atas)
$query = "SELECT * FROM produk ORDER BY id DESC";
$result = mysqli_query($conn, $query);

$data = [];
while($row = mysqli_fetch_assoc($result)) {
    $data[] = $row;
}

echo json_encode($data);
?>