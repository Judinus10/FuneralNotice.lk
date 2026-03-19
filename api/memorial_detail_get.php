<?php
declare(strict_types=1);
session_start();
require_once __DIR__ . '/../db.php';

header('Content-Type: application/json; charset=utf-8');

function safeOut($v): string {
    return htmlspecialchars((string)$v, ENT_QUOTES, 'UTF-8');
}

// function absUrl(?string $path): string {
//     if (!$path) return '';
//     if (preg_match('~^https?://~i', $path)) return $path;

//     $https = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off');
//     $scheme = $https ? 'https' : 'http';
//     $host = $_SERVER['HTTP_HOST'] ?? 'localhost';
//     $base = rtrim($scheme . '://' . $host, '/');
//     return $base . '/' . ltrim($path, '/');
// }

function absUrl(?string $path): string {
    if (!$path) return '';
    if (preg_match('~^https?://~i', $path)) return $path;

    return 'https://ripnews.lk/' . ltrim($path, '/');
}
function fmtDate(?string $d): string {
    if (!$d) return '';
    try {
        return (new DateTime($d))->format('d M Y');
    } catch (Throwable $e) {
        return (string)$d;
    }
}

function fmtDateMulti(?string $d): string {
    if (!$d) return '';
    try {
        $dt = new DateTime($d);
        return $dt->format('d') . '<br>' . $dt->format('M') . '<br>' . $dt->format('Y');
    } catch (Throwable $e) {
        return (string)$d;
    }
}

function calcAge(?string $b, ?string $d): ?int {
    if (!$b || !$d) return null;
    try {
        return (new DateTime($b))->diff(new DateTime($d))->y;
    } catch (Throwable $e) {
        return null;
    }
}

function timeAgo(?string $dt): string {
    if (!$dt) return '';
    try {
        $date = new DateTime($dt);
        $now = new DateTime();
        $diff = $now->diff($date);

        if ($diff->y > 0) return $diff->y . ' year' . ($diff->y > 1 ? 's' : '') . ' ago';
        if ($diff->m > 0) return $diff->m . ' month' . ($diff->m > 1 ? 's' : '') . ' ago';
        if ($diff->d >= 7) {
            $weeks = intdiv($diff->d, 7);
            return $weeks . ' week' . ($weeks > 1 ? 's' : '') . ' ago';
        }
        if ($diff->d > 0) return $diff->d . ' day' . ($diff->d > 1 ? 's' : '') . ' ago';
        if ($diff->h > 0) return $diff->h . ' hour' . ($diff->h > 1 ? 's' : '') . ' ago';
        if ($diff->i > 0) return $diff->i . ' minute' . ($diff->i > 1 ? 's' : '') . ' ago';
        return 'Just now';
    } catch (Throwable $e) {
        return '';
    }
}

function tributeHeading(?string $slug): string {
    $slug = strtolower(trim((string)$slug));
    if ($slug === 'wreath') return 'Our Deepest Sympathies';
    if ($slug === 'candle') return 'In Loving Memory';
    if ($slug === 'card' || $slug === 'cards') return 'With Heartfelt Condolences';
    if ($slug === 'letter') return 'Tribute Letter';
    return 'Tribute Message';
}

function truncateTribute(?string $text, int $max = 140): array {
    $text = trim((string)$text);
    if ($text === '') return ['text' => '', 'truncated' => false];
    if (mb_strlen($text) <= $max) return ['text' => $text, 'truncated' => false];

    $short = mb_substr($text, 0, $max);
    $lastSpace = mb_strrpos($short, ' ');
    if ($lastSpace !== false && $lastSpace > ($max - 40)) {
        $short = mb_substr($short, 0, $lastSpace);
    }
    return ['text' => $short . '…', 'truncated' => true];
}

function getNoticeHeading(array $post): string {
    $typeRaw = strtolower(trim((string)($post['type'] ?? '')));
    $death = $post['death_date'] ?? null;

    if (in_array($typeRaw, ['remembrance', 'rememberance', 'ninaivanjali'], true)) {
        if ($death) {
            try {
                $d = new DateTime($death);
                $now = new DateTime();
                $year = $d->diff($now)->y;
                if ($year < 1) $year = 1;
                return $year . 'ம் ஆண்டு நினைவேந்தல்';
            } catch (Throwable $e) {}
        }
        return 'நினைவேந்தல்';
    }

    return 'கண்ணீர் அஞ்சலி';
}

if (empty($_SESSION['csrf'])) {
    $_SESSION['csrf'] = bin2hex(random_bytes(16));
}

$id = isset($_GET['id']) ? max(0, (int)$_GET['id']) : 0;
if ($id <= 0) {
    echo json_encode(['ok' => false, 'message' => 'Invalid memorial id.']);
    exit;
}

