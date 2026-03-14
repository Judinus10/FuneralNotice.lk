<?php
declare(strict_types=1);

date_default_timezone_set('Asia/Colombo');
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

function cleanText(?string $value, int $max = 100): string
{
    $value = trim((string)$value);
    if ($value === '') {
        return '';
    }

    $value = preg_replace('/\s+/u', ' ', $value);
    return mb_substr($value, 0, $max);
}

function normalizePhoneForOtp(string $phone): string
{
    $phone = trim($phone);

    if ($phone === '') {
        return '';
    }

    if (strpos($phone, '+') === 0) {
        $digits = preg_replace('/\D+/', '', substr($phone, 1));

        if (preg_match('/^940(\d{9})$/', $digits, $m)) {
            return '+94' . $m[1];
        }

        return '+' . $digits;
    }

    $digits = preg_replace('/\D+/', '', $phone);

    if (preg_match('/^0\d{9}$/', $digits)) {
        return '+94' . substr($digits, 1);
    }

    if (preg_match('/^94\d{9}$/', $digits)) {
        return '+' . $digits;
    }

    return $digits !== '' ? '+' . $digits : '';
}

function tributeRequiresOtp(string $slug): bool
{
    return in_array($slug, ['letter', 'photo'], true);
}

try {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        out(false, 'Invalid request method.');
    }

    $postId = (int)($_POST['post_id'] ?? 0);
    $tributeSlug = cleanText($_POST['tribute_slug'] ?? '', 50);
    $phone = normalizePhoneForOtp((string)($_POST['phone'] ?? ''));
    $code = preg_replace('/\D+/', '', (string)($_POST['otp_code'] ?? ''));

    if ($postId <= 0) {
        out(false, 'Invalid memorial id.');
    }

    if ($tributeSlug === '') {
        out(false, 'Invalid tribute type.');
    }

    if (!tributeRequiresOtp($tributeSlug)) {
        out(false, 'OTP verification is not required for this tribute type.');
    }

    if ($phone === '') {
        out(false, 'Phone number is required.');
    }

    if (strlen($code) !== 6) {
        out(false, 'Please enter a valid 6-digit code.');
    }

    if (
        !empty($_SESSION['tribute_otp_post_id']) &&
        (int)$_SESSION['tribute_otp_post_id'] !== $postId
    ) {
        unset($_SESSION['tribute_otp_post_id'], $_SESSION['tribute_otp_phone']);
    }

    $pdo = db();

    $st = $pdo->prepare("
        SELECT id, otp_hash, expires_at
        FROM phone_otps
        WHERE post_id = :post_id
          AND phone = :phone
          AND verified = 0
        ORDER BY id DESC
        LIMIT 1
    ");
    $st->execute([
        ':post_id' => $postId,
        ':phone' => $phone,
    ]);

    $row = $st->fetch(PDO::FETCH_ASSOC);

    if (!$row) {
        out(false, 'Verification code not found. Please resend the code.');
    }

    $expiresAt = strtotime((string)$row['expires_at']);
    if (!$expiresAt || time() > $expiresAt) {
        out(false, 'Code expired. Please resend the code.');
    }

    if (!password_verify($code, (string)$row['otp_hash'])) {
        out(false, 'Invalid verification code.');
    }

    $upd = $pdo->prepare("
        UPDATE phone_otps
        SET verified = 1
        WHERE id = :id
    ");
    $upd->execute([
        ':id' => (int)$row['id'],
    ]);

    $_SESSION['tribute_otp_post_id'] = $postId;
    $_SESSION['tribute_otp_phone'] = $phone;

    out(true, 'Phone number verified successfully.');
} catch (Throwable $e) {
    error_log('tribute_otp_verify.php error: ' . $e->getMessage());
    out(false, 'Failed to verify code.');
}