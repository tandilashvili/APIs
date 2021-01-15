<?php
include 'config.php';
include '../classes/crypto.class.php';

// Sets API URL that the request is gonna be sent
$api_URL = 'http://localhost/APIs/server/';


// Prepares personal_id parameter to send with the request
$personal_id = '';
$personal_id_signature = '';
if (!empty($_GET['personal_id'])) {
    $personal_id = trim($_GET['personal_id']);
    $personal_id_signature = Crypto::getSignature($private_key, OPENSSL_ALGO_SHA256, $personal_id);
}


// Prepares API Request parameters
$params = [
    'personal_id' => Crypto::encryptText($personal_id, $server_public_key),
    'personal_id_signature' => $personal_id_signature
];
// Converts parameters into JSON format
$JSON_request = json_encode($params);
// Sets request content type
$content_type = 'application/json';
// Sets headers for the request
$curl_headers = array(
    "Content-Type: $content_type; charset=utf-8",
    'Content-Length: ' . strlen($JSON_request)
);


// print_r($params);


// Creates cURL object and sets necessary settings
$ch = curl_init($api_URL);
curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
curl_setopt($ch, CURLOPT_POSTFIELDS, $JSON_request);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, $curl_headers);


// Calculates latency of the request
$startTime = microtime(1);
$res = curl_exec($ch);
$latency = number_format(microtime(1) - $startTime, 5);


// Adds parameter to the service about latency
$response_array = json_decode($res, 1);
$response_array['data']['service_latency'] = $latency;


// Decrypts person details is the status code is equal to 200
if ($response_array['status']['code'] == 200) {
    $encrypted = $response_array['data']['person_details'];
    $signature = $response_array['data']['person_details_signature'] ?? '';
    $decrypted = Crypto::decryptText($encrypted, $private_key);
    if (Crypto::verifySignature(
        $server_public_key, 
        OPENSSL_ALGO_SHA256, 
        $signature, 
        $decrypted
    )) {
        $response_array['data']['person_details'] = json_decode($decrypted, 1);
        unset($response_array['data']['person_details_signature']);
    }
}


// Encodes the response
$res = json_encode($response_array, JSON_PRETTY_PRINT);


// Sets content type to MIME type of JSON
header('Content-Type: application/json');


// Returning response
echo ($res);
