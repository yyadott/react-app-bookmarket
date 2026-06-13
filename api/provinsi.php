<?php

header('Content-Type: application/json');

$apikey = "7ff8406f12c653758df1a5fa6d6bf474";

$url = "https://rajaongkir.komerce.id/api/v1/destination/province";

$ch = curl_init();

curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

curl_setopt($ch, CURLOPT_HTTPHEADER, [
    "Key: $apikey"
]);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

curl_close($ch);

// error handling
if ($httpCode != 200) {
    echo json_encode([
        "meta" => [
            "status" => "error",
            "message" => "API error"
        ],
        "data" => []
    ]);
    exit;
}

echo $response;
