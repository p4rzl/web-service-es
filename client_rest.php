<?php

declare(strict_types=1);

$url = 'https://jsonplaceholder.typicode.com/users/3';

$ch = curl_init();
if ($ch === false) {
    die("Impossibile inizializzare cURL.\n");
}

curl_setopt_array($ch, [
    CURLOPT_URL => $url,
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_HTTPGET => true,
    CURLOPT_TIMEOUT => 15,
    CURLOPT_CONNECTTIMEOUT => 10,
    CURLOPT_HTTPHEADER => [
        'Accept: application/json',
    ],
]);

$response = curl_exec($ch);

if ($response === false) {
    $error = curl_error($ch);
    curl_close($ch);
    die("Errore cURL: {$error}\n");
}

$httpCode = (int) curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

if ($httpCode < 200 || $httpCode >= 300) {
    die("Richiesta REST fallita. HTTP code: {$httpCode}\n");
}

$data = json_decode($response, true);
if (!is_array($data)) {
    die("Risposta JSON non valida.\n");
}

$nome = $data['name'] ?? 'N/D';
$email = $data['email'] ?? 'N/D';
$citta = $data['address']['city'] ?? 'N/D';

echo "Risposta JSON completa:\n";
echo json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . "\n\n";

echo "Dati utente (ID 3):\n";
echo "Nome : {$nome}\n";
echo "Email: {$email}\n";
echo "Citta: {$citta}\n";
