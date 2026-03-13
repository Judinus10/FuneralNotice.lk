<?php
header('Content-Type: application/json; charset=utf-8');
date_default_timezone_set('Asia/Colombo');
session_start();

require_once __DIR__ . '/db.php';
require_once __DIR__ . '/sms_send.php';

$raw = file_get_contents('php://input');
$data = json_decode($raw, true) ?: [];

$postId = (int) ($data['post_id'] ?? 0);

if ($postId <= 0) {
  echo json_encode(['ok' => false, 'message' => 'Invalid post.']);
  exit;
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
    echo json_encode(['ok' => false, 'message' => 'No phone found for this post.']);
    exit;
  }

  $userId = (int) $row['user_id'];
  $phone = (string) $row['phone'];

  // Expire old unverified OTPs
  $pdo->prepare("
  UPDATE phone_otps
  SET expires_at = NOW()
  WHERE post_id = :post_id AND verified = 0
")->execute([':post_id' => $postId]);

  $otpCode = (string) random_int(100000, 999999);
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

  echo json_encode(['ok' => true]);
  exit;

} catch (Throwable $e) {
  error_log("resend_otp error: " . $e->getMessage());
  echo json_encode(['ok' => false, 'message' => 'Could not resend OTP.']);
  exit;
}
