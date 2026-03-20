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
    <title>Cookie Policy - FuneralNotice.lk</title>
    <meta name="description" content="Read the Cookie Policy of FuneralNotice.lk.">

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
                <h1 class="legal-title">Cookie Policy</h1>
                <p class="legal-subtitle">
                    This page explains how cookies and similar technologies may be used on FuneralNotice.lk.
                </p>
            </div>
        </section>

        <div class="legal-content-wrap">
            <div class="legal-content">
                <div class="legal-top-line"></div>
                <div class="legal-body">
                    <p class="legal-intro">
                        Cookies are small data files stored on your device to help websites function properly, remember your preferences, and improve your browsing experience.
                    </p>

                    <section class="legal-section">
                        <h2>1. What Cookies Are</h2>
                        <p>
                            Cookies are small text files placed on your browser or device when you visit a website. They help remember session details, technical settings, and basic user preferences.
                        </p>
                    </section>

                    <section class="legal-section">
                        <h2>2. Why We Use Cookies</h2>
                        <p>
                            We may use cookies to support core website functions, keep sessions active, remember language choices, improve performance, and maintain website security.
                        </p>
                    </section>

                    <section class="legal-section">
                        <h2>3. Types of Cookies</h2>
                        <p>
                            These may include essential cookies, session cookies, preference cookies, and limited analytics-related cookies where needed to improve functionality and reliability.
                        </p>
                    </section>

                    <section class="legal-section">
                        <h2>4. Third-Party Cookies</h2>
                        <p>
                            Some third-party services or embedded tools may place their own cookies as part of hosting, analytics, or technical service delivery.
                        </p>
                    </section>

                    <section class="legal-section">
                        <h2>5. Managing Cookies</h2>
                        <p>
                            You can manage, block, or delete cookies in your browser settings. But if essential cookies are disabled, some parts of the website may not work as expected.
                        </p>
                    </section>

                    <section class="legal-section">
                        <h2>6. Policy Updates</h2>
                        <p>
                            This Cookie Policy may be updated when website features, legal requirements, or technical processes change.
                        </p>
                    </section>

                    <section class="legal-section">
                        <h2>7. Contact</h2>
                        <p>
                            If you have questions about cookies or website tracking, please use the <a href="contact.php">Contact Us</a> page.
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
        loadComponent('navbar.php?page=cookies.php', 'navbar-placeholder');
        loadComponent('footer.php', 'footer-placeholder');
    </script>
</body>
</html>