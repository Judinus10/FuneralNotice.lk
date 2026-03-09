<?php
require_once __DIR__ . '/home_helpers.php';

try {
    $today = today_sl();

    $st = db()->prepare("
        SELECT id, label, start_date, end_date, is_enabled
        FROM site_maintenance
        WHERE is_enabled = 1
          AND ? BETWEEN start_date AND end_date
        ORDER BY created_at DESC
        LIMIT 1
    ");
    $st->execute([$today]);

    $maintenance = $st->fetch(PDO::FETCH_ASSOC) ?: null;

    json_response([
        'ok' => true,
        'maintenance' => $maintenance,
    ]);
} catch (Throwable $e) {
    json_response([
        'ok' => false,
        'maintenance' => null,
    ]);
}