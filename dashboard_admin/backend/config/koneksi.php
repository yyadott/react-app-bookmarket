<?php
// Mengizinkan akses dari React (CORS)
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS, DELETE, PUT");
header("Access-Control-Allow-Headers: Content-Type, Authorization");

$host = "localhost";
$user = "root"; // Default Laragon
$pass = "";     // Default Laragon (kosong)
$db   = "db_bookmarket";

$conn = mysqli_connect($host, $user, $pass, $db);

if (!$conn) {
    die("Koneksi database gagal: " . mysqli_connect_error());
}
?>