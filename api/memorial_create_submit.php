<?php
declare(strict_types=1);

date_default_timezone_set('Asia/Colombo');
session_start();

require_once __DIR__ . '/../db.php';
require_once __DIR__ . '/../sms_send.php';

header('Content-Type: application/json; charset=utf-8');

function out(array $data, int $status = 200): void
{
    http_response_code($status);
    echo json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    exit;
}

function nullIfEmpty($v)
{
    $v = trim((string)$v);
    return $v === '' ? null : $v;
}

function validateUploadedImage(array $file, array $allowedMimes, int $maxBytes): array
{
    if (!isset($file['error']) || $file['error'] === UPLOAD_ERR_NO_FILE) {
        throw new RuntimeException('File is required.');
    }
    if ($file['error'] !== UPLOAD_ERR_OK) {
        throw new RuntimeException('Upload error.');
    }
    if ($file['size'] > $maxBytes) {
        throw new RuntimeException('File too large.');
    }

    $finfo = new finfo(FILEINFO_MIME_TYPE);
    $mime = $finfo->file($file['tmp_name']) ?: ($file['type'] ?? '');

    $allowed = [
        'image/jpeg' => 'jpg',
        'image/png'  => 'png',
        'image/webp' => 'webp',
    ];

    if (!isset($allowed[$mime])) {
        throw new RuntimeException('File format not allowed.');
    }

    return ['mime' => $mime, 'ext' => $allowed[$mime]];
}

function saveUploadedImageNamed(array $file, string $destDir, string $urlBase, string $filename): array
{
    if (!is_dir($destDir)) {
        @mkdir($destDir, 0775, true);
    }

    $destAbs = rtrim($destDir, '/\\') . DIRECTORY_SEPARATOR . $filename;

    if (!move_uploaded_file($file['tmp_name'], $destAbs)) {
        throw new RuntimeException('Could not save file.');
    }

    $urlPath = rtrim($urlBase, '/\\') . '/' . $filename;

    return [
        'path' => $urlPath,
        'name' => $filename,
    ];
}

$agentId = isset($_SESSION['agent_id']) ? (int)$_SESSION['agent_id'] : null;

if (empty($_SESSION['captcha_passed'])) {
    out([
        'ok' => false,
        'message' => 'Captcha not verified. Please try again.'
    ], 422);
}

if (!defined('MAX_IMAGE_MB')) {
    define('MAX_IMAGE_MB', 6);
}
if (!defined('UPLOADS_DIR')) {
    define('UPLOADS_DIR', dirname(__DIR__) . '/uploads');
}
if (!defined('UPLOADS_URL_BASE')) {
    define('UPLOADS_URL_BASE', 'uploads');
}
if (!is_dir(UPLOADS_DIR)) {
    @mkdir(UPLOADS_DIR, 0775, true);
}

