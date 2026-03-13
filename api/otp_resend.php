<?php
declare(strict_types=1);

header('Content-Type: application/json; charset=utf-8');
date_default_timezone_set('Asia/Colombo');
session_start();

require_once __DIR__ . '/../db.php';
require_once __DIR__ . '/../sms_send.php';

function out(bool $ok, string $message = '', array $extra = []): void
{
    echo json_encode(array_merge(['ok' => $ok, 'message' => $message], $extra), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    exit;
}

$raw = file_get_contents('php://input');
$data = json_decode($raw, true) ?: [];

$postId = (int)($data['post_id'] ?? 0);

if ($postId <= 0) {
    out(false, 'Invalid post.');
}

try {
    $pdo = db();

    $st = $pdo->prepare("
        SELECT user_id, phone
        FROM phone_otps
        WHERE post_id = :post_id
        ORDER BY id DESC
        LIMIT 1
    ");
    $st->execute([':post_id' => $postId]);
    $row = $st->fetch(PDO::FETCH_ASSOC);

    if (!$row) {
        out(false, 'No phone found for this post.');
    }

    $userId = (int)$row['user_id'];
    $phone = (string)$row['phone'];

    $pdo->prepare("
        UPDATE phone_otps
        SET expires_at = NOW()
        WHERE post_id = :post_id AND verified = 0
    ")->execute([':post_id' => $postId]);

    $otpCode = (string)random_int(100000, 999999);
    $otpHash = password_hash($otpCode, PASSWORD_DEFAULT);

    $pdo->prepare("
        INSERT INTO phone_otps (post_id, user_id, phone, otp_hash, expires_at, verified)
        VALUES (:post_id, :user_id, :phone, :otp_hash, (NOW() + INTERVAL 5 MINUTE), 0)
    ")->execute([
        ':post_id' => $postId,
        ':user_id' => $userId,
        ':phone' => $phone,
        ':otp_hash' => $otpHash
    ]);

    sendOtpSms($phone, $otpCode);

    out(true, 'OTP resent successfully.');
} catch (Throwable $e) {
    error_log("otp_resend.php error: " . $e->getMessage());
    out(false, 'Could not resend OTP.');
}