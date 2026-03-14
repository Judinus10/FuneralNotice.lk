<?php
declare(strict_types=1);

date_default_timezone_set('Asia/Colombo');
session_start();

require_once __DIR__ . '/../db.php';
require_once __DIR__ . '/../sms_send.php';

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
    $mode = cleanText($_POST['mode'] ?? 'send', 20);

    if ($postId <= 0) {
        out(false, 'Invalid memorial id.');
    }

    if ($tributeSlug === '') {
        out(false, 'Invalid tribute type.');
    }

    if (!tributeRequiresOtp($tributeSlug)) {
        out(false, 'OTP is not required for this tribute type.');
    }

    if ($phone === '') {
        out(false, 'Phone number is required.');
    }

    $pdo = db();

    $check = $pdo->prepare("SELECT id FROM posts WHERE id = ? LIMIT 1");
    $check->execute([$postId]);

    if (!$check->fetchColumn()) {
        out(false, 'Memorial not found.');
    }

    // Simple rate limit: 45 seconds per resend/send for same post + phone
    $rate = $pdo->prepare("
        SELECT created_at
        FROM phone_otps
        WHERE post_id = :post_id AND phone = :phone
        ORDER BY id DESC
        LIMIT 1
    ");
    $rate->execute([
        ':post_id' => $postId,
        ':phone' => $phone,
    ]);

    $lastCreatedAt = $rate->fetchColumn();

    if ($lastCreatedAt) {
        $lastTs = strtotime((string)$lastCreatedAt);
        if ($lastTs && (time() - $lastTs) < 45) {
            out(false, 'Please wait a little before requesting another code.');
        }
    }

    // Expire any unverified OTPs for same post + phone
    $expire = $pdo->prepare("
        UPDATE phone_otps
        SET expires_at = NOW()
        WHERE post_id = :post_id
          AND phone = :phone
          AND verified = 0
    ");
    $expire->execute([
        ':post_id' => $postId,
        ':phone' => $phone,
    ]);

    $otpCode = (string)random_int(100000, 999999);
    $otpHash = password_hash($otpCode, PASSWORD_DEFAULT);

    $ins = $pdo->prepare("
        INSERT INTO phone_otps (
            post_id,
            user_id,
            phone,
            otp_hash,
            expires_at,
            verified
        ) VALUES (
            :post_id,
            0,
            :phone,
            :otp_hash,
            (NOW() + INTERVAL 5 MINUTE),
            0
        )
    ");

    $ins->execute([
        ':post_id' => $postId,
        ':phone' => $phone,
        ':otp_hash' => $otpHash,
    ]);

    sendOtpSms($phone, $otpCode);

    $_SESSION['tribute_otp_post_id'] = $postId;
    $_SESSION['tribute_otp_phone'] = $phone;

    out(true, $mode === 'resend' ? 'Verification code resent successfully.' : 'Verification code sent successfully.');
} catch (Throwable $e) {
    error_log('tribute_otp_send.php error: ' . $e->getMessage());
    out(false, 'Failed to send verification code.');
}