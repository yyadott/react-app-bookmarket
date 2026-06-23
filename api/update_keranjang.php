<?php
// api/update_keranjang.php
session_start();
header('Content-Type: application/json');
require_once '../koneksi.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['status' => 'error', 'message' => 'Metode akses tidak sah']);
    exit;
}

// Mengambil payload JSON dari JavaScript fetch
$input = json_decode(file_get_contents('php://input'), true);
$id = isset($input['id']) ? intval($input['id']) : 0;
$action = isset($input['action']) ? $input['action'] : 'update';
$qty = isset($input['qty']) ? intval($input['qty']) : 1;

if ($id <= 0) {
    echo json_encode(['status' => 'error', 'message' => 'ID Produk tidak valid']);
    exit;
}

// ==========================================
// EKSEKUSI JIKA ACTION ADALAH DELETE
// ==========================================
if ($action === 'delete') {
    if (isset($_SESSION['keranjang'][$id])) {
        unset($_SESSION['keranjang'][$id]);
    }
    $grand_total_baru = hitungGrandTotal($koneksi);
    echo json_encode([
        'status' => 'deleted', 
        'grandtotal' => 'Rp ' . number_format($grand_total_baru, 0, ',', '.'),
        'sisa_item' => count($_SESSION['keranjang'])
    ]);
    exit;
}

// ==========================================
// EKSEKUSI JIKA ACTION ADALAH UPDATE (+ / -)
// ==========================================
// PERBAIKAN: Hanya mengambil harga & stok (stok di-convert ke int karena tipe varchar di DB)
$query_produk = mysqli_query($koneksi, "SELECT harga, stok FROM produk WHERE id = '$id'");
$produk = mysqli_fetch_assoc($query_produk);

if (!$produk) {
    echo json_encode(['status' => 'error', 'message' => 'Buku tidak ditemukan di pangkalan data']);
    exit;
}

// Konversi aman tipe varchar stok dari DB menjadi integer murni di PHP
$stok_tersedia = intval($produk['stok']); 

// Batasi kuantitas agar tidak melewati batas maksimal stok
if ($qty > $stok_tersedia) {
    $qty = $stok_tersedia;
}

// Jika kuantitas diturunkan sampai 0 atau minus, otomatis hapus item
if ($qty <= 0) {
    if (isset($_SESSION['keranjang'][$id])) {
        unset($_SESSION['keranjang'][$id]);
    }
    $grand_total_baru = hitungGrandTotal($koneksi);
    echo json_encode([
        'status' => 'deleted', 
        'grandtotal' => 'Rp ' . number_format($grand_total_baru, 0, ',', '.'),
        'sisa_item' => count($_SESSION['keranjang'])
    ]);
    exit;
}

// Simpan kuantitas baru ke dalam session
$_SESSION['keranjang'][$id] = $qty;

// Hitung subtotal baris item ini
$subtotal_item = intval($produk['harga']) * $qty;
$grand_total_baru = hitungGrandTotal($koneksi);

echo json_encode([
    'status' => 'success',
    'qty' => $qty,
    'subtotal' => 'Rp ' . number_format($subtotal_item, 0, ',', '.'),
    'grandtotal' => 'Rp ' . number_format($grand_total_baru, 0, ',', '.')
]);

// Fungsi hitung akumulasi total belanja keseluruhan
function hitungGrandTotal($koneksi) {
    $total = 0;
    if (isset($_SESSION['keranjang']) && is_array($_SESSION['keranjang'])) {
        foreach ($_SESSION['keranjang'] as $id_session => $qty_session) {
            $id_session = intval($id_session);
            $qty_session = intval($qty_session);
            
            $p = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT harga FROM produk WHERE id = '$id_session'"));
            if ($p) {
                $total += (intval($p['harga']) * $qty_session);
            }
        }
    }
    return $total;
}