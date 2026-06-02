<?php
include '../config/koneksi.php';

// Pastikan header CORS ada (bisa ditaruh di koneksi.php agar global)
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");

include '../config/koneksi.php';

// Menangani permintaan OPTIONS dari browser
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit;
}

$method = $_SERVER['REQUEST_METHOD'];

switch($method) {
    case 'GET': 
        // Mengambil katalog buku
        $result = mysqli_query($conn, "SELECT * FROM tbl_buku");
        $buku = mysqli_fetch_all($result, MYSQLI_ASSOC);
        echo json_encode($buku);
        break;

    case 'POST': 
        // Tambah Buku Baru
        $data = json_decode(file_get_contents("php://input"), true);
        if(!empty($data)) {
            $judul = mysqli_real_escape_string($conn, $data['judul']);
            $penulis = mysqli_real_escape_string($conn, $data['penulis']);
            $harga = $data['harga'];
            $stok = $data['stok'];
            $id_kategori = $data['id_kategori'];

            $query = "INSERT INTO tbl_buku (judul, penulis, harga, stok, id_kategori) 
                      VALUES ('$judul', '$penulis', '$harga', '$stok', '$id_kategori')";
            
            if (mysqli_query($conn, $query)) {
                echo json_encode(["success" => true, "message" => "Buku berhasil ditambahkan"]);
            } else {
                echo json_encode(["success" => false, "message" => "Gagal menambahkan buku: " . mysqli_error($conn)]);
            }
        }
        break;

    case 'DELETE':
        // Menghapus Buku berdasarkan ID
        $id = $_GET['id'];
        $query = "DELETE FROM tbl_buku WHERE id_buku = $id";
        if(mysqli_query($conn, $query)) {
            echo json_encode(["success" => true, "message" => "Buku berhasil dihapus"]);
        } else {
            echo json_encode(["success" => false, "message" => "Gagal menghapus buku"]);
        }
        break;

    case 'OPTIONS':
        // Menangani pre-flight request dari browser
        exit;
}
?>