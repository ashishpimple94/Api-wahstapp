<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    http_response_code(200);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] != 'POST') {
    http_response_code(405);
    echo json_encode(array('success' => false, 'error' => 'Method not allowed'));
    exit();
}

$input = file_get_contents('php://input');
$data = json_decode($input, true);

if (!$data) {
    http_response_code(400);
    echo json_encode(array('success' => false, 'error' => 'Invalid JSON'));
    exit();
}

$test = array(
    'status' => 'success',
    'message' => 'WhatsApp proxy endpoint is working',
    'received_data' => array(
        'has_phone_number' => !empty($data['phone_number']),
        'has_phone_number_id' => !empty($data['phone_number_id']),
        'has_api_key' => !empty($data['api_key']),
        'has_message' => !empty($data['message']),
        'has_message_type' => !empty($data['message_type']),
        'has_location' => !empty($data['location'])
    ),
    'php_version' => phpversion(),
    'curl_enabled' => function_exists('curl_init'),
    'timestamp' => date('Y-m-d H:i:s')
);

echo json_encode($test);
exit();

