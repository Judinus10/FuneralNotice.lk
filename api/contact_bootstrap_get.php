<?php
declare(strict_types=1);

session_start();
require_once __DIR__ . '/../db.php';

header('Content-Type: application/json; charset=utf-8');

function out(bool $ok, string $message = '', array $extra = []): void
{
    echo json_encode(
        array_merge(['ok' => $ok, 'message' => $message], $extra),
        JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES
    );
    exit;
}

try {
    $cfg = [
        'hotline_caption' => '24/7 HOT LINE INTERNATIONAL NUMBER',
        'hotline_number' => '',
        'hotline_note' => 'In case the local number is not reachable, please use the hotline number.',
        'support_email' => 'support@example.com',
        'default_country' => 'Sri Lanka',
        'map_iframe' => ''
    ];

    try {
        $st = db()->query("SELECT content_en FROM rules_pages WHERE page_key='contact_info' LIMIT 1");
        $row = $st->fetch(PDO::FETCH_ASSOC);
        if ($row && !empty($row['content_en'])) {
            $json = json_decode((string)$row['content_en'], true);
            if (is_array($json)) {
                $cfg = array_replace($cfg, $json);
            }
        }
    } catch (Throwable $e) {
        // ignore config load failure, keep defaults
    }

    $org = [
        'id' => null,
        'email' => '',
        'support_email' => '',
        'phone' => ''
    ];

    try {
        $st = db()->query("
            SELECT id, org_email AS email, support_email, org_phone AS phone
            FROM org_details
            ORDER BY id DESC
            LIMIT 1
        ");
        $row = $st->fetch(PDO::FETCH_ASSOC);
        if ($row) {
            $org = $row;
        }
    } catch (Throwable $e) {
        // ignore org load failure
    }

    $hotlineNumber = trim((string)($org['phone'] ?? ''));
    if ($hotlineNumber === '') {
        $hotlineNumber = trim((string)($cfg['hotline_number'] ?? ''));
    }
    if ($hotlineNumber === '') {
        $hotlineNumber = '+94 00 000 0000';
    }

    $supportEmail = trim((string)($org['support_email'] ?? ''));
    if ($supportEmail === '') {
        $supportEmail = trim((string)($org['email'] ?? ''));
    }
    if ($supportEmail === '') {
        $supportEmail = trim((string)($cfg['support_email'] ?? 'support@example.com'));
    }

    $countries = [];
    if (!empty($org['id'])) {
        try {
            $st = db()->prepare("
                SELECT country_name, local_phone, COALESCE(note,'') AS note
                FROM org_country_numbers
                WHERE org_id = ?
                ORDER BY sort_order, country_name
            ");
            $st->execute([$org['id']]);
            $raw = $st->fetchAll(PDO::FETCH_ASSOC) ?: [];

            $countries = array_values(array_filter($raw, function ($row) {
                $name = trim((string)($row['country_name'] ?? ''));
                $phone = trim((string)($row['local_phone'] ?? ''));
                return $name !== '' && $phone !== '';
            }));
        } catch (Throwable $e) {
            $countries = [];
        }
    }

    $phoneCountries = [];
    try {
        $st = db()->query("SELECT id, name, code FROM phone_countries ORDER BY sort_order, name");
        $phoneCountries = $st->fetchAll(PDO::FETCH_ASSOC) ?: [];
    } catch (Throwable $e) {
        $phoneCountries = [];
    }

    if (empty($phoneCountries)) {
        $phoneCountries = [
            ['name' => 'Sri Lanka', 'code' => '+94'],
            ['name' => 'India', 'code' => '+91'],
            ['name' => 'United Kingdom', 'code' => '+44'],
            ['name' => 'United States', 'code' => '+1'],
        ];
    }

    $defaultPhoneCode = '+94';
    foreach ($phoneCountries as $pc) {
        $name = strtolower(trim((string)($pc['name'] ?? '')));
        $code = trim((string)($pc['code'] ?? ''));
        if ($code === '+94' || $name === 'sri lanka') {
            $defaultPhoneCode = $code;
            break;
        }
    }

    out(true, 'Bootstrap loaded.', [
        'data' => [
            'hotline_caption' => (string)($cfg['hotline_caption'] ?? '24/7 HOT LINE INTERNATIONAL NUMBER'),
            'hotline_number' => $hotlineNumber,
            'hotline_note' => (string)($cfg['hotline_note'] ?? ''),
            'support_email' => $supportEmail,
            'office_email' => trim((string)($org['email'] ?? '')),
            'map_iframe' => (string)($cfg['map_iframe'] ?? ''),
            'countries' => $countries,
            'phone_countries' => $phoneCountries,
            'default_phone_code' => $defaultPhoneCode
        ]
    ]);
} catch (Throwable $e) {
    error_log('contact_bootstrap_get.php error: ' . $e->getMessage());
    out(false, 'Failed to load contact page data.');
}