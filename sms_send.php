<?php
// sms_send.php — dual gateway (Notify.lk for +94, Twilio for others) + OTP JSON API
declare(strict_types=1);

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/db.php';
require_once __DIR__ . '/vendor/autoload.php'; // Twilio SDK

use Twilio\Rest\Client;

/* ============================================================
   SETTINGS FROM DB (site_settings id=1)
   ============================================================ */

function getSiteSettings(): array
{
    static $cache = null;
    if (is_array($cache)) return $cache;

    // Ensure row exists
    db()->exec("INSERT INTO site_settings (id) VALUES (1) ON DUPLICATE KEY UPDATE id=id");

    $st = db()->query("SELECT * FROM site_settings WHERE id=1 LIMIT 1");
    $row = $st->fetch(PDO::FETCH_ASSOC) ?: [];

    $cache = $row;
    return $cache;
}

function setting(string $key, string $default = ''): string
{
    $s = getSiteSettings();
    return trim((string)($s[$key] ?? $default));
}

function requireSetting(string $key, string $label): string
{
    $v = setting($key, '');
    if ($v === '') {
        throw new RuntimeException("Missing {$label} in Admin → Site Offers & Settings.");
    }
    return $v;
}

/* ============================================================
   PHONE HELPERS
   ============================================================ */

/**
 * Normalise a phone number to E.164-ish format:
 * - Keep a single leading +
 * - Remove all other non-digits
 */
function normalizeToE164(string $phone): string
{
    $phone = trim($phone);

    if (strpos($phone, '+') === 0) {
    $digits = preg_replace('/\D+/', '', substr($phone, 1));

    // FIX: if someone typed +94 0XXXXXXXXX -> convert to +94XXXXXXXXX
    if (preg_match('/^940(\d{9})$/', $digits, $m)) {
        return '+94' . $m[1];
    }

    return '+' . $digits;
}


    $digits = preg_replace('/\D+/', '', $phone);

    // If local SL number like 0771234567 -> convert to +94771234567
    if (preg_match('/^0\d{9}$/', $digits)) {
        return '+94' . substr($digits, 1);
    }

    // If it starts with 94 and length is 11, assume Sri Lanka
    if (preg_match('/^94\d{9}$/', $digits)) {
        return '+' . $digits;
    }

    return '+' . $digits;
}

/** Sri Lankan mobile in E.164 – +94xxxxxxxxx */
function isSriLankaNumber(string $phoneE164): bool
{
    return (bool)preg_match('/^\+94\d{9}$/', $phoneE164);
}

/* ============================================================
   MAIN SEND FUNCTION
   - If Sri Lanka: try Notify first, if fails -> Twilio fallback ✅
   - Others: Twilio
   ============================================================ */

function sendOtpSms(string $phone, string $otp): void
{
    $phoneE164 = normalizeToE164($phone);

    $notifyEnabled = (int)(setting('notify_enabled', '1')) === 1;
    $twilioEnabled = (int)(setting('twilio_enabled', '1')) === 1;

    $errors = [];

    if (isSriLankaNumber($phoneE164)) {
        if ($notifyEnabled) {
            try {
                sendOtpViaNotify($phoneE164, $otp);
                return;
            } catch (Throwable $e) {
                $errors[] = 'Notify: ' . $e->getMessage();
            }
        }

        if ($twilioEnabled) {
            try {
                sendOtpViaTwilio($phoneE164, $otp);
                return;
            } catch (Throwable $e) {
                $errors[] = 'Twilio: ' . $e->getMessage();
            }
        }

        throw new RuntimeException('OTP SMS failed. ' . implode(' | ', $errors));
    }

    if (!$twilioEnabled) {
        throw new RuntimeException('Twilio is disabled/unavailable.');
    }

    // Non-SL
    sendOtpViaTwilio($phoneE164, $otp);
}

/* ============================================================
   GATEWAY A — TWILIO
   ============================================================ */
function sendOtpViaTwilio(string $phoneE164, string $otp): void
{
    $accountSid  = requireSetting('twilio_account_sid', 'Twilio Account SID');
    $authToken   = requireSetting('twilio_auth_token', 'Twilio Auth Token');
    $fromNumber  = requireSetting('twilio_from_number', 'Twilio From Number');

    $client = new Client($accountSid, $authToken);

    $client->messages->create(
        $phoneE164,
        [
            'from' => $fromNumber,
            'body' => "Your RIPNEWS.LK verification code is: {$otp}. It will expire in 1 minutes."
        ]
    );
}

/* ============================================================
   GATEWAY B — NOTIFY.LK
   - Throws exception on failure so fallback can happen ✅
   ============================================================ */
