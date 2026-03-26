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
    const previewTemplateFrame = document.getElementById('previewTemplateFrame');
    const previewTemplateImage = document.getElementById('previewTemplateImage');
    const previewTemplateStatus = document.getElementById('previewTemplateStatus');

    const templateSection = document.getElementById('templateSection');
    const templateGallery = document.getElementById('templateGallery');
    const templateLoading = document.getElementById('templateLoading');
    const templateEmpty = document.getElementById('templateEmpty');
    const templateIdInput = document.getElementById('template_id');

    const deliveryBlock = document.getElementById('deliveryBlock');
    const sendToHomeHidden = document.getElementById('send_to_home');
    const sendToHomeInputs = document.querySelectorAll('input[name="delivery_choice"]');
    const deliveryOptions = document.querySelectorAll('.delivery-option');
    const deliveryPriceWrap = document.getElementById('deliveryPriceWrap');
    const deliveryPriceBox = document.getElementById('deliveryPriceBox');

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
    const forceDelivery = !!cfg.forceDelivery;

    const i18n = {
        loadingTemplates: cfg.i18n?.loadingTemplates || 'Loading templates...',
        noTemplateDesigns: cfg.i18n?.noTemplateDesigns || 'No template designs are configured.',
        selectTemplateForPrice: cfg.i18n?.selectTemplateForPrice || 'Select a template to see delivery price.',
        notVerified: cfg.i18n?.notVerified || 'Not verified',
        verified: cfg.i18n?.verified || 'Verified',
        postingOnWebsite: cfg.i18n?.postingOnWebsite || 'Posting on website',
        phoneNotAdded: cfg.i18n?.phoneNotAdded || 'Phone not added',
        noTemplateSelected: cfg.i18n?.noTemplateSelected || 'No template selected',
        photoLinksCountDefault: cfg.i18n?.photoLinksCountDefault || '0 photo links added',
        sendToHome: cfg.i18n?.sendToHome || 'Send to Home',

        yourName: 'Your Name',
        country: 'Country',
        previewMessageDefault: 'Your tribute message will appear here.',
        phoneVerified: 'Phone verified',
        phoneVerificationRequired: 'Phone added - verification required',
        sendFailed: 'Send failed',
        invalidCode: 'Invalid code',
        verifyFailed: 'Verify failed',
        codeSent: 'Code sent',
        mobileVerifiedSuccess: 'Mobile number verified successfully.',
        verifyMobileIfDelivery: 'Verify the mobile number only if delivery is needed.',
        sendDeliveryOnly: 'This tribute is delivery only.',
        sendHomeFirst: 'Turn on home delivery first.',
        enterMobileFirst: 'Enter mobile number first.',
        sendVerificationFailed: 'Failed to send verification code.',
        sendVerificationGeneric: 'Something went wrong while sending the verification code.',
        codeSentSuccess: 'Verification code sent.',
        codeSentHint: 'Enter the 6-digit code and verify your mobile number.',
        invalidSixDigitCode: 'Enter a valid 6-digit verification code.',
        verificationFailed: 'Verification failed.',
        verificationGeneric: 'Something went wrong while verifying the code.',
        enterYourName: 'Please enter your name.',
        enterTributeMessage: 'Please enter your tribute message.',
        selectTemplate: 'Please select a template.',
        enterFullNameDelivery: 'Please enter full name for delivery.',
        enterMobileDelivery: 'Please enter mobile number for delivery.',
        selectTemplateBeforeDelivery: 'Please select a template before delivery.',
        verifyMobileBeforeSubmitting: 'Please verify your mobile number before submitting.',
        submitFailed: 'Failed to submit tribute.',
        submitGeneric: 'Something went wrong while submitting tribute.',
        submitSuccess: 'Tribute submitted successfully.',
        submitting: 'Submitting...',
        sending: 'Sending...',
        resending: 'Resending...',
        verifying: 'Verifying...'
    };

    let otpVerified = false;
    let otpCurrentPhone = '';
    let templates = [];
    let selectedTemplate = null;
    let hasTemplates = false;

    function selectedSendToHome() {
        if (!supportsDelivery) return false;
        if (forceDelivery) return true;

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
        setOtpBadge('neutral', i18n.notVerified, i18n.verifyMobileIfDelivery);
        updatePreview();
    }

    function markVerified(phoneValue) {
        otpVerified = true;
        otpCurrentPhone = phoneValue;
        setOtpBadge('success', i18n.verified, i18n.mobileVerifiedSuccess);
        updatePreview();
    }

    function normalizeCountry(v) {
        return String(v || '').trim().toLowerCase();
    }

    function isSriLankanTarget() {
        const code = String(phoneCode?.value || '').trim();
        const country = normalizeCountry(byCountry?.value || '');
        return code === '+94' || country === 'sri lanka' || country === 'lk' || country === 'sl';
    }

    function getCurrencyCode() {
        return isSriLankanTarget() ? 'LKR' : 'USD';
    }

    function formatAmount(n) {
        const num = Number(n || 0);
        return num.toLocaleString(undefined, {
            minimumFractionDigits: num % 1 === 0 ? 0 : 2,
            maximumFractionDigits: 2,
        });
    }

    function currentTypeLabel() {
        return (cfg.title || 'tribute').toLowerCase();
    }

    function getSelectedTemplatePrice() {
        if (!selectedTemplate) return null;
        return isSriLankanTarget()
            ? Number(selectedTemplate.price_local || 0)
            : Number(selectedTemplate.price_foreign || 0);
    }

    function updatePriceBox() {
        if (!supportsDelivery || !deliveryPriceWrap || !deliveryPriceBox) return;

        const sendToHome = selectedSendToHome();
        if (!sendToHome) {
            deliveryPriceWrap.style.display = 'none';
            return;
        }

        deliveryPriceWrap.style.display = 'block';

        if (!hasTemplates) {
            deliveryPriceBox.textContent = 'This tribute type has no template-based delivery pricing.';
            return;
        }

        if (!selectedTemplate) {
            deliveryPriceBox.textContent = i18n.selectTemplateForPrice;
            return;
        }

        const currency = getCurrencyCode();
        const amount = getSelectedTemplatePrice();
        const itemLabel = currentTypeLabel();

        deliveryPriceBox.textContent = `This selected ${itemLabel} will cost ${currency} ${formatAmount(amount)} to send.`;
    }

    function setSelectedTemplate(template) {
        selectedTemplate = template || null;

        if (templateIdInput) {
            templateIdInput.value = selectedTemplate ? String(selectedTemplate.id) : '0';
        }

        if (templateGallery) {
            templateGallery.querySelectorAll('.template-card').forEach(card => {
                const isMatch = Number(card.dataset.templateId || 0) === Number(selectedTemplate?.id || 0);
                card.classList.toggle('is-selected', isMatch);
            });
        }

        updatePreview();
        updatePriceBox();
    }

    function renderTemplates() {
        if (!templateSection || !templateGallery || !templateLoading || !templateEmpty) return;

        templateLoading.style.display = 'none';
        templateGallery.innerHTML = '';

        if (!hasTemplates) {
            templateSection.style.display = 'none';
            templateEmpty.style.display = 'block';
            setSelectedTemplate(null);
            return;
        }

        templateSection.style.display = 'block';
        templateEmpty.style.display = 'none';

        templateGallery.innerHTML = templates.map(tpl => {
            const sizeText = tpl.frame_width_cm && tpl.frame_height_cm
                ? `${tpl.frame_width_cm}cm × ${tpl.frame_height_cm}cm`
                : '';

            return `
                <button type="button"
                        class="template-card"
                        data-template-id="${tpl.id}">
                    <div class="template-thumb">
                        <img src="${tpl.image_url}" alt="${escapeHtml(tpl.name || 'Template')}">
                    </div>
                    <div class="template-meta">
                        <div class="template-name">${escapeHtml(tpl.name || 'Template')}</div>
                        ${sizeText ? `<div class="template-size">${escapeHtml(sizeText)}</div>` : ''}
                        <div class="template-prices">
                            <span>LKR ${formatAmount(tpl.price_local || 0)}</span>
                            <span>USD ${formatAmount(tpl.price_foreign || 0)}</span>
                        </div>
                    </div>
                </button>
            `;
        }).join('');

        templateGallery.querySelectorAll('.template-card').forEach(card => {
            card.addEventListener('click', () => {
                const id = Number(card.dataset.templateId || 0);
                const found = templates.find(t => Number(t.id) === id) || null;
                setSelectedTemplate(found);
            });
        });

        setSelectedTemplate(templates[0] || null);
    }

    async function loadTemplates() {
        if (!cfg.templatesApi || !cfg.slug) return;

        if (templateSection) {
            templateSection.style.display = 'block';
        }
        if (templateLoading) {
            templateLoading.style.display = 'flex';
        }
        if (templateEmpty) {
            templateEmpty.style.display = 'none';
        }

        try {
            const url = `${cfg.templatesApi}?tribute_slug=${encodeURIComponent(cfg.slug)}`;
            const res = await fetch(url, { credentials: 'same-origin' });
            const json = await res.json();

            if (!json.ok) {
                templates = [];
                hasTemplates = false;
                renderTemplates();
                return;
            }

            templates = Array.isArray(json.templates) ? json.templates : [];
            hasTemplates = !!json.has_templates && templates.length > 0;
            renderTemplates();
        } catch (err) {
            templates = [];
            hasTemplates = false;
            renderTemplates();
        }
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
                previewDeliveryStatus.textContent = i18n.postingOnWebsite;
                previewDeliveryStatus.className = 'preview-extra neutral';
            }
        } else {
            if (previewPhoneStatus) {
                previewPhoneStatus.style.display = 'inline-flex';
            }

            if (previewDeliveryStatus) {
                previewDeliveryStatus.textContent = i18n.sendToHome;
                previewDeliveryStatus.className = 'preview-extra warning';
            }
        }

        updatePriceBox();
        updatePreview();
    }

    function updatePreview() {
        if (previewName) {
            previewName.textContent = (byName?.value || '').trim() || i18n.yourName;
        }

        if (previewCountry) {
            previewCountry.textContent = (byCountry?.value || '').trim() || i18n.country;
        }

        if (previewMessage) {
            previewMessage.textContent = (message?.value || '').trim() || i18n.previewMessageDefault;
        }

        if (previewPhotos && photoLinks) {
            const lines = photoLinks.value
                .split('\n')
                .map(v => v.trim())
                .filter(Boolean);

            previewPhotos.textContent = `${lines.length} photo link${lines.length === 1 ? '' : 's'} added`;
        }

        if (previewTemplateFrame && previewTemplateImage && previewTemplateStatus) {
            if (selectedTemplate && selectedTemplate.image_url) {
                previewTemplateFrame.style.display = 'block';
                previewTemplateImage.src = selectedTemplate.image_url;
                previewTemplateStatus.style.display = 'inline-flex';
                previewTemplateStatus.textContent = `${selectedTemplate.name || 'Template'} selected`;
                previewTemplateStatus.className = 'preview-extra success';
            } else {
                previewTemplateFrame.style.display = 'none';
                if (hasTemplates) {
                    previewTemplateStatus.style.display = 'inline-flex';
                    previewTemplateStatus.textContent = i18n.noTemplateSelected;
                    previewTemplateStatus.className = 'preview-extra warning';
                } else {
                    previewTemplateStatus.style.display = 'none';
                }
            }
        }

        if (supportsDelivery && previewPhoneStatus) {
            const sendToHome = selectedSendToHome();
            const currentPhone = fullPhone();
            const codeOnly = (phoneCode?.value || '').trim();

            if (!sendToHome) {
                previewPhoneStatus.style.display = 'none';
            } else {
                previewPhoneStatus.style.display = 'inline-flex';

                if (!currentPhone || currentPhone === codeOnly) {
                    previewPhoneStatus.textContent = i18n.phoneNotAdded;
                    previewPhoneStatus.className = 'preview-extra neutral';
                } else if (otpVerified && currentPhone === otpCurrentPhone) {
                    previewPhoneStatus.textContent = i18n.phoneVerified;
                    previewPhoneStatus.className = 'preview-extra success';
                } else {
                    previewPhoneStatus.textContent = i18n.phoneVerificationRequired;
                    previewPhoneStatus.className = 'preview-extra warning';
                }
            }
        }

        updatePriceBox();
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
            setAlert('error', i18n.enterYourName);
            byName?.focus();
            return false;
        }

        if (!messageValue) {
            setAlert('error', i18n.enterTributeMessage);
            message?.focus();
            return false;
        }

        if (hasTemplates && !selectedTemplate) {
            setAlert('error', i18n.selectTemplate);
            return false;
        }

        if (sendToHome) {
            const privateName = (fullName?.value || '').trim();
            const phoneValue = fullPhone();
            const codeOnly = (phoneCode?.value || '').trim();
            const mobileValue = (mobile?.value || '').trim();

            if (!privateName) {
                setAlert('error', i18n.enterFullNameDelivery);
                fullName?.focus();
                return false;
            }

            if (!mobileValue || !phoneValue || phoneValue === codeOnly) {
                setAlert('error', i18n.enterMobileDelivery);
                mobile?.focus();
                return false;
            }

            if (hasTemplates && !selectedTemplate) {
                setAlert('error', i18n.selectTemplateBeforeDelivery);
                return false;
            }

            if (!otpVerified || otpCurrentPhone !== phoneValue) {
                setAlert('error', i18n.verifyMobileBeforeSubmitting);
                otpCode?.focus();
                return false;
            }
        }

        return true;
    }

    async function sendOtp(mode = 'send') {
        clearAlert();

        if (!selectedSendToHome()) {
            setAlert('error', forceDelivery ? i18n.sendDeliveryOnly : i18n.sendHomeFirst);
            return;
        }

        const phoneValue = fullPhone();
        const codeOnly = (phoneCode?.value || '').trim();
        const mobileValue = (mobile?.value || '').trim();

        if (!mobileValue || !phoneValue || phoneValue === codeOnly) {
            setAlert('error', i18n.enterMobileFirst);
            mobile?.focus();
            return;
        }

        const activeButton = mode === 'resend' ? btnResendOtp : btnSendOtp;
        setButtonLoading(
            activeButton,
            mode === 'resend'
                ? `<i class="fa-solid fa-spinner fa-spin"></i> ${i18n.resending}`
                : `<i class="fa-solid fa-spinner fa-spin"></i> ${i18n.sending}`
        );

        try {
            const res = await fetch(cfg.sendOtpApi || '../sms_send.php', {
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
                const msg = json.error || json.message || i18n.sendVerificationFailed;
                setAlert('error', msg);
                setOtpBadge('error', i18n.sendFailed, msg);
                return;
            }

            otpVerified = false;
            otpCurrentPhone = phoneValue;

            setAlert('success', i18n.codeSentSuccess);
            setOtpBadge('warning', i18n.codeSent, i18n.codeSentHint);
            updatePreview();
        } catch (err) {
            setAlert('error', i18n.sendVerificationGeneric);
            setOtpBadge('error', i18n.sendFailed, i18n.sendVerificationGeneric);
        } finally {
            restoreButton(activeButton);
        }
    }

    async function verifyOtp() {
        clearAlert();

        if (!selectedSendToHome()) {
            setAlert('error', forceDelivery ? i18n.sendDeliveryOnly : i18n.sendHomeFirst);
            return;
        }

        const phoneValue = fullPhone();
        const codeOnly = (phoneCode?.value || '').trim();
        const mobileValue = (mobile?.value || '').trim();
        const codeValue = (otpCode?.value || '').trim();

        if (!mobileValue || !phoneValue || phoneValue === codeOnly) {
            setAlert('error', i18n.enterMobileFirst);
            mobile?.focus();
            return;
        }

        if (!/^\d{6}$/.test(codeValue)) {
            setAlert('error', i18n.invalidSixDigitCode);
            otpCode?.focus();
            return;
        }

        setButtonLoading(btnVerifyOtp, `<i class="fa-solid fa-spinner fa-spin"></i> ${i18n.verifying}`);

        try {
            const res = await fetch(cfg.sendOtpApi || '../sms_send.php', {
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
                const msg = json.error || json.message || i18n.verificationFailed;
                setAlert('error', msg);
                setOtpBadge('error', i18n.invalidCode, msg);
                return;
            }

            markVerified(phoneValue);
            setAlert('success', i18n.mobileVerifiedSuccess);
        } catch (err) {
            setAlert('error', i18n.verificationGeneric);
            setOtpBadge('error', i18n.verifyFailed, i18n.verificationGeneric);
        } finally {
            restoreButton(btnVerifyOtp);
        }
    }

    function escapeHtml(v) {
        const div = document.createElement('div');
        div.textContent = v == null ? '' : String(v);
        return div.innerHTML;
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

    if (!forceDelivery) {
        sendToHomeInputs.forEach(input => {
            input.addEventListener('change', updateDeliveryUI);
        });
    }

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

        setButtonLoading(btnSubmit, `<i class="fa-solid fa-spinner fa-spin"></i> ${i18n.submitting}`);

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
            extraData.currency_code = getCurrencyCode();

            fd.set('send_to_home', sendToHome ? '1' : '0');
            fd.set('template_id', selectedTemplate ? String(selectedTemplate.id) : '0');
            fd.append('extra_json', JSON.stringify(extraData));

            const res = await fetch(cfg.submitApi || '../api/tribute_entry_create.php', {
                method: 'POST',
                body: fd,
                credentials: 'same-origin'
            });

            const json = await res.json();

            if (!json.ok) {
                setAlert('error', json.message || i18n.submitFailed);
                return;
            }

            setAlert('success', json.message || i18n.submitSuccess);

            setTimeout(() => {
                window.parent.postMessage('close-tribute-overlay', '*');
                window.parent.location.reload();
            }, 900);
        } catch (err) {
            setAlert('error', i18n.submitGeneric);
        } finally {
            restoreButton(btnSubmit);
        }
    });

    syncPhoneCodeDisplay();

    if (forceDelivery && sendToHomeHidden) {
        sendToHomeHidden.value = '1';
    }

    updateDeliveryUI();
    updatePreview();
    loadTemplates();
})();