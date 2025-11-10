<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
header('Content-Type: application/json');

$test = array(
    'php_version' => phpversion(),
    'curl_enabled' => function_exists('curl_init'),
    'json_enabled' => function_exists('json_encode'),
    'allow_url_fopen' => ini_get('allow_url_fopen'),
    'max_execution_time' => ini_get('max_execution_time'),
    'memory_limit' => ini_get('memory_limit'),
    'post_max_size' => ini_get('post_max_size'),
    'upload_max_filesize' => ini_get('upload_max_filesize'),
    'server_software' => $_SERVER['SERVER_SOFTWARE'] ?? 'Unknown',
    'request_method' => $_SERVER['REQUEST_METHOD'],
    'request_uri' => $_SERVER['REQUEST_URI'],
    'timestamp' => date('Y-m-d H:i:s')
);

if (function_exists('curl_init')) {
    $ch = curl_init('https://www.google.com');
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 5);
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
    $test['curl_test'] = @curl_exec($ch) !== false ? 'Working' : 'Failed: ' . curl_error($ch);
    curl_close($ch);
} else {
    $test['curl_test'] = 'cURL not available';
}

echo json_encode($test, JSON_PRETTY_PRINT);
exit();

