<?php
require_once __DIR__ . '/home_helpers.php';

$VISIBLE_STATUS = 'published';

try {
    $sql = "
        SELECT TRIM(lived_place) AS district, COUNT(*) AS total
        FROM posts
        WHERE status = :status
          AND country = 'Sri Lanka'
          AND lived_place IS NOT NULL
          AND TRIM(lived_place) <> ''
          AND " . post_is_active_sql('posts') . "
        GROUP BY TRIM(lived_place)
        ORDER BY total DESC, district ASC
        LIMIT 20
    ";

    $st = db()->prepare($sql);
    $st->execute(['status' => $VISIBLE_STATUS]);
    $rows = $st->fetchAll(PDO::FETCH_ASSOC);

    json_response([
        'ok' => true,
        'districts' => $rows,
    ]);
} catch (Throwable $e) {
    json_response([
        'ok' => false,
        'districts' => [],
    ], 500);
}