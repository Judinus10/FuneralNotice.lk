<?php
require_once __DIR__ . '/home_helpers.php';

try {
    $sid = ensure_sid();

    json_response([
        'ok' => true,
        'sid' => $sid,
    ]);
} catch (Throwable $e) {
    json_response([
        'ok' => false,
        'message' => 'Failed to create session.',
    ], 500);
}