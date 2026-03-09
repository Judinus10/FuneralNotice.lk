<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FuneralNotice.lk - Keeping Memories Alive</title>

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="style/common.css">
    <link rel="stylesheet" href="style/navbar.css">
    <link rel="stylesheet" href="style/footer.css">
    <link rel="stylesheet" href="style/index.css">
</head>

<body>
    <!-- Navbar Component -->
    <div id="navbar-placeholder"></div>

    <div class="main-body">
        <div class="container">
            <!-- Mobile Sponsored Ads -->
            <div class="mobile-sponsored-section" id="mobileSponsoredSection"></div>

            <div class="sidebar-toggle-container"></div>

            <div class="feeds">
                <!-- LEFT SIDEBAR -->
                <div class="sidebar sidebar-left" id="leftSidebar">
                    <div class="list-multiple-item">
                        <div class="header">
                            <h2><i class="fas fa-map-marker-alt"></i> Browse by District</h2>
                        </div>
                        <div class="list">
                            <ul id="recentPosts">
                                <li>
                                    <a href="#">
                                        <span class="info">
                                            <span class="label-main">Loading...</span>
                                            <span class="label-secondary">Please wait</span>
                                        </span>
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>

                <!-- CENTER -->
                <div class="main-content">
                    <div class="section-tabs">
                        <div class="tabs-header">
                            <div class="contact-display">
                                <i class="fas fa-phone"></i>
                                <span class="phone-number" id="orgPhoneText">+94</span>
                                <span class="badge">24/7</span>
                            </div>
                            <a href="#" id="orgWhatsappBtn" class="whatsapp-btn hide-on-mobile" target="_blank" rel="noopener">
                                <i class="fab fa-whatsapp"></i> Chat with us
                            </a>
                        </div>

                        <div class="tabs-nav">
                            <a href="#" class="tab-link active" data-type="all">
                                <i class="fas fa-home"></i> Home
                            </a>
                            <a href="#" class="tab-link" data-type="obituary">
                                <i class="fas fa-book"></i> Obituaries
                            </a>
                            <a href="#" class="tab-link" data-type="remembrance">
                                <i class="fas fa-calendar-heart"></i> Remembrance
                            </a>
                        </div>

                        <div class="feeds-container" id="feedsContainer">
                            <div class="feed-card">
                                <div class="feed-header">
                                    <span class="head">Loading memorials...</span>
                                    <span class="actions">Please wait</span>
                                </div>
                            </div>
                        </div>

                        <div class="load-more-container">
                            <button class="load-more-btn" id="loadMoreBtn">
                                <i class="fas fa-spinner"></i> Load More Memorials
                            </button>
                            <p style="margin-top: 8px; color: var(--light-text); font-size: 0.75rem;">
                                Showing <span id="currentCount">0</span> memorials
                            </p>
                        </div>
                    </div>
                </div>

                <!-- RIGHT SIDEBAR -->
                <div class="sidebar sidebar-right" id="rightSidebar">
                    <!-- Recent Comments -->
                    <div class="list-multiple-item square hide-on-mobile">
                        <div class="header cover-tribute">
                            <h2><i class="fas fa-heart"></i> Recent Comments</h2>
                        </div>
                        <div class="list recent-tributes-container">
                            <ul id="recentTributes">
                                <li>
                                    <a href="#">
                                        <div class="tribute-info">
                                            <span class="label-main">Loading...</span>
                                            <span class="label-secondary">Please wait</span>
                                        </div>
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </div>

                    <!-- Sponsored Ads -->
                    <div class="list-multiple-item sticky-sponsor hide-on-mobile">
                        <div class="header"
                            style="background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);">
                            <h2><i class="fas fa-ad"></i> Sponsored Ads</h2>
                            <a href="#" id="placeAdBtn"
                                target="_blank" rel="noopener" class="whatsapp-btn-small">
                                <i class="fab fa-whatsapp"></i>
                                Place Ad
                            </a>
                        </div>
                        <div class="list">
                            <div id="sponsoredCarousel" class="sponsored-carousel"></div>
                            <div class="ad-dots" id="adDots"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Footer Component -->
    <div id="footer-placeholder"></div>

    <script src="script/common.js"></script>
    <script>
        loadComponent('navbar.php', 'navbar-placeholder');
        loadComponent('footer.php', 'footer-placeholder');
    </script>
    <script src="script/index.js"></script>
</body>

</html>