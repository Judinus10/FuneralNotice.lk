(function () {
    const cfg = window.TRIBUTE_PAGE || {};

    const form = document.getElementById('tributeEntryForm');
    const alertBox = document.getElementById('tributeFormAlert');

    const byName = document.getElementById('by_name');
    const byCountry = document.getElementById('by_country');
    const message = document.getElementById('message');
    const photoLinks = document.getElementById('photo_links');

    const fullName = document.getElementById('full_name');
    const phoneCode = document.getElementById('phone_code');
    const mobile = document.getElementById('mobile');
    const otpCode = document.getElementById('otp_code');
    const phoneCodeDisplay = document.getElementById('phoneCodeDisplay');

    const previewName = document.getElementById('previewName');
    const previewCountry = document.getElementById('previewCountry');
    const previewMessage = document.getElementById('previewMessage');
    const previewPhotos = document.getElementById('previewPhotos');
    const previewPhoneStatus = document.getElementById('previewPhoneStatus');
    const previewDeliveryStatus = document.getElementById('previewDeliveryStatus');

    const deliveryBlock = document.getElementById('deliveryBlock');
    const sendToHomeHidden = document.getElementById('send_to_home');
    const sendToHomeInputs = document.querySelectorAll('input[name="delivery_choice"]');
    const deliveryOptions = document.querySelectorAll('.delivery-option');

    const btnBackChooser = document.getElementById('btnBackChooser');
    const btnBackBottom = document.getElementById('btnBackBottom');
    const btnCloseTribute = document.getElementById('btnCloseTribute');
    const btnSubmit = document.getElementById('btnSubmitTribute');

    const btnSendOtp = document.getElementById('btnSendOtp');
    const btnVerifyOtp = document.getElementById('btnVerifyOtp');
    const btnResendOtp = document.getElementById('btnResendOtp');

    const otpStatusBadge = document.getElementById('otpStatusBadge');
    const otpStatusText = document.getElementById('otpStatusText');

    const supportsDelivery = !!cfg.supportsDelivery;

    let otpVerified = false;
    let otpCurrentPhone = '';

    function selectedSendToHome() {
        const checked = document.querySelector('input[name="delivery_choice"]:checked');
        return checked ? checked.value === '1' : false;
    }

    function fullPhone() {
        const code = (phoneCode?.value || '').trim();
        const number = (mobile?.value || '').trim();
        return `${code} ${number}`.trim();
    }

    function syncPhoneCodeDisplay() {
        if (!phoneCode || !phoneCodeDisplay) return;

        const selected = phoneCode.options[phoneCode.selectedIndex];
        const codeOnly = selected?.getAttribute('data-code') || phoneCode.value || '';
        phoneCodeDisplay.textContent = codeOnly;
    }

    function setAlert(type, msg) {
        if (!alertBox) return;
        alertBox.className = `tribute-alert ${type}`;
        alertBox.textContent = msg || '';
        alertBox.style.display = 'block';
    }

    function clearAlert() {
        if (!alertBox) return;
        alertBox.style.display = 'none';
        alertBox.textContent = '';
        alertBox.className = 'tribute-alert';
    }

    function setOtpBadge(type, text, subtext) {
        if (otpStatusBadge) {
            otpStatusBadge.className = `otp-status-badge ${type}`;
            otpStatusBadge.textContent = text;
        }

        if (otpStatusText) {
            otpStatusText.textContent = subtext || '';
        }
    }

    function resetVerificationState() {
        otpVerified = false;
        otpCurrentPhone = '';
        setOtpBadge('neutral', 'Not verified', 'Verify the mobile number only if delivery is needed.');
        updatePreview();
    }

    function markVerified(phoneValue) {
        otpVerified = true;
        otpCurrentPhone = phoneValue;
        setOtpBadge('success', 'Verified', 'Mobile number verified successfully.');
        updatePreview();
    }

    function updateDeliveryUI() {
        if (!supportsDelivery) return;

        const sendToHome = selectedSendToHome();

        if (sendToHomeHidden) {
            sendToHomeHidden.value = sendToHome ? '1' : '0';
        }

        if (deliveryBlock) {
            deliveryBlock.style.display = sendToHome ? 'block' : 'none';
        }

        deliveryOptions.forEach(label => {
            const input = label.querySelector('input');
            label.classList.toggle('active', !!input?.checked);
        });

        if (!sendToHome) {
            resetVerificationState();

            if (previewPhoneStatus) {
                previewPhoneStatus.style.display = 'none';
            }

            if (previewDeliveryStatus) {
                previewDeliveryStatus.textContent = 'Not sending to home';
                previewDeliveryStatus.className = 'preview-extra neutral';
            }
        } else {
            if (previewPhoneStatus) {
                previewPhoneStatus.style.display = 'inline-flex';
            }

            if (previewDeliveryStatus) {
                previewDeliveryStatus.textContent = 'Sending to home';
                previewDeliveryStatus.className = 'preview-extra warning';
            }
        }

        updatePreview();
    }

    function updatePreview() {
        if (previewName) {
            previewName.textContent = (byName?.value || '').trim() || 'Your Name';
        }

        if (previewCountry) {
            previewCountry.textContent = (byCountry?.value || '').trim() || 'Country';
        }

        if (previewMessage) {
            previewMessage.textContent = (message?.value || '').trim() || 'Your tribute message will appear here.';
        }

        if (previewPhotos && photoLinks) {
            const lines = photoLinks.value
                .split('\n')
                .map(v => v.trim())
                .filter(Boolean);

            previewPhotos.textContent = `${lines.length} photo link${lines.length === 1 ? '' : 's'} added`;
        }

        if (supportsDelivery && previewPhoneStatus) {
            const sendToHome = selectedSendToHome();
            const currentPhone = fullPhone();

            if (!sendToHome) {
                previewPhoneStatus.style.display = 'none';
            } else {
                previewPhoneStatus.style.display = 'inline-flex';

                if (!currentPhone || currentPhone === (phoneCode?.value || '').trim()) {
                    previewPhoneStatus.textContent = 'Phone not added';
                    previewPhoneStatus.className = 'preview-extra neutral';
                } else if (otpVerified && currentPhone === otpCurrentPhone) {
                    previewPhoneStatus.textContent = 'Phone verified';
                    previewPhoneStatus.className = 'preview-extra success';
                } else {
                    previewPhoneStatus.textContent = 'Phone added - verification required';
                    previewPhoneStatus.className = 'preview-extra warning';
                }
            }
        }
    }

    function goBackChooser() {
        window.parent.postMessage('back-to-tribute-chooser', '*');
    }

    function closeOverlay() {
        window.parent.postMessage('close-tribute-overlay', '*');
    }

    function setButtonLoading(button, html) {
        if (!button) return;
        button.dataset.originalHtml = button.innerHTML;
        button.innerHTML = html;
        button.disabled = true;
    }

    function restoreButton(button) {
        if (!button) return;
        if (button.dataset.originalHtml) {
            button.innerHTML = button.dataset.originalHtml;
        }
        button.disabled = false;
    }

    function validateBasicForm() {
        const nameValue = (byName?.value || '').trim();
        const messageValue = (message?.value || '').trim();
        const sendToHome = supportsDelivery ? selectedSendToHome() : false;

        if (!nameValue) {
            setAlert('error', 'Please enter your name.');
            byName?.focus();
            return false;
        }

        if (!messageValue) {
            setAlert('error', 'Please enter your tribute message.');
            message?.focus();
            return false;
        }

        if (sendToHome) {
            const privateName = (fullName?.value || '').trim();
            const phoneValue = fullPhone();
            const codeOnly = (phoneCode?.value || '').trim();
            const mobileValue = (mobile?.value || '').trim();

            if (!privateName) {
                setAlert('error', 'Please enter full name for delivery.');
                fullName?.focus();
                return false;
            }

            if (!mobileValue || !phoneValue || phoneValue === codeOnly) {
                setAlert('error', 'Please enter mobile number for delivery.');
                mobile?.focus();
                return false;
            }

            if (!otpVerified || otpCurrentPhone !== phoneValue) {
                setAlert('error', 'Please verify your mobile number before submitting.');
                otpCode?.focus();
                return false;
            }
        }

        return true;
    }

    async function sendOtp(mode = 'send') {
        clearAlert();

        if (!selectedSendToHome()) {
            setAlert('error', 'Turn on home delivery first.');
            return;
        }

        const phoneValue = fullPhone();
        const codeOnly = (phoneCode?.value || '').trim();
        const mobileValue = (mobile?.value || '').trim();

        if (!mobileValue || !phoneValue || phoneValue === codeOnly) {
            setAlert('error', 'Enter mobile number first.');
            mobile?.focus();
            return;
        }

        const activeButton = mode === 'resend' ? btnResendOtp : btnSendOtp;
        setButtonLoading(
            activeButton,
            mode === 'resend'
                ? '<i class="fa-solid fa-spinner fa-spin"></i> Resending...'
                : '<i class="fa-solid fa-spinner fa-spin"></i> Sending...'
        );

        try {
            const res = await fetch(cfg.sendOtpApi || '../sms_send', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded'
                },
                body: new URLSearchParams({
                    action: 'send_otp',
                    phone: phoneValue
                })
            });

            const json = await res.json();

            if (!json.ok) {
                const msg = json.error || json.message || 'Failed to send verification code.';
                setAlert('error', msg);
                setOtpBadge('error', 'Send failed', msg);
                return;
            }

            otpVerified = false;
            otpCurrentPhone = phoneValue;

            setAlert('success', 'Verification code sent.');
            setOtpBadge('warning', 'Code sent', 'Enter the 6-digit code and verify your mobile number.');
            updatePreview();
        } catch (err) {
            setAlert('error', 'Something went wrong while sending the verification code.');
            setOtpBadge('error', 'Send failed', 'Something went wrong while sending the verification code.');
        } finally {
            restoreButton(activeButton);
        }
    }

    async function verifyOtp() {
        clearAlert();

        if (!selectedSendToHome()) {
            setAlert('error', 'Turn on home delivery first.');
            return;
        }

        const phoneValue = fullPhone();
        const codeOnly = (phoneCode?.value || '').trim();
        const mobileValue = (mobile?.value || '').trim();
        const codeValue = (otpCode?.value || '').trim();

        if (!mobileValue || !phoneValue || phoneValue === codeOnly) {
            setAlert('error', 'Enter mobile number first.');
            mobile?.focus();
            return;
        }

        if (!/^\d{6}$/.test(codeValue)) {
            setAlert('error', 'Enter a valid 6-digit verification code.');
            otpCode?.focus();
            return;
        }

        setButtonLoading(btnVerifyOtp, '<i class="fa-solid fa-spinner fa-spin"></i> Verifying...');

        try {
            const res = await fetch(cfg.sendOtpApi || '../sms_send', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded'
                },
                body: new URLSearchParams({
                    action: 'verify_otp',
                    otp: codeValue
                })
            });

            const json = await res.json();

            if (!json.ok) {
                const msg = json.error || json.message || 'Verification failed.';
                setAlert('error', msg);
                setOtpBadge('error', 'Invalid code', msg);
                return;
            }

            markVerified(phoneValue);
            setAlert('success', 'Mobile number verified successfully.');
        } catch (err) {
            setAlert('error', 'Something went wrong while verifying the code.');
            setOtpBadge('error', 'Verify failed', 'Something went wrong while verifying the code.');
        } finally {
            restoreButton(btnVerifyOtp);
        }
    }

    [byName, byCountry, message, photoLinks, fullName, mobile].forEach(el => {
        el?.addEventListener('input', updatePreview);
        el?.addEventListener('change', updatePreview);
    });

    phoneCode?.addEventListener('change', function () {
        syncPhoneCodeDisplay();
        updatePreview();
        resetVerificationState();
    });

    mobile?.addEventListener('input', function () {
        const currentPhone = fullPhone();

        if (!currentPhone) {
            resetVerificationState();
            return;
        }

        if (otpVerified && currentPhone === otpCurrentPhone) {
            updatePreview();
            return;
        }

        resetVerificationState();
    });

    sendToHomeInputs.forEach(input => {
        input.addEventListener('change', updateDeliveryUI);
    });

    otpCode?.addEventListener('input', clearAlert);

    btnBackChooser?.addEventListener('click', goBackChooser);
    btnBackBottom?.addEventListener('click', goBackChooser);
    btnCloseTribute?.addEventListener('click', closeOverlay);

    btnSendOtp?.addEventListener('click', function () {
        sendOtp('send');
    });

    btnResendOtp?.addEventListener('click', function () {
        sendOtp('resend');
    });

    btnVerifyOtp?.addEventListener('click', function () {
        verifyOtp();
    });

    form?.addEventListener('submit', async function (e) {
        e.preventDefault();

        clearAlert();

        if (!validateBasicForm()) {
            return;
        }

        setButtonLoading(btnSubmit, '<i class="fa-solid fa-spinner fa-spin"></i> Submitting...');

        try {
            const fd = new FormData(form);

            const extraData = {};
            const sendToHome = supportsDelivery ? selectedSendToHome() : false;

            if (photoLinks) {
                const lines = photoLinks.value
                    .split('\n')
                    .map(v => v.trim())
                    .filter(Boolean);

                extraData.photo_links = lines;
            }

            extraData.send_to_home = sendToHome ? 1 : 0;

            fd.set('send_to_home', sendToHome ? '1' : '0');
            fd.append('extra_json', JSON.stringify(extraData));

            const res = await fetch(cfg.submitApi || '../api/tribute_entry_create.php', {
                method: 'POST',
                body: fd,
                credentials: 'same-origin'
            });

            const json = await res.json();

            if (!json.ok) {
                setAlert('error', json.message || 'Failed to submit tribute.');
                return;
            }

            setAlert('success', json.message || 'Tribute submitted successfully.');

            setTimeout(() => {
                window.parent.postMessage('close-tribute-overlay', '*');
                window.parent.location.reload();
            }, 900);
        } catch (err) {
            setAlert('error', 'Something went wrong while submitting tribute.');
        } finally {
            restoreButton(btnSubmit);
        }
    });

    syncPhoneCodeDisplay();
    updateDeliveryUI();
    updatePreview();
})();