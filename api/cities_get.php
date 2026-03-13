<?php
declare(strict_types=1);

date_default_timezone_set('Asia/Colombo');

header('Content-Type: application/json; charset=utf-8');
header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
header('Pragma: no-cache');

function out(array $data, int $status = 200): void
{
    http_response_code($status);
    echo json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    exit;
}

$country = trim((string) ($_GET['country'] ?? ''));

if ($country === '') {
    out([
        'ok' => false,
        'message' => 'Country is required.',
        'cities' => []
    ], 422);
}

if (!function_exists('curl_init')) {
    out([
        'ok' => false,
        'message' => 'cURL is not enabled on this server.',
        'cities' => []
    ], 500);
}

try {
    $payload = json_encode(
        ['country' => $country],
        JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES
    );

    if ($payload === false) {
        out([
            'ok' => false,
            'message' => 'Failed to build request payload.',
            'cities' => []
        ], 500);
    }

    $ch = curl_init('https://countriesnow.space/api/v0.1/countries/cities');

    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_POST => true,
        CURLOPT_POSTFIELDS => $payload,
        CURLOPT_HTTPHEADER => [
            'Content-Type: application/json',
            'Accept: application/json'
        ],
        CURLOPT_CONNECTTIMEOUT => 10,
        CURLOPT_TIMEOUT => 20,

        // local Windows/XAMPP setups often choke on SSL cert chain
        CURLOPT_SSL_VERIFYPEER => false,
        CURLOPT_SSL_VERIFYHOST => 0,
    ]);

    $response = curl_exec($ch);
    $curlErr = curl_error($ch);
    $httpCode = (int) curl_getinfo($ch, CURLINFO_HTTP_CODE);

    curl_close($ch);

    if ($response === false || $response === '') {
        out([
            'ok' => false,
            'message' => 'City API request failed: ' . ($curlErr !== '' ? $curlErr : 'empty response'),
            'cities' => []
        ], 500);
    }

    $decoded = json_decode($response, true);

    if (!is_array($decoded)) {
        out([
            'ok' => false,
            'message' => 'City API returned non-JSON response.',
            'debug_http_code' => $httpCode,
            'debug_response' => substr($response, 0, 300),
            'cities' => []
        ], 500);
    }

    // countriesnow usually returns: { error: false, msg: "...", data: [...] }
    $apiError = $decoded['error'] ?? null;
    $apiMsg = trim((string) ($decoded['msg'] ?? ''));

    if ($httpCode !== 200) {
        out([
            'ok' => false,
            'message' => $apiMsg !== '' ? $apiMsg : 'City API returned HTTP ' . $httpCode . '.',
            'cities' => []
        ], 500);
    }

    if ($apiError === true) {
        out([
            'ok' => false,
            'message' => $apiMsg !== '' ? $apiMsg : 'City API reported an error.',
            'cities' => []
        ], 500);
    }

    $cities = $decoded['data'] ?? [];

    if (!is_array($cities)) {
        out([
            'ok' => false,
            'message' => 'City API data format is invalid.',
            'cities' => []
        ], 500);
    }

    $cities = array_values(array_unique(array_filter(array_map(
        static fn($v) => trim((string) $v),
        $cities
    ))));

    natcasesort($cities);
    $cities = array_values($cities);

    out([
        'ok' => true,
        'country' => $country,
        'cities' => $cities
    ]);
} catch (Throwable $e) {
    out([
        'ok' => false,
        'message' => 'Server error: ' . $e->getMessage(),
        'cities' => []
    ], 500);
}