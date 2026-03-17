<?php

declare(strict_types=1);

$url = 'http://www.w3schools.com/xml/tempconvert.asmx';

$soapRequest = <<<'XML'
<soap:Envelope xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/">
  <soap:Body>
    <CelsiusToFahrenheit xmlns="https://www.w3schools.com/xml/">
      <Celsius>25</Celsius>
    </CelsiusToFahrenheit>
  </soap:Body>
</soap:Envelope>
XML;

$ch = curl_init();
if ($ch === false) {
    die("Impossibile inizializzare cURL.\n");
}

curl_setopt_array($ch, [
    CURLOPT_URL => $url,
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_POST => true,
    CURLOPT_POSTFIELDS => $soapRequest,
    CURLOPT_TIMEOUT => 20,
    CURLOPT_CONNECTTIMEOUT => 10,
    CURLOPT_HTTPHEADER => [
        'Content-Type: text/xml; charset=utf-8',
        'SOAPAction: "https://www.w3schools.com/xml/CelsiusToFahrenheit"',
        'Content-Length: ' . strlen($soapRequest),
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
    die("Richiesta SOAP fallita. HTTP code: {$httpCode}\nRisposta:\n{$response}\n");
}

$fahrenheitValue = null;

$xml = @simplexml_load_string($response);
if ($xml !== false) {
    $bodyNodes = $xml->children('http://schemas.xmlsoap.org/soap/envelope/')->Body;
    if ($bodyNodes !== null) {
        $responseNodes = $bodyNodes->children('https://www.w3schools.com/xml/')->CelsiusToFahrenheitResponse;
        if ($responseNodes !== null) {
            $resultNode = $responseNodes->CelsiusToFahrenheitResult;
            if ($resultNode !== null) {
                $fahrenheitValue = (string) $resultNode;
            }
        }
    }
}

echo "Risposta XML completa:\n";
echo $response . "\n\n";

if ($fahrenheitValue !== null && $fahrenheitValue !== '') {
    echo "Risultato conversione: 25 C = {$fahrenheitValue} F\n";
} else {
    echo "Valore Fahrenheit non trovato nella risposta XML.\n";
}
