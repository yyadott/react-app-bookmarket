<?php
require '../config/koneksi.php';
error_reporting(0);

if(isset($_POST['id'])) {
    $id = mysqli_real_escape_string($conn, $_POST['id']);
    $nama_buku = mysqli_real_escape_string($conn, $_POST['nama_buku']);
    $harga = mysqli_real_escape_string($conn, $_POST['harga']);
    $stok = mysqli_real_escape_string($conn, $_POST['stok']);
    $kategori = mysqli_real_escape_string($conn, $_POST['kategori']);
    
    // Tambahkan pengamanan (escape string) juga untuk penerbit dan sinopsis
    $penerbit = isset($_POST['penerbit']) ? mysqli_real_escape_string($conn, $_POST['penerbit']) : '';
    $sinopsis = isset($_POST['sinopsis']) ? mysqli_real_escape_string($conn, $_POST['sinopsis']) : '';

    // Cek apakah ada file gambar BARU yang diupload
    if(isset($_FILES['gambar']) && $_FILES['gambar']['error'] === UPLOAD_ERR_OK) {
        
        // 1. Hapus gambar lama terlebih dahulu
        $queryGet = "SELECT gambar FROM produk WHERE id='$id'";
        $res = mysqli_query($conn, $queryGet);
        $row = mysqli_fetch_assoc($res);
        if($row && $row['gambar'] != "") {
            $old_file = "uploads/" . $row['gambar'];
            if(file_exists($old_file)) { unlink($old_file); }
        }

        // 2. Upload gambar baru
        $file_name = $_FILES['gambar']['name'];
        $file_tmp = $_FILES['gambar']['tmp_name'];
        $nama_bersih = preg_replace("/[^a-zA-Z0-9.]/", "_", $file_name);
        $nama_gambar = time() . "_" . $nama_bersih;
        
        move_uploaded_file($file_tmp, "uploads/" . $nama_gambar);

        // Update database dengan gambar baru (Perhatikan nama variabelnya saya samakan jadi $queryUpdate)
        // Dan tambahkan gambar='$nama_gambar'
        $queryUpdate = "UPDATE produk SET 
          nama_buku='$nama_buku', harga='$harga', stok='$stok', kategori='$kategori', 
          penerbit='$penerbit', sinopsis='$sinopsis', gambar='$nama_gambar' 
          WHERE id='$id'";
    } else {
        // Update database TANPA merubah gambar
        // Perbaikan: Tambahkan penerbit dan sinopsis di sini!
        $queryUpdate = "UPDATE produk SET 
          nama_buku='$nama_buku', harga='$harga', stok='$stok', kategori='$kategori',
          penerbit='$penerbit', sinopsis='$sinopsis' 
          WHERE id='$id'";
    }

    // Sekarang variabelnya sudah pasti bernama $queryUpdate
    if(mysqli_query($conn, $queryUpdate)) {
        echo json_encode(["status" => "success", "message" => "Produk berhasil diperbarui!"]);
    } else {
        echo json_encode(["status" => "error", "message" => "Gagal memperbarui: " . mysqli_error($conn)]);
    }
}
?>