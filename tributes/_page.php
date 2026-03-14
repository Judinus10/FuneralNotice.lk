<?php
declare(strict_types=1);

if (!isset($TRIBUTE_META) || !is_array($TRIBUTE_META)) {
    http_response_code(500);
    exit('TRIBUTE_META is required.');
}

require __DIR__ . '/_common.php';

$title = $TRIBUTE_META['title'] ?? 'Tribute';
$subtitle = $TRIBUTE_META['subtitle'] ?? '';
$slug = $TRIBUTE_META['slug'] ?? 'message';
$icon = $TRIBUTE_META['icon'] ?? 'fa-heart';
$accent = $TRIBUTE_META['accent'] ?? 'purple';
$showOrg = !empty($TRIBUTE_META['show_org']);
$allowPhotoLinks = !empty($TRIBUTE_META['allow_photo_links']);
$supportsDelivery = !empty($TRIBUTE_META['supports_delivery']);

$messageLabel = $TRIBUTE_META['message_label'] ?? 'Message';
$messagePlaceholder = $TRIBUTE_META['message_placeholder'] ?? 'Write your tribute message';
$helperText = $TRIBUTE_META['helper_text'] ?? 'Share your condolences respectfully.';

$phoneLabel = $TRIBUTE_META['phone_label'] ?? 'Mobile Number';
$phonePlaceholder = $TRIBUTE_META['phone_placeholder'] ?? 'Enter mobile number';
$deliveryText = $TRIBUTE_META['delivery_text'] ?? 'A verified phone number is required if you want this tribute delivered to the home.';

/* -------------------------------
   Load phone country codes from DB
-------------------------------- */
$phoneCountries = [];
try {
    $st = db()->query("SELECT id, name, code FROM phone_countries ORDER BY sort_order, name");
    $phoneCountries = $st->fetchAll(PDO::FETCH_ASSOC);
} catch (Throwable $e) {
    $phoneCountries = [];
}

$defaultPhoneCode = '';
foreach ($phoneCountries as $pc) {
    $code = trim((string)$pc['code']);
    $name = strtolower(trim((string)$pc['name']));
    if ($code === '+94' || $name === 'sri lanka') {
        $defaultPhoneCode = $code;
        break;
    }
}
if ($defaultPhoneCode === '') {
    $defaultPhoneCode = $phoneCountries[0]['code'] ?? '+94';
}

