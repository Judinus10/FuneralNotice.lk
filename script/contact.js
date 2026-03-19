/**
 * contact.js
 * Real backend version for FuneralNotice.lk contact page
 */

(function () {
    const API_BOOTSTRAP = 'api/contact_bootstrap_get.php';
    const API_OTP_SEND = 'api/contact_otp_send.php';
    const API_OTP_VERIFY = 'api/contact_otp_verify.php';
    const API_MESSAGE_SUBMIT = 'api/contact_message_submit.php';

    let bootstrapData = null;
    let timerId = null;
    let otpRemaining = 60;
    let lastPhone = '';
    let verifiedPhone = '';

    const I18N = window.CONTACT_I18N || {};

    function t(key, fallback = '') {
        const value = I18N[key];
        return typeof value === 'string' && value.trim() !== '' ? value : fallback;
    }

    function qs(selector, root = document) {
        return root.querySelector(selector);
    }

    function qsa(selector, root = document) {
        return Array.from(root.querySelectorAll(selector));
    }

    function showToast(message, type = 'info', timeout = 3500) {
        const wrap = qs('#toastWrap');
        if (!wrap) return;

        const item = document.createElement('div');
        item.className = `toast-item toast-${type}`;
        item.textContent = message;
        wrap.appendChild(item);

        requestAnimationFrame(() => item.classList.add('show'));

        setTimeout(() => {
            item.classList.remove('show');
            setTimeout(() => item.remove(), 250);
        }, timeout);
    }

    function showFormMessage(message, type = 'error') {
        const box = qs('#formMessage');
        if (!box) return;

        box.className = `api-message show ${type}`;
        box.textContent = message;

        window.scrollTo({
            top: box.getBoundingClientRect().top + window.scrollY - 120,
            behavior: 'smooth'
        });
    }

    function clearFormMessage() {
        const box = qs('#formMessage');
        if (!box) return;
        box.className = 'api-message';
        box.textContent = '';
    }

    function escapeHtml(value) {
        return String(value ?? '')
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#039;');
    }

    async function postForm(url, data) {
        const fd = new FormData();
        Object.entries(data).forEach(([key, value]) => fd.append(key, value));

        const res = await fetch(url, {
            method: 'POST',
            body: fd,
            credentials: 'same-origin'
        });

        let json;
        try {
            json = await res.json();
        } catch (e) {
            throw new Error('Invalid server response.');
        }

        return json;
    }

    async function getJson(url) {
        const res = await fetch(url, {
            method: 'GET',
            credentials: 'same-origin'
        });

        let json;
        try {
            json = await res.json();
        } catch (e) {
            throw new Error('Invalid server response.');
        }

        return json;
    }

    function buildContactCards(data) {
        const grid = qs('#contactInfoGrid');
        if (!grid) return;

        const cards = [];

        cards.push(`
            <div class="contact-card">
                <div class="contact-icon">
                    <i class="fas fa-phone-alt"></i>
                </div>
                <h3>${escapeHtml(t('hotlineTitle', data.hotline_caption || 'Hotline'))}</h3>
                <p>${escapeHtml(t('hotlineNote', data.hotline_note || 'Reach our support hotline for urgent help.'))}</p>
                <div class="contact-detail-wrap">
                    <a class="contact-detail" href="tel:${escapeHtml((data.hotline_number || '').replace(/\s+/g, ''))}">
                        ${escapeHtml(data.hotline_number || '')}
                    </a>
                </div>
            </div>
        `);

        cards.push(`
            <div class="contact-card">
                <div class="contact-icon">
                    <i class="fas fa-envelope"></i>
                </div>
                <h3>${escapeHtml(t('emailSupportTitle', 'Email Support'))}</h3>
                <p>${escapeHtml(t('emailSupportNote', 'Send us your inquiry and we will respond as soon as possible.'))}</p>
                <div class="contact-detail-wrap">
                    ${data.support_email ? `
                        <a class="contact-detail" href="mailto:${escapeHtml(data.support_email)}">
                            ${escapeHtml(data.support_email)}
                        </a>
                    ` : ''}
                    ${data.office_email && data.office_email !== data.support_email ? `
                        <a class="contact-detail" href="mailto:${escapeHtml(data.office_email)}">
                            ${escapeHtml(data.office_email)}
                        </a>
                    ` : ''}
                </div>
            </div>
        `);

        const countryHtml = Array.isArray(data.countries) && data.countries.length
            ? `
                <div class="country-list">
                    ${data.countries.map(item => `
                        <div class="country-pill">
                            <strong>${escapeHtml(item.country_name)}</strong>
                            <span>${escapeHtml(item.local_phone)}</span>
                            ${item.note ? `<div class="contact-note">${escapeHtml(item.note)}</div>` : ''}
                        </div>
                    `).join('')}
                </div>
            `
            : `<div class="contact-note">${escapeHtml(t('noIntlNumbers', 'No alternate country contact numbers found.'))}</div>`;

        cards.push(`
            <div class="contact-card">
                <div class="contact-icon">
                    <i class="fas fa-globe-asia"></i>
                </div>
                <h3>${escapeHtml(t('intlTitle', 'International Numbers'))}</h3>
                <p>${escapeHtml(t('intlNote', 'Use the most suitable number based on your country.'))}</p>
                ${countryHtml}
            </div>
        `);

        grid.innerHTML = cards.join('');
    }

    function buildPhoneCodeOptions(data) {
        const select = qs('#phone_code');
        if (!select) return;

        const list = Array.isArray(data.phone_countries) ? data.phone_countries : [];
        const selectedCode = data.default_phone_code || '+94';

        select.innerHTML = list.map(item => {
            const code = String(item.code || '').trim();
            const name = String(item.name || '').trim();
            const selected = code === selectedCode ? 'selected' : '';
            return `<option value="${escapeHtml(code)}" ${selected}>${escapeHtml(name)} (${escapeHtml(code)})</option>`;
        }).join('');
    }

    function getPhoneValue() {
        const code = qs('#phone_code')?.value?.trim() || '';
        const mobile = qs('#mobile')?.value?.trim() || '';
        return `${code}${mobile}`.trim();
    }

    function validateFormBeforeOtp() {
        const name = qs('#name')?.value.trim() || '';
        const email = qs('#email')?.value.trim() || '';
        const mobile = qs('#mobile')?.value.trim() || '';
        const subject = qs('#subject')?.value.trim() || '';
        const message = qs('#message')?.value.trim() || '';

        if (!name) return t('fillRequired', 'Please fill all required fields.');
        if (!email) return t('fillRequired', 'Please fill all required fields.');
        if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)) return t('invalidEmail', 'Please enter a valid email address.');
        if (!mobile) return t('fillRequired', 'Please fill all required fields.');
        if (mobile.replace(/\D/g, '').length < 7) return t('invalidPhone', 'Please enter a valid phone number.');
        if (!subject) return t('fillRequired', 'Please fill all required fields.');
        if (!message) return t('fillRequired', 'Please fill all required fields.');

        return '';
    }

    function setSubmitLoading(loading) {
        const btn = qs('#submitBtn');
        if (!btn) return;

        btn.disabled = loading;
        btn.innerHTML = loading
            ? `<i class="fas fa-spinner fa-spin"></i> <span>${escapeHtml(t('pleaseWait', 'Please wait...'))}</span>`
            : `<i class="fas fa-paper-plane"></i> <span>${escapeHtml(t('sendMessage', 'Send Message'))}</span>`;
    }

    function setOtpError(message = '') {
        const box = qs('#otpError');
        if (!box) return;
        box.textContent = message;
    }

    function setOtpButtonMode(mode) {
        const btn = qs('#otpVerifyBtn');
        if (!btn) return;

        btn.dataset.mode = mode;
        btn.textContent = mode === 'resend'
            ? t('resendOtp', 'Resend Code')
            : t('verify', 'Verify');
        btn.disabled = false;
    }

    function startOtpTimer(seconds = 60) {
        const timerEl = qs('#otpTimer');
        otpRemaining = seconds;

        if (timerEl) timerEl.textContent = String(otpRemaining);

        if (timerId) clearInterval(timerId);

        timerId = setInterval(() => {
            otpRemaining--;
            if (timerEl) timerEl.textContent = String(otpRemaining);

            if (otpRemaining <= 0) {
                clearInterval(timerId);
                timerId = null;
                setOtpButtonMode('resend');
                setOtpError(t('otpExpired', 'OTP has expired.'));
            }
        }, 1000);
    }

    function openOtpPopup(phone) {
        lastPhone = phone;
        const popup = qs('#otpPopup');
        const input = qs('#otpCodeInput');

        if (!popup || !input) return;

        popup.classList.add('show');
        input.value = '';
        setOtpError('');
        setOtpButtonMode('verify');
        startOtpTimer(60);
        input.focus();
    }

    function closeOtpPopup() {
        const popup = qs('#otpPopup');
        if (popup) popup.classList.remove('show');

        if (timerId) {
            clearInterval(timerId);
            timerId = null;
        }

        setOtpError('');
    }

    async function loadBootstrap() {
        const loader = qs('#contactPageLoader');
        const content = qs('#contactContent');

        try {
            const res = await getJson(API_BOOTSTRAP);

            if (!res.ok) {
                throw new Error(res.message || 'Failed to load page.');
            }

            bootstrapData = res.data || {};
            buildContactCards(bootstrapData);
            buildPhoneCodeOptions(bootstrapData);

            if (loader) loader.classList.add('hidden');
            if (content) content.classList.remove('hidden');
        } catch (err) {
            if (loader) loader.classList.add('hidden');
            showFormMessage(err.message || 'Failed to load contact page data.', 'error');
        }
    }

    async function sendOtp(phone, isResend = false) {
        const btn = qs('#otpVerifyBtn');
        if (btn) {
            btn.disabled = true;
            btn.textContent = t('pleaseWait', 'Please wait...');
        }

        setOtpError('');

        const res = await postForm(API_OTP_SEND, { phone });

        if (!res.ok) {
            throw new Error(res.message || 'Failed to send OTP.');
        }

        showToast(
            isResend
                ? t('otpResentSuccess', 'OTP resent successfully.')
                : t('otpSentSuccess', 'OTP sent successfully.'),
            'success'
        );

        const input = qs('#otpCodeInput');
        if (input) input.value = '';

        setOtpButtonMode('verify');
        startOtpTimer(60);
    }

    async function verifyOtp(phone, otp) {
        const btn = qs('#otpVerifyBtn');
        if (btn) {
            btn.disabled = true;
            btn.textContent = t('verifying', 'Verifying...');
        }

        setOtpError('');

        const res = await postForm(API_OTP_VERIFY, { phone, otp });

        if (!res.ok) {
            throw new Error(res.message || t('otpInvalid', 'Invalid OTP code.'));
        }

        verifiedPhone = phone;
        return res;
    }

    async function submitContactForm() {
        const form = qs('#contactForm');
        if (!form) return;

        const res = await postForm(API_MESSAGE_SUBMIT, {
            name: qs('#name')?.value.trim() || '',
            email: qs('#email')?.value.trim() || '',
            phone_code: qs('#phone_code')?.value.trim() || '',
            mobile: qs('#mobile')?.value.trim() || '',
            subject: qs('#subject')?.value.trim() || '',
            message: qs('#message')?.value.trim() || ''
        });

        if (!res.ok) {
            throw new Error(res.message || t('messageFailed', 'Failed to save your message.'));
        }

        showFormMessage(res.message || t('messageSent', 'Your message has been sent successfully.'), 'success');
        showToast(t('messageSent', 'Your message has been sent successfully.'), 'success');
        form.reset();

        if (bootstrapData?.default_phone_code) {
            qs('#phone_code').value = bootstrapData.default_phone_code;
        }

        verifiedPhone = '';
    }

    function bindForm() {
        const form = qs('#contactForm');
        const otpCancelBtn = qs('#otpCancelBtn');
        const otpVerifyBtn = qs('#otpVerifyBtn');
        const otpInput = qs('#otpCodeInput');

        if (!form) return;

        form.addEventListener('submit', async (e) => {
            e.preventDefault();
            clearFormMessage();

            const error = validateFormBeforeOtp();
            if (error) {
                showFormMessage(error, 'error');
                return;
            }

            const phone = getPhoneValue();

            if (verifiedPhone && verifiedPhone === phone) {
                try {
                    setSubmitLoading(true);
                    await submitContactForm();
                } catch (err) {
                    showFormMessage(err.message || t('messageFailed', 'Failed to send your message.'), 'error');
                } finally {
                    setSubmitLoading(false);
                }
                return;
            }

            try {
                setSubmitLoading(true);
                await sendOtp(phone, false);
                openOtpPopup(phone);
            } catch (err) {
                showFormMessage(err.message || 'Failed to send OTP.', 'error');
                showToast(err.message || 'Failed to send OTP.', 'error');
            } finally {
                setSubmitLoading(false);
            }
        });

        otpCancelBtn?.addEventListener('click', () => {
            closeOtpPopup();
            showToast(t('otpCancelled', 'OTP cancelled.'), 'info');
        });

        otpVerifyBtn?.addEventListener('click', async () => {
            const mode = otpVerifyBtn.dataset.mode || 'verify';

            if (mode === 'resend') {
                try {
                    otpVerifyBtn.disabled = true;
                    await sendOtp(lastPhone, true);
                } catch (err) {
                    setOtpError(err.message || t('resendOtpFailed', 'Failed to resend OTP.'));
                    showToast(err.message || t('resendOtpFailed', 'Failed to resend OTP.'), 'error');
                    otpVerifyBtn.disabled = false;
                }
                return;
            }

            const otp = (otpInput?.value || '').replace(/\D/g, '');

            if (otp.length !== 6) {
                setOtpError(t('enterOtp', 'Enter the 6-digit OTP code.'));
                return;
            }

            try {
                await verifyOtp(lastPhone, otp);
                closeOtpPopup();
                showToast(t('phoneVerified', 'Phone verified successfully.'), 'success');

                setSubmitLoading(true);
                await submitContactForm();
            } catch (err) {
                const msg = err.message || t('otpInvalid', 'OTP verification failed.');
                setOtpError(msg);
                showToast(msg, 'error');
            } finally {
                setSubmitLoading(false);
                setOtpButtonMode('verify');
            }
        });

        otpInput?.addEventListener('keydown', (e) => {
            if (e.key === 'Enter') {
                e.preventDefault();
                otpVerifyBtn?.click();
            }
            if (e.key === 'Escape') {
                e.preventDefault();
                otpCancelBtn?.click();
            }
        });
    }

    function bindLegacyExtras() {
        document.getElementById('createFuneralNoticeBtn')?.addEventListener('click', function () {
            this.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Creating...';
            this.disabled = true;

            setTimeout(() => {
                window.location.href = 'create.php';
            }, 500);
        });

        document.querySelector('.language-selector')?.addEventListener('click', function () {
            const span = this.querySelector('span');
            if (span) {
                span.textContent = span.textContent === 'English' ? 'தமிழ்' : 'English';
            }
        });

        qsa('.search-box input[type="text"]').forEach(input => {
            input.addEventListener('keypress', function (e) {
                if (e.key === 'Enter' && this.value.trim()) {
                    showToast(`${t('searchingFor', 'Searching for:')} ${this.value}`, 'info');
                }
            });
        });

        const currentPage = window.location.pathname.split('/').pop();
        qsa('.mobile-menu-nav a').forEach(link => {
            link.classList.remove('active');
            if (
                link.getAttribute('href') === currentPage ||
                (currentPage === '' && link.getAttribute('href') === 'index.php')
            ) {
                link.classList.add('active');
            }
        });

        if (window.innerWidth <= 992 && typeof initMobileApp === 'function') {
            initMobileApp();
        }
    }

    document.addEventListener('DOMContentLoaded', async function () {
        bindForm();
        bindLegacyExtras();
        await loadBootstrap();
    });
})();