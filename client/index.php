<?php

$API_URL = 'http://localhost/APIs/server/';
$personal_id = $_GET['personal_id'];

// Builds the request URL
$request_URL = $API_URL . '?personal_id=' . $personal_id;

// Retrieves the API response and calculates its latency
$time = microtime(1);
$response = file_get_contents($request_URL);
$latency = microtime(1) - $time;

// Converts the response xml to an array
$xml = simplexml_load_string($response, "SimpleXMLElement");
$json = json_encode($xml);
$array = json_decode($json, 1);

// Adds latency info to the API response
$array['latency'] = $latency;

// Converts the array back to JSON
$json = json_encode($array);

// Sets header to JSON
header('Content-Type: application/json');

echo $json;
