<?php
// Izinkan akses dari frontend React (CORS)
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");

$host = "localhost";
$user = "root";
$pass = ""; // Default password Laragon adalah kosong [cite: 57]
$db   = "db_bookmarket"; // Sesuai Logical Design [cite: 156]

$conn = mysqli_connect($host, $user, $pass, $db);

if (!$conn) {
    die(json_encode(["success" => false, "message" => "Koneksi database gagal"]));
}
?>