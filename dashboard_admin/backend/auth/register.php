<?php
require '../config/koneksi.php';
error_reporting(0);

$data = json_decode(file_get_contents("php://input"), true);

if(isset($data['nama']) && isset($data['email']) && isset($data['password'])) {
    $nama = mysqli_real_escape_string($conn, $data['nama']);
    $email = mysqli_real_escape_string($conn, $data['email']);
    
    // Hash password demi keamanan
    $password = password_hash($data['password'], PASSWORD_DEFAULT);

    // Cek apakah email sudah pernah didaftarkan
    $cek_email = mysqli_query($conn, "SELECT * FROM user WHERE email='$email'");
    if(mysqli_num_rows($cek_email) > 0) {
        echo json_encode(["status" => "error", "message" => "Email sudah terdaftar! Silakan gunakan email lain."]);
    } else {
        // Masukkan data termasuk nama ke database
        $query = "INSERT INTO user (nama, email, password) VALUES ('$nama', '$email', '$password')";
        if(mysqli_query($conn, $query)) {
            echo json_encode(["status" => "success", "message" => "Registrasi berhasil! Silakan login."]);
        } else {
            echo json_encode(["status" => "error", "message" => "Gagal registrasi: " . mysqli_error($conn)]);
        }
    }
} else {
    echo json_encode(["status" => "error", "message" => "Data tidak lengkap."]);
}
?>