<?php
include 'config.php';


// Sets API URL that the request is gonna be sent
$api_URL = 'http://localhost/APIs/server/';


// Prepares personal_id parameter to send with the request
$personal_id = '';
if (!empty($_GET['personal_id'])) {
    $personal_id = trim($_GET['personal_id']);
}


// Prepares API Request parameters
$params = ['personal_id' => encrypt_text($personal_id, $server_public_key)];
// Converts parameters into JSON format
$JSON_request = json_encode($params);
// Sets request content type
$content_type = 'application/json';
// Sets headers for the request
$curl_headers = array(
    "Content-Type: $content_type; charset=utf-8",
    'Content-Length: ' . strlen($JSON_request)
);


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
    $response_array['data']['person_details'] = json_decode(decrypt_text($response_array['data']['person_details'], $private_key), 1);
}


// Encodes the response
$res = json_encode($response_array);


// Sets content type to MIME type of JSON
header('Content-Type: application/json');


// Returning response
echo ($res);





// Encrypts the text using the secret key
function encrypt_text($text, $server_public_key)
{
    openssl_public_encrypt($text, $encrypted_message, $server_public_key);

    return base64_encode($encrypted_message);
}

// Decrypts encrypted text (first parameter) using the secret key (second parameter)
function decrypt_text($encrypted_base64, $private_key)
{
    $encrypted_string = base64_decode($encrypted_base64);
    openssl_private_decrypt($encrypted_string, $original_text, $private_key);

    return $original_text;
}
