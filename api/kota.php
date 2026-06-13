<?php

header('Content-Type: application/json');

$apikey = "7ff8406f12c653758df1a5fa6d6bf474";

$id = isset($_GET['id']) ? $_GET['id'] : '';

$url = "https://rajaongkir.komerce.id/api/v1/destination/city/" . $id;

$ch = curl_init();

curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

curl_setopt($ch, CURLOPT_HTTPHEADER, [
    "key: $apikey"
]);

$response = curl_exec($ch);

curl_close($ch);

echo $response;
