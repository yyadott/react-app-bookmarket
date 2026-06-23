<?php
// api/ongkir.php
ob_clean();
header('Content-Type: application/json');

$json_input = file_get_contents('php://input');
$request_data = json_decode($json_input, true);

$origin       = isset($request_data['origin']) ? intval($request_data['origin']) : 105; // Cimahi
$destination  = isset($request_data['destination']) ? intval($request_data['destination']) : 0;
$weight       = isset($request_data['weight']) ? intval($request_data['weight']) : 1000;
$courier      = isset($request_data['courier']) ? $request_data['courier'] : '';

if ($destination === 0 || empty($courier)) {
    echo json_encode(["status" => "success", "data" => [["cost" => 0]]]);
    exit;
}

// =====================================================================
// METODE HIBRIDA SIMULASI JARAK OTOMATIS (SANGAT COCOK UNTUK DEMO TUBES)
// =====================================================================
// Kita hitung "jarak" berdasarkan selisih absolut ID Kota Asal dan ID Kota Tujuan
$selisih_id = abs($origin - $destination);

// Tentukan tarif dasar per kilogram berdasarkan jenis kurir agar bervariasi
$tarif_dasar = 8000;
if ($courier === 'jne') {
    $tarif_dasar = 9000;
} elseif ($courier === 'sicepat') {
    $tarif_dasar = 7500;
} elseif ($courier === 'jnt') {
    $tarif_dasar = 8500;
}

// Hitung faktor jarak (makin jauh selisih ID-nya, tambah sedikit biayanya)
$biaya_jarak = ($selisih_id % 15) * 1200; 

// Hitung total ongkir berdasarkan berat barang (per 1000 gram)
$faktor_berat = ceil($weight / 1000);
$total_ongkir_hitung = ($tarif_dasar + $biaya_jarak) * $faktor_berat;

// Batasi ongkir minimal Rp 7.000 dan maksimal Rp 90.000 agar logis
if ($total_ongkir_hitung < 7000) $total_ongkir_hitung = 7000;
if ($total_ongkir_hitung > 90000) $total_ongkir_hitung = 45000;

// Kembalikan ke Javascript checkout dalam format yang dikenali
echo json_encode([
    "status" => "success",
    "message" => "Kalkulasi jarak lokal sukses",
    "data" => [
        [
            "cost" => $total_ongkir_hitung
        ]
    ]
]);
exit;