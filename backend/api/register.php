<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: Content-Type");
header("Content-Type: application/json");
header("Access-Control-Allow-Methods: POST");

include '../config/koneksi.php';

// Mengambil data JSON dari request body
$data = json_decode(file_get_contents("php://input"), true);

// 1. Pastikan semua field wajib telah diisi
if (!empty($data['nama']) && !empty($data['email']) && !empty($data['password'])) {

    $nama = mysqli_real_escape_string($conn, $data['nama']);
    $email = mysqli_real_escape_string($conn, $data['email']);
    $password_raw = $data['password'];

    // 2. Validasi Panjang Password (Min 8, Max 20)
    // Dilakukan sebelum password_hash karena hashing akan mengubah panjang string
    $pass_length = strlen($password_raw);
    
    if ($pass_length < 8) {
        echo json_encode([
            "success" => false, 
            "message" => "Registrasi gagal: Password terlalu pendek (Minimal 8 karakter)."
        ]);
        exit;
    }

    if ($pass_length > 20) {
        echo json_encode([
            "success" => false, 
            "message" => "Registrasi gagal: Password terlalu panjang (Maksimal 20 karakter)."
        ]);
        exit;
    }

    // 3. Cek apakah Email sudah terdaftar
    $check = mysqli_query($conn, "SELECT email FROM tbl_user WHERE email = '$email'");
    if (mysqli_num_rows($check) > 0) {
        echo json_encode([
            "success" => false, 
            "message" => "Email sudah digunakan, silakan gunakan email lain."
        ]);
    } else {
        // 4. Hashing Password menggunakan BCRYPT (Hasilnya akan menjadi 60 karakter)
        $password_hashed = password_hash($password_raw, PASSWORD_BCRYPT);
        $role = 'Pelanggan'; 

        // 5. Query Insert data ke database
        // id_user dikosongkan karena asumsinya Auto Increment di DB
        $query = "INSERT INTO tbl_user (nama, email, password, role) 
                  VALUES ('$nama', '$email', '$password_hashed', '$role')";

        if (mysqli_query($conn, $query)) {
            echo json_encode([
                "success" => true, 
                "message" => "Registrasi berhasil! Silakan login."
            ]);
        } else {
            // Memberikan pesan error teknis jika query gagal (berguna untuk debugging)
            echo json_encode([
                "success" => false, 
                "message" => "Terjadi kesalahan sistem: " . mysqli_error($conn)
            ]);
        }
    }

} else {
    // Pesan jika input kosong
    echo json_encode([
        "success" => false, 
        "message" => "Data tidak lengkap. Nama, email, dan password wajib diisi."
    ]);
}
?>