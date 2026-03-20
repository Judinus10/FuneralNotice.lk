<?php
declare(strict_types=1);
session_start();
require_once __DIR__ . '/translator/language.php';
?>
<!DOCTYPE html>
<html lang="<?= htmlspecialchars(current_lang(), ENT_QUOTES, 'UTF-8') ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars(t('privacy_page_title'), ENT_QUOTES, 'UTF-8') ?></title>
    <meta name="description" content="<?= htmlspecialchars(t('privacy_meta_description'), ENT_QUOTES, 'UTF-8') ?>">

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="style/common.css">
    <link rel="stylesheet" href="style/navbar.css">
    <link rel="stylesheet" href="style/footer.css">
    <link rel="stylesheet" href="style/legal-pages.css">
</head>
<body>
    <div id="navbar-placeholder"></div>

    <main class="legal-page">
        <section class="legal-hero">
            <div class="legal-hero-inner">
                <h1 class="legal-title"><?= htmlspecialchars(t('privacy_hero_title'), ENT_QUOTES, 'UTF-8') ?></h1>
                <p class="legal-subtitle"><?= htmlspecialchars(t('privacy_hero_subtitle'), ENT_QUOTES, 'UTF-8') ?></p>
            </div>
        </section>

        <div class="legal-content-wrap">
            <div class="legal-content">
                <div class="legal-top-line"></div>
                <div class="legal-body">
                    <p class="legal-intro"><?= htmlspecialchars(t('privacy_intro'), ENT_QUOTES, 'UTF-8') ?></p>

                    <section class="legal-section">
                        <h2><?= htmlspecialchars(t('privacy_section_1_title'), ENT_QUOTES, 'UTF-8') ?></h2>
                        <p><?= htmlspecialchars(t('privacy_section_1_text'), ENT_QUOTES, 'UTF-8') ?></p>
                    </section>

                    <section class="legal-section">
                        <h2><?= htmlspecialchars(t('privacy_section_2_title'), ENT_QUOTES, 'UTF-8') ?></h2>
                        <p><?= htmlspecialchars(t('privacy_section_2_text'), ENT_QUOTES, 'UTF-8') ?></p>
                    </section>

                    <section class="legal-section">
                        <h2><?= htmlspecialchars(t('privacy_section_3_title'), ENT_QUOTES, 'UTF-8') ?></h2>
                        <p><?= htmlspecialchars(t('privacy_section_3_text'), ENT_QUOTES, 'UTF-8') ?></p>
                    </section>

                    <section class="legal-section">
                        <h2><?= htmlspecialchars(t('privacy_section_4_title'), ENT_QUOTES, 'UTF-8') ?></h2>
                        <p><?= htmlspecialchars(t('privacy_section_4_text'), ENT_QUOTES, 'UTF-8') ?></p>
                    </section>

                    <section class="legal-section">
                        <h2><?= htmlspecialchars(t('privacy_section_5_title'), ENT_QUOTES, 'UTF-8') ?></h2>
                        <p><?= htmlspecialchars(t('privacy_section_5_text'), ENT_QUOTES, 'UTF-8') ?></p>
                    </section>

                    <section class="legal-section">
                        <h2><?= htmlspecialchars(t('privacy_section_6_title'), ENT_QUOTES, 'UTF-8') ?></h2>
                        <p><?= htmlspecialchars(t('privacy_section_6_text'), ENT_QUOTES, 'UTF-8') ?></p>
                    </section>

                    <section class="legal-section">
                        <h2><?= htmlspecialchars(t('privacy_section_7_title'), ENT_QUOTES, 'UTF-8') ?></h2>
                        <p>
                            <?= htmlspecialchars(t('privacy_section_7_text_before_link'), ENT_QUOTES, 'UTF-8') ?>
                            <a href="cookies.php"><?= htmlspecialchars(t('privacy_section_7_text_link'), ENT_QUOTES, 'UTF-8') ?></a>
                            <?= htmlspecialchars(t('privacy_section_7_text_after_link'), ENT_QUOTES, 'UTF-8') ?>
                        </p>
                    </section>

                    <section class="legal-section">
                        <h2><?= htmlspecialchars(t('privacy_section_8_title'), ENT_QUOTES, 'UTF-8') ?></h2>
                        <p><?= htmlspecialchars(t('privacy_section_8_text'), ENT_QUOTES, 'UTF-8') ?></p>
                    </section>

                    <section class="legal-section">
                        <h2><?= htmlspecialchars(t('privacy_section_9_title'), ENT_QUOTES, 'UTF-8') ?></h2>
                        <p>
                            <?= htmlspecialchars(t('privacy_section_9_text_before_link'), ENT_QUOTES, 'UTF-8') ?>
                            <a href="contact.php"><?= htmlspecialchars(t('privacy_section_9_text_link'), ENT_QUOTES, 'UTF-8') ?></a>
                            <?= htmlspecialchars(t('privacy_section_9_text_after_link'), ENT_QUOTES, 'UTF-8') ?>
                        </p>
                    </section>
                </div>
            </div>
        </div>
    </main>

    <div id="footer-placeholder"></div>

    <script src="script/common.js"></script>
    <script src="script/navbar.js"></script>
    <script src="script/translator.js"></script>
    <script>
        loadComponent('navbar.php?page=privacy.php', 'navbar-placeholder');
        loadComponent('footer.php', 'footer-placeholder');
    </script>
</body>
</html>