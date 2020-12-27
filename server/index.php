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

// Sets API status code and text
$result = [
    'status' => [
        'code' => $status_code,
        'text' => $HTTP_statuses[$status_code]
    ]
];

// Sends user details, if there is no error
if($status_code == 200) {
    $result['body'] = [
        'person_details' => $users[$personal_id]
    ];
}

// Converts response array to XML
$xml = arrayToXml($result);

// Sets content type to MIME type of JSON
header('Content-Type: text/xml');

// Sets HTTP response status
http_response_code($status_code);

// Returns response of the request
echo $xml;


// The function converts an array to an XML
function arrayToXml($array, $rootElement = null, $xml = null) { 
    $_xml = $xml; 
      
    // If there is no Root Element then insert root 
    if ($_xml === null) { 
        $_xml = new SimpleXMLElement($rootElement !== null ? $rootElement : '<response/>'); 
    } 
      
    // Visit all key value pair 
    foreach ($array as $k => $v) { 
          
        // If there is nested array then 
        if (is_array($v)) {  
              
            // Call function for nested array 
            arrayToXml($v, $k, $_xml->addChild($k)); 
            } 
              
        else { 
              
            // Simply add child element.  
            $_xml->addChild($k, $v); 
        } 
    } 
      
    return $_xml->asXML(); 
} 