<?php
// Test script untuk mengecek API endpoint
$url = 'http://localhost/ShalMonic/dataoverview/fetch?start_date=2025-01-01&end_date=2025-12-31';

echo "Testing API endpoint: $url\n";

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HEADER, false);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

echo "HTTP Code: $httpCode\n";
echo "Response:\n";
echo $response . "\n";

if ($response) {
    $data = json_decode($response, true);
    if (is_array($data) && count($data) > 0) {
        echo "\nFirst record:\n";
        print_r($data[0]);
    } else {
        echo "\nNo data or invalid JSON response\n";
    }
} 