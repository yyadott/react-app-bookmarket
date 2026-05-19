<?php
include '../config/koneksi.php';

$result = mysqli_query($conn, "SELECT * FROM kategori");

$data = [];
while($row = mysqli_fetch_assoc($result)){
    $data[] = $row;
}

echo json_encode($data);
?>