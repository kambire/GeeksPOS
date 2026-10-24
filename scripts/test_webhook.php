<?php
$url = 'http://geekspos_n8n:5678/webhook/restaurante-web';
$payload = [
    'phone' => '595992641358',
    'name' => 'Rodrigo Web',
    'message' => 'Hola, qué promos de hamburguesas tienen hoy?',
    'source' => 'webapp'
];

$ch = curl_init($url);
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_TIMEOUT, 30);

$start = microtime(true);
$response = curl_exec($ch);
$elapsed = microtime(true) - $start;
$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$err = curl_error($ch);
curl_close($ch);

echo "HTTP Code: $http_code (took " . round($elapsed, 2) . "s)\n";
if ($err) echo "Error: $err\n";
echo "Response:\n$response\n";
