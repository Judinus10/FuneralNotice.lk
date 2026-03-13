<?php
declare(strict_types=1);
session_start();

if (empty($_SESSION['csrf'])) {
    $_SESSION['csrf'] = bin2hex(random_bytes(16));
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Memorial - FuneralNotice.lk</title>
    <meta name="description" content="View memorial on FuneralNotice.lk.">

    <meta property="og:type" content="website">
    <meta property="og:url" content="">
    <meta property="og:title" content="Memorial – FuneralNotice.lk">
    <meta property="og:description" content="View memorial on FuneralNotice.lk.">
    <meta property="og:image" content="">
    <meta property="og:image:type" content="image/png">
    <meta property="og:image:width" content="1200">
    <meta property="og:image:height" content="630">
    <meta property="og:site_name" content="FuneralNotice.lk">

    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="Memorial – FuneralNotice.lk">
    <meta name="twitter:description" content="View memorial on FuneralNotice.lk.">
    <meta name="twitter:image" content="">

    <link rel="icon" type="image/png" href="assets/favicon/favicon.png">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

    <link rel="stylesheet" href="style/common.css">
    <link rel="stylesheet" href="style/navbar.css">
    <link rel="stylesheet" href="style/footer.css">
    <link rel="stylesheet" href="style/memorial-detail.css">
</head>
<body>
    <div id="navbar-placeholder"></div>

    <div class="main-body">
        <div class="container">
            <section class="hero" id="posterArea">
                <div class="watermark">FuneralNotice.lk</div>

                <button class="poster-download-btn" id="btnDownloadPoster" title="Download memorial poster" style="display:none;">
                    <i class="fa-solid fa-download"></i>
                </button>

                <div class="hero-title" id="noticeHeading">In Loving Memory</div>

                <div class="portrait-area">
                    <div class="dates-container">
                        <div class="date-pill date-left">
                            <div>தோற்றம்</div>
                            <div id="birthDate">-</div>
                        </div>

                        <div class="portrait-wrap">
                            <img src="assets/q1.png" alt="" class="frame-img"
                                 onerror="this.src='assets/Frame.png'">
                            <img src="assets/defaultavt.png" alt="Memorial Portrait" class="portrait" id="memorialPortrait"
                                 onerror="this.onerror=null;this.src='assets/defaultavt.png'">
                        </div>

                        <div class="date-pill date-right">
                            <div>மறைவு</div>
                            <div id="deathDate">-</div>
                        </div>
                    </div>
                </div>

                <div>
                    <h1 id="memorialName">Loading...</h1>
                    <div class="age-badge" id="memorialAge" style="display:none;">Age</div>
                    <div class="meta" id="memorialLocation">Loading...</div>

                    <div class="actions">
                        <a class="btn btn-primary" id="btnRipVideo" href="#" target="_blank" style="display:none;">
                            <i class="fa-solid fa-video"></i> RIP Video
                        </a>

                        <button class="btn btn-primary" id="btnTributeNow">
                            <i class="fa-solid fa-heart"></i> Share Your Feeling Now
                        </button>

                        <button class="btn btn-primary" id="btnSendFlowers" style="display:none;">
                            <i class="fa-solid fa-fan"></i> Send Flowers
                        </button>

                        <button class="btn btn-primary" id="btnDonate">
                            <i class="fa-solid fa-hand-holding-heart"></i> Donate
                        </button>

                        <button class="btn btn-primary" id="btnShare" style="display:none;">
                            <i class="fa-solid fa-share-nodes"></i> Share
                        </button>
                    </div>
                </div>
            </section>

            <div class="about-layout">
                <div class="about-summary-row">
                    <div class="about-col">
                        <section class="section about-section" id="about">
                            <h3 class="section-title">About</h3>
                            <div id="lifeStory" style="color:var(--light-text); line-height:1.7; font-size:0.95rem;">
                                <p>Loading memorial details...</p>
                            </div>
                        </section>

                        <section class="section" id="tributes">
                            <div style="display:flex;align-items:center;gap:8px;flex-wrap:wrap">
                                <h3 class="section-title" style="margin:0;display:flex;align-items:center;gap:6px;font-size:1.2rem;">
                                    <i class="fa-solid fa-heart" style="color:var(--primary);font-size:0.9rem;"></i>
                                    Comments &amp; Condolences
                                </h3>
                                <span class="badge-tribute" id="tributeCountBadge" title="Total tributes" style="display:none;">
                                    <i class="fa-solid fa-heart"></i> <span id="tributeCountText">0</span>
                                </span>
                            </div>

                            <div id="inlineTributeWrap"></div>
                            <div id="tributesList"></div>

                            <div id="tributeBottomActions"
                                 style="margin-top:12px;display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap;gap:8px;">
                                <button class="btn btn-primary" id="btnWriteTribute" style="display:none;">
                                    <i class="fa-solid fa-pen"></i> Write a Comment
                                </button>

                                <a href="#" class="btn btn-primary" id="btnViewMoreTributes" style="display:none;">
                                    View more Comments
                                </a>
                            </div>
                        </section>
                    </div>

                    <aside class="summary-col">
                        <div class="summary-card">
                            <div class="summary-head">INFORMATION</div>
                            <ul class="summary-list" id="summaryList"></ul>
                        </div>

                        <div class="ad-card" id="sponsoredBox" style="display:none;">
                            <div class="ad-head ad-head-row">
                                <span class="ad-title">SPONSORED</span>
                                <a href="#" target="_blank" class="ad-add-btn" id="addAdBtn" style="display:none;">+ ADD YOUR AD</a>
                            </div>

                            <div class="ad-viewport" aria-label="Sponsored ads">
                                <button class="ad-arrow ad-prev" id="adPrev" aria-label="Previous ad" style="display:none;">❮</button>
                                <button class="ad-arrow ad-next" id="adNext" aria-label="Next ad" style="display:none;">❯</button>

                                <div class="ad-track" id="adTrack"></div>
                            </div>

                            <div class="ad-dots" id="adDots" aria-hidden="true"></div>
                        </div>
                    </aside>
                </div>
            </div>
        </div>
    </div>

    <!-- Tribute modal -->
    <div class="modal-backdrop" id="modalTributeType">
        <div class="modal tribute-type-modal">
            <h4>Share Your Feelings</h4>
            <form id="tributeForm">
                <input type="hidden" name="csrf" value="<?= htmlspecialchars($_SESSION['csrf'], ENT_QUOTES, 'UTF-8') ?>">
                <input type="hidden" name="post_id" id="tribute_post_id" value="">

                <div class="row">
                    <label>Your Name</label>
                    <input type="text" name="sender_name" required>
                </div>

                <div class="row">
                    <label>Message</label>
                    <textarea name="message" required placeholder="Share your memories, prayers or condolences"></textarea>
                </div>

                <div class="modal-actions">
                    <button type="button" class="btn" data-close="#modalTributeType">Cancel</button>
                    <button class="btn btn-primary" type="submit">Post Tribute</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Send Flowers modal -->
    <div class="modal-backdrop" id="modalFlowers">
        <div class="modal modal--flowers">
            <div class="flowers-layout">
                <div class="flowers-info">
                    <div id="flowersInfoBox">
                        <p class="flowers-text">
                            Please leave your contact details and our team will contact you shortly regarding flower / wreath arrangements.
                        </p>
                    </div>

                    <div class="flowers-note">
                        <i class="fa-solid fa-circle-info"></i>
                        <span>We will not display your contact details in public.</span>
                    </div>
                </div>

                <div class="flowers-form">
                    <h3>Send Flowers Request</h3>

                    <form id="flowersForm">
                        <input type="hidden" name="csrf" value="<?= htmlspecialchars($_SESSION['csrf'], ENT_QUOTES, 'UTF-8') ?>">
                        <input type="hidden" name="post_id" id="flowers_post_id" value="">

                        <div class="row"><label>Your Name</label><input type="text" name="full_name" required></div>
                        <div class="row"><label>Your Email</label><input type="text" name="email" required></div>

                        <div class="row">
                            <label>Your Phone</label>
                            <div class="phone-row">
                                <div class="custom-select" data-select-id="flowers_phone_code">
                                    <button type="button" class="custom-select-trigger">
                                        <span class="custom-select-text">+94</span>
                                    </button>
                                    <div class="custom-options" id="flowersPhoneOptions"></div>
                                </div>

                                <select name="phone_code" id="flowers_phone_code" class="native-select-hidden"></select>
                                <input type="text" name="mobile" required placeholder="Enter mobile number">
                            </div>
                        </div>

                        <div class="row"><label>Country</label><input type="text" name="country" required></div>
                        <div class="row"><label>Message (Optional)</label><textarea name="message" placeholder="Share any notes..."></textarea></div>

                        <div class="modal-actions flowers-actions">
                            <button type="button" class="btn flowers-btn-secondary" data-close="#modalFlowers">Cancel</button>
                            <button class="btn flowers-btn-primary" type="submit">Send Flowers</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Donation modal -->
    <div class="modal-backdrop" id="modalDonation">
        <div class="modal modal--flowers modal--donation">
            <div class="flowers-layout">
                <div class="flowers-info donation-info">
                    <h4 class="flowers-hotline-label">Support the Family</h4>
                    <p class="flowers-text">
                        Please share your details below. Our admin team will contact you with bank account information.
                    </p>

                    <div id="donationHotlineBox"></div>

                    <div class="flowers-note">
                        <i class="fa-solid fa-circle-info"></i>
                        <span>We will not display your contact details in public.</span>
                    </div>
                </div>

                <div class="flowers-form donation-form">
                    <h3>Donation Request</h3>

                    <form id="donationForm">
                        <input type="hidden" name="csrf" value="<?= htmlspecialchars($_SESSION['csrf'], ENT_QUOTES, 'UTF-8') ?>">
                        <input type="hidden" name="post_id" id="donation_post_id" value="">

                        <div class="row"><label>Your Name</label><input type="text" name="full_name" required></div>
                        <div class="row"><label>Your Email</label><input type="text" name="email" required></div>

                        <div class="row">
                            <label>Your Phone</label>
                            <div class="phone-row">
                                <div class="custom-select" data-select-id="donate_phone_code">
                                    <button type="button" class="custom-select-trigger">
                                        <span class="custom-select-text">+94</span>
                                    </button>
                                    <div class="custom-options" id="donatePhoneOptions"></div>
                                </div>

                                <select name="phone_code" id="donate_phone_code" class="native-select-hidden"></select>
                                <input type="text" name="mobile" required placeholder="Enter mobile number">
                            </div>
                        </div>

                        <div class="row"><label>Country</label><input type="text" name="country" required></div>

                        <div class="row">
                            <label>Planned Donation Amount (Optional)</label>
                            <div class="amount-row">
                                <input type="number" name="amount" step="0.01" placeholder="Amount">

                                <div class="select-wrap">
                                    <input type="hidden" name="currency" id="currencyValue" value="LKR">
                                    <div class="dd dd-currency" id="currencyDD">
                                        <button type="button" class="dd-toggle" id="currencyBtn">LKR</button>
                                        <div class="dd-menu">
                                            <button type="button" class="dd-item active" data-value="LKR">LKR</button>
                                            <button type="button" class="dd-item" data-value="USD">USD</button>
                                            <button type="button" class="dd-item" data-value="EUR">EUR</button>
                                            <button type="button" class="dd-item" data-value="GBP">GBP</button>
                                            <button type="button" class="dd-item" data-value="Other">Other</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <label>Preferred time / method to contact you (Optional)</label>
                            <input type="text" name="preferred_time" placeholder="e.g. WhatsApp evening, call after 6pm">
                        </div>

                        <div class="row">
                            <label>Message (Optional)</label>
                            <textarea name="message" placeholder="Any additional notes..."></textarea>
                        </div>

                        <div class="modal-actions flowers-actions">
                            <button type="button" class="btn flowers-btn-secondary" data-close="#modalDonation">Cancel</button>
                            <button class="btn btn-primary" type="submit">Submit Request</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div id="toastStack" class="toast-stack" aria-live="polite" aria-atomic="true"></div>

    <div id="footer-placeholder"></div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
    <script src="script/common.js"></script>
    <script>
        loadComponent('navbar.php', 'navbar-placeholder');
        loadComponent('footer.php', 'footer-placeholder');
    </script>
    <script src="script/memorial-detail.js"></script>
</body>
</html>