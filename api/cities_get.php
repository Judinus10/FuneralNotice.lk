<?php
declare(strict_types=1);

date_default_timezone_set('Asia/Colombo');

header('Content-Type: application/json; charset=utf-8');

function out(array $data, int $status = 200): void
{
    http_response_code($status);
    echo json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    exit;
}

$country = trim((string)($_GET['country'] ?? ''));

if ($country === '') {
    out([
        'ok' => false,
        'message' => 'Country is required.',
        'cities' => []
    ], 422);
}

try {
    $payload = json_encode(['country' => $country], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

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
        CURLOPT_SSL_VERIFYPEER => true,
        CURLOPT_SSL_VERIFYHOST => 2,
    ]);

    $response = curl_exec($ch);

    if ($response === false) {
        $err = curl_error($ch);
        curl_close($ch);
        out([
            'ok' => false,
            'message' => 'City API request failed: ' . $err,
            'cities' => []
        ], 500);
    }

    $httpCode = (int)curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    $decoded = json_decode($response, true);

    if ($httpCode !== 200 || !is_array($decoded)) {
        out([
            'ok' => false,
            'message' => 'City API returned invalid response.',
            'cities' => []
        ], 500);
    }

    $cities = $decoded['data'] ?? [];
    if (!is_array($cities)) {
        $cities = [];
    }

    $cities = array_values(array_unique(array_filter(array_map(
        static fn($v) => trim((string)$v),
        $cities
    ))));

    sort($cities, SORT_NATURAL | SORT_FLAG_CASE);

    out([
        'ok' => true,
        'country' => $country,
        'cities' => $cities
    ]);
} catch (Throwable $e) {
    out([
        'ok' => false,
        'message' => $e->getMessage(),
        'cities' => []
    ], 500);
}