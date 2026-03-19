<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// require_once __DIR__ . '/../db.php';
require_once __DIR__ . '/home_helpers.php';

header('Content-Type: application/json; charset=utf-8');

// function json_response(array $data, int $status = 200): void
// {
//     http_response_code($status);
//     echo json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
//     exit;
// }

// function time_ago(string $date): string
// {
//     $ts = strtotime($date);
//     if (!$ts) {
//         return '';
//     }

//     $diff = time() - $ts;
//     if ($diff < 1) {
//         return 'just now';
//     }

//     $units = [
//         31536000 => 'year',
//         2592000  => 'month',
//         604800   => 'week',
//         86400    => 'day',
//         3600     => 'hour',
//         60       => 'minute',
//         1        => 'second',
//     ];

//     foreach ($units as $sec => $name) {
//         if ($diff >= $sec) {
//             $val = (int) floor($diff / $sec);
//             return $val . ' ' . $name . ($val > 1 ? 's' : '') . ' ago';
//         }
//     }

//     return 'just now';
// }

// function years_range(?string $birth, ?string $death): string
// {
//     $y1 = $birth ? date('Y', strtotime($birth)) : '—';
//     $y2 = $death ? date('Y', strtotime($death)) : '—';
//     return $y1 . ' - ' . $y2;
// }

// function remembrance_years(?string $death_date): ?int
// {
//     if (!$death_date) {
//         return null;
//     }

//     try {
//         $death = new DateTime($death_date);
//         $now = new DateTime('now', new DateTimeZone('Asia/Colombo'));
//         $diff = $death->diff($now);
//         return max(1, $diff->y);
//     } catch (Throwable $e) {
//         return null;
//     }
// }

// function ordinal_en(int $n): string
// {
//     $suffix = 'th';
//     if (($n % 100) < 11 || ($n % 100) > 13) {
//         switch ($n % 10) {
//             case 1:
//                 $suffix = 'st';
//                 break;
//             case 2:
//                 $suffix = 'nd';
//                 break;
//             case 3:
//                 $suffix = 'rd';
//                 break;
//         }
//     }
//     return $n . $suffix;
// }

