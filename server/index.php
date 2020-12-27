<?php

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
        'gender' => 'Female'
    ],
];

// Retrieves the user personal id, that is going to be searched
$personal_id = $_GET['personal_id'] ?? '';

// Checks whether the user exists
if (!array_key_exists($personal_id, $users)) {
    $status_code = 404;
}

// Checks against bad request
if (!preg_match('/[0-9]{11}$/', $personal_id)) {
    $status_code = 400;
}

// Sends user details, if there is no error
$body = '';
if($status_code == 200) {
    $body = "<body>    
        <person_details>
            <first_name>{$users[$personal_id]['first_name']}</first_name>
            <last_name>{$users[$personal_id]['last_name']}</last_name>
            <gender>{$users[$personal_id]['gender']}</gender>
        </person_details>
    </body>";
}

// Sets content type to MIME type of JSON
header('Content-Type: text/xml');

// Sets HTTP response status
http_response_code($status_code);

// Returns response of the request
$xml = 
"<response>
    <status>
        <code>$status_code</code>
        <text>{$HTTP_statuses[$status_code]}</text>
    </status>"
    . $body . "
</response>";

echo $xml;

