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
        // 'biography' => 'Lorem, ipsum dolor sit amet consectetur adipisicing elit. Sequi unde fugit voluptates odio! Ullam eligendi nam tenetur architecto molestias voluptatibus dolorum! Ratione soluta quia minus at, laborum eius eligendi optio!'
    ],
    '01015021211' => [
        'first_name' => 'George',
        'last_name' => 'Bolkvadze',
        'biography' => 'Lorem, ipsum dolor sit amet consectetur adipisicing elit'
    ],
    '01015021212' => [
        'first_name' => 'Tamar',
        'last_name' => 'Gelashvili',
        'biography' => 'Lorem, ipsum dolor sit'
    ],
];

// Define API password
const PASSWORD = 'f0klkasldkfjasdflkflkajdfs517d_';

// Retrieves service parameters
$serviceRequest = json_decode(file_get_contents('php://input'), 1);

$personal_id = $serviceRequest['personal_id'] ?? '';

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

    // Encodes returning data to json format
    $found_user_details = json_encode($users[$personal_id]);
    
    // Makes encryption in both symmetric and asymmetric ways
    // Calculates symmetric encryption latency
    $time = microtime(1);
    $encrypted_symmetrically = encrypt_text_symmetric($found_user_details, PASSWORD);
    $symmetric_latency = (microtime(1) - $time)*100;

    // Calculates asymmetric encryption latency
    $time = microtime(1);
    $encrypted_asymmetrically = encrypt_text_asymmetric($found_user_details, $alice_public_key);
    $asymmetric_latency = (microtime(1) - $time)*100;

    $result['data'] = [
        'symmetric_latency' => $symmetric_latency,
        'asymmetric_latency' => $asymmetric_latency,
        'number_of_times_faster' => intval($asymmetric_latency / $symmetric_latency),
        'person_details_symmetric' => $encrypted_symmetrically,
        'person_details_asymmetric' => $encrypted_asymmetrically
    ];
}

// Sets content type to MIME type of JSON
header('Content-Type: application/json');

// Sets HTTP response status
http_response_code($status_code);

// Returns response of the request
echo json_encode($result);





// Encrypts the text using the secret key symmetric way
function encrypt_text_symmetric($text, $secret_key)
{
    return openssl_encrypt($text, "AES-128-ECB", $secret_key);
}

// Encrypts the text using Alice's public key
function encrypt_text_asymmetric($text, $alice_public_key)
{
    openssl_public_encrypt($text, $encrypted_message, $alice_public_key);

    return base64_encode($encrypted_message);
}
