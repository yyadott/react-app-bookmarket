<?php
include '../config/koneksi.php';

$id = $_GET['id'];

$query = mysqli_query($conn, "SELECT * FROM tbl_buku WHERE id_buku='$id'");
$data = mysqli_fetch_assoc($query);

echo json_encode($data);
?>