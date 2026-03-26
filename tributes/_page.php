<?php
declare(strict_types=1);

if (!isset($TRIBUTE_META) || !is_array($TRIBUTE_META)) {
    http_response_code(500);
    exit('TRIBUTE_META is required.');
}

require __DIR__ . '/_common.php';

function tribute_text(array $meta, string $key, string $fallback = ''): string
{
    $value = $meta[$key] ?? $fallback;

    if (is_string($value) && str_starts_with($value, 'tr:')) {
        return t(substr($value, 3));
    }

    return (string)$value;
}

$title = tribute_text($TRIBUTE_META, 'title', t('tribute_default_title'));
$subtitle = tribute_text($TRIBUTE_META, 'subtitle', '');
$slug = $TRIBUTE_META['slug'] ?? 'message';
$icon = $TRIBUTE_META['icon'] ?? 'fa-heart';
$accent = $TRIBUTE_META['accent'] ?? 'purple';

$showOrg = !empty($TRIBUTE_META['show_org']);
$allowPhotoLinks = !empty($TRIBUTE_META['allow_photo_links']);
$supportsDelivery = !empty($TRIBUTE_META['supports_delivery']);
$forceDelivery = !empty($TRIBUTE_META['force_delivery']);

$messageLabel = tribute_text($TRIBUTE_META, 'message_label', t('tribute_message_label'));
$messagePlaceholder = tribute_text($TRIBUTE_META, 'message_placeholder', t('tribute_message_placeholder'));
$helperText = tribute_text($TRIBUTE_META, 'helper_text', t('tribute_helper_text'));