try {
    $pdo = db();

    $p = $pdo->prepare("SELECT * FROM posts WHERE id=? LIMIT 1");
    $p->execute([$id]);
    $post = $p->fetch(PDO::FETCH_ASSOC);

    if (!$post) {
        echo json_encode(['ok' => false, 'message' => 'Memorial not found.']);
        exit;
    }

    $statusLower = strtolower(trim((string)($post['status'] ?? '')));
    if (in_array($statusLower, ['expired', 'rejected'], true)) {
        echo json_encode([
            'ok' => false,
            'message' => $statusLower === 'expired'
                ? 'This memorial has expired.'
                : 'This memorial has been rejected and is not available.'
        ]);
        exit;
    }

    $phoneCountries = [];
    try {
        $st = $pdo->query("SELECT id, name, code FROM phone_countries ORDER BY sort_order, name");
        $phoneCountries = $st->fetchAll(PDO::FETCH_ASSOC);
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
        $code = trim((string)$pc['code']);
        $name = strtolower(trim((string)$pc['name']));
        if ($code === '+94' || $name === 'sri lanka') {
            $defaultPhoneCode = $code;
            break;
        }
    }

    $lv = $pdo->prepare("SELECT live_link FROM live_coverages WHERE post_id=? AND status='approved' ORDER BY id DESC LIMIT 1");
    $lv->execute([$id]);
    $liveLink = $lv->fetchColumn() ?: '';

    $tq = $pdo->prepare("
        SELECT id, sender_name, message, created_at, tribute_type
        FROM tributes
        WHERE post_id=? AND status='approved'
        ORDER BY id DESC
    ");
    $tq->execute([$id]);
    $tributes = $tq->fetchAll(PDO::FETCH_ASSOC);

    $teq = $pdo->prepare("
        SELECT
            e.*,
            tt.title AS type_title,
            tt.slug AS type_slug
        FROM tribute_entries e
        JOIN tribute_types tt ON tt.id = e.tribute_type_id
        WHERE e.post_id = ?
          AND e.status = 'approved'
          AND e.delivery = 0
        ORDER BY e.created_at DESC, e.id DESC
    ");
    $teq->execute([$id]);
    $tributeEntries = $teq->fetchAll(PDO::FETCH_ASSOC);

    $mergedTributes = [];

    foreach ($tributeEntries as $e) {
        $hasBanner = !empty($e['template_id']);
        $tr = truncateTribute($e['message'] ?? '', $hasBanner ? 60 : 140);
        $ago = timeAgo($e['created_at'] ?? null);

        $meta = '';
        if (!empty($e['by_country'])) $meta .= $e['by_country'];
        if (!empty($meta) && $ago) $meta .= ' • ';
        if ($ago) $meta .= $ago;

        $bannerUrl = '';
        if ($hasBanner) {
            $bannerUrl = 'api/tribute_template_image.php?id=' . (int)$e['template_id'] . '&mode=banner';
        }

        $mergedTributes[] = [
            'id' => (int)$e['id'],
            'kind' => $hasBanner ? 'banner' : 'text',
            'heading' => tributeHeading($e['type_slug'] ?? null),
            'banner_image' => $bannerUrl,
            'short_message' => $tr['text'],
            'has_more' => $tr['truncated'],
            'by_name' => $e['by_name'] ?? '',
            'by_org' => $e['by_org'] ?? '',
            'meta' => $meta,
            'created_at' => $e['created_at'] ?? '',
        ];
    }

    foreach ($tributes as $t) {
        $ago = timeAgo($t['created_at'] ?? null);
        $meta = fmtDate($t['created_at'] ?? '');
        if ($ago) $meta .= ' • ' . $ago;

        $mergedTributes[] = [
            'id' => (int)$t['id'],
            'kind' => 'text',
            'heading' => 'Tribute Message',
            'banner_image' => '',
            'short_message' => $t['message'] ?? '',
            'has_more' => false,
            'by_name' => $t['sender_name'] ?? '',
            'by_org' => '',
            'meta' => $meta,
            'created_at' => $t['created_at'] ?? '',
        ];
    }

    usort($mergedTributes, function ($a, $b) {
        return strtotime($b['created_at'] ?? '') <=> strtotime($a['created_at'] ?? '');
    });

    $coverPath = trim((string)($post['cover_image_path'] ?? ''));
    $portrait = $coverPath !== '' ? absUrl($coverPath) : absUrl('assets/defaultavt.png');

    $noticeHeading = getNoticeHeading($post);
    $fullName = trim((string)($post['full_name'] ?? ''));
    $birth = fmtDate($post['birth_date'] ?? null);
    $death = fmtDate($post['death_date'] ?? null);
    $pageUrl = absUrl('memorial-detail.php?id=' . $id);

    $seoTitle = $fullName
        ? ($fullName . ' – ' . $noticeHeading . ' | FuneralNotice.lk')
        : 'Memorial | FuneralNotice.lk';

    $shareDesc = $fullName
        ? ("View memorial for {$fullName}" . (($birth || $death) ? " ({$birth} – {$death})" : "") . " on FuneralNotice.lk.")
        : "Memorial notice on FuneralNotice.lk";

    $orgPhone = '';
    try {
        $orgPhone = $pdo->query("SELECT org_phone FROM org_details ORDER BY id ASC LIMIT 1")->fetchColumn() ?: '';
    } catch (Throwable $e) {
        $orgPhone = '';
    }

    $waAddAdLink = '';
    if ($orgPhone) {
        $waDigits = preg_replace('/\D+/', '', $orgPhone);
        if ($waDigits) {
            $waAddAdLink = "https://wa.me/{$waDigits}?text=" . urlencode("Hi, I want to add an advertisement");
        }
    }

    $ads = [];
    try {
        $stAds = $pdo->prepare("
            SELECT id, ad_title, ad_image, author_phone, publish_at, expire_at
            FROM ads
            WHERE NOW() >= publish_at AND NOW() <= expire_at
            ORDER BY publish_at DESC, id DESC
        ");
        $stAds->execute();
        $rows = $stAds->fetchAll(PDO::FETCH_ASSOC);

        foreach ($rows as $ad) {
            $img = trim((string)($ad['ad_image'] ?? ''));
            if ($img === '') continue;

            $adDigits = preg_replace('/\D+/', '', (string)($ad['author_phone'] ?? ''));
            $adWaLink = $adDigits
                ? "https://wa.me/{$adDigits}?text=" . urlencode("Hi, I am interested in your advertisement.")
                : '';

            $ads[] = [
                'id' => (int)$ad['id'],
                'title' => $ad['ad_title'] ?: 'Sponsored',
                'image' => absUrl('admin/uploads/ads/' . rawurlencode($img)),
                'whatsapp' => $adWaLink,
                'view_url' => absUrl('admin/uploads/ads/' . rawurlencode($img)),
            ];
        }
    } catch (Throwable $e) {
        $ads = [];
    }

    $bornPlace = trim((string)($post['birth_place'] ?? ''));
    $bornCountry = trim((string)($post['birth_country'] ?? ($post['country'] ?? '')));
    $livedPlace = trim((string)($post['lived_place'] ?? ''));
    $livedCountry = trim((string)($post['lived_country'] ?? ($post['country'] ?? '')));
    $religion = trim((string)($post['religion'] ?? ''));

    $bornSummary = trim($bornPlace . ($bornCountry ? ', ' . $bornCountry : ''), ', ');
    $livedSummary = trim($livedPlace . ($livedCountry ? ', ' . $livedCountry : ''), ', ');

    echo json_encode([
        'ok' => true,
        'csrf' => $_SESSION['csrf'],
        'max_show' => 10,
        'total_tributes' => count($mergedTributes),
        'notice_heading' => $noticeHeading,
        'seo_title' => $seoTitle,
        'share_title' => $seoTitle,
        'share_desc' => $shareDesc,
        'share_image' => $portrait,
        'share_url' => $pageUrl,
        'live_link' => $liveLink,
        'org_phone' => $orgPhone,
        'add_ad_link' => $waAddAdLink,
        'default_phone_code' => $defaultPhoneCode,
        'phone_countries' => array_map(function ($pc) {
            return [
                'name' => (string)$pc['name'],
                'code' => (string)$pc['code'],
            ];
        }, $phoneCountries),
        'post' => [
            'id' => (int)$post['id'],
            'full_name' => (string)($post['full_name'] ?? ''),
            'type' => (string)($post['type'] ?? ''),
            'status' => (string)($post['status'] ?? ''),
            'is_pending' => ((string)($post['status'] ?? '') === 'pending'),
            'birth_date_multi' => fmtDateMulti($post['birth_date'] ?? null),
            'death_date_multi' => fmtDateMulti($post['death_date'] ?? null),
            'age' => calcAge($post['birth_date'] ?? null, $post['death_date'] ?? null),
            'portrait' => $portrait,
            'location_text' => trim((string)($post['lived_place'] ?? '') . (!empty($post['country']) ? ', ' . (string)$post['country'] : '')),
            'bio_html' => (($post['bio'] ?? '') !== '')
                ? nl2br(safeOut($post['bio']))
                : '<em style="color:#64748b">No biography added.</em>',
        ],
        'summary' => [
            'born_place' => $bornSummary,
            'lived_place' => $livedSummary,
            'religion' => $religion,
        ],
        'tributes' => $mergedTributes,
        'ads' => $ads,
    ], JSON_UNESCAPED_SLASHES);

} catch (Throwable $e) {
    echo json_encode([
        'ok' => false,
        'message' => 'Server error while loading memorial details.'
    ]);
}