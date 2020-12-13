<?php

$api_URL = 'http://localhost/APIs/server/';

// Define API password
const PASSWORD = 'f0f962a5517d_';

$content_type = 'application/json';

$ch = curl_init($api_URL);
curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

$curl_headers = array(
    "User: User1",
    "Password: " . PASSWORD,
    "Content-Type: $content_type; charset=utf-8"
);

curl_setopt($ch, CURLOPT_HTTPHEADER, $curl_headers);

// Calculates latency of the request
$startTime = microtime(1);
$res = curl_exec($ch);
$latency = number_format(microtime(1) - $startTime, 5);

// Adds parameter to the service about latency
$response_array = json_decode($res, 1);
$response_array['data']['service_latency'] = $latency;

if ($response_array['data']['letter_hash'] != hash("sha256", $response_array['data']['letter'])) {
    $response_array['status'] = ['code' => '403', 'text' => 'The content is faked!'];
    unset($response_array['data']);

    // Sets HTTP response status
    http_response_code($response_array['status']['code']);
}

$res = json_encode($response_array);

// Sets content type to MIME type of JSON
header('Content-Type: application/json');

// Returning response
echo ($res);