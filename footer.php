<?php
declare(strict_types=1);
require_once __DIR__ . '/translator/language.php';
?>

<!-- Footer -->
<footer class="hide-on-mobile">
    <div class="container">
        <div class="footer-content">
            <div class="footer-logo">
                <img src="funeral notice logo.png" alt="FuneralNotice.lk" class="logo-img">
            </div>

            <div class="footer-tagline">
                <?= htmlspecialchars(t('footer_tagline'), ENT_QUOTES, 'UTF-8') ?>
            </div>

            <div class="footer-links">
                <a href="about.php"><?= htmlspecialchars(t('nav_about'), ENT_QUOTES, 'UTF-8') ?></a>
                <a href="terms.php"><?= htmlspecialchars(t('footer_terms'), ENT_QUOTES, 'UTF-8') ?></a>
                <a href="#"><?= htmlspecialchars(t('footer_report_us'), ENT_QUOTES, 'UTF-8') ?></a>
                <a href="privacy.php"><?= htmlspecialchars(t('footer_privacy_policy'), ENT_QUOTES, 'UTF-8') ?></a>
                <a href="cookies.php"><?= htmlspecialchars(t('footer_cookie_policy'), ENT_QUOTES, 'UTF-8') ?></a>
            </div>

            <div class="copyright">
                © <?= date('Y') ?> FuneralNotice.lk. <?= htmlspecialchars(t('footer_all_rights_reserved'), ENT_QUOTES, 'UTF-8') ?>
            </div>
        </div>
    </div>
</footer>

<!-- Mobile Bottom Navigation -->
<div class="mobile-bottom-nav">
    <div class="nav-container">
        <div class="mobile-nav-item" data-page="home" onclick="window.location.href='index.php'">
            <i class="fas fa-home"></i>
            <span class="nav-label"><?= htmlspecialchars(t('nav_home'), ENT_QUOTES, 'UTF-8') ?></span>
        </div>

        <div class="mobile-nav-item" data-page="about" onclick="window.location.href='about.php'">
            <i class="fas fa-info-circle"></i>
            <span class="nav-label"><?= htmlspecialchars(t('nav_about'), ENT_QUOTES, 'UTF-8') ?></span>
        </div>

        <div class="mobile-nav-item" data-page="contact" onclick="window.location.href='contact.php'">
            <i class="fas fa-envelope"></i>
            <span class="nav-label"><?= htmlspecialchars(t('nav_contact'), ENT_QUOTES, 'UTF-8') ?></span>
        </div>

        <div class="mobile-nav-item" data-page="whatsapp" onclick="window.open('https://wa.me/94711234567', '_blank')">
            <i class="fab fa-whatsapp"></i>
            <span class="nav-label"><?= htmlspecialchars(t('footer_whatsapp'), ENT_QUOTES, 'UTF-8') ?></span>
        </div>

        <div class="mobile-nav-item" data-page="menu" id="mobileBottomMenuBtn">
            <i class="fas fa-bars"></i>
            <span class="nav-label"><?= htmlspecialchars(t('nav_menu'), ENT_QUOTES, 'UTF-8') ?></span>
        </div>
    </div>
</div>

<!-- Mobile Create Notice Button -->
<a href="create.php"
   class="mobile-create-notice-float"
   id="mobileCreateNoticeBtn"
   aria-label="<?= htmlspecialchars(t('nav_create'), ENT_QUOTES, 'UTF-8') ?>">
    <i class="fas fa-plus"></i>
</a>