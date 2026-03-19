<?php
declare(strict_types=1);
require_once __DIR__ . '/translator/language.php';
?>
<!DOCTYPE html>
<html lang="<?= htmlspecialchars(current_lang(), ENT_QUOTES, 'UTF-8') ?>">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars(t('create_page_title'), ENT_QUOTES, 'UTF-8') ?></title>

    <meta property="og:type" content="website">
    <meta property="og:url" content="https://funeralnotice.lk/create">
    <meta property="og:title" content="<?= htmlspecialchars(t('create_og_title'), ENT_QUOTES, 'UTF-8') ?>">
    <meta property="og:description"
        content="<?= htmlspecialchars(t('create_meta_description'), ENT_QUOTES, 'UTF-8') ?>">
    <meta property="og:image" content="https://ripnews.lk/uploads/posts/76/cover.png">
    <meta property="og:image:type" content="image/png">
    <meta property="og:image:width" content="1200">
    <meta property="og:image:height" content="630">
    <meta property="og:site_name" content="FuneralNotice.lk">

    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="<?= htmlspecialchars(t('create_og_title'), ENT_QUOTES, 'UTF-8') ?>">
    <meta name="twitter:description"
        content="<?= htmlspecialchars(t('create_meta_description'), ENT_QUOTES, 'UTF-8') ?>">
    <meta name="twitter:image" content="https://ripnews.lk/uploads/posts/76/cover.png">

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="style/common.css">
    <link rel="stylesheet" href="style/navbar.css">
    <link rel="stylesheet" href="style/footer.css">
    <link rel="stylesheet" href="style/create.css">
</head>

