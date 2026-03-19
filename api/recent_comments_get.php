<?php
require_once __DIR__ . '/home_helpers.php';

$VISIBLE_STATUS = 'published';

try {
    $sql = "
        SELECT
            p.id,
            p.full_name,
            MAX(x.created_at) AS last_tribute_at,
            MAX(p.cover_image_path) AS cover_image_path,
            COUNT(*) AS tribute_count
        FROM (
            SELECT post_id, created_at
            FROM tributes
            WHERE status = 'approved'

            UNION ALL

            SELECT post_id, created_at
            FROM tribute_entries
            WHERE status = 'approved'
              AND delivery = 0
        ) x
        JOIN posts p ON p.id = x.post_id
        WHERE p.status = :status
          AND " . post_is_active_sql('p') . "
        GROUP BY p.id, p.full_name
        ORDER BY last_tribute_at DESC
        LIMIT 10
    ";

    $st = db()->prepare($sql);
    $st->execute(['status' => $VISIBLE_STATUS]);
    $rows = $st->fetchAll(PDO::FETCH_ASSOC);

    $data = array_map(function ($row) {
        $cover = !empty($row['cover_image_path'])
            ? ltrim($row['cover_image_path'], '/')
            : 'cover.php?id=' . (int) $row['id'];
        // $cover = !empty($row['cover_image_path'])
        //     ? abs_upload_url($row['cover_image_path'])
        //     : 'https://ripnews.lk/cover.php?id=' . (int) $row['id'];

        return [
            'id' => (int) $row['id'],
            'full_name' => $row['full_name'],
            'cover_image' => $cover,
            'tribute_count' => (int) $row['tribute_count'],
            'last_tribute_at' => $row['last_tribute_at'],
            'last_tribute_ago' => time_ago($row['last_tribute_at']),
        ];
    }, $rows);

    json_response([
        'ok' => true,
        'recent_comments' => $data,
    ]);
} catch (Throwable $e) {
    json_response([
        'ok' => false,
        'recent_comments' => [],
    ], 500);
}