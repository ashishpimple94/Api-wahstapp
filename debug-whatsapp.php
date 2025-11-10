<?php
header('Content-Type: application/json');
error_reporting(0);
ini_set('display_errors', 0);

$test = array(
    'status' => 'ok',
    'message' => 'Debug endpoint working',
    'method' => $_SERVER['REQUEST_METHOD'],
    'has_input' => !empty(file_get_contents('php://input')),
    'curl_enabled' => function_exists('curl_init'),
    'json_enabled' => function_exists('json_encode')
);

echo json_encode($test);
exit();

