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
    $tributeSlug = trim((string)($_GET['tribute_slug'] ?? ''));
    if ($tributeSlug === '') {
        out(false, 'Missing tribute type.');
    }

    $pdo = db();

    $typeStmt = $pdo->prepare("
        SELECT id, slug, title
        FROM tribute_types
        WHERE slug = ?
          AND is_active = 1
        LIMIT 1
    ");
    $typeStmt->execute([$tributeSlug]);
    $type = $typeStmt->fetch(PDO::FETCH_ASSOC);

    if (!$type) {
        out(false, 'Tribute type not found.');
    }

    $tplStmt = $pdo->prepare("
        SELECT
            id,
            tribute_type_id,
            name,
            slug,
            price_local,
            price_foreign,
            frame_width_cm,
            frame_height_cm
        FROM tribute_templates
        WHERE tribute_type_id = ?
          AND is_active = 1
        ORDER BY sort_order ASC, id ASC
    ");
    $tplStmt->execute([(int)$type['id']]);
    $rows = $tplStmt->fetchAll(PDO::FETCH_ASSOC);

    $templates = [];
    foreach ($rows as $r) {
        $templates[] = [
            'id' => (int)$r['id'],
            'name' => (string)($r['name'] ?? ''),
            'slug' => (string)($r['slug'] ?? ''),
            'price_local' => (float)($r['price_local'] ?? 0),
            'price_foreign' => (float)($r['price_foreign'] ?? 0),
            'frame_width_cm' => $r['frame_width_cm'] !== null ? (float)$r['frame_width_cm'] : null,
            'frame_height_cm' => $r['frame_height_cm'] !== null ? (float)$r['frame_height_cm'] : null,
'image_url' => '../api/tribute_template_image.php?id=' . (int)$r['id'] . '&mode=image',
'banner_url' => '../api/tribute_template_image.php?id=' . (int)$r['id'] . '&mode=banner',            'type_title' => (string)$type['title'],
            'type_slug' => (string)$type['slug'],
        ];
    }

    out(true, '', [
        'tribute_type' => [
            'id' => (int)$type['id'],
            'slug' => (string)$type['slug'],
            'title' => (string)$type['title'],
        ],
        'templates' => $templates,
        'has_templates' => !empty($templates),
    ]);
} catch (Throwable $e) {
    error_log('tribute_templates_get.php error: ' . $e->getMessage());
    out(false, 'Failed to load tribute templates.');
}