<?php
require_once __DIR__ . '/home_helpers.php';

try {
    $org = db()->query("SELECT org_phone FROM org_details ORDER BY id DESC LIMIT 1")->fetch(PDO::FETCH_ASSOC);
    $phoneRaw = trim((string) ($org['org_phone'] ?? ''));

    $link = normalize_whatsapp_link(
        $phoneRaw,
        'Hi, I want to place a Sponsored Ad on FuneralNotice.lk'
    );

    json_response([
        'ok' => true,
        'phone_raw' => $phoneRaw,
        'whatsapp_link' => $link,
    ]);
} catch (Throwable $e) {
    json_response([
        'ok' => false,
        'phone_raw' => '',
        'whatsapp_link' => '',
    ], 500);
}