$phoneLabel = tribute_text($TRIBUTE_META, 'phone_label', t('tribute_phone_label'));
$phonePlaceholder = tribute_text($TRIBUTE_META, 'phone_placeholder', t('tribute_phone_placeholder'));
$deliveryText = tribute_text($TRIBUTE_META, 'delivery_text', t('tribute_delivery_text_default'));

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
$defaultSendToHome = ($supportsDelivery && $forceDelivery) ? 1 : 0;
$deliveryBlockStyle = ($supportsDelivery && $forceDelivery) ? '' : 'display:none;';
$deliveryPriceWrapStyle = ($supportsDelivery && $forceDelivery) ? '' : 'display:none;';
$previewDeliveryText = ($supportsDelivery && $forceDelivery) ? t('tribute_send_to_home') : t('tribute_posting_on_website');
$previewPhoneStyle = ($supportsDelivery && $forceDelivery) ? '' : 'display:none;';
?>
<!DOCTYPE html>
<html lang="<?= h(current_lang()) ?>">
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
                <?= h(t('common_back')) ?>
            </button>

            <div class="tribute-top-title">
                <strong><?= h($title) ?></strong>
                <span><?= h(t('tribute_for')) ?> <?= h($postName) ?></span>
            </div>

            <button type="button" class="tribute-top-btn danger" id="btnCloseTribute">
                <i class="fa-solid fa-xmark"></i>
                <?= h(t('common_close')) ?>
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
                    <div class="preview-heading"><?= h(t('tribute_live_preview')) ?></div>

                    <div class="preview-template-frame" id="previewTemplateFrame" style="display:none;">
                        <img id="previewTemplateImage" src="" alt="<?= h(t('tribute_selected_template_preview')) ?>">
                    </div>

                    <div class="preview-message" id="previewMessage"><?= h(t('tribute_preview_message_default')) ?></div>

                    <div class="preview-meta">
                        <span id="previewName"><?= h(t('tribute_your_name')) ?></span>
                        <span id="previewCountry"><?= h(t('tribute_country')) ?></span>
                    </div>

                    <?php if ($supportsDelivery): ?>
                        <div class="preview-extra neutral" id="previewDeliveryStatus"><?= h($previewDeliveryText) ?></div>
                        <div class="preview-extra neutral" id="previewPhoneStatus" style="<?= $previewPhoneStyle ?>">
                            <?= h(t('tribute_phone_not_added')) ?>
                        </div>
                    <?php endif; ?>

                    <div class="preview-extra neutral" id="previewTemplateStatus" style="display:none;">
                        <?= h(t('tribute_no_template_selected')) ?>
                    </div>

                    <?php if ($allowPhotoLinks): ?>
                        <div class="preview-extra" id="previewPhotos"><?= h(t('tribute_photo_links_count_default')) ?></div>
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
                    <input type="hidden" name="template_id" id="template_id" value="0">
                    <input type="hidden" name="send_to_home" id="send_to_home" value="<?= (int)$defaultSendToHome ?>">

                    <div class="form-grid">
                        <div class="field">
                            <label for="by_name"><?= h(t('tribute_your_name')) ?> <span>*</span></label>
                            <input type="text" id="by_name" name="by_name" required maxlength="120" placeholder="<?= h(t('tribute_enter_your_name')) ?>">
                        </div>

                        <?php if ($showOrg): ?>
                            <div class="field">
                                <label for="by_org"><?= h(t('tribute_organization_family_optional')) ?></label>
                                <input type="text" id="by_org" name="by_org" maxlength="120" placeholder="<?= h(t('tribute_organization_family_placeholder')) ?>">
                            </div>
                        <?php endif; ?>

                        <div class="field">
                            <label for="by_country"><?= h(t('tribute_country_optional')) ?></label>
                            <input
                                type="text"
                                id="by_country"
                                name="by_country"
                                maxlength="120"
                                placeholder="<?= h(t('tribute_country_placeholder')) ?>"
                                value="<?= h($_POST['by_country'] ?? '') ?>"
                            >
                        </div>

                        <?php if ($supportsDelivery): ?>
                            <div class="field field-full" id="templateSection">
                                <label><?= h(t('tribute_choose_design')) ?></label>

                                <div class="tribute-section-note">
                                    <?= h(t('tribute_design_note')) ?>
                                </div>

                                <div class="template-loading" id="templateLoading">
                                    <i class="fa-solid fa-spinner fa-spin"></i>
                                    <span><?= h(t('tribute_loading_templates')) ?></span>
                                </div>

                                <div class="template-empty" id="templateEmpty" style="display:none;">
                                    <?= h(t('tribute_no_template_designs')) ?>
                                </div>

                                <div class="template-gallery" id="templateGallery"></div>
                            </div>
                        <?php endif; ?>

                        <?php if ($supportsDelivery && !$forceDelivery): ?>
                            <div class="field field-full">
                                <label><?= h(t('tribute_choose_what_you_want')) ?></label>
                                <div class="delivery-choice" id="deliveryChoice">
                                    <label class="delivery-option active">
                                        <input type="radio" name="delivery_choice" value="0" checked>
                                        <span><?= h(t('tribute_post_on_website')) ?></span>
                                    </label>
                                    <label class="delivery-option">
                                        <input type="radio" name="delivery_choice" value="1">
                                        <span><?= h(t('tribute_send_to_home')) ?></span>
                                    </label>
                                </div>
                                <small><?= h(t('tribute_delivery_choice_note')) ?></small>
                            </div>
                        <?php endif; ?>

                        <?php if ($supportsDelivery): ?>
                            <div class="field field-full" id="deliveryPriceWrap" style="<?= $deliveryPriceWrapStyle ?>">
                                <div class="delivery-price-box" id="deliveryPriceBox">
                                    <?= h(t('tribute_select_template_for_price')) ?>
                                </div>
                            </div>
                        <?php endif; ?>

                        <?php if ($supportsDelivery): ?>
                            <div class="delivery-block" id="deliveryBlock" style="<?= $deliveryBlockStyle ?>">
                                <div class="delivery-note">
                                    <i class="fa-solid fa-truck-fast"></i>
                                    <span><?= h($deliveryText) ?></span>
                                </div>

                                <div class="form-grid">
                                    <div class="field">
                                        <label for="full_name"><?= h(t('tribute_full_name')) ?> <span>*</span></label>
                                        <input type="text" id="full_name" name="full_name" maxlength="120" placeholder="<?= h(t('tribute_enter_full_name')) ?>">
                                        <small><?= h(t('tribute_internal_use_only')) ?></small>
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
                                        <small><?= h(t('tribute_otp_required_only_for_delivery')) ?></small>
                                    </div>

                                    <div class="field field-full">
                                        <label for="otp_code"><?= h(t('tribute_verification_code')) ?></label>

                                        <div class="otp-stack">
                                            <div class="otp-row">
                                                <input
                                                    type="text"
                                                    id="otp_code"
                                                    name="otp_code"
                                                    maxlength="6"
                                                    inputmode="numeric"
                                                    autocomplete="one-time-code"
                                                    placeholder="<?= h(t('tribute_enter_6_digit_code')) ?>"
                                                >
                                                <button type="button" class="btn btn-light btn-otp" id="btnSendOtp">
                                                    <i class="fa-solid fa-paper-plane"></i>
                                                    <?= h(t('tribute_send_code')) ?>
                                                </button>
                                            </div>

                                            <div class="otp-row otp-row-secondary">
                                                <button type="button" class="btn btn-light btn-otp" id="btnVerifyOtp">
                                                    <i class="fa-solid fa-shield-check"></i>
                                                    <?= h(t('tribute_verify_code')) ?>
                                                </button>

                                                <button type="button" class="btn btn-light btn-otp" id="btnResendOtp">
                                                    <i class="fa-solid fa-rotate-right"></i>
                                                    <?= h(t('tribute_resend')) ?>
                                                </button>
                                            </div>
                                        </div>

                                        <div class="otp-status-wrap">
                                            <span class="otp-status-badge neutral" id="otpStatusBadge"><?= h(t('tribute_not_verified')) ?></span>
                                            <small id="otpStatusText"><?= h(t('tribute_verify_mobile_only_if_delivery')) ?></small>
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
                                <label for="photo_links"><?= h(t('tribute_photo_links_optional')) ?></label>
                                <textarea
                                    id="photo_links"
                                    name="photo_links"
                                    rows="5"
                                    placeholder="<?= h(t('tribute_photo_links_placeholder')) ?>"
                                ></textarea>
                            </div>
                        <?php endif; ?>
                    </div>

                    <div id="tributeFormAlert" class="tribute-alert" style="display:none;"></div>

                    <div class="tribute-actions">
                        <button type="button" class="btn btn-light" id="btnBackBottom"><?= h(t('common_back')) ?></button>
                        <button type="submit" class="btn btn-primary" id="btnSubmitTribute">
                            <i class="fa-solid fa-paper-plane"></i>
                            <?= h(t('tribute_submit_tribute')) ?>
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
            'forceDelivery' => $forceDelivery,
            'postId' => (int)$postId,
            'sendOtpApi' => '../sms_send.php',
            'submitApi' => '../api/tribute_entry_create.php',
            'templatesApi' => '../api/tribute_templates_get.php',
            'templateImageApi' => '../api/tribute_template_image.php',
            'i18n' => [
                'loadingTemplates' => t('tribute_loading_templates'),
                'noTemplateDesigns' => t('tribute_no_template_designs'),
                'selectTemplateForPrice' => t('tribute_select_template_for_price'),
                'notVerified' => t('tribute_not_verified'),
                'verified' => t('tribute_verified'),
                'postingOnWebsite' => t('tribute_posting_on_website'),
                'phoneNotAdded' => t('tribute_phone_not_added'),
                'noTemplateSelected' => t('tribute_no_template_selected'),
                'photoLinksCountDefault' => t('tribute_photo_links_count_default'),
                'sendToHome' => t('tribute_send_to_home'),
            ]
        ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) ?>;
    </script>
    <script src="../script/tribute-common.js"></script>
</body>
</html>