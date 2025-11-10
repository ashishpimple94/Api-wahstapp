<?php
// Test CORS configuration
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With, Accept, Origin');
header('Access-Control-Allow-Credentials: false');
header('Access-Control-Max-Age: 3600');
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    http_response_code(200);
    exit();
}

$test = array(
    'status' => 'success',
    'message' => 'CORS is working correctly',
    'method' => $_SERVER['REQUEST_METHOD'],
    'origin' => isset($_SERVER['HTTP_ORIGIN']) ? $_SERVER['HTTP_ORIGIN'] : 'Not set',
    'headers' => array(
        'access-control-allow-origin' => '*',
        'access-control-allow-methods' => 'GET, POST, OPTIONS',
        'access-control-allow-headers' => 'Content-Type, Authorization, X-Requested-With, Accept, Origin'
    ),
    'timestamp' => date('Y-m-d H:i:s')
);

echo json_encode($test);
exit();

