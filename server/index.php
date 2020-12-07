<?php

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
        'users' => $users
    ];
}

// Sets content type to MIME type of JSON
header('Content-Type: application/json');

// Sets HTTP response status
http_response_code($status_code);

// Returns response of the request
echo json_encode($result);