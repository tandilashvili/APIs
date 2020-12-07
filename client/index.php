<?php

$api_URL = 'http://localhost/APIs/server/';

// Define API password
const PASSWORD = 'f0f962a5517d_';
$request_time = date('Y-m-d|H:i:s');
$params = [
    'request_time' => $request_time,
    'password_hash' => hash("sha256", PASSWORD . $request_time)
];

$JSON_request = json_encode($params);
$content_type = 'application/json';

$ch = curl_init($api_URL);
curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
curl_setopt($ch, CURLOPT_POSTFIELDS, $JSON_request);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

$curl_headers = array(
    "Content-Type: $content_type; charset=utf-8",
    'Content-Length: ' . strlen($JSON_request)
);

curl_setopt($ch, CURLOPT_HTTPHEADER, $curl_headers);

// Calculates latency of the request
$startTime = microtime(1);
$res = curl_exec($ch);
$latency = number_format(microtime(1) - $startTime, 5);

// Adds parameter to the service about latency
$response_array = json_decode($res, 1);
$response_array['body']['service_latency'] = $latency;
$res = json_encode($response_array);

// Sets content type to MIME type of JSON
header('Content-Type: application/json');

// Returning response
echo ($res);

