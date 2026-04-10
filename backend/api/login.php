<?php
include '../config/koneksi.php';
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit;
}
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, GET, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");

$data = json_decode(file_get_contents("php://input"), true);

if (!empty($data['email']) && !empty($data['password'])) {
    $email = mysqli_real_escape_string($conn, $data['email']);
    $password = $data['password'];

    $result = mysqli_query($conn, "SELECT * FROM tbl_user WHERE email = '$email'");
    
    if (mysqli_num_rows($result) === 1) {
        $user = mysqli_fetch_assoc($result);
        // Verifikasi kecocokan password 
        if (password_verify($password, $user['password'])) {
            echo json_encode([
                "success" => true,
                "message" => "Login Berhasil",
                "role" => $user['role'], // Mengembalikan role untuk routing di React [cite: 111, 329]
                "user_id" => $user['id_user']
            ]);
            exit;
        }
    }
    // Skenario Eksepsi: Kredensial tidak valid [cite: 115, 333]
    echo json_encode(["success" => false, "message" => "Email atau password yang Anda masukkan salah."]);
}
?>