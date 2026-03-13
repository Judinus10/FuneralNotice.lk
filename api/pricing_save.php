<?php
declare(strict_types=1);

date_default_timezone_set('Asia/Colombo');
session_start();

header('Content-Type: application/json; charset=utf-8');

require_once __DIR__ . '/../db.php';

function out(array $data, int $status = 200): void
{
    http_response_code($status);
    echo json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    exit;
}

function currency_for_post(int $postId): string
{
    if ($postId <= 0) {
        return 'LKR';
    }

    try {
        $pdo = db();
        $st = $pdo->prepare("
            SELECT u.phone
            FROM posts p
            JOIN users u ON p.user_id = u.id
            WHERE p.id = :pid
            LIMIT 1
        ");
        $st->execute([':pid' => $postId]);
        $row = $st->fetch(PDO::FETCH_ASSOC);

        if ($row && !empty($row['phone'])) {
            $phone = trim((string) $row['phone']);
            if (strpos($phone, '+94') === 0) {
                return 'LKR';
            }
        }
    } catch (Throwable $e) {
    }

    return 'USD';
}

function load_post_type(int $postId): string
{
    try {
        $st = db()->prepare("SELECT type FROM posts WHERE id = :pid LIMIT 1");
        $st->execute([':pid' => $postId]);
        $row = $st->fetch(PDO::FETCH_ASSOC);

        if ($row && !empty($row['type'])) {
            $type = strtolower(trim((string)$row['type']));
            if (in_array($type, ['obituary', 'remembrance'], true)) {
                return $type;
            }
        }
    } catch (Throwable $e) {
    }

    return 'obituary';
}

function load_pricing_defaults(): array
{
    return [
        'memorial_time' => [
            ['label' => '3 Days', 'days' => 3, 'lkr' => 1500, 'usd' => 10],
            ['label' => '7 Days', 'days' => 7, 'lkr' => 1500, 'usd' => 10],
            ['label' => '14 Days', 'days' => 14, 'lkr' => 2500, 'usd' => 25],
            ['label' => '30 Days', 'days' => 30, 'lkr' => 4500, 'usd' => 50],
            ['label' => '60 Days', 'days' => 60, 'lkr' => 7500, 'usd' => 75],
            ['label' => 'Lifetime', 'days' => 0, 'lkr' => 10000, 'usd' => 100],
        ],
        'live_arrangement' => [
            'label' => 'Live Arrangement (All Island)',
            'lkr' => 35000,
            'usd' => 200
        ],
        'remembrance_days' => [
            ['label' => 'Day 1', 'days' => 1, 'lkr' => 0, 'usd' => 0],
            ['label' => 'Day 2', 'days' => 2, 'lkr' => 0, 'usd' => 0],
            ['label' => 'Day 3', 'days' => 3, 'lkr' => 0, 'usd' => 0],
            ['label' => 'Day 4', 'days' => 4, 'lkr' => 0, 'usd' => 0],
            ['label' => 'Day 5', 'days' => 5, 'lkr' => 0, 'usd' => 0],
        ],
        'social_media' => [
            'label' => 'Social Media Publish (FB & Insta)',
            'lkr' => 10000,
            'usd' => 50
        ],
        'media_website' => [
            ['label' => 'FuneralNews.lk', 'lkr' => 5000, 'usd' => 20],
            ['label' => 'FuneralNotice.lk', 'lkr' => 0, 'usd' => 0],
        ],
    ];
}

function load_pricing_from_db(array $defaults): array
{
    $pricing = $defaults;

    try {
        $rows = db()->query("
            SELECT section, label, days, lkr, usd, sort_order
            FROM pricing_items
            WHERE is_hidden = 0
            ORDER BY section, sort_order, id
        ")->fetchAll(PDO::FETCH_ASSOC);
    } catch (Throwable $e) {
        return $defaults;
    }

    if (!$rows) {
        return $defaults;
    }

    $pricing['memorial_time'] = [];
    $pricing['remembrance_days'] = [];
    $pricing['media_website'] = [];

    foreach ($rows as $r) {
        $section = $r['section'] ?? '';
        $label = (string)($r['label'] ?? '');
        $days = $r['days'] !== null ? (int)$r['days'] : null;
        $lkr = (float)($r['lkr'] ?? 0);
        $usd = (float)($r['usd'] ?? 0);

        switch ($section) {
            case 'memorial_time':
                $pricing['memorial_time'][] = ['label' => $label, 'days' => (int)($days ?? 0), 'lkr' => $lkr, 'usd' => $usd];
                break;
            case 'live_arrangement':
                $pricing['live_arrangement'] = ['label' => $label, 'lkr' => $lkr, 'usd' => $usd];
                break;
            case 'remembrance_days':
                $pricing['remembrance_days'][] = ['label' => $label, 'days' => (int)($days ?? 0), 'lkr' => $lkr, 'usd' => $usd];
                break;
            case 'social_media':
                $pricing['social_media'] = ['label' => $label, 'lkr' => $lkr, 'usd' => $usd];
                break;
            case 'media_website':
                $pricing['media_website'][] = ['label' => $label, 'lkr' => $lkr, 'usd' => $usd];
                break;
        }
    }

    return $pricing;
}

$postId = (int)($_POST['post_id'] ?? 0);
$days = (int)($_POST['duration_days'] ?? -1);
$hasLive = !empty($_POST['has_live_coverage']) ? 1 : 0;
$hasSocial = !empty($_POST['has_social_media']) ? 1 : 0;
$hasMedia = !empty($_POST['has_media_website']) ? 1 : 0;

$mediaSelection = json_decode((string)($_POST['media_sites'] ?? '[]'), true);
if (!is_array($mediaSelection)) {
    $mediaSelection = [];
}
$mediaSelection = array_values(array_filter(array_map('trim', $mediaSelection)));

if ($postId <= 0) {
    out(['ok' => false, 'message' => 'Missing post id.'], 422);
}

if ($days < 0) {
    out(['ok' => false, 'message' => 'Please select a valid duration.'], 422);
}

$currency = currency_for_post($postId);
$postType = load_post_type($postId);
$pricing = load_pricing_from_db(load_pricing_defaults());

$baseKey = $postType === 'remembrance' ? 'remembrance_days' : 'memorial_time';

$totalLkr = 0.0;
$totalUsd = 0.0;

if (!empty($pricing[$baseKey])) {
    foreach ($pricing[$baseKey] as $row) {
        if ((int)($row['days'] ?? -1) === $days) {
            $totalLkr += (float)($row['lkr'] ?? 0);
            $totalUsd += (float)($row['usd'] ?? 0);
            break;
        }
    }
}

if ($hasLive && !empty($pricing['live_arrangement'])) {
    $totalLkr += (float)($pricing['live_arrangement']['lkr'] ?? 0);
    $totalUsd += (float)($pricing['live_arrangement']['usd'] ?? 0);
}

if ($hasSocial && !empty($pricing['social_media'])) {
    $totalLkr += (float)($pricing['social_media']['lkr'] ?? 0);
    $totalUsd += (float)($pricing['social_media']['usd'] ?? 0);
}

if (!empty($mediaSelection) && !empty($pricing['media_website'])) {
    foreach ($pricing['media_website'] as $m) {
        $label = $m['label'] ?? '';
        if ($label && in_array($label, $mediaSelection, true)) {
            $totalLkr += (float)($m['lkr'] ?? 0);
            $totalUsd += (float)($m['usd'] ?? 0);
        }
    }
}

try {
    $pdo = db();

    $sql = "UPDATE posts
            SET duration_days     = :days,
                has_live_coverage = :live,
                has_social_media  = :soc,
                has_media_website = :media,
                total_lkr         = :total_lkr,
                total_usd         = :total_usd,
                billing_currency  = :cur,
                media_selection   = :media_selection,
                status            = 'pending'
            WHERE id = :post_id";

    $st = $pdo->prepare($sql);
    $st->bindValue(':days', $days, PDO::PARAM_INT);
    $st->bindValue(':live', $hasLive, PDO::PARAM_INT);
    $st->bindValue(':soc', $hasSocial, PDO::PARAM_INT);
    $st->bindValue(':media', $hasMedia, PDO::PARAM_INT);
    $st->bindValue(':total_lkr', $totalLkr);
    $st->bindValue(':total_usd', $totalUsd);
    $st->bindValue(':cur', $currency);
    $st->bindValue(':media_selection', json_encode($mediaSelection));
    $st->bindValue(':post_id', $postId, PDO::PARAM_INT);
    $st->execute();

    out([
        'ok' => true,
        'message' => 'Pricing saved.',
        'redirect' => 'memorial-detail.php?id=' . $postId
    ]);
} catch (Throwable $e) {
    error_log('pricing_save.php error: ' . $e->getMessage());
    out([
        'ok' => false,
        'message' => 'Could not save pricing.'
    ], 500);
}