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
$messageLabel = $TRIBUTE_META['message_label'] ?? 'Message';
$messagePlaceholder = $TRIBUTE_META['message_placeholder'] ?? 'Write your tribute message';
$helperText = $TRIBUTE_META['helper_text'] ?? 'Share your condolences respectfully.';
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
                <form id="tributeEntryForm" class="tribute-form">
                    <input type="hidden" name="csrf" value="<?= h($csrf) ?>">
                    <input type="hidden" name="post_id" value="<?= (int)$postId ?>">
                    <input type="hidden" name="tribute_slug" value="<?= h($slug) ?>">

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
                            <input type="text" id="by_country" name="by_country" maxlength="120" placeholder="Sri Lanka, UK, Canada...">
                        </div>

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
                                <small>These links will be stored in extra data. Render them later when you build the full photo tribute display.</small>
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
        ], JSON_UNESCAPED_SLASHES) ?>;
    </script>
    <script src="../script/tribute-common.js"></script>
</body>
</html>