$selectedPhoneCode = trim((string)($_POST['phone_code'] ?? $defaultPhoneCode));
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= h($title) ?> - FuneralNotice.lk</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="../style/tribute-common.css">
</head>
<body class="tribute-page theme-<?= h($accent) ?>">
    <div class="tribute-shell">
        <header class="tribute-topbar">
            <button type="button" class="tribute-top-btn" id="btnBackChooser">
                <i class="fa-solid fa-arrow-left"></i>
                Back
            </button>

            <div class="tribute-top-title">
                <strong><?= h($title) ?></strong>
                <span>for <?= h($postName) ?></span>
            </div>

            <button type="button" class="tribute-top-btn danger" id="btnCloseTribute">
                <i class="fa-solid fa-xmark"></i>
                Close
            </button>
        </header>

        <div class="tribute-body">
            <aside class="tribute-preview-card">
                <div class="tribute-preview-icon">
                    <i class="fa-solid <?= h($icon) ?>"></i>
                </div>

                <h1><?= h($title) ?></h1>
                <p class="tribute-preview-subtitle"><?= h($subtitle) ?></p>

                <div class="tribute-preview-box">
                    <div class="preview-heading">Live Preview</div>
                    <div class="preview-message" id="previewMessage">Your tribute message will appear here.</div>

                    <div class="preview-meta">
                        <span id="previewName">Your Name</span>
                        <span id="previewCountry">Country</span>
                    </div>

                    <?php if ($supportsDelivery): ?>
                        <div class="preview-extra neutral" id="previewDeliveryStatus">Not sending to home</div>
                        <div class="preview-extra neutral" id="previewPhoneStatus" style="display:none;">Phone not added</div>
                    <?php endif; ?>

                    <?php if ($allowPhotoLinks): ?>
                        <div class="preview-extra" id="previewPhotos">0 photo links added</div>
                    <?php endif; ?>
                </div>

                <div class="tribute-helper">
                    <i class="fa-solid fa-circle-info"></i>
                    <span><?= h($helperText) ?></span>
                </div>
            </aside>

            <section class="tribute-form-card">
                <form id="tributeEntryForm" class="tribute-form" novalidate>
                    <input type="hidden" name="csrf" value="<?= h($csrf) ?>">
                    <input type="hidden" name="post_id" value="<?= (int)$postId ?>">
                    <input type="hidden" name="tribute_slug" value="<?= h($slug) ?>">
                    <input type="hidden" name="template_id" value="0">
                    <input type="hidden" name="send_to_home" id="send_to_home" value="0">

                    <div class="form-grid">
                        <div class="field">
                            <label for="by_name">Your Name <span>*</span></label>
                            <input type="text" id="by_name" name="by_name" required maxlength="120" placeholder="Enter your name">
                        </div>

                        <?php if ($showOrg): ?>
                            <div class="field">
                                <label for="by_org">Organization / Family (optional)</label>
                                <input type="text" id="by_org" name="by_org" maxlength="120" placeholder="Family, Friends, Company...">
                            </div>
                        <?php endif; ?>

                        <div class="field">
                            <label for="by_country">Country (optional)</label>
                            <input
                                type="text"
                                id="by_country"
                                name="by_country"
                                maxlength="120"
                                placeholder="Sri Lanka, UK, Canada..."
                                value="<?= h($_POST['by_country'] ?? '') ?>"
                            >
                        </div>

                        <?php if ($supportsDelivery): ?>
                            <div class="field field-full">
                                <label>Do you want to send this <?= h(strtolower($title)) ?> to the home?</label>
                                <div class="delivery-choice" id="deliveryChoice">
                                    <label class="delivery-option active">
                                        <input type="radio" name="delivery_choice" value="0" checked>
                                        <span>No</span>
                                    </label>
                                    <label class="delivery-option">
                                        <input type="radio" name="delivery_choice" value="1">
                                        <span>Yes</span>
                                    </label>
                                </div>
                                <small>Default is no. Choose yes only if this tribute should be sent to the home.</small>
                            </div>

                            <div class="delivery-block" id="deliveryBlock" style="display:none;">
                                <div class="delivery-note">
                                    <i class="fa-solid fa-truck-fast"></i>
                                    <span><?= h($deliveryText) ?></span>
                                </div>

                                <div class="form-grid">
                                    <div class="field">
                                        <label for="full_name">Full Name <span>*</span></label>
                                        <input type="text" id="full_name" name="full_name" maxlength="120" placeholder="Enter full name">
                                        <small>This is for internal use only.</small>
                                    </div>

                                    <div class="field">
                                        <label><?= h($phoneLabel) ?> <span>*</span></label>
                                        <div class="phone-inline">
                                            <div class="phone-code-wrap">
                                                <select id="phone_code" name="phone_code" class="phone-code-select">
                                                    <?php foreach ($phoneCountries as $pc): ?>
                                                        <?php
                                                        $code = trim((string)$pc['code']);
                                                        $name = trim((string)$pc['name']);
                                                        ?>
                                                        <option
                                                            value="<?= h($code) ?>"
                                                            data-code="<?= h($code) ?>"
                                                            <?= $selectedPhoneCode === $code ? 'selected' : '' ?>
                                                        >
                                                            <?= h($name . ' (' . $code . ')') ?>
                                                        </option>
                                                    <?php endforeach; ?>
                                                </select>
                                                <span class="phone-code-display" id="phoneCodeDisplay"><?= h($selectedPhoneCode) ?></span>
                                            </div>

                                            <input
                                                type="text"
                                                id="mobile"
                                                name="mobile"
                                                maxlength="30"
                                                placeholder="<?= h($phonePlaceholder) ?>"
                                            >
                                        </div>
                                        <small>OTP verification is required only for home delivery.</small>
                                    </div>

                                    <div class="field field-full">
                                        <label for="otp_code">Verification Code</label>

                                        <div class="otp-stack">
                                            <div class="otp-row">
                                                <input
                                                    type="text"
                                                    id="otp_code"
                                                    name="otp_code"
                                                    maxlength="6"
                                                    inputmode="numeric"
                                                    autocomplete="one-time-code"
                                                    placeholder="Enter 6-digit code"
                                                >
                                                <button type="button" class="btn btn-light btn-otp" id="btnSendOtp">
                                                    <i class="fa-solid fa-paper-plane"></i>
                                                    Send Code
                                                </button>
                                            </div>

                                            <div class="otp-row otp-row-secondary">
                                                <button type="button" class="btn btn-light btn-otp" id="btnVerifyOtp">
                                                    <i class="fa-solid fa-shield-check"></i>
                                                    Verify Code
                                                </button>

                                                <button type="button" class="btn btn-light btn-otp" id="btnResendOtp">
                                                    <i class="fa-solid fa-rotate-right"></i>
                                                    Resend
                                                </button>
                                            </div>
                                        </div>

                                        <div class="otp-status-wrap">
                                            <span class="otp-status-badge neutral" id="otpStatusBadge">Not verified</span>
                                            <small id="otpStatusText">Verify the mobile number only if delivery is needed.</small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endif; ?>

                        <div class="field field-full">
                            <label for="message"><?= h($messageLabel) ?> <span>*</span></label>
                            <textarea
                                id="message"
                                name="message"
                                required
                                maxlength="2000"
                                placeholder="<?= h($messagePlaceholder) ?>"
                            ></textarea>
                        </div>

                        <?php if ($allowPhotoLinks): ?>
                            <div class="field field-full">
                                <label for="photo_links">Photo Links (optional)</label>
                                <textarea
                                    id="photo_links"
                                    name="photo_links"
                                    rows="5"
                                    placeholder="Paste one image URL per line&#10;https://example.com/photo1.jpg&#10;https://example.com/photo2.jpg"
                                ></textarea>
                            </div>
                        <?php endif; ?>
                    </div>

                    <div id="tributeFormAlert" class="tribute-alert" style="display:none;"></div>

                    <div class="tribute-actions">
                        <button type="button" class="btn btn-light" id="btnBackBottom">Back</button>
                        <button type="submit" class="btn btn-primary" id="btnSubmitTribute">
                            <i class="fa-solid fa-paper-plane"></i>
                            Submit Tribute
                        </button>
                    </div>
                </form>
            </section>
        </div>
    </div>

    <script>
        window.TRIBUTE_PAGE = <?= json_encode([
            'slug' => $slug,
            'title' => $title,
            'allowPhotoLinks' => $allowPhotoLinks,
            'supportsDelivery' => $supportsDelivery,
            'postId' => (int)$postId,
            'sendOtpApi' => '../sms_send.php',
            'submitApi' => '../api/tribute_entry_create.php',
        ], JSON_UNESCAPED_SLASHES) ?>;
    </script>
    <script src="../script/tribute-common.js"></script>
</body>
</html>