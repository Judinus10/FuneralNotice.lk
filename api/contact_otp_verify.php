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
    $otpCode = preg_replace('/\D+/', '', (string)($_POST['otp'] ?? ''));
    $phoneRaw = trim((string)($_POST['phone'] ?? ''));

    if ($phoneRaw === '') {
        out(false, 'Phone number is required.');
    }

    if (strlen($otpCode) !== 6) {
        out(false, 'Enter a valid 6-digit code.');
    }

    if (empty($_SESSION['contact_otp'])) {
        out(false, 'No OTP found. Please request a new code.');
    }

    $phoneE164 = normalizeToE164($phoneRaw);
    $data = $_SESSION['contact_otp'];

    if (time() > (int)($data['expires_at'] ?? 0)) {
        unset($_SESSION['contact_otp']);
        out(false, 'Code expired. Please request a new code.');
    }

    if ($phoneE164 !== (string)($data['phone'] ?? '')) {
        out(false, 'Phone mismatch. Please resend OTP.');
    }

    if ($otpCode !== (string)($data['code'] ?? '')) {
        out(false, 'Incorrect OTP code.');
    }

    $_SESSION['contact_otp_ok'] = [
        'phone' => (string)$data['phone'],
        'verified_at' => time(),
    ];

    unset($_SESSION['contact_otp']);

    out(true, 'OTP verified successfully.');
} catch (Throwable $e) {
    error_log('contact_otp_verify.php error: ' . $e->getMessage());
    out(false, 'OTP verification failed.');
}