<?php
// Test API endpoint - Simple test
header('Content-Type: application/json');
echo '{"status":"ok","message":"API endpoint is working","php_version":"' . phpversion() . '","curl_enabled":"' . (function_exists('curl_init') ? 'yes' : 'no') . '"}';
?>

