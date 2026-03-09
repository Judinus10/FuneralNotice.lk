<!-- Footer -->
<footer class="hide-on-mobile">
    <div class="container">
        <div class="footer-content">
            <div class="footer-logo">
                <img src="funeral notice logo.png" alt="FuneralNotice.lk" class="logo-img">
            </div>
            <div class="footer-tagline">
                Keeping Memories Alive - A dignified platform to honor and remember loved ones across Sri Lanka
            </div>

            <div class="footer-links">
                <a href="about.php">About Us</a>
                <a href="terms.php">Terms</a>
                <a href="#">Report Us</a>
                <a href="privacy.php">Privacy Policy</a>
                <a href="cookies.php">Cookie Policy</a>
            </div>

            <div class="copyright">
                © 2023. FuneralNotice.lk. All rights reserved.
            </div>
        </div>
    </div>
</footer>

<!-- UPDATED Mobile Bottom Navigation - Removed obituaries and remembrance, added about and contact -->
<div class="mobile-bottom-nav">
    <div class="nav-container">
        <div class="mobile-nav-item" data-page="home" onclick="window.location.href='index.php'">
            <i class="fas fa-home"></i>
            <span class="nav-label">Home</span>
        </div>
        <div class="mobile-nav-item" data-page="about" onclick="window.location.href='about.php'">
            <i class="fas fa-info-circle"></i>
            <span class="nav-label">About</span>
        </div>
        <div class="mobile-nav-item" data-page="contact" onclick="window.location.href='contact.php'">
            <i class="fas fa-envelope"></i>
            <span class="nav-label">Contact</span>
        </div>
        <div class="mobile-nav-item" data-page="whatsapp" onclick="window.open('https://wa.me/94711234567', '_blank')">
            <i class="fab fa-whatsapp"></i>
            <span class="nav-label">WhatsApp</span>
        </div>
        <div class="mobile-nav-item" data-page="menu" id="mobileBottomMenuBtn">
            <i class="fas fa-bars"></i>
            <span class="nav-label">Menu</span>
        </div>
    </div>
</div>

<!-- UPDATED: Mobile Create Notice Button (replaces floating WhatsApp) -->
<a href="create.php" class="mobile-create-notice-float" id="mobileCreateNoticeBtn">
    <i class="fas fa-plus"></i>
</a>