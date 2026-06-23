<?php
// api/provinsi.php
ob_clean(); 
header('Content-Type: application/json');

$apikey = "7ff8406f12c653758df1a5fa6d6bf474";
$url = "https://rajaongkir.komerce.id/api/v1/destination/province";

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
    echo json_encode([
        "status" => "error",
        "message" => "Gagal mengambil data dari server API Komerce.",
        "data" => []
    ]);
    exit;
}

$data_asli = json_decode($response, true);
$data_terformat = [];

$list_provinsi = isset($data_asli['data']) ? $data_asli['data'] : (is_array($data_asli) ? $data_asli : null);

if (is_array($list_provinsi)) {
    foreach ($list_provinsi as $item) {
        $id = isset($item['id']) ? $item['id'] : (isset($item['province_id']) ? $item['province_id'] : '');
        $name = isset($item['name']) ? $item['name'] : (isset($item['province_name']) ? $item['province_name'] : (isset($item['label']) ? $item['label'] : ''));
        
        if ($id !== '' && $name !== '') {
            $data_terformat[] = [
                'id'   => $id,
                'name' => $name
            ];
        }
    }
    echo json_encode(["status" => "success", "data" => $data_terformat]);
} else {
    echo json_encode(["status" => "error", "message" => "Format data tidak dikenali", "data" => []]);
}