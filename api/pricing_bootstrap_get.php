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
    if ($postId <= 0) {
        return 'obituary';
    }

    try {
        $st = db()->prepare("SELECT type FROM posts WHERE id = :pid LIMIT 1");
        $st->execute([':pid' => $postId]);
        $row = $st->fetch(PDO::FETCH_ASSOC);

        if ($row && !empty($row['type'])) {
            $type = strtolower(trim((string) $row['type']));
            if (in_array($type, ['obituary', 'remembrance'], true)) {
                return $type;
            }
        }
    } catch (Throwable $e) {
    }

    return 'obituary';
}

function load_info_text(): string
{
    $rulesText = '';
    $proceduresText = '';

    try {
        $row = db()->query("
            SELECT COALESCE(content_en, content_ta, content_si) AS body
            FROM rules_pages
            WHERE page_key='rules'
            LIMIT 1
        ")->fetch(PDO::FETCH_ASSOC);

        if ($row && !empty($row['body'])) {
            $rulesText = trim((string) $row['body']);
        }

        $row2 = db()->query("
            SELECT COALESCE(content_en, content_ta, content_si) AS body
            FROM rules_pages
            WHERE page_key='procedures'
            LIMIT 1
        ")->fetch(PDO::FETCH_ASSOC);

        if ($row2 && !empty($row2['body'])) {
            $proceduresText = trim((string) $row2['body']);
        }
    } catch (Throwable $e) {
    }

    return trim($rulesText . "\n\n" . $proceduresText);
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
        'additional_days' => [
            'label' => 'Additional Days (per day)',
            'lkr' => 500,
            'usd' => 5,
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
            case 'additional_days':
                $pricing['additional_days'] = ['label' => $label, 'lkr' => $lkr, 'usd' => $usd];
                break;
        }
    }

    return $pricing;
}

$postId = (int)($_GET['post_id'] ?? 0);

if ($postId <= 0) {
    out([
        'ok' => false,
        'message' => 'Missing post id.'
    ], 422);
}

$defaults = load_pricing_defaults();
$pricing = load_pricing_from_db($defaults);

out([
    'ok' => true,
    'post_id' => $postId,
    'currency' => currency_for_post($postId),
    'post_type' => load_post_type($postId),
    'pricing' => $pricing,
    'info_text' => load_info_text()
]);