<body>
    <div id="navbar-placeholder"></div>

    <div class="main-body">
        <div class="create-memorial-section">
            <div class="container">
                <div class="create-memorial-header">
                    <div class="create-head-top">
                        <div>
                            <h1 id="pageTitle"><?= t('create_heading') ?></h1>
                            <p id="pageSubtitle"><?= t('create_subtitle') ?></p>
                        </div>
                    </div>
                </div>

                <div class="stepper-wrap">
                    <div class="stepper-track">
                        <div class="stepper-fill" id="stepperFill"></div>

                        <div class="stepper-step is-active" data-step="1">
                            <span class="dot"></span>
                            <span class="label"><?= t('create_step_post_details') ?></span>
                        </div>

                        <div class="stepper-step is-disabled" data-step="2">
                            <span class="dot"></span>
                            <span class="label"><?= t('create_step_post_owner') ?></span>
                        </div>

                        <div class="stepper-step is-disabled" data-step="3">
                            <span class="dot"></span>
                            <span class="label"><?= t('create_step_sms_verify') ?></span>
                        </div>

                        <div class="stepper-step is-disabled" data-step="4">
                            <span class="dot"></span>
                            <span class="label"><?= t('create_step_duration') ?></span>
                        </div>

                        <div class="stepper-step is-disabled" data-step="5">
                            <span class="dot"></span>
                            <span class="label"><?= t('create_step_submit') ?></span>
                        </div>
                    </div>
                </div>

                <div class="create-memorial-card">
                    <form id="memorialForm" enctype="multipart/form-data" novalidate>
                        <input type="hidden" name="final_submit" value="1">
                        <input type="hidden" name="lived_place" id="lived_place">

                        <div class="step-panel active" id="step-1">
                            <h2 class="card-title"><?= t('create_step1_title') ?></h2>

                            <div class="form-row">
                                <div class="form-group">
                                    <label for="type"><?= t('create_post_type') ?> <span class="required">*</span></label>
                                    <select class="form-control" id="type" name="type" required>
                                        <option value=""><?= t('create_select_post_type') ?></option>
                                    </select>
                                </div>

                                <div class="form-group">
                                    <label for="religion"><?= t('create_religion') ?> <span class="required">*</span></label>
                                    <select class="form-control" id="religion" name="religion" required>
                                        <option value=""><?= t('create_select_religion') ?></option>
                                    </select>
                                </div>
                            </div>

                            <div class="form-row">
                                <div class="form-group">
                                    <label for="first_name"><?= t('create_first_name') ?> <span class="required">*</span></label>
                                    <input type="text" class="form-control" id="first_name" name="first_name" required>
                                </div>

                                <div class="form-group">
                                    <label for="last_name"><?= t('create_last_name') ?> <span class="required">*</span></label>
                                    <input type="text" class="form-control" id="last_name" name="last_name" required>
                                </div>
                            </div>

                            <div class="form-row">
                                <div class="form-group">
                                    <label for="birth_date"><?= t('create_birth_date') ?> <span class="required">*</span></label>
                                    <input type="date" class="form-control" id="birth_date" name="birth_date" required>
                                </div>

                                <div class="form-group">
                                    <label for="birth_place"><?= t('create_birth_place') ?> <span class="required">*</span></label>
                                    <input type="text" class="form-control" id="birth_place" name="birth_place" required>
                                </div>
                            </div>

                            <div class="form-row">
                                <div class="form-group">
                                    <label for="death_date"><?= t('create_death_date') ?> <span class="required">*</span></label>
                                    <input type="date" class="form-control" id="death_date" name="death_date" required>
                                </div>

                                <div class="form-group">
                                    <label for="death_place"><?= t('create_death_place') ?> <span class="required">*</span></label>
                                    <input type="text" class="form-control" id="death_place" name="death_place" required>
                                </div>
                            </div>

                            <div class="form-row">
                                <div class="form-group">
                                    <label for="country"><?= t('create_lived_country') ?> <span class="required">*</span></label>
                                    <select class="form-control" id="country" name="country" required>
                                        <option value=""><?= t('create_loading_countries') ?></option>
                                    </select>
                                </div>

                                <div class="form-group">
                                    <label for="lived_place_search"><?= t('create_lived_place') ?> <span class="required">*</span></label>
                                    <input type="text" class="form-control" id="lived_place_search"
                                        placeholder="<?= htmlspecialchars(t('create_type_to_search_city'), ENT_QUOTES, 'UTF-8') ?>" autocomplete="off">
                                    <select class="form-control city-list" id="lived_place_list" size="6" disabled></select>
                                    <div class="form-hint"><?= t('create_city_hint') ?></div>
                                </div>
                            </div>

                            <div class="form-row">
                                <div class="form-group">
                                    <label for="address"><?= t('create_address') ?> <span class="required">*</span></label>
                                    <input type="text" class="form-control" id="address" name="address" required
                                        placeholder="<?= htmlspecialchars(t('create_address_placeholder'), ENT_QUOTES, 'UTF-8') ?>">
                                </div>

                                <div class="form-group">
                                    <label for="other_countries"><?= t('create_other_countries') ?></label>
                                    <input type="text" class="form-control" id="other_countries" name="other_countries"
                                        placeholder="<?= htmlspecialchars(t('create_other_countries_placeholder'), ENT_QUOTES, 'UTF-8') ?>">
                                </div>
                            </div>

                            <div class="form-group full-width">
                                <label for="bio"><?= t('create_biography') ?></label>
                                <textarea class="form-control" id="bio" name="bio" rows="5"
                                    placeholder="<?= htmlspecialchars(t('create_bio_placeholder'), ENT_QUOTES, 'UTF-8') ?>"></textarea>
                                <div class="form-hint"><?= t('create_bio_hint') ?></div>
                            </div>

                            <div class="create-memorial-card inner-card">
                                <h2 class="card-title"><?= t('create_photo_title') ?></h2>

                                <div class="photo-upload-container" id="uploadContainer">
                                    <i class="fas fa-cloud-upload-alt"></i>
                                    <h3><?= t('create_upload_photo_heading') ?></h3>
                                    <p><?= t('create_upload_photo_text') ?></p>
                                    <div class="upload-btn" id="choosePhotoBtn">
                                        <i class="fas fa-upload"></i> <?= t('create_choose_photo') ?>
                                    </div>
                                    <input type="file" id="photoUpload" name="cover_image"
                                        accept=".jpg,.jpeg,.png,.webp,image/jpeg,image/png,image/webp"
                                        style="display:none;">
                                </div>

                                <div id="uploadedPhotoContainer" style="display:none;">
                                    <div class="uploaded-photo">
                                        <img id="uploadedImage" src="" alt="<?= htmlspecialchars(t('create_uploaded_photo_alt'), ENT_QUOTES, 'UTF-8') ?>">
                                        <div class="uploaded-photo-info">
                                            <div class="uploaded-photo-name" id="photoName">Photo.jpg</div>
                                            <div class="uploaded-photo-size" id="photoSize">2.4 MB</div>
                                        </div>
                                        <div class="remove-photo" id="removePhotoBtn">
                                            <i class="fas fa-times"></i>
                                        </div>
                                    </div>
                                </div>

                                <div class="form-hint"><?= t('create_photo_hint') ?></div>
                            </div>

                            <div class="create-actions next-only">
                                <button type="button" class="submit-btn" id="btnNext1">
                                    <i class="fas fa-arrow-right"></i> <?= t('create_next_your_details') ?>
                                </button>
                            </div>
                        </div>

                        <div class="step-panel" id="step-2">
                            <h2 class="card-title"><?= t('create_step2_title') ?></h2>

                            <div class="form-row">
                                <div class="form-group">
                                    <label for="contact_name"><?= t('create_your_full_name') ?> <span class="required">*</span></label>
                                    <input type="text" class="form-control" id="contact_name" name="contact_name" required>
                                </div>

                                <div class="form-group">
                                    <label for="phone"><?= t('create_phone_number') ?> <span class="required">*</span></label>
                                    <div class="phone-grid">
                                        <select class="form-control" id="phone_code" name="phone_code" required>
                                            <option value=""><?= t('create_code') ?></option>
                                        </select>
                                        <input type="text" class="form-control" id="phone" name="phone" required
                                            placeholder="<?= htmlspecialchars(t('create_enter_phone_number'), ENT_QUOTES, 'UTF-8') ?>">
                                    </div>
                                </div>
                            </div>

                            <div class="form-row">
                                <div class="form-group">
                                    <label for="phone_alt"><?= t('create_alt_phone') ?></label>
                                    <div class="phone-grid">
                                        <select class="form-control" id="phone_alt_code" name="phone_alt_code">
                                            <option value=""><?= t('create_code') ?></option>
                                        </select>
                                        <input type="text" class="form-control" id="phone_alt" name="phone_alt"
                                            placeholder="<?= htmlspecialchars(t('create_enter_alt_number'), ENT_QUOTES, 'UTF-8') ?>">
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label for="id_type"><?= t('create_id_type') ?> <span class="required">*</span></label>
                                    <select class="form-control" id="id_type" name="id_type" required>
                                        <option value=""><?= t('create_select_id_type') ?></option>
                                    </select>
                                </div>
                            </div>

                            <div class="form-row">
                                <div class="form-group full-width">
                                    <label for="id_number"><?= t('create_id_number') ?> <span class="required">*</span></label>
                                    <input type="text" class="form-control" id="id_number" name="id_number" required>
                                </div>
                            </div>

                            <div id="nicFields" style="display:none;">
                                <div class="form-row">
                                    <div class="form-group">
                                        <label for="nic_front"><?= t('create_nic_front') ?> <span class="required">*</span></label>
                                        <input type="file" class="form-control" id="nic_front" name="nic_front"
                                            accept=".jpg,.jpeg,.png,.webp,image/jpeg,image/png,image/webp">
                                    </div>

                                    <div class="form-group">
                                        <label for="nic_back"><?= t('create_nic_back') ?> <span class="required">*</span></label>
                                        <input type="file" class="form-control" id="nic_back" name="nic_back"
                                            accept=".jpg,.jpeg,.png,.webp,image/jpeg,image/png,image/webp">
                                    </div>
                                </div>
                            </div>

                            <div id="passportFields" style="display:none;">
                                <div class="form-row">
                                    <div class="form-group full-width">
                                        <label for="passport_image"><?= t('create_passport_image') ?> <span class="required">*</span></label>
                                        <input type="file" class="form-control" id="passport_image"
                                            name="passport_image"
                                            accept=".jpg,.jpeg,.png,.webp,image/jpeg,image/png,image/webp">
                                    </div>
                                </div>
                            </div>

                            <div class="create-actions">
                                <button type="button" class="back-btn" id="btnBack1">
                                    <i class="fas fa-arrow-left"></i> <?= t('create_back') ?>
                                </button>

                                <button type="submit" class="submit-btn" id="finalSubmitBtn">
                                    <i class="fas fa-plus-circle"></i> <?= t('create_submit_continue') ?>
                                </button>
                            </div>
                        </div>
                    </form>
                </div>

            </div>
        </div>
    </div>

    <div id="footer-placeholder"></div>

    <div id="startPopup" class="popup-overlay">
        <div class="popup-box">
            <h2><?= t('create_start_popup_title') ?></h2>
            <p><?= t('create_start_popup_text') ?></p>
            <div class="popup-buttons">
                <button id="btnCallTeam" class="btn-outline popup-btn"><?= t('create_call_team') ?></button>
                <button id="btnFillManual" class="btn-primary popup-btn"><?= t('create_fill_manually') ?></button>
            </div>
        </div>
    </div>

    <div id="captchaPopup" class="popup-overlay" style="display:none;">
        <div class="popup-box">
            <h2><?= t('create_quick_verification') ?></h2>
            <p><?= t('create_quick_verification_text') ?></p>
            <div class="captcha-block">
                <div class="captcha-question" id="captchaQ"><?= t('common_loading') ?></div>
                <input id="captchaInput" type="text" inputmode="numeric" maxlength="4" class="form-control otp-input"
                    placeholder="<?= htmlspecialchars(t('create_answer'), ENT_QUOTES, 'UTF-8') ?>">
                <div class="form-hint error-text" id="captchaErr" style="display:none;"></div>
            </div>
            <div class="popup-buttons">
                <button id="captchaCancelBtn" class="btn-outline popup-btn"><?= t('common_cancel') ?></button>
                <button id="captchaOkBtn" class="btn-primary popup-btn"><?= t('create_continue') ?></button>
            </div>
        </div>
    </div>

    <div id="otpPopup" class="popup-overlay" style="display:none;">
        <div class="popup-box">
            <h2><?= t('create_verify_number') ?></h2>
            <p id="otpInfoText">
                <?= t('create_otp_info_text') ?>
            </p>

            <div class="captcha-block">
                <input id="otpCodeInput" type="text" maxlength="6" inputmode="numeric" class="form-control otp-input"
                    placeholder="------">
                <div class="form-hint" id="otpTimerLabel"><?= t('create_time_remaining') ?>: <span id="otpTimer">60</span> <?= t('contact_seconds') ?></div>
                <div class="form-hint error-text" id="otpError" style="display:none;"></div>
            </div>

            <div class="popup-buttons">
                <button id="otpCancelBtn" class="btn-outline popup-btn"><?= t('common_cancel') ?></button>
                <button id="otpVerifyBtn" class="btn-primary popup-btn"><?= t('contact_verify') ?></button>
            </div>
        </div>
    </div>

    <div class="toast-wrap" id="toastWrap"></div>

    <script src="script/common.js"></script>
    <script src="script/navbar.js"></script>
    <script src="script/translator.js"></script>
    <script>
        loadComponent('navbar.php?page=create.php', 'navbar-placeholder');
        loadComponent('footer.php', 'footer-placeholder');
    </script>
    <script src="script/create.js"></script>
</body>

</html>