try {
    // session cookie
    if (!isset($_COOKIE['rn_sid']) || !is_string($_COOKIE['rn_sid']) || $_COOKIE['rn_sid'] === '') {
        $sid = bin2hex(random_bytes(16));
        $isHttps = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off');

        setcookie('rn_sid', $sid, [
            'expires'  => time() + (86400 * 365),
            'path'     => '/',
            'secure'   => $isHttps,
            'httponly' => true,
            'samesite' => 'Lax',
        ]);

        $_COOKIE['rn_sid'] = $sid;
    }

    $sid = $_COOKIE['rn_sid'];

    // frontend sends page + limit + type + district
    $page = max(1, (int)($_GET['page'] ?? 1));
    $limit = max(1, min(20, (int)($_GET['limit'] ?? 6)));
    $offset = ($page - 1) * $limit;

    $type = trim((string)($_GET['type'] ?? 'all'));
    $district = trim((string)($_GET['district'] ?? ''));

    $allowedTypes = ['all', 'obituary', 'remembrance'];
    if (!in_array($type, $allowedTypes, true)) {
        $type = 'all';
    }

    $where = [];
    $params = [];

    $where[] = "p.status = :status";
    $params['status'] = 'published';

    $where[] = "(
        p.duration_days = 0
        OR (
            p.duration_start IS NOT NULL
            AND p.duration_end IS NOT NULL
            AND CURRENT_DATE BETWEEN DATE(p.duration_start) AND DATE(p.duration_end)
        )
    )";

    if ($type !== 'all') {
        $where[] = "p.type = :type";
        $params['type'] = $type;
    }

    if ($district !== '') {
        $where[] = "LOWER(TRIM(p.lived_place)) = :district";
        $params['district'] = mb_strtolower($district);
    }

    $whereSql = implode(' AND ', $where);

    // count query - only uses WHERE params
    $countSql = "SELECT COUNT(*) FROM posts p WHERE {$whereSql}";
    $countSt = db()->prepare($countSql);
    $countSt->execute($params);
    $total = (int) $countSt->fetchColumn();

    // main feed query
    $sql = "
        SELECT
            p.id,
            p.type,
            p.full_name,
            p.religion,
            p.birth_date,
            p.birth_place,
            p.death_date,
            p.death_place,
            p.lived_place,
            p.country,
            p.bio,
            p.age_years,
            p.gallery,
            p.memorial_time_pricing_id AS package_id,
            p.cover_image_path,
            p.duration_start,
            p.duration_end,
            p.status,
            p.created_at,

            (
                (SELECT COUNT(*) FROM tributes t
                 WHERE t.post_id = p.id AND t.status = 'approved')
                +
                (SELECT COUNT(*) FROM tribute_entries e
                 WHERE e.post_id = p.id AND e.status = 'approved' AND e.delivery = 0)
            ) AS tribute_count,

            (SELECT COUNT(*) FROM post_reactions r WHERE r.post_id = p.id) AS react_total,

            (
                SELECT r2.reaction
                FROM post_reactions r2
                WHERE r2.post_id = p.id
                  AND r2.session_id = :sid
                LIMIT 1
            ) AS my_reaction,

            (
                SELECT lc.live_link
                FROM live_coverages lc
                WHERE lc.post_id = p.id
                  AND lc.status = 'approved'
                  AND lc.live_link IS NOT NULL
                  AND lc.live_link <> ''
                ORDER BY lc.created_at DESC
                LIMIT 1
            ) AS rip_video_link

        FROM posts p
        WHERE {$whereSql}
        ORDER BY COALESCE(p.duration_start, p.created_at) DESC, p.id DESC
        LIMIT :limit OFFSET :offset
    ";

    $st = db()->prepare($sql);

    // bind WHERE params
    foreach ($params as $key => $value) {
        $st->bindValue(':' . $key, $value, PDO::PARAM_STR);
    }

    // bind query-specific params
    $st->bindValue(':sid', $sid, PDO::PARAM_STR);
    $st->bindValue(':limit', $limit, PDO::PARAM_INT);
    $st->bindValue(':offset', $offset, PDO::PARAM_INT);

    $st->execute();
    $rows = $st->fetchAll(PDO::FETCH_ASSOC);

    $items = array_map(function ($m) {
        $isRemembrance = (($m['type'] ?? '') === 'remembrance');
        $remYears = $isRemembrance ? remembrance_years($m['death_date'] ?? null) : null;

        if ($isRemembrance) {
            $typeLabel = $remYears
                ? ordinal_en($remYears) . ' Year Remembrance'
                : 'Remembrance';
        } else {
            $typeLabel = 'Obituary';
        }

        $cover = !empty($m['cover_image_path'])
            ? abs_upload_url($m['cover_image_path'])
            : 'assets/defaultavt.png';

        // $cover = !empty($m['cover_image_path'])
        // ? 'https://ripnews.lk/' . ltrim($m['cover_image_path'], '/')
        // : 'assets/defaultavt.png';

        return [
            'id' => (int) $m['id'],
            'type' => $m['type'] ?: 'obituary',
            'type_label' => $typeLabel,
            'full_name' => $m['full_name'] ?? '',
            'cover_image' => $cover,
            'birth_date' => $m['birth_date'],
            'death_date' => $m['death_date'],
            'birth_place' => $m['birth_place'] ?? '',
            'lived_place' => $m['lived_place'] ?? '',
            'country' => $m['country'] ?? '',
            'created_at' => $m['created_at'] ?? '',
            'time_ago' => time_ago($m['duration_start'] ?: $m['created_at']),
            'years_range' => years_range($m['birth_date'] ?? null, $m['death_date'] ?? null),
            'tribute_count' => (int) ($m['tribute_count'] ?? 0),
            'react_total' => (int) ($m['react_total'] ?? 0),
            'my_reaction' => $m['my_reaction'] ?? '',
            'rip_video_link' => $m['rip_video_link'] ?? '',
            'details_url' => 'memorial-detail.php?id=' . (int) $m['id'],
        ];
    }, $rows);

    json_response([
        'ok' => true,
        'page' => $page,
        'limit' => $limit,
        'total' => $total,
        'has_more' => ($offset + $limit) < $total,
        'items' => $items,
    ]);
} catch (Throwable $e) {
    json_response([
        'ok' => false,
        'page' => 1,
        'limit' => 6,
        'total' => 0,
        'has_more' => false,
        'items' => [],
        'message' => $e->getMessage(),
    ], 500);
}