<?php
declare(strict_types=1);

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

try {
    $name = trim((string)($_POST['name'] ?? ''));
    $email = trim((string)($_POST['email'] ?? ''));
    $phoneCode = trim((string)($_POST['phone_code'] ?? '+94'));
    $mobile = trim((string)($_POST['mobile'] ?? ''));
    $subject = trim((string)($_POST['subject'] ?? ''));
    $message = trim((string)($_POST['message'] ?? ''));

    if ($name === '') {
        out(false, 'Full name is required.');
    }

    if ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        out(false, 'Valid email is required.');
    }

    if ($mobile === '') {
        out(false, 'Phone number is required.');
    }

    if ($subject === '') {
        out(false, 'Subject is required.');
    }

    if ($message === '') {
        out(false, 'Message is required.');
    }

    $phoneFull = trim($phoneCode . ' ' . $mobile);
    $phoneE164 = normalizeToE164($phoneFull);

    $otpOkPhone = (string)($_SESSION['contact_otp_ok']['phone'] ?? '');

    if ($otpOkPhone === '' || $otpOkPhone !== $phoneE164) {
        out(false, 'Phone number is not verified. Please verify OTP first.');
    }

    $finalMessage = "[Subject: {$subject}]\n\n{$message}";

    $st = db()->prepare("
        INSERT INTO contact_messages (
            post_id,
            sender_name,
            sender_email,
            sender_phone,
            message,
            created_at,
            is_relayed
        ) VALUES (
            NULL,
            :name,
            :email,
            :phone,
            :message,
            NOW(),
            0
        )
    ");

    $st->execute([
        ':name' => $name,
        ':email' => $email,
        ':phone' => $phoneFull,
        ':message' => $finalMessage,
    ]);

    unset($_SESSION['contact_otp_ok']);

    out(true, 'Message sent successfully.');
} catch (Throwable $e) {
    error_log('contact_message_submit.php error: ' . $e->getMessage());
    out(false, 'Failed to save your message.');
}