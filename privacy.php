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
    <title>Privacy Policy - FuneralNotice.lk</title>
    <meta name="description" content="Read the Privacy Policy of FuneralNotice.lk.">

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
                <h1 class="legal-title">Privacy Policy</h1>
                <p class="legal-subtitle">
                    This page explains how FuneralNotice.lk collects, uses, stores, and protects user information.
                </p>
            </div>
        </section>

        <div class="legal-content-wrap">
            <div class="legal-content">
                <div class="legal-top-line"></div>
                <div class="legal-body">
                    <p class="legal-intro">
                        We respect your privacy and take reasonable steps to protect the information you provide while using our website and services.
                    </p>

                    <section class="legal-section">
                        <h2>1. Information We Collect</h2>
                        <p>
                            We may collect personal details such as your name, email address, phone number, memorial content, tribute submissions, and other information you choose to provide.
                        </p>
                    </section>

                    <section class="legal-section">
                        <h2>2. How We Use Information</h2>
                        <p>
                            We use submitted information to provide website services, manage memorial content, communicate with users, improve platform performance, and support administration and security.
                        </p>
                    </section>

                    <section class="legal-section">
                        <h2>3. Public and Private Data</h2>
                        <p>
                            Some memorial and tribute content may be displayed publicly on the website. Private contact details are handled more carefully and are not intended for public display unless required.
                        </p>
                    </section>

                    <section class="legal-section">
                        <h2>4. Data Protection</h2>
                        <p>
                            We apply reasonable safeguards to reduce the risk of unauthorized access, misuse, alteration, or disclosure. However, no online system can guarantee complete security.
                        </p>
                    </section>

                    <section class="legal-section">
                        <h2>5. Sharing of Information</h2>
                        <p>
                            We do not sell personal information. Data may only be shared where needed for service delivery, legal compliance, technical operation, or protection of the website and its users.
                        </p>
                    </section>

                    <section class="legal-section">
                        <h2>6. Third-Party Services</h2>
                        <p>
                            Some hosting, payment, messaging, analytics, or embedded tools may process limited user information as part of their technical role in supporting the website.
                        </p>
                    </section>

                    <section class="legal-section">
                        <h2>7. Cookies and Related Technology</h2>
                        <p>
                            We may use cookies and similar technologies to improve performance, remember preferences, and support session functions. Read our <a href="cookies.php">Cookie Policy</a> for more details.
                        </p>
                    </section>

                    <section class="legal-section">
                        <h2>8. Policy Updates</h2>
                        <p>
                            This Privacy Policy may be revised when needed. Continued use of the website after updates means you accept the revised version.
                        </p>
                    </section>

                    <section class="legal-section">
                        <h2>9. Contact</h2>
                        <p>
                            For questions about privacy or data handling, please use the <a href="contact.php">Contact Us</a> page.
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