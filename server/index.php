<?php
include 'config.php';


// Potential HTTP status codes
$HTTP_statuses = [
    '200' => 'OK',
    '400' => 'Bad Request',
    '401' => 'Unauthorized',
];

// Default HTTP status code
$status_code = 200;

// Existing users
$letter = 'Lorem, ipsum dolor sit amet consectetur adipisicing elit. Sequi unde fugit voluptates odio! Ullam eligendi nam tenetur architecto molestias voluptatibus dolorum! Ratione soluta quia minus at, laborum eius eligendi optio!';



// Generating the letter signature, using PRIVATE KEY
$letter_signature = getSignature($private_key, OPENSSL_ALGO_SHA256, $letter);

// // Makes the letter faked
// $letter .= '.';


// Define API password
const PASSWORD = 'f0f962a5517d_';

// Retrieves service parameters
$serviceRequest = json_decode(file_get_contents('php://input'), 1);

$request_time = $serviceRequest['request_time'] ?? '';
$password_hash_provided = $serviceRequest['password_hash'] ?? '';

// Generating pass hash, that must be compared to the provided hash
$password_hash_generated = hash("sha256", PASSWORD . $request_time);

// Checks against bad request
if (empty($request_time) || empty($password_hash_provided)) {
    $status_code = 400;
}
else
// Checks Authentication
if ($password_hash_provided != $password_hash_generated) {
    $status_code = 401;
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
    $result['data'] = [
        'letter' => $letter,
        'letter_signature' => $letter_signature,
    ];
}

// Sets content type to MIME type of JSON
header('Content-Type: application/json');

// Sets HTTP response status
http_response_code($status_code);

// Returns response of the request
echo json_encode($result);



function getSignature($private_key, $algorithm, $string_to_sign) {

    $binary_signature = "";

    // Create signature on $data
    openssl_sign($string_to_sign, $binary_signature, $private_key, $algorithm);

    // Create base64 version of the signature
    $signature = base64_encode($binary_signature);
    
    return $signature;
}