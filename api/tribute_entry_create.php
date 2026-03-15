<?php
declare(strict_types=1);

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

function cleanText(?string $value, int $max = 255): string
{
    $value = trim((string)$value);
    if ($value === '') {
        return '';
    }

    $value = preg_replace('/\s+/u', ' ', $value);
    return mb_substr($value, 0, $max);
}

function normalizePhoneForSession(?string $phone): string
{
    $phone = trim((string)$phone);
    if ($phone === '') {
        return '';
    }

    $phone = preg_replace('/\s+/', ' ', $phone);
    return trim($phone);
}

function isSriLanka(?string $phoneCode, ?string $country): bool
{
    $code = trim((string)$phoneCode);
    $country = strtolower(trim((string)$country));
    return $code === '+94' || $country === 'sri lanka' || $country === 'lk' || $country === 'sl';
}

try {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        out(false, 'Invalid request method.');
    }

    if (!hash_equals($_SESSION['csrf'] ?? '', $_POST['csrf'] ?? '')) {
        out(false, 'Security token invalid. Please refresh and try again.');
    }

    $postId = isset($_POST['post_id']) ? max(0, (int)$_POST['post_id']) : 0;
    $tributeSlug = cleanText($_POST['tribute_slug'] ?? '', 50);

    $message = trim((string)($_POST['message'] ?? ''));
    $byName = cleanText($_POST['by_name'] ?? '', 120);
    $byOrg = cleanText($_POST['by_org'] ?? '', 120);
    $byCountry = cleanText($_POST['by_country'] ?? '', 120);

    $sendToHome = (int)($_POST['send_to_home'] ?? 0) === 1 ? 1 : 0;

    $fullName = cleanText($_POST['full_name'] ?? '', 120);
    $phoneCode = cleanText($_POST['phone_code'] ?? '', 20);
    $mobileLocal = cleanText($_POST['mobile'] ?? '', 30);

    $contactMobile = normalizePhoneForSession(trim($phoneCode . ' ' . $mobileLocal));
    $contactEmail = '';

    $templateId = isset($_POST['template_id']) && (int)$_POST['template_id'] > 0
        ? (int)$_POST['template_id']
        : null;

    $extraJsonRaw = (string)($_POST['extra_json'] ?? '');
    $extraData = [];

    if ($postId <= 0) {
        out(false, 'Invalid memorial id.');
    }

    if ($tributeSlug === '') {
        out(false, 'Invalid tribute type.');
    }

    if ($byName === '' || $message === '') {
        out(false, 'Please fill your name and message.');
    }

    if (mb_strlen($message) > 2000) {
        out(false, 'Message is too long.');
    }

    if ($extraJsonRaw !== '') {
        $decoded = json_decode($extraJsonRaw, true);
        if (!is_array($decoded)) {
            out(false, 'Invalid extra data.');
        }
        $extraData = $decoded;
    }

    $pdo = db();

    $postCheck = $pdo->prepare("SELECT id FROM posts WHERE id = ? LIMIT 1");
    $postCheck->execute([$postId]);
    if (!$postCheck->fetchColumn()) {
        out(false, 'Memorial not found.');
    }

    $typeStmt = $pdo->prepare("
        SELECT id, title, slug
        FROM tribute_types
        WHERE slug = ?
          AND is_active = 1
        LIMIT 1
    ");
    $typeStmt->execute([$tributeSlug]);
    $tributeType = $typeStmt->fetch(PDO::FETCH_ASSOC);

    if (!$tributeType) {
        out(false, 'Tribute type not configured.');
    }

    $tributeTypeId = (int)$tributeType['id'];

    $countStmt = $pdo->prepare("
        SELECT COUNT(*)
        FROM tribute_templates
        WHERE tribute_type_id = ?
          AND is_active = 1
    ");
    $countStmt->execute([$tributeTypeId]);
    $templateCount = (int)$countStmt->fetchColumn();
    $templateRequired = $templateCount > 0;

    $selectedTemplate = null;

    if ($templateRequired && $templateId === null) {
        out(false, 'Please select a template.');
    }

    if ($templateId !== null) {
        $tplStmt = $pdo->prepare("
            SELECT
                id,
                tribute_type_id,
                name,
                price_local,
                price_foreign,
                is_active
            FROM tribute_templates
            WHERE id = ?
              AND tribute_type_id = ?
              AND is_active = 1
            LIMIT 1
        ");
        $tplStmt->execute([$templateId, $tributeTypeId]);
        $selectedTemplate = $tplStmt->fetch(PDO::FETCH_ASSOC);

        if (!$selectedTemplate) {
            out(false, 'Selected template is invalid.');
        }
    }

    if ($sendToHome === 1) {
        if ($fullName === '' || $contactMobile === '') {
            out(false, 'Full name and mobile number are required for home delivery.');
        }

        $otpOk = $_SESSION['candle_otp_ok'] ?? null;
        $verifiedPhone = is_array($otpOk) ? trim((string)($otpOk['phone_raw'] ?? '')) : '';

        if ($verifiedPhone === '' || $verifiedPhone !== $contactMobile) {
            out(false, 'Please verify your mobile number via SMS before sending to a home address.');
        }

        if ($templateRequired && !$selectedTemplate) {
            out(false, 'Please select a template before home delivery.');
        }
    }

    if (isset($extraData['photo_links']) && is_array($extraData['photo_links'])) {
        $cleanLinks = [];

        foreach ($extraData['photo_links'] as $link) {
            $link = trim((string)$link);
            if ($link === '') {
                continue;
            }
            if (!filter_var($link, FILTER_VALIDATE_URL)) {
                continue;
            }
            $cleanLinks[] = $link;
        }

        $extraData['photo_links'] = array_values(array_slice($cleanLinks, 0, 20));
    }

    $currencyCode = '';
    $priceAmount = null;

    if ($sendToHome === 1 && $selectedTemplate) {
        $local = isSriLanka($phoneCode, $byCountry);
        $currencyCode = $local ? 'LKR' : 'USD';
        $priceAmount = $local
            ? (float)($selectedTemplate['price_local'] ?? 0)
            : (float)($selectedTemplate['price_foreign'] ?? 0);
    }

    $ins = $pdo->prepare("
        INSERT INTO tribute_entries (
            post_id,
            tribute_type_id,
            template_id,
            message,
            by_name,
            by_org,
            by_country,
            delivery,
            contact_full_name,
            contact_mobile,
            contact_email,
            currency_code,
            price_amount
        ) VALUES (
            :post_id,
            :tribute_type_id,
            :template_id,
            :message,
            :by_name,
            :by_org,
            :by_country,
            :delivery,
            :contact_full_name,
            :contact_mobile,
            :contact_email,
            :currency_code,
            :price_amount
        )
    ");

    $ins->bindValue(':post_id', $postId, PDO::PARAM_INT);
    $ins->bindValue(':tribute_type_id', $tributeTypeId, PDO::PARAM_INT);
    $ins->bindValue(':template_id', $templateId, $templateId === null ? PDO::PARAM_NULL : PDO::PARAM_INT);
    $ins->bindValue(':message', $message, PDO::PARAM_STR);
    $ins->bindValue(':by_name', $byName, PDO::PARAM_STR);
    $ins->bindValue(':by_org', $byOrg, PDO::PARAM_STR);
    $ins->bindValue(':by_country', $byCountry, PDO::PARAM_STR);
    $ins->bindValue(':delivery', $sendToHome, PDO::PARAM_INT);
    $ins->bindValue(':contact_full_name', $fullName, PDO::PARAM_STR);
    $ins->bindValue(':contact_mobile', $contactMobile, PDO::PARAM_STR);
    $ins->bindValue(':contact_email', $contactEmail, PDO::PARAM_STR);
    $ins->bindValue(':currency_code', $currencyCode !== '' ? $currencyCode : null, $currencyCode !== '' ? PDO::PARAM_STR : PDO::PARAM_NULL);
    $ins->bindValue(':price_amount', $priceAmount, $priceAmount !== null ? PDO::PARAM_STR : PDO::PARAM_NULL);
    $ins->execute();

    if ($sendToHome === 1) {
        unset($_SESSION['candle_otp_ok'], $_SESSION['candle_otp']);
    }

    if ($sendToHome === 1 && $priceAmount !== null) {
        out(true, "Thank you. Your delivery request is waiting for admin approval. Price: {$currencyCode} {$priceAmount}.");
    }

    out(true, 'Thank you. Your tribute is waiting for admin approval.');
} catch (Throwable $e) {
    error_log('tribute_entry_create.php error: ' . $e->getMessage());
    out(false, 'Failed to submit tribute.');
}