function sendOtpViaNotify(string $phoneE164, string $otp): void
{
    $notifyUserId = requireSetting('notify_user_id', 'Notify User ID');
    $notifyApiKey = requireSetting('notify_api_key', 'Notify API Key');
    $senderId     = requireSetting('notify_sender_id', 'Notify Sender ID');

    $to = ltrim($phoneE164, '+');

    $payload = [
        'user_id'    => $notifyUserId,
        'api_key'    => $notifyApiKey,
        'sender_id'  => $senderId,
        'to'         => $to,
        'message'    => "Your RIPNEWS.LK verification code is: {$otp}. It will expire in 1 minutes.",
    ];

    $ch = curl_init('https://app.notify.lk/api/v1/send');
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($payload));
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 8);
    curl_setopt($ch, CURLOPT_TIMEOUT, 18);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);

    $response = curl_exec($ch);

    if ($response === false) {
        $err = curl_error($ch);
        curl_close($ch);
        throw new RuntimeException('Notify.lk cURL error: ' . $err);
    }

    $httpCode = (int)curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    error_log("Notify.lk raw response (HTTP {$httpCode}): " . $response);

    $data = json_decode($response, true);

    $ok = ($httpCode === 200) && is_array($data) && (
        (($data['status'] ?? '') === 'success') ||
        (($data['success'] ?? false) === true) ||
        (($data['status'] ?? null) === 1)
    );

    if (!$ok) {
        throw new RuntimeException('Notify.lk failed. HTTP ' . $httpCode . ' Response: ' . $response);
    }
}

/* ============================================================
   JSON API FOR OTP
   - candle: send_otp, verify_otp (kept)
   - contact: contact_send_otp, contact_verify_otp (added)
   ============================================================ */

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    header('Content-Type: application/json; charset=utf-8');

    $action = (string)($_POST['action'] ?? '');

    // ---------------------------
    // CANDLE (existing)
    // ---------------------------
    if ($action === 'send_otp') {
        $phoneRaw = trim((string)($_POST['phone'] ?? ''));

        if ($phoneRaw === '') {
            echo json_encode(['ok' => false, 'error' => 'Missing phone number.']);
            exit;
        }

        $otp = (string)random_int(100000, 999999);

        try {
            sendOtpSms($phoneRaw, $otp);

            $_SESSION['candle_otp'] = [
                'phone_raw'   => $phoneRaw,
                'code'        => $otp,
                'expires_at'  => time() + 300,
            ];

            echo json_encode(['ok' => true]);
        } catch (Throwable $e) {
            error_log('send_otp failed: ' . $e->getMessage());
            echo json_encode(['ok' => false, 'error' => 'Failed to send SMS.']);
        }
        exit;
    }

    if ($action === 'verify_otp') {
        $code = trim((string)($_POST['otp'] ?? ''));

        if ($code === '' || empty($_SESSION['candle_otp'])) {
            echo json_encode(['ok' => false, 'error' => 'No OTP to verify. Please request again.']);
            exit;
        }

        $data = $_SESSION['candle_otp'];

        if (time() > (int)($data['expires_at'] ?? 0)) {
            unset($_SESSION['candle_otp']);
            echo json_encode(['ok' => false, 'error' => 'Code expired. Please request a new one.']);
            exit;
        }

        if ($code !== (string)($data['code'] ?? '')) {
            echo json_encode(['ok' => false, 'error' => 'Incorrect code.']);
            exit;
        }

        $_SESSION['candle_otp_ok'] = [
            'phone_raw'   => (string)$data['phone_raw'],
            'verified_at' => time(),
        ];
        unset($_SESSION['candle_otp']);

        echo json_encode(['ok' => true]);
        exit;
    }

    // ---------------------------
    // CONTACT (NEW)
    // ---------------------------
    if ($action === 'contact_send_otp') {
        $phoneRaw = trim((string)($_POST['phone'] ?? ''));

        if ($phoneRaw === '') {
            echo json_encode(['ok' => false, 'error' => 'Missing phone number.']);
            exit;
        }

        $otp = (string)random_int(100000, 999999);

        try {
            sendOtpSms($phoneRaw, $otp);

            $_SESSION['contact_otp'] = [
                'phone'      => normalizeToE164($phoneRaw),
                'code'       => $otp,
                'expires_at' => time() + 300,
            ];

            echo json_encode(['ok' => true]);
        } catch (Throwable $e) {
            error_log('contact_send_otp failed: ' . $e->getMessage());
            echo json_encode(['ok' => false, 'error' => 'Failed to send SMS.']);
        }
        exit;
    }

    if ($action === 'contact_verify_otp') {
        $otpCode  = preg_replace('/\D+/', '', (string)($_POST['otp'] ?? ''));
        $phoneRaw = trim((string)($_POST['phone'] ?? ''));
        $phoneE164 = normalizeToE164($phoneRaw);

        if ($otpCode === '' || empty($_SESSION['contact_otp'])) {
            echo json_encode(['ok' => false, 'error' => 'No OTP to verify. Please request again.']);
            exit;
        }

        $data = $_SESSION['contact_otp'];

        if (time() > (int)($data['expires_at'] ?? 0)) {
            unset($_SESSION['contact_otp']);
            echo json_encode(['ok' => false, 'error' => 'Code expired. Please request a new one.']);
            exit;
        }

        if ($phoneE164 !== (string)($data['phone'] ?? '')) {
            echo json_encode(['ok' => false, 'error' => 'Phone mismatch. Please resend OTP.']);
            exit;
        }

        if ($otpCode !== (string)($data['code'] ?? '')) {
            echo json_encode(['ok' => false, 'error' => 'Incorrect code.']);
            exit;
        }

        $_SESSION['contact_otp_ok'] = [
            'phone'       => (string)$data['phone'],
            'verified_at' => time(),
        ];
        unset($_SESSION['contact_otp']);

        echo json_encode(['ok' => true]);
        exit;
    }

    echo json_encode(['ok' => false, 'error' => 'Unknown action.']);
    exit;
}
