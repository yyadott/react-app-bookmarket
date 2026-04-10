<?php
include '../config/koneksi.php';

// Mengambil data JSON yang dikirim oleh React
$data = json_decode(file_get_contents("php://input"), true);

if (!empty($data['email']) && !empty($data['password'])) {
    $nama = mysqli_real_escape_string($conn, $data['nama']);
    $email = mysqli_real_escape_string($conn, $data['email']);
    // Password di-hash demi keamanan [cite: 374]
    $password = password_hash($data['password'], PASSWORD_BCRYPT);
    $role = 'Pelanggan'; // Default role sesuai SRS [cite: 62, 306]

    // Cek apakah email sudah terdaftar (Skenario Eksepsi 3a) [cite: 122, 340]
    $check = mysqli_query($conn, "SELECT email FROM tbl_user WHERE email = '$email'");
    if (mysqli_num_rows($check) > 0) {
        echo json_encode(["success" => false, "message" => "Email sudah digunakan. Silakan gunakan email lain."]);
    } else {
        $query = "INSERT INTO tbl_user (nama, email, password, role) VALUES ('$nama', '$email', '$password', '$role')";
        if (mysqli_query($conn, $query)) {
            echo json_encode(["success" => true, "message" => "Registrasi berhasil! Silakan login."]);
        }
    }
} else {
    echo json_encode(["success" => false, "message" => "Nama, email, dan password wajib diisi."]);
}
?>