<?php
require_once __DIR__ . '/home_helpers.php';

try {
    $st = db()->prepare("
        SELECT id, ad_title, ad_image, author_phone, publish_at, expire_at
        FROM ads
        WHERE NOW() >= publish_at
          AND NOW() <= expire_at
        ORDER BY publish_at DESC, id DESC
        LIMIT 20
    ");
    $st->execute();

    $rows = $st->fetchAll(PDO::FETCH_ASSOC);

    $ads = [];
    foreach ($rows as $row) {
        $img = trim((string) ($row['ad_image'] ?? ''));
        if ($img === '') continue;

        $ads[] = [
            'id' => (int) $row['id'],
            'title' => trim((string) ($row['ad_title'] ?? 'Sponsored')),
            'image' => abs_upload_url('admin/uploads/ads/' . rawurlencode($img)),
            'whatsapp_link' => normalize_whatsapp_link(
                (string) ($row['author_phone'] ?? ''),
                'Hi, I am interested in your advertisement.'
            ),
        ];
    }

    json_response([
        'ok' => true,
        'ads' => $ads,
    ]);
} catch (Throwable $e) {
    json_response([
        'ok' => false,
        'ads' => [],
    ], 500);
}