try {
    $type = $_POST['type'] ?? '';
    $first_name = trim($_POST['first_name'] ?? '');
    $last_name = trim($_POST['last_name'] ?? '');
    $full_name = trim($first_name . ' ' . $last_name);
    $religion = trim($_POST['religion'] ?? '');
    $birth_date = trim($_POST['birth_date'] ?? '');
    $birth_place = trim($_POST['birth_place'] ?? '');
    $death_date = trim($_POST['death_date'] ?? '');
    $death_place = trim($_POST['death_place'] ?? '');
    $lived_place = trim($_POST['lived_place'] ?? '');
    $country = trim($_POST['country'] ?? '');
    $address = trim($_POST['address'] ?? '');
    $other_countries = nullIfEmpty($_POST['other_countries'] ?? '');
    $bio = nullIfEmpty($_POST['bio'] ?? '');

    $user_name = trim($_POST['contact_name'] ?? '');
    $phone_code = trim($_POST['phone_code'] ?? '');
    $phone_local = preg_replace('/\D+/', '', (string)($_POST['phone'] ?? ''));
    if ($phone_code === '+94') {
        $phone_local = ltrim($phone_local, '0');
    }
    $phone = normalizeToE164($phone_code . $phone_local);

    $phone_alt_code = trim($_POST['phone_alt_code'] ?? '');
    $phone_alt_local = trim($_POST['phone_alt'] ?? '');
    $phone_alt = $phone_alt_local === '' ? null : trim($phone_alt_code . ' ' . $phone_alt_local);

    $id_type = trim($_POST['id_type'] ?? '');
    $id_number = trim($_POST['id_number'] ?? '');

    $errors = [];

    if (!in_array($type, ['obituary', 'remembrance'], true)) $errors[] = 'Invalid post type.';
    if ($first_name === '') $errors[] = 'First name is required.';
    if ($last_name === '') $errors[] = 'Last name is required.';
    if ($religion === '') $errors[] = 'Religion is required.';
    if ($birth_date === '') $errors[] = 'Birth date is required.';
    if ($birth_place === '') $errors[] = 'Birth place is required.';
    if ($death_date === '') $errors[] = 'Death date is required.';
    if ($death_place === '') $errors[] = 'Death place is required.';
    if ($lived_place === '') $errors[] = 'Lived place is required.';
    if ($country === '') $errors[] = 'Country is required.';
    if ($address === '') $errors[] = 'Address is required.';
    if ($user_name === '') $errors[] = 'Your name is required.';
    if ($phone === '' || $phone_local === '') $errors[] = 'Phone number is required.';
    if (!in_array($id_type, ['NIC', 'Passport'], true)) $errors[] = 'ID type required.';
    if ($id_number === '') $errors[] = 'ID number required.';

    $cover_file = $_FILES['cover_image'] ?? null;
    $coverMeta = null;

    try {
        $coverMeta = validateUploadedImage($cover_file, [], MAX_IMAGE_MB * 1024 * 1024);
    } catch (Throwable $e) {
        $errors[] = 'Cover image: ' . $e->getMessage();
    }

    $nicFrontMeta = null;
    $nicBackMeta = null;
    $passportMeta = null;

    if ($id_type === 'NIC') {
        if (empty($_FILES['nic_front']['name'])) $errors[] = 'NIC front image required.';
        if (empty($_FILES['nic_back']['name'])) $errors[] = 'NIC back image required.';

        if (!$errors) {
            $nicFrontMeta = validateUploadedImage($_FILES['nic_front'], [], MAX_IMAGE_MB * 1024 * 1024);
            $nicBackMeta = validateUploadedImage($_FILES['nic_back'], [], MAX_IMAGE_MB * 1024 * 1024);
        }
    }

    if ($id_type === 'Passport') {
        if (empty($_FILES['passport_image']['name'])) $errors[] = 'Passport image required.';

        if (!$errors) {
            $passportMeta = validateUploadedImage($_FILES['passport_image'], [], MAX_IMAGE_MB * 1024 * 1024);
        }
    }

    if ($errors) {
        out([
            'ok' => false,
            'message' => implode(' ', $errors)
        ], 422);
    }

    $pdo = db();
    $pdo->beginTransaction();

    $u = $pdo->prepare("
        INSERT INTO users
          (full_name, phone, phone_alt, id_type, id_number,
           nic_front_path, nic_front_mime,
           nic_back_path, nic_back_mime,
           passport_path, passport_mime,
           created_at)
        VALUES
          (:full_name, :phone, :phone_alt, :id_type, :id_number,
           NULL, NULL,
           NULL, NULL,
           NULL, NULL,
           NOW())
    ");
    $u->execute([
        ':full_name' => $user_name,
        ':phone' => $phone,
        ':phone_alt' => $phone_alt,
        ':id_type' => $id_type,
        ':id_number' => $id_number,
    ]);
    $user_id = (int)$pdo->lastInsertId();

    $p = $pdo->prepare("
        INSERT INTO posts
          (type, full_name, religion, birth_date, birth_place,
           death_date, death_place,
           lived_place, country, address, other_countries, bio,
           age_years,
           cover_image_path, cover_image_mime,
           gallery,
           memorial_time_pricing_id, memorial_pricing_id, duration_days,
           has_live_coverage, has_social_media, has_media_website,
           agent_id, user_id, duration_start, duration_end, status, created_at)
        VALUES
          (:type, :full_name, :religion, :birth_date, :birth_place,
           :death_date, :death_place,
           :lived_place, :country, :address, :other_countries, :bio,
           NULL,
           NULL, NULL,
           JSON_ARRAY(),
           NULL, NULL, 0,
           0, 0, 0,
           :agent_id, :user_id, NULL, NULL, 'unverified', NOW())
    ");
    $p->execute([
        ':type' => $type,
        ':full_name' => $full_name,
        ':religion' => $religion,
        ':birth_date' => $birth_date,
        ':birth_place' => $birth_place,
        ':death_date' => $death_date,
        ':death_place' => $death_place,
        ':lived_place' => $lived_place,
        ':country' => $country,
        ':address' => $address,
        ':other_countries' => $other_countries,
        ':bio' => $bio,
        ':agent_id' => $agentId,
        ':user_id' => $user_id,
    ]);
    $createdId = (int)$pdo->lastInsertId();

    $postDirAbs = UPLOADS_DIR . '/posts/' . $createdId;
    $postDirUrl = UPLOADS_URL_BASE . '/posts/' . $createdId;

    $coverFilename = 'cover.' . $coverMeta['ext'];
    $coverSaved = saveUploadedImageNamed($cover_file, $postDirAbs, $postDirUrl, $coverFilename);

    $pdo->prepare("UPDATE posts SET cover_image_path = :p, cover_image_mime = :m WHERE id = :id")
        ->execute([
            ':p' => $coverSaved['path'],
            ':m' => $coverMeta['mime'],
            ':id' => $createdId,
        ]);

    $proofDirAbs = $postDirAbs . '/proofs';
    $proofDirUrl = $postDirUrl . '/proofs';

    $nic_front_path = null;
    $nic_front_mime = null;
    $nic_back_path = null;
    $nic_back_mime = null;
    $passport_path = null;
    $passport_mime = null;

    if ($id_type === 'NIC') {
        $nfSaved = saveUploadedImageNamed($_FILES['nic_front'], $proofDirAbs, $proofDirUrl, 'nic_front.' . $nicFrontMeta['ext']);
        $nbSaved = saveUploadedImageNamed($_FILES['nic_back'], $proofDirAbs, $proofDirUrl, 'nic_back.' . $nicBackMeta['ext']);

        $nic_front_path = $nfSaved['path'];
        $nic_back_path = $nbSaved['path'];
        $nic_front_mime = $nicFrontMeta['mime'];
        $nic_back_mime = $nicBackMeta['mime'];
    }

    if ($id_type === 'Passport') {
        $ppSaved = saveUploadedImageNamed($_FILES['passport_image'], $proofDirAbs, $proofDirUrl, 'passport.' . $passportMeta['ext']);
        $passport_path = $ppSaved['path'];
        $passport_mime = $passportMeta['mime'];
    }

    $otpCode = (string)random_int(100000, 999999);
    $otpHash = password_hash($otpCode, PASSWORD_DEFAULT);

    $otpStmt = $pdo->prepare("
        INSERT INTO phone_otps (post_id, user_id, phone, otp_hash, expires_at, verified)
        VALUES (:post_id, :user_id, :phone, :otp_hash, (NOW() + INTERVAL 5 MINUTE), 0)
    ");
    $otpStmt->execute([
        ':post_id' => $createdId,
        ':user_id' => $user_id,
        ':phone' => $phone,
        ':otp_hash' => $otpHash,
    ]);

    $upd = $pdo->prepare("
        UPDATE users SET
          nic_front_path = :nfp, nic_front_mime = :nfm,
          nic_back_path  = :nbp, nic_back_mime  = :nbm,
          passport_path  = :pp,  passport_mime  = :pm
        WHERE id = :uid
    ");
    $upd->execute([
        ':nfp' => $nic_front_path,
        ':nfm' => $nic_front_mime,
        ':nbp' => $nic_back_path,
        ':nbm' => $nic_back_mime,
        ':pp' => $passport_path,
        ':pm' => $passport_mime,
        ':uid' => $user_id,
    ]);

    $pdo->commit();

    $_SESSION['otp_post_id'] = $createdId;
    unset($_SESSION['captcha_passed'], $_SESSION['captcha_answer'], $_SESSION['captcha_question']);

    try {
        sendOtpSms($phone, $otpCode);
    } catch (Throwable $e) {
        error_log('sendOtpSms failed: ' . $e->getMessage());
    }

    out([
        'ok' => true,
        'message' => 'Submitted successfully. OTP sent to your phone.',
        'post_id' => $createdId,
        'otp_required' => true
    ]);
} catch (Throwable $e) {
    if (isset($pdo) && $pdo instanceof PDO && $pdo->inTransaction()) {
        $pdo->rollBack();
    }

    out([
        'ok' => false,
        'message' => $e->getMessage()
    ], 500);
}