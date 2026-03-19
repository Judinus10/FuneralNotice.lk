<?php
require_once __DIR__ . '/../db.php';

function json_response($data, int $status = 200): void
{
    http_response_code($status);
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    exit;
}

function ensure_sid(): string
{
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

    return $_COOKIE['rn_sid'];
}

function today_sl(): string
{
    return (new DateTime('now', new DateTimeZone('Asia/Colombo')))->format('Y-m-d');
}

function time_ago(string $date): string
{
    $ts = strtotime($date);
    if (!$ts) return '';

    $diff = time() - $ts;
    if ($diff < 1) return 'just now';

    $units = [
        31536000 => 'year',
        2592000  => 'month',
        604800   => 'week',
        86400    => 'day',
        3600     => 'hour',
        60       => 'minute',
        1        => 'second',
    ];

    foreach ($units as $sec => $name) {
        if ($diff >= $sec) {
            $val = (int) floor($diff / $sec);
            return $val . ' ' . $name . ($val > 1 ? 's' : '') . ' ago';
        }
    }

    return 'just now';
}

function years_range(?string $birth, ?string $death): string
{
    $y1 = $birth ? date('Y', strtotime($birth)) : '—';
    $y2 = $death ? date('Y', strtotime($death)) : '—';
    return $y1 . ' - ' . $y2;
}

function remembrance_years(?string $death_date): ?int
{
    if (!$death_date) return null;

    try {
        $death = new DateTime($death_date);
        $now   = new DateTime('now', new DateTimeZone('Asia/Colombo'));
        $diff  = $death->diff($now);
        return max(1, $diff->y);
    } catch (Throwable $e) {
        return null;
    }
}

function ordinal_en(int $n): string
{
    $suffix = 'th';
    if (($n % 100) < 11 || ($n % 100) > 13) {
        switch ($n % 10) {
            case 1: $suffix = 'st'; break;
            case 2: $suffix = 'nd'; break;
            case 3: $suffix = 'rd'; break;
        }
    }
    return $n . $suffix;
}

function post_is_active_sql(string $alias = 'p'): string
{
    return "(
        {$alias}.duration_days = 0
        OR (
            {$alias}.duration_start IS NOT NULL
            AND {$alias}.duration_end IS NOT NULL
            AND CURRENT_DATE BETWEEN DATE({$alias}.duration_start) AND DATE({$alias}.duration_end)
        )
    )";
}

function normalize_whatsapp_link(string $phoneRaw, string $message): string
{
    $digits = preg_replace('/\D+/', '', trim($phoneRaw));
    if (!$digits) return '';

    if (str_starts_with($digits, '0')) {
        $digits = '94' . substr($digits, 1);
    }

    return "https://wa.me/{$digits}?text=" . urlencode($message);
}

function abs_upload_url(string $path): string
{
    $scheme = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
    $base   = $scheme . '://' . $_SERVER['HTTP_HOST'];
    $path   = ltrim($path, '/');
    return $base . '/' . $path;
}

// function abs_upload_url(string $path): string
// {
//     $path = ltrim($path, '/');
//     return 'https://ripnews.lk/' . $path;
// }