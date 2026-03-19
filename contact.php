<?php
declare(strict_types=1);
session_start();
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact Us - FuneralNotice.lk</title>

    <meta property="og:type" content="website">
    <meta property="og:url" content="https://funeralnotice.lk/contact">
    <meta property="og:title" content="Contact Us – FuneralNotice.lk">
    <meta property="og:description"
        content="Contact our compassionate team for assistance with funeral notices and memorials.">
    <meta property="og:image" content="https://ripnews.lk/uploads/posts/76/cover.png">
    <meta property="og:image:type" content="image/png">
    <meta property="og:image:width" content="1200">
    <meta property="og:image:height" content="630">
    <meta property="og:site_name" content="FuneralNotice.lk">

    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="Contact Us – FuneralNotice.lk">
    <meta name="twitter:description"
        content="Contact our compassionate team for assistance with funeral notices and memorials.">
    <meta name="twitter:image" content="https://ripnews.lk/uploads/posts/76/cover.png">

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="style/common.css">
    <link rel="stylesheet" href="style/navbar.css">
    <link rel="stylesheet" href="style/footer.css">
    <link rel="stylesheet" href="style/contact.css">

    <style>
        .contact-page-loader {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 20px;
            margin: 30px 0 40px;
        }

        .contact-skeleton {
            height: 180px;
            border-radius: 18px;
            background: linear-gradient(90deg, #f1f5f9 25%, #f8fafc 50%, #f1f5f9 75%);
            background-size: 200% 100%;
            animation: shimmer 1.5s linear infinite;
        }

        @keyframes shimmer {
            0% { background-position: -200% 0; }
            100% { background-position: 200% 0; }
        }

        .hidden {
            display: none !important;
        }

        .contact-grid {
            margin-top: 20px;
        }

        .contact-detail-wrap {
            display: flex;
            flex-direction: column;
            gap: 8px;
            margin-top: 12px;
        }

        .contact-note {
            color: var(--light-text);
            font-size: .95rem;
            line-height: 1.6;
        }

        .phone-row {
            display: grid;
            grid-template-columns: 120px minmax(0, 1fr);
            gap: 12px;
        }

        .form-control[disabled],
        .submit-btn[disabled] {
            opacity: .7;
            cursor: not-allowed;
        }

        .api-message {
            margin: 0 0 24px;
            padding: 14px 16px;
            border-radius: 12px;
            display: none;
            font-weight: 600;
        }

        .api-message.show {
            display: block;
        }

        .api-message.success {
            background: #ecfdf5;
            color: #166534;
            border: 1px solid #bbf7d0;
        }

        .api-message.error {
            background: #fef2f2;
            color: #b91c1c;
            border: 1px solid #fecaca;
        }

        .popup-overlay {
            position: fixed;
            inset: 0;
            background: rgba(15, 23, 42, 0.45);
            display: none;
            align-items: center;
            justify-content: center;
            z-index: 99999;
            padding: 15px;
        }

        .popup-overlay.show {
            display: flex;
        }

        .popup-box {
            background: #fff;
            width: 100%;
            max-width: 420px;
            border-radius: 22px;
            padding: 24px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.25);
            text-align: center;
        }

        .popup-title {
            font-size: 1.35rem;
            font-weight: 800;
            color: var(--secondary);
            margin-bottom: 8px;
        }

        .popup-subtitle {
            color: var(--light-text);
            margin-bottom: 18px;
        }

        .otp-input {
            width: 100%;
            height: 56px;
            border: 1px solid var(--border);
            border-radius: 14px;
            text-align: center;
            font-size: 1.6rem;
            letter-spacing: .45em;
            padding-left: .45em;
            outline: none;
            margin-bottom: 12px;
        }

        .otp-input:focus {
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(255, 0, 0, 0.08);
        }

        .otp-timer {
            font-size: .92rem;
            color: var(--light-text);
            margin-bottom: 12px;
        }

        .otp-error {
            color: #b91c1c;
            font-size: .92rem;
            font-weight: 600;
            min-height: 22px;
            margin-bottom: 12px;
        }

        .otp-actions {
            display: flex;
            gap: 12px;
        }

        .otp-actions .submit-btn {
            flex: 1;
            justify-content: center;
        }

        .otp-cancel-btn {
            background: #fff;
            color: var(--secondary);
            border: 1px solid var(--border);
            box-shadow: none;
        }

        .toast-wrap {
            position: fixed;
            right: 18px;
            bottom: 18px;
            z-index: 100000;
            display: flex;
            flex-direction: column;
            gap: 10px;
        }

        .toast-item {
            min-width: 250px;
            max-width: 360px;
            padding: 14px 16px;
            border-radius: 12px;
            color: #fff;
            font-weight: 700;
            box-shadow: 0 16px 40px rgba(0, 0, 0, 0.18);
            opacity: 0;
            transform: translateY(10px);
            transition: .25s ease;
        }

        .toast-item.show {
            opacity: 1;
            transform: translateY(0);
        }

        .toast-success { background: #16a34a; }
        .toast-error { background: #dc2626; }
        .toast-info { background: #2563eb; }

        .country-list {
            margin-top: 14px;
            display: flex;
            flex-direction: column;
            gap: 10px;
        }

        .country-pill {
            background: #f8fafc;
            border: 1px solid var(--border);
            border-radius: 12px;
            padding: 12px 14px;
        }

        .country-pill strong {
            display: block;
            color: var(--secondary);
            margin-bottom: 4px;
        }

        .country-pill span {
            color: var(--primary);
            font-weight: 700;
        }

        @media (max-width: 768px) {
            .contact-page-loader {
                grid-template-columns: 1fr;
            }

            .phone-row {
                grid-template-columns: 1fr;
            }

            .otp-actions {
                flex-direction: column;
            }
        }
    </style>
</head>

<body>
    <div id="navbar-placeholder"></div>

    <div class="main-body">
        <div class="container">

            <div class="contact-page-loader" id="contactPageLoader">
                <div class="contact-skeleton"></div>
                <div class="contact-skeleton"></div>
                <div class="contact-skeleton"></div>
            </div>

            <div id="contactContent" class="hidden">
                <div class="contact-grid" id="contactInfoGrid"></div>

                <div class="contact-form-section">
                    <div class="section-header">
                        <h2 class="section-title">Send Us a Message</h2>
                        <p class="section-subtitle">
                            Fill out the form below and our team will get back to you as soon as possible.
                        </p>
                    </div>

                    <div id="formMessage" class="api-message"></div>

                    <form id="contactForm" novalidate>
                        <div class="form-grid">
                            <div class="form-group">
                                <label class="form-label" for="name">Full Name *</label>
                                <input type="text" id="name" name="name" class="form-control" placeholder="Your full name" required>
                            </div>
                            <div class="form-group">
                                <label class="form-label" for="email">Email Address *</label>
                                <input type="email" id="email" name="email" class="form-control" placeholder="Your email address" required>
                            </div>
                        </div>

                        <div class="form-grid">
                            <div class="form-group">
                                <label class="form-label" for="phone_code">Phone Number *</label>
                                <div class="phone-row">
                                    <select id="phone_code" name="phone_code" class="form-control"></select>
                                    <input type="tel" id="mobile" name="mobile" class="form-control" placeholder="Your phone number" required>
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="form-label" for="subject">Subject *</label>
                                <select id="subject" name="subject" class="form-control" required>
                                    <option value="">Select a subject</option>
                                    <option value="Obituary Notice">Obituary Notice</option>
                                    <option value="Remembrance Notice">Remembrance Notice</option>
                                    <option value="Tribute Creation">Tribute Creation</option>
                                    <option value="Technical Support">Technical Support</option>
                                    <option value="Billing Inquiry">Billing Inquiry</option>
                                    <option value="Other Inquiry">Other Inquiry</option>
                                </select>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="form-label" for="message">Your Message *</label>
                            <textarea id="message" name="message" class="form-control"
                                placeholder="Please provide details about your inquiry..." rows="5" required></textarea>
                        </div>

                        <div class="form-submit">
                            <button type="submit" class="submit-btn" id="submitBtn">
                                <i class="fas fa-paper-plane"></i>
                                <span>Send Message</span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>

        </div>
    </div>

    <div id="footer-placeholder"></div>

    <div id="otpPopup" class="popup-overlay">
        <div class="popup-box">
            <div class="popup-title">Verify OTP</div>
            <div class="popup-subtitle">Enter the 6-digit code sent to your phone.</div>

            <input type="text" id="otpCodeInput" class="otp-input" maxlength="6" inputmode="numeric" autocomplete="one-time-code">

            <div class="otp-timer">
                Time remaining: <span id="otpTimer">60</span> seconds
            </div>

            <div id="otpError" class="otp-error"></div>

            <div class="otp-actions">
                <button type="button" class="submit-btn otp-cancel-btn" id="otpCancelBtn">Cancel</button>
                <button type="button" class="submit-btn" id="otpVerifyBtn">Verify</button>
            </div>
        </div>
    </div>

    <div class="toast-wrap" id="toastWrap"></div>

    <script src="script/common.js"></script>
    <script>
        loadComponent('navbar.php', 'navbar-placeholder');
        loadComponent('footer.php', 'footer-placeholder');
    </script>
    <script src="script/contact.js"></script>
</body>

</html>