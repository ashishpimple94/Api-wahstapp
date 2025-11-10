<?php
error_reporting(0);
ini_set('display_errors', 0);
ini_set('log_errors', 1);

while (ob_get_level() > 0) {
    ob_end_clean();
}
ob_start();

// CORS headers - MUST be set before any output
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, OPTIONS, GET');
header('Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With, Accept, Origin');
header('Access-Control-Allow-Credentials: false');
header('Access-Control-Max-Age: 3600');
header('Content-Type: application/json; charset=UTF-8');

// Handle OPTIONS preflight request
if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    ob_clean();
    http_response_code(200);
    header('Access-Control-Allow-Origin: *');
    header('Access-Control-Allow-Methods: POST, OPTIONS, GET');
    header('Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With, Accept, Origin');
    header('Access-Control-Max-Age: 3600');
    ob_end_flush();
    exit();
}

if ($_SERVER['REQUEST_METHOD'] != 'POST') {
    ob_clean();
    http_response_code(405);
    header('Access-Control-Allow-Origin: *');
    echo json_encode(array('success' => false, 'error' => 'Method not allowed'));
    ob_end_flush();
    exit();
}

$input = @file_get_contents('php://input');
if ($input === false) {
    ob_clean();
    http_response_code(400);
    header('Access-Control-Allow-Origin: *');
    echo json_encode(array('success' => false, 'error' => 'Could not read input'));
    ob_end_flush();
    exit();
}

$data = @json_decode($input, true);

if ($data === null && json_last_error() !== JSON_ERROR_NONE) {
    ob_clean();
    http_response_code(400);
    header('Access-Control-Allow-Origin: *');
    echo json_encode(array('success' => false, 'error' => 'Invalid JSON: ' . json_last_error_msg()));
    ob_end_flush();
    exit();
}

if (empty($data['phone_number']) || empty($data['phone_number_id']) || empty($data['api_key'])) {
    ob_clean();
    http_response_code(400);
    header('Access-Control-Allow-Origin: *');
    echo json_encode(array('success' => false, 'error' => 'Missing required fields'));
    ob_end_flush();
    exit();
}

if (!empty($data['message_type']) && $data['message_type'] == 'location' && !empty($data['location'])) {
    if (empty($data['location']['latitude']) || empty($data['location']['longitude']) || 
        empty($data['location']['name']) || empty($data['location']['address'])) {
        ob_clean();
        http_response_code(400);
        header('Access-Control-Allow-Origin: *');
        echo json_encode(array('success' => false, 'error' => 'Missing location fields'));
        ob_end_flush();
        exit();
    }
    $payload = array(
        'messaging_product' => 'whatsapp',
        'recipient_type' => 'individual',
        'to' => trim($data['phone_number']),
        'type' => 'location',
        'location' => array(
            'latitude' => (float)$data['location']['latitude'],
            'longitude' => (float)$data['location']['longitude'],
            'name' => $data['location']['name'],
            'address' => $data['location']['address']
        )
    );
} else {
    if (empty($data['message'])) {
        ob_clean();
        http_response_code(400);
        header('Access-Control-Allow-Origin: *');
        echo json_encode(array('success' => false, 'error' => 'Missing message'));
        ob_end_flush();
        exit();
    }
    $payload = array(
        'messaging_product' => 'whatsapp',
        'to' => trim($data['phone_number']),
        'type' => 'text',
        'text' => array('body' => $data['message'])
    );
}

if (!function_exists('curl_init')) {
    ob_clean();
    http_response_code(500);
    header('Access-Control-Allow-Origin: *');
    echo json_encode(array('success' => false, 'error' => 'cURL not available on server'));
    ob_end_flush();
    exit();
}

$url = 'https://waba.xtendonline.com/v3/' . trim($data['phone_number_id']) . '/messages';

$ch = @curl_init($url);
if ($ch === false) {
    ob_clean();
    http_response_code(500);
    header('Access-Control-Allow-Origin: *');
    echo json_encode(array('success' => false, 'error' => 'Failed to initialize cURL'));
    ob_end_flush();
    exit();
}

