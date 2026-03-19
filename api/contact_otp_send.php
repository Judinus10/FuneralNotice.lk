<?php
declare(strict_types=1);

session_start();
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

try {
    $phoneRaw = trim((string)($_POST['phone'] ?? ''));

    if ($phoneRaw === '') {
        out(false, 'Phone number is required.');
    }

    $otp = (string) random_int(100000, 999999);

    sendOtpSms($phoneRaw, $otp);

    $_SESSION['contact_otp'] = [
        'phone' => normalizeToE164($phoneRaw),
        'code' => $otp,
        'expires_at' => time() + 300,
    ];

    unset($_SESSION['contact_otp_ok']);

    out(true, 'OTP sent successfully.');
} catch (Throwable $e) {
    error_log('contact_otp_send.php error: ' . $e->getMessage());
    out(false, 'Failed to send OTP.');
}