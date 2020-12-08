<?php

// Potential HTTP status codes
$HTTP_statuses = [
    '200' => 'OK',
    '400' => 'Bad Request',
    '401' => 'Unauthorized',
];

$allowed_users = ['User1', 'User2', 'User3'];

// Retrieves service parameters
$serviceRequest = json_decode(file_get_contents('php://input'), 1);

// Define API password
const PASSWORD = 'f0f962a5517d_';

// Default HTTP status code
$status_code = 200;

// Get all headers from the request
$headers = getallheaders();
$hmac = '';
if (isset($headers['Hmac'])) {
    $hmac = $headers['Hmac'];

    if (isset($serviceRequest['param1']) && isset($serviceRequest['param2']) && $serviceRequest['param3']) {
        $params_c = $serviceRequest['param1'].$serviceRequest['param2'].$serviceRequest['param3'];
        $hmac_generated = hash_hmac('sha256', $params_c, PASSWORD);
    }
}

// letter to return as a response
$letter = 'Lorem, ipsum dolor sit amet consectetur adipisicing elit. Sequi unde fugit voluptates odio! Ullam eligendi nam tenetur architecto molestias voluptatibus dolorum! Ratione soluta quia minus at, laborum eius eligendi optio!';
// Generating the letter hash, for signing purpose
$letter_hash = hash("sha256", $letter);

// // Makes the letter faked
// $letter .= '.';

// Checks Authentication
if ((isset($headers['Hmac']) && $headers['Hmac'] != $hmac_generated)) {
    $status_code = 401;
}

// Checks against bad request
if (!isset($headers['Hmac']) || 
    !isset($serviceRequest['param1']) || 
    !isset($serviceRequest['param2']) || 
    !isset($serviceRequest['param3'])) {
    $status_code = 400;
}

// Sets API status code and text
$result = [
    'status' => [
        'code' => $status_code,
        'text' => $HTTP_statuses[$status_code]
    ]
];

// Sends the letter, if there is no error
if($status_code == 200) {
    $result['data'] = [
        'letter' => $letter,
        'letter_hash' => $letter_hash,
    ];
}

// Sets content type to MIME type of JSON
header('Content-Type: application/json');

// Sets HTTP response status
http_response_code($status_code);

// Returns response of the request
echo json_encode($result);
