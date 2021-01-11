<?php
include 'config.php';


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
$response_array['data']['service_latency'] = $latency;

// Verifies signature using server's certificate
if (!verifySignature(
        $server_certificate, 
        OPENSSL_ALGO_SHA256, 
        $response_array['data']['letter_signature'], 
        $response_array['data']['letter']
    )) {
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





function verifySignature($server_public_key, $algorithm, $signature, $string) {

    // Extracting public key from the specified certificate
    $public_key = openssl_pkey_get_public($server_public_key);
    /*
    // Another way to get the certificate's content
    $public_key = openssl_pkey_get_public(file_get_contents('./cert/bank_crystal.cer'));
    */

    // $public key resource is also acceptable, so converting 
    // resource to public key string is not necessary
    $key_data = openssl_pkey_get_details($public_key);
    $server_public_key = $key_data['key'];
    
    // Extract original binary signature 
    $binary_signature = base64_decode($signature);

    // Check signature
    $result = openssl_verify($string, $binary_signature, $server_public_key, $algorithm);

    return $result;
}