$payloadJson = @json_encode($payload);
if ($payloadJson === false) {
    @curl_close($ch);
    ob_clean();
    http_response_code(500);
    header('Access-Control-Allow-Origin: *');
    echo json_encode(array('success' => false, 'error' => 'Failed to encode payload'));
    ob_end_flush();
    exit();
}

curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, $payloadJson);
curl_setopt($ch, CURLOPT_HTTPHEADER, array(
    'Content-Type: application/json',
    'apikey: ' . trim($data['api_key'])
));
curl_setopt($ch, CURLOPT_TIMEOUT, 30);
curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
curl_setopt($ch, CURLOPT_MAXREDIRS, 3);

$response = @curl_exec($ch);
$httpCode = @curl_getinfo($ch, CURLINFO_HTTP_CODE);
$curlError = @curl_error($ch);
$curlErrno = @curl_errno($ch);
@curl_close($ch);

ob_clean();

// Always set CORS headers before sending response
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, OPTIONS, GET');
header('Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With, Accept, Origin');

if ($response === false || $curlErrno !== 0) {
    http_response_code(500);
    $errorMsg = !empty($curlError) ? $curlError : 'Network error (cURL error: ' . $curlErrno . ')';
    echo json_encode(array(
        'success' => false,
        'error' => 'Network error',
        'message' => $errorMsg,
        'errno' => $curlErrno
    ));
    ob_end_flush();
    exit();
}

if (empty($response)) {
    http_response_code(500);
    echo json_encode(array(
        'success' => false,
        'error' => 'Empty response from WhatsApp API',
        'http_code' => $httpCode
    ));
    ob_end_flush();
    exit();
}

$responseData = @json_decode($response, true);

if ($responseData === null && json_last_error() !== JSON_ERROR_NONE) {
    http_response_code(500);
    $preview = strlen($response) > 200 ? substr($response, 0, 200) . '...' : $response;
    echo json_encode(array(
        'success' => false,
        'error' => 'Invalid JSON response from WhatsApp API',
        'http_code' => $httpCode,
        'response_preview' => $preview,
        'json_error' => json_last_error_msg()
    ));
    ob_end_flush();
    exit();
}

if ($httpCode >= 400) {
    http_response_code($httpCode);
    $errorMsg = 'WhatsApp API error';
    if (!empty($responseData['error'])) {
        if (is_array($responseData['error'])) {
            $errorMsg = !empty($responseData['error']['message']) ? $responseData['error']['message'] : 
                       (!empty($responseData['error']['code']) ? 'Error code: ' . $responseData['error']['code'] : 'API error');
        } else {
            $errorMsg = $responseData['error'];
        }
    }
    echo json_encode(array(
        'success' => false,
        'error' => $errorMsg,
        'http_code' => $httpCode,
        'details' => $responseData
    ));
    ob_end_flush();
    exit();
}

if (!empty($responseData['error'])) {
    http_response_code(400);
    $errorMsg = 'WhatsApp API error';
    if (is_array($responseData['error'])) {
        $errorMsg = !empty($responseData['error']['message']) ? $responseData['error']['message'] : 
                   (!empty($responseData['error']['code']) ? 'Error code: ' . $responseData['error']['code'] : 'API error');
    } else {
        $errorMsg = $responseData['error'];
    }
    echo json_encode(array(
        'success' => false,
        'error' => $errorMsg,
        'details' => $responseData
    ));
    ob_end_flush();
    exit();
}

http_response_code(200);
$messageId = null;
if (!empty($responseData['messages']) && is_array($responseData['messages']) && !empty($responseData['messages'][0]['id'])) {
    $messageId = $responseData['messages'][0]['id'];
}

echo json_encode(array(
    'success' => true,
    'message_id' => $messageId,
    'phone_number' => trim($data['phone_number']),
    'data' => $responseData
));
ob_end_flush();
exit();
