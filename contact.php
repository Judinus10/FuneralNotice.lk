<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact Us - FuneralNotice.lk</title>

    <!-- Open Graph Tags -->
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
    </style>
</head>

<body>
    <!-- Navbar Component -->
    <div id="navbar-placeholder"></div>

    <!-- About Page Content -->
    <div class="main-body">
        <!-- Contact Hero Section -->
        <!-- <section class="contact-hero">
            <div class="hero-content">
                <h1 class="hero-title">Contact Our Compassionate Team</h1>
                <p class="hero-subtitle">We're here to support you during this difficult time. Reach out to us through
                    any channel below.</p>
            </div>
        </section> -->

        <div class="container">
            <!-- Contact Grid -->


            <!-- Contact Form Section -->
            <div class="contact-form-section">
                <div class="section-header">
                    <h2 class="section-title">Send Us a Message</h2>
                    <p class="section-subtitle">Fill out the form below and our team will get back to you as soon as
                        possible.</p>
                </div>

                <form id="contactForm">
                    <div class="form-grid">
                        <div class="form-group">
                            <label class="form-label" for="name">Full Name *</label>
                            <input type="text" id="name" class="form-control" placeholder="Your full name" required>
                        </div>
                        <div class="form-group">
                            <label class="form-label" for="email">Email Address *</label>
                            <input type="email" id="email" class="form-control" placeholder="Your email address"
                                required>
                        </div>
                    </div>

                    <div class="form-grid">
                        <div class="form-group">
                            <label class="form-label" for="phone">Phone Number *</label>
                            <input type="tel" id="phone" class="form-control" placeholder="Your phone number" required>
                        </div>
                        <div class="form-group">
                            <label class="form-label" for="subject">Subject *</label>
                            <select id="subject" class="form-control" required>
                                <option value="">Select a subject</option>
                                <option value="obituary">Obituary Notice</option>
                                <option value="remembrance">Remembrance Notice</option>
                                <option value="tribute">Tribute Creation</option>
                                <option value="technical">Technical Support</option>
                                <option value="billing">Billing Inquiry</option>
                                <option value="other">Other Inquiry</option>
                            </select>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="message">Your Message *</label>
                        <textarea id="message" class="form-control"
                            placeholder="Please provide details about your inquiry..." rows="5" required></textarea>
                    </div>

                    <div class="form-submit">
                        <button type="submit" class="submit-btn">
                            <i class="fas fa-paper-plane"></i> Send Message
                        </button>
                    </div>
                </form>
            </div>

            <!-- Map Section -->
            <!-- <div class="map-section">
                <div class="section-header">
                    <h2 class="section-title">Our Location</h2>
                    <p class="section-subtitle">Visit our office or contact us at the details below</p>
                </div>

                <div class="map-content">
                    <div class="location-info">
                        <h3>FuneralNotice.lk Headquarters</h3>

                        <div class="location-details">
                            <div class="location-item">
                                <div class="location-icon">
                                    <i class="fas fa-map-marker-alt"></i>
                                </div>
                                <div class="location-text">
                                    <h4>Address</h4>
                                    <p>123 Galle Road, Colombo 03,<br>Sri Lanka</p>
                                </div>
                            </div>

                            <div class="location-item">
                                <div class="location-icon">
                                    <i class="fas fa-phone"></i>
                                </div>
                                <div class="location-text">
                                    <h4>Phone</h4>
                                    <p>+94 11 234 5678</p>
                                </div>
                            </div>

                            <div class="location-item">
                                <div class="location-icon">
                                    <i class="fas fa-envelope"></i>
                                </div>
                                <div class="location-text">
                                    <h4>Email</h4>
                                    <p>office@funeralnotice.lk</p>
                                </div>
                            </div>

                            <div class="location-item">
                                <div class="location-icon">
                                    <i class="fas fa-clock"></i>
                                </div>
                                <div class="location-text">
                                    <h4>Business Hours</h4>
                                    <p>Monday - Friday: 8:00 AM - 8:00 PM<br>
                                        Saturday: 9:00 AM - 6:00 PM<br>
                                        Sunday: 10:00 AM - 4:00 PM</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="map-container">
                        <div class="map-placeholder">
                            <i class="fas fa-map-marked-alt"></i>
                            <p>Colombo Office Location</p>
                            <small>123 Galle Road, Colombo 03</small>
                        </div>
                    </div>
                </div>
            </div> -->
        </div>
    </div>

    <!-- Footer Component -->
    <div id="footer-placeholder"></div>

    <script src="script/common.js"></script>
    <script>
        // Load components when DOM is ready
        loadComponent('navbar.php', 'navbar-placeholder');
        loadComponent('footer.php', 'footer-placeholder');
    </script>
    <script src="script/contact.js"></script>
</body>

</html>