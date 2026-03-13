<?php
date_default_timezone_set('Asia/Colombo');
session_start();
require_once __DIR__ . '/db.php';

header('Content-Type: application/json; charset=utf-8');

function out($ok, $message = '', $extra = [])
{
  echo json_encode(array_merge(['ok' => $ok, 'message' => $message], $extra));
  exit;
}

try {
  $raw = file_get_contents('php://input');
  $data = json_decode($raw, true);

  if (!is_array($data))
    out(false, 'Invalid request body (JSON expected).');

  $post_id = (int) ($data['post_id'] ?? 0);
  $code = preg_replace('/\D+/', '', (string) ($data['code'] ?? ''));

  if ($post_id <= 0)
    out(false, 'Invalid post id.');
  if (strlen($code) !== 6)
    out(false, 'Invalid code.');

  if (!empty($_SESSION['otp_post_id']) && (int) $_SESSION['otp_post_id'] !== $post_id) {
    // don't block; just clear session and allow DB-based verify
    unset($_SESSION['otp_post_id']);
  }


  $pdo = db();

  // ✅ Only latest ACTIVE unverified OTP
  $st = $pdo->prepare("
    SELECT id, otp_hash, expires_at
    FROM phone_otps
    WHERE post_id = :post_id AND verified = 0
    ORDER BY id DESC
    LIMIT 1
  ");
  $st->execute([':post_id' => $post_id]);
  $row = $st->fetch(PDO::FETCH_ASSOC);

  if (!$row)
    out(false, 'OTP not found. Please resend code.');

  $expiresAt = strtotime((string) $row['expires_at']);
  if (!$expiresAt || time() > $expiresAt) {
    out(false, 'Code expired. Please resend code.');
  }

  if (!password_verify($code, (string) $row['otp_hash'])) {
    out(false, 'Invalid code. Please try again.');
  }

  // ✅ Mark this OTP verified
  $pdo->prepare("UPDATE phone_otps SET verified = 1 WHERE id = :id")
    ->execute([':id' => (int) $row['id']]);
  // ✅ Move post from unverified -> pending after OTP success
  $pdo->prepare("
  UPDATE posts
  SET status = 'draft'
  WHERE id = :post_id
")->execute([
        ':post_id' => $post_id
      ]);


  unset($_SESSION['otp_post_id']);

  out(true, 'Verified.', ['redirect' => 'pricing.php?post_id=' . $post_id]);

} catch (Throwable $e) {
  error_log("verify_otp.php error: " . $e->getMessage());
  out(false, 'Server error. Check error logs.');
}
