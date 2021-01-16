<?php
include 'config.php';
include '../classes/crypto.class.php';


// Potential HTTP status codes
$HTTP_statuses = [
    '200' => 'OK',
    '400' => 'Bad Request',
    '401' => 'Unauthorized',
    '404' => 'Not Found',
];


// Default HTTP status code
$status_code = 200;


// Existing users
$users = [
    '01015021210' => [
        'first_name' => 'Valeri',
        'last_name' => 'Tandilashvili',
        'gender' => 'Male'
    ],
    '01015021211' => [
        'first_name' => 'George',
        'last_name' => 'Bolkvadze',
        'gender' => 'Male'
    ],
    '01015021212' => [
        'first_name' => 'Tamar',
        'last_name' => 'Gelashvili',
        'gender' => 'Male'
    ],
];


// Retrieves service parameters
$serviceRequest = json_decode(file_get_contents('php://input'), 1);
$symmetric_secret_key_encrypted = $serviceRequest['symetric_key'] ?? '';
$personal_id_encrypted = $serviceRequest['personal_id'] ?? '';
$personal_id_signature = $serviceRequest['personal_id_signature'] ?? '';


// Extracting symmetric secret key
$symmetric_secret_key = Crypto::decryptText($symmetric_secret_key_encrypted, $private_key);

// Decrypts encrypted personal_id parameter
if (!empty($personal_id_encrypted)) {
    $personal_id = Crypto::decryptTextSymmetric($personal_id_encrypted, $symmetric_secret_key);
}


// Checks whether the user exists
if (!Crypto::verifySignature(
    $client_public_key, 
    OPENSSL_ALGO_SHA256, 
    $personal_id_signature,
    $personal_id
)) {
    $status_code = 401;
}


// Checks whether the user exists
if (!array_key_exists($personal_id, $users)) {
    $status_code = 404;
}


// Checks against bad request
if (!preg_match('/[0-9]{11}$/', $personal_id)) {
    $status_code = 400;
}


// Sets API status code and text
$result = [
    'status' => [
        'code' => $status_code,
        'text' => $HTTP_statuses[$status_code]
    ]
];


// Sends user details, if there is no error
if($status_code == 200) {
    $response = json_encode($users[$personal_id]);
    $result['data'] = [
        'person_details' => Crypto::encryptTextSymmetric($response, $symmetric_secret_key),
        'person_details_signature' => Crypto::getSignature($private_key, OPENSSL_ALGO_SHA256, $response)
    ];
}


// Sets response content type to JSON MIME type
header('Content-Type: application/json');


// Sets HTTP response status
http_response_code($status_code);


// Returns response of the request
echo json_encode($result);
