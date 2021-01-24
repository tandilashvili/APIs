<?php
include 'config.php';
$api_URL = 'http://localhost/APIs/server/';

// Define request parameters
$params = [
    'personal_id' => $_GET['personal_id']
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
$response_array['data']['service_latency'] = $latency;

// Decrypt users content
$response_array['data']['person_details'] = json_decode(decrypt_text($response_array['data']['person_details'], $private_key), 1);
$res = json_encode($response_array, JSON_PRETTY_PRINT);

// Sets content type to MIME type of JSON
header('Content-Type: application/json');

// Returning response
echo ($res);



// Decrypts encrypted text (first parameter) using the secret key (second parameter)
function decrypt_text($encrypted_base64, $private_key)
{
    $encrypted_string = base64_decode($encrypted_base64);
    openssl_private_decrypt($encrypted_string, $original_text, $private_key);

    return $original_text;
}
