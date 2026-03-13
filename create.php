<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Funeral Notice - FuneralNotice.lk</title>

    <meta property="og:type" content="website">
    <meta property="og:url" content="https://funeralnotice.lk/create">
    <meta property="og:title" content="Create a Funeral Notice – FuneralNotice.lk">
    <meta property="og:description" content="Create a beautiful online funeral notice to honor and remember your loved one.">
    <meta property="og:image" content="https://ripnews.lk/uploads/posts/76/cover.png">
    <meta property="og:image:type" content="image/png">
    <meta property="og:image:width" content="1200">
    <meta property="og:image:height" content="630">
    <meta property="og:site_name" content="FuneralNotice.lk">

    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="Create a Funeral Notice – FuneralNotice.lk">
    <meta name="twitter:description" content="Create a beautiful online funeral notice to honor and remember your loved one.">
    <meta name="twitter:image" content="https://ripnews.lk/uploads/posts/76/cover.png">

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="style/common.css">
    <link rel="stylesheet" href="style/navbar.css">
    <link rel="stylesheet" href="style/footer.css">
    <link rel="stylesheet" href="style/create.css">
</head>
<body>
    <div id="navbar-placeholder"></div>

    <div class="main-body">
        <div class="create-memorial-section">
            <div class="container">
                <div class="create-memorial-header">
                    <div class="create-head-top">
                        <div>
                            <h1 id="pageTitle">Create a Funeral Notice</h1>
                            <p id="pageSubtitle">Honor your loved one with a beautiful online funeral notice. Share their life story, photos, and accept tributes from friends and family.</p>
                        </div>
                    </div>
                </div>

                <div class="create-memorial-card">
                    <form id="memorialForm" enctype="multipart/form-data" novalidate>
                        <input type="hidden" name="final_submit" value="1">
                        <input type="hidden" name="lived_place" id="lived_place">

                        <div class="step-panel active" id="step-1">
                            <h2 class="card-title">Step 1 · Post Details</h2>

                            <div class="form-row">
                                <div class="form-group">
                                    <label for="type">Post Type <span class="required">*</span></label>
                                    <select class="form-control" id="type" name="type" required>
                                        <option value="">Select post type…</option>
                                    </select>
                                </div>

                                <div class="form-group">
                                    <label for="religion">Religion <span class="required">*</span></label>
                                    <select class="form-control" id="religion" name="religion" required>
                                        <option value="">Select religion…</option>
                                    </select>
                                </div>
                            </div>

                            <div class="form-row">
                                <div class="form-group">
                                    <label for="first_name">First Name <span class="required">*</span></label>
                                    <input type="text" class="form-control" id="first_name" name="first_name" required>
                                </div>

                                <div class="form-group">
                                    <label for="last_name">Last Name <span class="required">*</span></label>
                                    <input type="text" class="form-control" id="last_name" name="last_name" required>
                                </div>
                            </div>

                            <div class="form-row">
                                <div class="form-group">
                                    <label for="birth_date">Birth Date <span class="required">*</span></label>
                                    <input type="date" class="form-control" id="birth_date" name="birth_date" required>
                                </div>

                                <div class="form-group">
                                    <label for="birth_place">Birth Place <span class="required">*</span></label>
                                    <input type="text" class="form-control" id="birth_place" name="birth_place" required>
                                </div>
                            </div>

                            <div class="form-row">
                                <div class="form-group">
                                    <label for="death_date">Death Date <span class="required">*</span></label>
                                    <input type="date" class="form-control" id="death_date" name="death_date" required>
                                </div>

                                <div class="form-group">
                                    <label for="death_place">Death Place <span class="required">*</span></label>
                                    <input type="text" class="form-control" id="death_place" name="death_place" required>
                                </div>
                            </div>

                            <div class="form-row">
                                <div class="form-group">
                                    <label for="country">Lived Country <span class="required">*</span></label>
                                    <select class="form-control" id="country" name="country" required>
                                        <option value="">Loading countries…</option>
                                    </select>
                                </div>

                                <div class="form-group">
                                    <label for="lived_place_search">Lived Place <span class="required">*</span></label>
                                    <input type="text" class="form-control" id="lived_place_search" placeholder="Type to search city" autocomplete="off">
                                    <select class="form-control city-list" id="lived_place_list" size="6" disabled></select>
                                    <div class="form-hint">Type to search, then click a city from the list.</div>
                                </div>
                            </div>

                            <div class="form-row">
                                <div class="form-group">
                                    <label for="address">Address <span class="required">*</span></label>
                                    <input type="text" class="form-control" id="address" name="address" required placeholder="Street, city / town">
                                </div>

                                <div class="form-group">
                                    <label for="other_countries">Other Countries Lived (optional)</label>
                                    <input type="text" class="form-control" id="other_countries" name="other_countries" placeholder="e.g., UK, Canada">
                                </div>
                            </div>

                            <div class="form-group full-width">
                                <label for="bio">Biography / Life Story</label>
                                <textarea class="form-control" id="bio" name="bio" rows="5" placeholder="Share the life story, achievements, and memories of your loved one..."></textarea>
                                <div class="form-hint">You can write about their life, career, family, hobbies, and special memories.</div>
                            </div>

                            <div class="create-memorial-card inner-card">
                                <h2 class="card-title">Funeral Notice Photo</h2>

                                <div class="photo-upload-container" id="uploadContainer">
                                    <i class="fas fa-cloud-upload-alt"></i>
                                    <h3>Upload a Funeral Notice Photo</h3>
                                    <p>Choose a clear, respectful photo of your loved one. Max file size: 6MB</p>
                                    <div class="upload-btn" id="choosePhotoBtn">
                                        <i class="fas fa-upload"></i> Choose Photo
                                    </div>
                                    <input type="file" id="photoUpload" name="cover_image" accept=".jpg,.jpeg,.png,.webp,image/jpeg,image/png,image/webp" style="display:none;">
                                </div>

                                <div id="uploadedPhotoContainer" style="display:none;">
                                    <div class="uploaded-photo">
                                        <img id="uploadedImage" src="" alt="Uploaded Photo">
                                        <div class="uploaded-photo-info">
                                            <div class="uploaded-photo-name" id="photoName">Photo.jpg</div>
                                            <div class="uploaded-photo-size" id="photoSize">2.4 MB</div>
                                        </div>
                                        <div class="remove-photo" id="removePhotoBtn">
                                            <i class="fas fa-times"></i>
                                        </div>
                                    </div>
                                </div>

                                <div class="form-hint">Recommended photo size: 500x500 pixels or larger. Square photos work best.</div>
                            </div>

                            <div class="create-actions next-only">
                                <button type="button" class="submit-btn" id="btnNext1">
                                    <i class="fas fa-arrow-right"></i> Next: Your Details
                                </button>
                            </div>
                        </div>

                        <div class="step-panel" id="step-2">
                            <h2 class="card-title">Step 2 · Your Contact & Verification</h2>

                            <div class="form-row">
                                <div class="form-group">
                                    <label for="contact_name">Your Full Name <span class="required">*</span></label>
                                    <input type="text" class="form-control" id="contact_name" name="contact_name" required>
                                </div>

                                <div class="form-group">
                                    <label for="phone">Phone Number <span class="required">*</span></label>
                                    <div class="phone-grid">
                                        <select class="form-control" id="phone_code" name="phone_code" required>
                                            <option value="">Code</option>
                                        </select>
                                        <input type="text" class="form-control" id="phone" name="phone" required placeholder="Enter phone number">
                                    </div>
                                </div>
                            </div>

                            <div class="form-row">
                                <div class="form-group">
                                    <label for="phone_alt">Alternative Phone (optional)</label>
                                    <div class="phone-grid">
                                        <select class="form-control" id="phone_alt_code" name="phone_alt_code">
                                            <option value="">Code</option>
                                        </select>
                                        <input type="text" class="form-control" id="phone_alt" name="phone_alt" placeholder="Enter alternative number">
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label for="id_type">ID Type <span class="required">*</span></label>
                                    <select class="form-control" id="id_type" name="id_type" required>
                                        <option value="">Select ID type…</option>
                                    </select>
                                </div>
                            </div>

                            <div class="form-row">
                                <div class="form-group full-width">
                                    <label for="id_number">ID Number <span class="required">*</span></label>
                                    <input type="text" class="form-control" id="id_number" name="id_number" required>
                                </div>
                            </div>

                            <div id="nicFields" style="display:none;">
                                <div class="form-row">
                                    <div class="form-group">
                                        <label for="nic_front">NIC Front Image <span class="required">*</span></label>
                                        <input type="file" class="form-control" id="nic_front" name="nic_front" accept=".jpg,.jpeg,.png,.webp,image/jpeg,image/png,image/webp">
                                    </div>

                                    <div class="form-group">
                                        <label for="nic_back">NIC Back Image <span class="required">*</span></label>
                                        <input type="file" class="form-control" id="nic_back" name="nic_back" accept=".jpg,.jpeg,.png,.webp,image/jpeg,image/png,image/webp">
                                    </div>
                                </div>
                            </div>

                            <div id="passportFields" style="display:none;">
                                <div class="form-row">
                                    <div class="form-group full-width">
                                        <label for="passport_image">Passport Image <span class="required">*</span></label>
                                        <input type="file" class="form-control" id="passport_image" name="passport_image" accept=".jpg,.jpeg,.png,.webp,image/jpeg,image/png,image/webp">
                                    </div>
                                </div>
                            </div>

                            <div class="create-actions">
                                <button type="button" class="back-btn" id="btnBack1">
                                    <i class="fas fa-arrow-left"></i> Back
                                </button>

                                <button type="submit" class="submit-btn" id="finalSubmitBtn">
                                    <i class="fas fa-plus-circle"></i> Submit & Continue
                                </button>
                            </div>
                        </div>
                    </form>
                </div>

            </div>
        </div>
    </div>

    <div id="footer-placeholder"></div>

    <div id="startPopup" class="popup-overlay">
        <div class="popup-box">
            <h2>How would you like to continue?</h2>
            <p>Please choose how you want to create the memorial.</p>
            <div class="popup-buttons">
                <button id="btnCallTeam" class="btn-outline popup-btn">Call Our Team</button>
                <button id="btnFillManual" class="btn-primary popup-btn">Fill Manually</button>
            </div>
        </div>
    </div>

    <div id="captchaPopup" class="popup-overlay" style="display:none;">
        <div class="popup-box">
            <h2>Quick Verification</h2>
            <p>Please answer this simple question to continue.</p>
            <div class="captcha-block">
                <div class="captcha-question" id="captchaQ">Loading...</div>
                <input id="captchaInput" type="text" inputmode="numeric" maxlength="4" class="form-control otp-input" placeholder="Answer">
                <div class="form-hint error-text" id="captchaErr" style="display:none;"></div>
            </div>
            <div class="popup-buttons">
                <button id="captchaCancelBtn" class="btn-outline popup-btn">Cancel</button>
                <button id="captchaOkBtn" class="btn-primary popup-btn">Continue</button>
            </div>
        </div>
    </div>

    <div id="otpPopup" class="popup-overlay" style="display:none;">
        <div class="popup-box">
            <h2>Verify Your Number</h2>
            <p id="otpInfoText">
                We have sent a 6-digit verification code to your phone.
                Please enter it below within <b>60 seconds</b> to continue.
            </p>

            <div class="captcha-block">
                <input id="otpCodeInput" type="text" maxlength="6" inputmode="numeric" class="form-control otp-input" placeholder="------">
                <div class="form-hint" id="otpTimerLabel">Time remaining: <span id="otpTimer">60</span> seconds</div>
                <div class="form-hint error-text" id="otpError" style="display:none;"></div>
            </div>

            <div class="popup-buttons">
                <button id="otpCancelBtn" class="btn-outline popup-btn">Cancel</button>
                <button id="otpVerifyBtn" class="btn-primary popup-btn">Verify</button>
            </div>
        </div>
    </div>

    <div class="toast-wrap" id="toastWrap"></div>

    <script src="script/common.js"></script>
    <script>
        loadComponent('navbar.php', 'navbar-placeholder');
        loadComponent('footer.php', 'footer-placeholder');
    </script>
    <script src="script/create.js"></script>
</body>
</html>