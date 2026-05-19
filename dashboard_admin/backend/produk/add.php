<?php
require '../config/koneksi.php';
error_reporting(0);

// Menangkap semua data dari React
$nama_buku = mysqli_real_escape_string($conn, $_POST['nama_buku']);
$harga = mysqli_real_escape_string($conn, $_POST['harga']);
$stok = mysqli_real_escape_string($conn, $_POST['stok']);
$kategori = mysqli_real_escape_string($conn, $_POST['kategori']);

// Menangkap penerbit dan sinopsis (dengan pengamanan)
$penerbit = isset($_POST['penerbit']) ? mysqli_real_escape_string($conn, $_POST['penerbit']) : '';
$sinopsis = isset($_POST['sinopsis']) ? mysqli_real_escape_string($conn, $_POST['sinopsis']) : '';

$nama_gambar = ""; // Default kosong jika tidak ada gambar

// Proses upload gambar jika ada
if(isset($_FILES['gambar']) && $_FILES['gambar']['error'] === UPLOAD_ERR_OK) {
    $file_name = $_FILES['gambar']['name'];
    $file_tmp = $_FILES['gambar']['tmp_name'];
    
    // Membersihkan nama file agar aman dan tidak ada spasi
    $nama_bersih = preg_replace("/[^a-zA-Z0-9.]/", "_", $file_name);
    $nama_gambar = time() . "_" . $nama_bersih; // Menambahkan timestamp agar unik
    
    // Pindahkan file ke folder uploads
    move_uploaded_file($file_tmp, "uploads/" . $nama_gambar);
}

// Query untuk memasukkan data ke database (Pastikan urutan VALUES sesuai dengan nama kolomnya)
$query = "INSERT INTO produk (nama_buku, harga, stok, kategori, penerbit, sinopsis, gambar) 
          VALUES ('$nama_buku', '$harga', '$stok', '$kategori', '$penerbit', '$sinopsis', '$nama_gambar')";

if(mysqli_query($conn, $query)) {
    echo json_encode(["status" => "success", "message" => "Produk baru berhasil ditambahkan!"]);
} else {
    echo json_encode(["status" => "error", "message" => "Gagal menambahkan produk: " . mysqli_error($conn)]);
}
?>