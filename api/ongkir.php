<?php

header('Content-Type: application/json');

$apikey = "7ff8406f12c653758df1a5fa6d6bf474";

$origin = $_POST['origin'];
$destination = $_POST['destination'];
$weight = $_POST['weight'];
$courier = $_POST['courier'];

$ch = curl_init();

curl_setopt($ch, CURLOPT_URL, "https://rajaongkir.komerce.id/api/v1/calculate/district/domestic-cost");
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);

curl_setopt($ch, CURLOPT_HTTPHEADER, [
    "key: $apikey",
    "Content-Type: application/x-www-form-urlencoded"
]);

curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query([
    "origin" => $origin,
    "destination" => $destination,
    "weight" => $weight,
    "courier" => $courier,
    "price" => "lowest"
]));

$response = curl_exec($ch);

curl_close($ch);

echo $response;
