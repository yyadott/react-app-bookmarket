<?php
require '../config/koneksi.php';
error_reporting(0);

$data = json_decode(file_get_contents("php://input"), true);

if(isset($data['id'])) {
    $id = mysqli_real_escape_string($conn, $data['id']);

    // Cari nama file gambar sebelum datanya dihapus
    $queryGet = "SELECT gambar FROM produk WHERE id='$id'";
    $result = mysqli_query($conn, $queryGet);
    $row = mysqli_fetch_assoc($result);
    
    // Hapus file gambar dari folder uploads jika ada
    if($row && $row['gambar'] != "") {
        $file_path = "uploads/" . $row['gambar'];
        if(file_exists($file_path)) {
            unlink($file_path); // Perintah PHP untuk menghapus file fisik
        }
    }

    // Hapus data dari database
    $queryDelete = "DELETE FROM produk WHERE id='$id'";
    if(mysqli_query($conn, $queryDelete)) {
        echo json_encode(["status" => "success", "message" => "Produk berhasil dihapus!"]);
    } else {
        echo json_encode(["status" => "error", "message" => "Gagal menghapus produk."]);
    }
} else {
    echo json_encode(["status" => "error", "message" => "ID tidak ditemukan."]);
}
?>