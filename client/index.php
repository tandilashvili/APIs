<?php
include 'config.php';


// Sets API URL that the request is gonna be sent
$api_URL = 'http://localhost/APIs/server/';


// Prepares personal_id parameter to send with the request
$personal_id = '';
$personal_id_signature = '';
if (!empty($_GET['personal_id'])) {
    $personal_id = trim($_GET['personal_id']);
    $personal_id_signature = getSignature($private_key, OPENSSL_ALGO_SHA256, $personal_id);
}


// Prepares API Request parameters
$params = [
    'personal_id' => encryptText($personal_id, $server_public_key),
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
    $decrypted = decryptText($encrypted, $private_key);
    if (verifySignature(
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
$res = json_encode($response_array);


// Sets content type to MIME type of JSON
header('Content-Type: application/json');


// Returning response
echo ($res);





// Encrypts the text using the secret key
function encryptText($text, $server_public_key)
{
    openssl_public_encrypt($text, $encrypted_message, $server_public_key);

    return base64_encode($encrypted_message);
}

// Decrypts encrypted text (first parameter) using the secret key (second parameter)
function decryptText($encrypted_base64, $private_key)
{
    $encrypted_string = base64_decode($encrypted_base64);
    openssl_private_decrypt($encrypted_string, $original_text, $private_key);

    return $original_text;
}

// Returns digital signature of the string
function getSignature($private_key, $algorithm, $string_to_sign) {

    $binary_signature = "";

    // Create signature on $data
    openssl_sign($string_to_sign, $binary_signature, $private_key, $algorithm);

    // Create base64 version of the signature
    $signature = base64_encode($binary_signature);
    
    return $signature;
}

// verifies the signature using the client's public key
function verifySignature($server_public_key, $algorithm, $signature, $string) {

    // Extract original binary signature 
    $binary_signature = base64_decode($signature);

    // Check signature
    $result = openssl_verify($string, $binary_signature, $server_public_key, $algorithm);

    return $result;
}