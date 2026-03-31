<?php
declare(strict_types=1);
require_once __DIR__ . '/translator/language.php';

$currentPage = trim((string)($_GET['page'] ?? ''));
if ($currentPage === '') {
    $currentPage = basename($_SERVER['PHP_SELF'] ?? '');
}

$isIndexPage = ($currentPage === 'index');
$lang = current_lang();
?>
<!-- Top Navigation - Hidden on Mobile -->
<div class="header-top">
    <div class="container">
        <nav class="top-nav">
            <ul>
                <li><a href="index.php"><i class="fas fa-home"></i> <?= t('nav_home') ?></a></li>
                <li><a href="about.php"><i class="fas fa-info-circle"></i> <?= t('nav_about') ?></a></li>
                <li><a href="contact.php"><i class="fas fa-envelope"></i> <?= t('nav_contact') ?></a></li>
                <li class="has-dropdown">
                    <a href="javascript:void(0)"><i class="fas fa-globe"></i> <?= t('nav_pages') ?> <i class="fas fa-chevron-down dropdown-arrow"></i></a>
                    <ul class="dropdown-menu">
                        <li><a href="https://ripnews.lk" target="_blank">Ripnews.lk</a></li>
                        <li><a href="https://ripnotice.lk" target="_blank">Ripnotice.lk</a></li>
                        <li><a href="https://funeralnews.lk" target="_blank">Funeralnews.lk</a></li>
                        <li><a href="https://digitalnotice.lk" target="_blank">Digitalnotice.lk</a></li>
                    </ul>
                </li>

            </ul>
        </nav>

        <div class="lang-select-wrap">
            <select class="language-selector" data-lang-switcher>
                <option value="en" <?= $lang === 'en' ? 'selected' : '' ?>>English</option>
                <option value="ta" <?= $lang === 'ta' ? 'selected' : '' ?>>தமிழ்</option>
                <option value="si" <?= $lang === 'si' ? 'selected' : '' ?>>සිංහල</option>
            </select>
        </div>
    </div>
</div>

<!-- Mobile App Header -->
<div class="mobile-app-header">
    <div class="container">
        <div class="mobile-header-left">
            <img src="funeral notice logo.png" alt="FuneralNotice.lk" class="mobile-logo">
        </div>

        <div class="mobile-header-right">
            <div class="mobile-lang-select-wrap">
                <select class="mobile-language-selector" data-lang-switcher>
                    <option value="en" <?= $lang === 'en' ? 'selected' : '' ?>>EN</option>
                    <option value="ta" <?= $lang === 'ta' ? 'selected' : '' ?>>TA</option>
                    <option value="si" <?= $lang === 'si' ? 'selected' : '' ?>>SI</option>
                </select>
            </div>

            <?php if ($isIndexPage): ?>
                <div class="mobile-header-icon search-icon" id="mobileSearchIcon">
                    <i class="fas fa-search"></i>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Main Header -->
<header>
    <div class="container">
        <div class="main-header">
            <div class="header-container">
                <button class="mobile-menu-btn" id="mobileMenuBtn">
                    <i class="fas fa-bars"></i>
                </button>

                <?php if ($isIndexPage): ?>
                    <button class="mobile-search-toggle" id="mobileSearchToggle">
                        <i class="fas fa-search"></i>
                    </button>
                <?php endif; ?>

                <div class="logo-container">
                    <img src="funeral notice logo.png" alt="FuneralNotice.lk" class="logo-img">
                </div>

                <div class="header-actions">
                    <?php if ($isIndexPage): ?>
                        <div class="search-box live-search-wrap">
                            <i class="fas fa-search"></i>
                            <input
                                type="text"
                                placeholder="<?= htmlspecialchars(t('nav_search_placeholder'), ENT_QUOTES, 'UTF-8') ?>"
                                id="desktopSearchInput"
                                autocomplete="off">
                            <div class="live-search-results" id="desktopSearchResults"></div>
                        </div>
                    <?php endif; ?>

                    <a class="btn btn-primary" href="create.php" id="createFuneralNoticeBtn">
                        <i class="fas fa-plus"></i> <span class="hide-on-mobile"><?= t('nav_create') ?></span>
                    </a>

                    <button class="btn btn-outline hide-on-mobile" onclick="window.location.href='index.php'">
                        <?= t('nav_browse') ?>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <?php if ($isIndexPage): ?>
        <div class="mobile-search-container" id="mobileSearchContainer">
            <div class="container">
                <div class="mobile-search-box live-search-wrap">
                    <i class="fas fa-search"></i>
                    <input
                        type="text"
                        placeholder="<?= htmlspecialchars(t('nav_search_placeholder'), ENT_QUOTES, 'UTF-8') ?>"
                        id="mobileSearchInput"
                        autocomplete="off">
                    <div class="live-search-results" id="mobileSearchResults"></div>
                </div>
            </div>
        </div>
    <?php endif; ?>
</header>

<!-- Contact Info - Only shown on mobile -->
<div class="contact-info">
    <div class="container contact-container">
        <div class="contact-display">
            <i class="fas fa-phone"></i>
            <span class="phone-number">+94 11 234 5678</span>
            <span class="badge">24/7</span>
        </div>
        <div>
            <a href="#" class="whatsapp-btn">
                <i class="fab fa-whatsapp"></i> <?= t('nav_chat_whatsapp') ?>
            </a>
        </div>
    </div>
</div>

<div class="mobile-menu-overlay" id="mobileMenuOverlay"></div>
<div class="mobile-menu-container" id="mobileMenuContainer">
    <div class="mobile-menu-header">
        <div class="mobile-menu-title">
            <h3><i class="fas fa-compass"></i> <?= t('nav_menu') ?></h3>
        </div>
        <button class="mobile-menu-close" id="mobileMenuClose">
            <i class="fas fa-times"></i>
        </button>
    </div>

    <div class="mobile-menu-content">
        <div class="mobile-menu-section">
            <h4><i class="fas fa-compass"></i> <?= t('nav_navigation') ?></h4>
            <ul class="mobile-menu-nav">
                <li><a href="index.php" class="<?= $currentPage === 'index.php' ? 'active' : '' ?>"><i class="fas fa-home"></i> <?= t('nav_home') ?></a></li>
                <li><a href="about.php" class="<?= $currentPage === 'about.php' ? 'active' : '' ?>"><i class="fas fa-info-circle"></i> <?= t('nav_about') ?></a></li>
                <li><a href="create.php" class="<?= $currentPage === 'create.php' ? 'active' : '' ?>"><i class="fas fa-plus-circle"></i> <?= t('nav_create') ?></a></li>
                <li><a href="gallery.php" class="<?= $currentPage === 'gallery.php' ? 'active' : '' ?>"><i class="fas fa-images"></i> <?= t('nav_gallery') ?></a></li>
                <li><a href="contact.php" class="<?= $currentPage === 'contact.php' ? 'active' : '' ?>"><i class="fas fa-envelope"></i> <?= t('nav_contact') ?></a></li>
            </ul>
        </div>

        <div class="advertise-cta">
            <p><?= t('nav_advertise_question') ?></p>
            <a href="https://wa.me/94754727075" target="_blank" class="advertise-btn">
                <i class="fab fa-whatsapp"></i> <?= t('nav_contact_ads') ?>
            </a>
        </div>
    </div>
</div>

