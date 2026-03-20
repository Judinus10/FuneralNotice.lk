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
    <title>Terms and Conditions - FuneralNotice.lk</title>
    <meta name="description" content="Read the Terms and Conditions of FuneralNotice.lk.">

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
                <h1 class="legal-title">Terms and Conditions</h1>
                <p class="legal-subtitle">
                    Please read these terms carefully before using FuneralNotice.lk and any of its memorial, tribute, or related services.
                </p>
            </div>
        </section>

        <div class="legal-content-wrap">
            <div class="legal-content">
                <div class="legal-top-line"></div>
                <div class="legal-body">
                    <p class="legal-intro">
                        By using FuneralNotice.lk, you agree to follow these terms. If you do not agree with them, please do not use this website or any related services.
                    </p>

                    <section class="legal-section">
                        <h2>1. Acceptance of Terms</h2>
                        <p>
                            These terms apply to all visitors, users, and anyone submitting memorials, tributes, messages, or related content through the website.
                        </p>
                    </section>

                    <section class="legal-section">
                        <h2>2. Service Purpose</h2>
                        <p>
                            FuneralNotice.lk provides a platform for obituary notices, memorial pages, remembrance content, tribute submissions, and related communication services.
                        </p>
                    </section>

                    <section class="legal-section">
                        <h2>3. User Responsibilities</h2>
                        <p>
                            Users must provide respectful, accurate, and lawful content. You must not submit false, harmful, abusive, misleading, or unauthorized material through the website.
                        </p>
                    </section>

                    <section class="legal-section">
                        <h2>4. Content Submission</h2>
                        <p>
                            By submitting content, you confirm that you have the right to share it and that it does not violate any law, privacy right, copyright, or third-party interest.
                        </p>
                    </section>

                    <section class="legal-section">
                        <h2>5. Content Review and Removal</h2>
                        <p>
                            We may review, reject, edit, or remove any content that appears inappropriate, inaccurate, offensive, unlawful, or harmful to the website or its users.
                        </p>
                    </section>

                    <section class="legal-section">
                        <h2>6. Service Availability</h2>
                        <p>
                            We aim to keep the website available and working properly, but we do not guarantee uninterrupted access, error-free performance, or permanent availability of every feature.
                        </p>
                    </section>

                    <section class="legal-section">
                        <h2>7. Limitation of Liability</h2>
                        <p>
                            FuneralNotice.lk is not responsible for losses, delays, technical interruptions, user-submitted errors, or third-party service issues arising from the use of the website.
                        </p>
                    </section>

                    <section class="legal-section">
                        <h2>8. Changes to Terms</h2>
                        <p>
                            These terms may be updated from time to time. Continued use of the website after changes means you accept the revised terms.
                        </p>
                    </section>

                    <section class="legal-section">
                        <h2>9. Contact</h2>
                        <p>
                            If you have questions about these Terms and Conditions, please use the <a href="contact.php">Contact Us</a> page.
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
        loadComponent('navbar.php?page=terms.php', 'navbar-placeholder');
        loadComponent('footer.php', 'footer-placeholder');
    </script>
</body>
</html>