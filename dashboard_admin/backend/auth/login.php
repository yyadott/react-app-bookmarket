<?php
require '../config/koneksi.php';
error_reporting(0);

$data = json_decode(file_get_contents("php://input"), true);

if(isset($data['email']) && isset($data['password'])) {
    $email = mysqli_real_escape_string($conn, $data['email']);
    $password = $data['password'];

    // Ambil data user termasuk nama berdasarkan email
    $query = mysqli_query($conn, "SELECT * FROM user WHERE email='$email'");
    $user = mysqli_fetch_assoc($query);

    if($user) {
        // Verifikasi password (karena di register kita pakai password_hash)
        if(password_verify($password, $user['password'])) {
            echo json_encode([
                "status" => "success", 
                "message" => "Login Berhasil!",
                "data" => [
                    "nama" => $user['nama'], // Kirim nama user ke frontend
                    "email" => $user['email']
                ]
            ]);
        } else {
            echo json_encode(["status" => "error", "message" => "Password salah!"]);
        }
    } else {
        echo json_encode(["status" => "error", "message" => "Email tidak terdaftar!"]);
    }
}
?>