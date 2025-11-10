<?php
// Test WhatsApp API Proxy
// This file tests if the proxy can make requests

error_reporting(0);
ini_set('display_errors', 0);
ob_start();

header('Content-Type: application/json');

// Test data
$testData = array(
    'phone_number' => '919090385555',
    'phone_number_id' => '741032182432100',
    'api_key' => '798422d2-818f-11f0-98fc-02c8a5e042bd',
    'message' => 'Test message'
);

// Check cURL
if (!function_exists('curl_init')) {
    ob_clean();
    echo json_encode(array('status' => 'error', 'message' => 'cURL not enabled'));
    ob_end_flush();
    exit();
}

// Test URL
$url = 'https://waba.xtendonline.com/v3/' . $testData['phone_number_id'] . '/messages';
$payload = array(
    'messaging_product' => 'whatsapp',
    'to' => $testData['phone_number'],
    'type' => 'text',
    'text' => array('body' => $testData['message'])
);

$ch = curl_init($url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
curl_setopt($ch, CURLOPT_HTTPHEADER, array(
    'Content-Type: application/json',
    'apikey: ' . $testData['api_key']
));
curl_setopt($ch, CURLOPT_TIMEOUT, 10);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 1);

$response = curl_exec($ch);
$code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$error = curl_error($ch);
curl_close($ch);

ob_clean();

if ($response === false) {
    echo json_encode(array(
        'status' => 'error',
        'message' => 'cURL error',
        'error' => $error,
        'code' => $code
    ));
} else {
    $data = json_decode($response, true);
    echo json_encode(array(
        'status' => 'ok',
        'http_code' => $code,
        'response' => $data,
        'raw_response' => substr($response, 0, 500)
    ));
}

ob_end_flush();
?>

