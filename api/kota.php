<?php
// api/kota.php
ob_clean(); 
header('Content-Type: application/json');

$apikey = "7ff8406f12c653758df1a5fa6d6bf474";
$id = isset($_GET['id']) ? $_GET['id'] : '';

if (empty($id)) {
    echo json_encode(["status" => "success", "data" => []]);
    exit;
}

$url = "https://rajaongkir.komerce.id/api/v1/destination/city/" . $id;

// Ganti cURL dengan Stream Context HTTP bawaan PHP
$options = [
    "http" => [
        "method" => "GET",
        "header" => "key: " . $apikey . "\r\n"
    ]
];
$context = stream_context_create($options);
$response = @file_get_contents($url, false, $context);

if ($response === FALSE) {
    echo json_encode(["status" => "error", "message" => "Gagal mengambil data dari server API Komerce", "data" => []]);
    exit;
}

$data_asli = json_decode($response, true);
$data_terformat = [];

$list_kota = isset($data_asli['data']) ? $data_asli['data'] : (is_array($data_asli) ? $data_asli : null);

if (is_array($list_kota)) {
    foreach ($list_kota as $item) {
        $id_kota   = isset($item['id']) ? $item['id'] : (isset($item['city_id']) ? $item['city_id'] : '');
        $nama_kota = isset($item['name']) ? $item['name'] : (isset($item['city_name']) ? $item['city_name'] : (isset($item['label']) ? $item['label'] : ''));
        
        // PERBAIKAN: Deteksi ketat seluruh alternatif key kode pos dari API Komerce
        $kodepos = '';
        if (isset($item['postal_code']) && $item['postal_code'] !== '') {
            $kodepos = $item['postal_code'];
        } elseif (isset($item['zip_code']) && $item['zip_code'] !== '') {
            $kodepos = $item['zip_code'];
        } elseif (isset($item['post_code']) && $item['post_code'] !== '') {
            $kodepos = $item['post_code'];
        }

        if ($id_kota !== '' && $nama_kota !== '') {
            $data_terformat[] = [
                'id'       => $id_kota,
                'name'     => $nama_kota,
                'zip_code' => $kodepos // Dikunci ke key zip_code untuk dibaca oleh JavaScript
            ];
        }
    }
    echo json_encode(["status" => "success", "data" => $data_terformat]);
} else {
    echo json_encode(["status" => "error", "message" => "Format data tidak dikenali", "data" => []]);
}
exit;