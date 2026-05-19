<?php
require '../config/koneksi.php';
$query = "SELECT id, nama, email FROM user ORDER BY id DESC";
$result = mysqli_query($conn, $query);

$data = [];
while($row = mysqli_fetch_assoc($result)) {
    $data[] = $row;
}
echo json_encode($data);
?>