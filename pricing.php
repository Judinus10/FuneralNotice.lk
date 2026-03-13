<?php
$postId = 0;

if (isset($_GET['post_id'])) {
    $postId = (int) $_GET['post_id'];
} elseif (isset($_GET['id'])) {
    $postId = (int) $_GET['id'];
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pricing - FuneralNotice.lk</title>

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="style/common.css">
    <link rel="stylesheet" href="style/navbar.css">
    <link rel="stylesheet" href="style/footer.css">
    <link rel="stylesheet" href="style/pricing.css">
</head>

<body>
    <div id="navbar-placeholder"></div>

    <div class="main-body">
        <div class="pricing-section">
            <div class="container">
                <div class="pricing-header">
                    <h1>Pricing</h1>
                    <p>Select the memorial duration and any additional services.</p>
                </div>

                <div class="stepper-wrap">
                    <div class="stepper-track">
                        <div class="stepper-fill" id="stepperFill"></div>

                        <div class="stepper-step is-done" data-step="1">
                            <span class="dot"></span>
                            <span class="label">Post Details</span>
                        </div>

                        <div class="stepper-step is-done" data-step="2">
                            <span class="dot"></span>
                            <span class="label">Post Owner</span>
                        </div>

                        <div class="stepper-step is-done" data-step="3">
                            <span class="dot"></span>
                            <span class="label">SMS Verify</span>
                        </div>

                        <div class="stepper-step is-active" data-step="4">
                            <span class="dot"></span>
                            <span class="label">Duration</span>
                        </div>

                        <div class="stepper-step is-disabled" data-step="5">
                            <span class="dot"></span>
                            <span class="label">Submit</span>
                        </div>
                    </div>
                </div>

                <div class="pricing-layout">
                    <div class="pricing-left">
                        <div class="pricing-card">
                            <div class="pricing-card-head">
                                <h2>Services & Prices</h2>
                                <p id="pricingSubtitle">Loading pricing...</p>
                            </div>

                            <form id="pricingForm" novalidate>
                                <input type="hidden" id="postId" name="post_id" value="<?= (int) $postId ?>">
                                <input type="hidden" id="duration_days" name="duration_days" value="">
                                <input type="hidden" id="has_live_coverage" name="has_live_coverage" value="0">
                                <input type="hidden" id="has_social_media" name="has_social_media" value="0">
                                <input type="hidden" id="has_media_website" name="has_media_website" value="0">

                                <div class="feature-list" id="featureList">
                                    <div class="loading-box">Loading pricing options...</div>
                                </div>

                                <div class="pricing-actions-mobile">
                                    <button type="submit" form="pricingForm" class="btn btn-primary pricing-submit-btn"
                                        id="mobileSubmitBtn" disabled>
                                        <i class="fas fa-check"></i> Submit
                                    </button>
                                </div>
                            </form>
                        </div>

                        <div class="pricing-card info-card">
                            <div class="pricing-card-head">
                                <h2>Important Information</h2>
                            </div>
                            <div id="infoBlock" class="info-block">
                                Loading information...
                            </div>
                        </div>
                    </div>

                    <aside class="pricing-right">
                        <div class="basket-card" id="basketCard">
                            <div class="basket-head">
                                <h3>Your Basket</h3>
                                <span class="basket-badge" id="currencyBadge">Billing</span>
                            </div>

                            <div class="basket-label">Selected services</div>
                            <div class="basket-summary" id="basketSummary">
                                <span class="basket-empty">No services selected yet.</span>
                            </div>

                            <div class="basket-total-row">
                                <span>Total</span>
                                <strong id="basketTotal">LKR 0</strong>
                            </div>

                            <button type="submit" form="pricingForm" class="btn btn-primary pricing-submit-btn"
                                id="desktopSubmitBtn" disabled>
                                <i class="fas fa-check"></i> Submit
                            </button>

                            <div class="basket-footer">
                                <div class="basket-footer-title">What’s included</div>
                                <ul>
                                    <li>Memorial visibility for the selected period</li>
                                    <li>Basic support from FuneralNotice.lk</li>
                                    <li>Final confirmation after submission</li>
                                </ul>
                            </div>
                        </div>
                    </aside>
                </div>
            </div>
        </div>
    </div>

    <div id="footer-placeholder"></div>

    <div class="toast-stack" id="toastStack"></div>

    <script src="script/common.js"></script>
    <script>
        window.PRICING_POST_ID = <?= (int) $postId ?>;
        loadComponent('navbar.php', 'navbar-placeholder');
        loadComponent('footer.php', 'footer-placeholder');
    </script>
    <script src="script/pricing.js"></script>
</body>

</html>