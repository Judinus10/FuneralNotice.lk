<?php
declare(strict_types=1);
require_once __DIR__ . '/translator/language.php';
?>
<!DOCTYPE html>
<html lang="<?= htmlspecialchars(current_lang(), ENT_QUOTES, 'UTF-8') ?>">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars(t('about_page_title'), ENT_QUOTES, 'UTF-8') ?></title>
    <meta name="description"
        content="<?= htmlspecialchars(t('about_meta_description'), ENT_QUOTES, 'UTF-8') ?>">

    <!-- Open Graph Tags -->
    <meta property="og:type" content="website">
    <meta property="og:url" content="https://funeralnotice.lk/about.php">
    <meta property="og:title" content="<?= htmlspecialchars(t('about_page_title'), ENT_QUOTES, 'UTF-8') ?>">
    <meta property="og:description" content="<?= htmlspecialchars(t('about_og_description'), ENT_QUOTES, 'UTF-8') ?>">
    <meta property="og:image" content="https://ripnews.lk/uploads/posts/76/cover.png">
    <meta property="og:image:type" content="image/png">
    <meta property="og:image:width" content="1200">
    <meta property="og:image:height" content="630">
    <meta property="og:site_name" content="FuneralNotice.lk">

    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="<?= htmlspecialchars(t('about_page_title'), ENT_QUOTES, 'UTF-8') ?>">
    <meta name="twitter:description" content="<?= htmlspecialchars(t('about_og_description'), ENT_QUOTES, 'UTF-8') ?>">
    <meta name="twitter:image" content="https://ripnews.lk/uploads/posts/76/cover.png">

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css">

    <link rel="stylesheet" href="style/common.css">
    <link rel="stylesheet" href="style/navbar.css">
    <link rel="stylesheet" href="style/footer.css">
    <link rel="stylesheet" href="style/about.css">
</head>

<body>
    <div id="navbar-placeholder"></div>

    <div class="main-body">
        <section class="mission-section">
            <div class="container">
                <div class="section-header fade-in">
                    <h2 class="section-title"><?= t('about_mission_title') ?></h2>
                    <p class="section-subtitle"><?= t('about_mission_subtitle') ?></p>
                </div>

                <div class="mission-grid">
                    <div class="mission-card stagger-item">
                        <div class="mission-icon">
                            <i class="fas fa-heart-circle-check"></i>
                        </div>
                        <h3><?= t('about_preserve_title') ?></h3>
                        <p><?= t('about_preserve_text') ?></p>
                    </div>

                    <div class="mission-card stagger-item">
                        <div class="mission-icon">
                            <i class="fas fa-hand-holding-heart"></i>
                        </div>
                        <h3><?= t('about_support_title') ?></h3>
                        <p><?= t('about_support_text') ?></p>
                    </div>

                    <div class="mission-card stagger-item">
                        <div class="mission-icon">
                            <i class="fas fa-dove"></i>
                        </div>
                        <h3><?= t('about_honor_title') ?></h3>
                        <p><?= t('about_honor_text') ?></p>
                    </div>
                </div>
            </div>
        </section>

        <section class="values-section">
            <div class="container">
                <div class="section-header fade-in">
                    <h2 class="section-title"><?= t('about_values_title') ?></h2>
                    <p class="section-subtitle"><?= t('about_values_subtitle') ?></p>
                </div>

                <div class="values-grid">
                    <div class="value-card stagger-item">
                        <div class="value-icon-wrapper">
                            <i class="fas fa-hand-holding-heart"></i>
                        </div>
                        <h3><?= t('about_value_compassion_title') ?></h3>
                        <p><?= t('about_value_compassion_text') ?></p>
                    </div>

                    <div class="value-card stagger-item">
                        <div class="value-icon-wrapper">
                            <i class="fas fa-shield-alt"></i>
                        </div>
                        <h3><?= t('about_value_respect_title') ?></h3>
                        <p><?= t('about_value_respect_text') ?></p>
                    </div>

                    <div class="value-card stagger-item">
                        <div class="value-icon-wrapper">
                            <i class="fas fa-lock"></i>
                        </div>
                        <h3><?= t('about_value_privacy_title') ?></h3>
                        <p><?= t('about_value_privacy_text') ?></p>
                    </div>

                    <div class="value-card stagger-item">
                        <div class="value-icon-wrapper">
                            <i class="fas fa-award"></i>
                        </div>
                        <h3><?= t('about_value_excellence_title') ?></h3>
                        <p><?= t('about_value_excellence_text') ?></p>
                    </div>
                </div>
            </div>
        </section>
    </div>

    <div id="footer-placeholder"></div>

    <script src="script/common.js"></script>
    <script src="script/navbar.js"></script>
    <script src="script/translator.js"></script>
    <script>
        loadComponent('navbar.php?page=about.php', 'navbar-placeholder');
        loadComponent('footer.php', 'footer-placeholder');
    </script>
    <script src="script/about.js"></script>
</body>

</html>