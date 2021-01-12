<?php
include 'config.php';


// Potential HTTP status codes
$HTTP_statuses = [
    '200' => 'OK',
    '400' => 'Bad Request',
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
$personal_id = $serviceRequest['personal_id'] ?? '';


// Decrypts encrypted personal_id parameter
if (!empty($personal_id)) {
    $personal_id = decrypt_text($personal_id, $private_key);
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
    $result['data'] = [
        'person_details' => encrypt_text(json_encode($users[$personal_id]), $client_public_key)
    ];
}


// Sets response content type to JSON MIME type
header('Content-Type: application/json');


// Sets HTTP response status
http_response_code($status_code);


// Returns response of the request
echo json_encode($result);





// Encrypts the text using the secret key
function encrypt_text($text, $client_public_key)
{
    openssl_public_encrypt($text, $encrypted_message, $client_public_key);

    return base64_encode($encrypted_message);
}

// Decrypts encrypted text (first parameter) using the secret key (second parameter)
function decrypt_text($encrypted_base64, $private_key)
{
    $encrypted_string = base64_decode($encrypted_base64);
    openssl_private_decrypt($encrypted_string, $original_text, $private_key);

    return $original_text;
}
