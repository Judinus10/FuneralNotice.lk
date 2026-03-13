let uploadedPhoto = null;
let captchaQuestion = '';
let currentPostId = 0;
let otpTimerId = null;
let otpRemaining = 60;
let bootstrapData = null;

function showToast(message, type = "info", timeout = 3500) {
    const wrap = document.getElementById('toastWrap');
    if (!wrap) return;

    const item = document.createElement('div');
    item.className = `toast-item toast-${type}`;
    item.innerHTML = `<span class="toast-close">×</span>${message}`;

    wrap.appendChild(item);
    requestAnimationFrame(() => item.classList.add('show'));

    const close = () => {
        item.classList.remove('show');
        setTimeout(() => item.remove(), 250);
    };

    item.querySelector('.toast-close')?.addEventListener('click', close);
    setTimeout(close, timeout);
}

function formatFileSize(bytes) {
    if (bytes < 1024) return bytes + ' bytes';
    if (bytes < 1048576) return (bytes / 1024).toFixed(1) + ' KB';
    return (bytes / 1048576).toFixed(1) + ' MB';
}

function handlePhotoUpload(event) {
    const file = event.target.files[0];
    if (!file) return;

    if (file.size > 6 * 1024 * 1024) {
        showToast('File size exceeds 6MB limit.', 'error');
        event.target.value = '';
        return;
    }

    if (!file.type.match('image.*')) {
        showToast('Please select a valid image file.', 'error');
        event.target.value = '';
        return;
    }

    uploadedPhoto = file;

    const reader = new FileReader();
    reader.onload = function (e) {
        document.getElementById('uploadedImage').src = e.target.result;
        document.getElementById('photoName').textContent = file.name;
        document.getElementById('photoSize').textContent = formatFileSize(file.size);
        document.getElementById('uploadContainer').style.display = 'none';
        document.getElementById('uploadedPhotoContainer').style.display = 'block';
    };
    reader.readAsDataURL(file);
}

function removePhoto() {
    uploadedPhoto = null;
    document.getElementById('uploadContainer').style.display = 'block';
    document.getElementById('uploadedPhotoContainer').style.display = 'none';
    document.getElementById('photoUpload').value = '';
}

function showStep(stepNo) {
    document.querySelectorAll('.step-panel').forEach((panel, index) => {
        panel.classList.toggle('active', (index + 1) === stepNo);
    });
    window.scrollTo({ top: 0, behavior: 'smooth' });
}

function validateStep1() {
    const requiredIds = [
        'type', 'religion', 'first_name', 'last_name',
        'birth_date', 'birth_place', 'death_date', 'death_place',
        'country', 'address'
    ];

    for (const id of requiredIds) {
        const el = document.getElementById(id);
        if (!el || !String(el.value || '').trim()) {
            showToast('Please fill all required fields in Step 1.', 'error');
            el?.focus();
            return false;
        }
    }

    const livedPlace = document.getElementById('lived_place').value.trim();
    if (!livedPlace) {
        showToast('Please select a lived place.', 'error');
        document.getElementById('lived_place_search')?.focus();
        return false;
    }

    if (!document.getElementById('photoUpload').files.length) {
        showToast('Please upload a cover image.', 'error');
        return false;
    }

    return true;
}

function validateStep2() {
    const requiredIds = ['contact_name', 'phone_code', 'phone', 'id_type', 'id_number'];

    for (const id of requiredIds) {
        const el = document.getElementById(id);
        if (!el || !String(el.value || '').trim()) {
            showToast('Please fill all required fields in Step 2.', 'error');
            el?.focus();
            return false;
        }
    }

    const idType = document.getElementById('id_type').value;
    if (idType === 'NIC') {
        if (!document.getElementById('nic_front').files.length) {
            showToast('NIC front image is required.', 'error');
            return false;
        }
        if (!document.getElementById('nic_back').files.length) {
            showToast('NIC back image is required.', 'error');
            return false;
        }
    }

    if (idType === 'Passport') {
        if (!document.getElementById('passport_image').files.length) {
            showToast('Passport image is required.', 'error');
            return false;
        }
    }

    return true;
}

async function loadBootstrap() {
    const res = await fetch('api/create_bootstrap_get.php', {
        credentials: 'include'
    });
    const data = await res.json();

    if (!data.ok) {
        showToast(data.message || 'Failed to load form data.', 'error');
        return;
    }

    bootstrapData = data;
    captchaQuestion = data.captcha_question || 'Loading...';
    document.getElementById('captchaQ').textContent = captchaQuestion;

    fillSelect('type', data.post_types, 'value', 'label', 'Select post type…');
    fillSimpleSelect('religion', data.religions, 'Select religion…');
    fillSimpleSelect('id_type', data.id_types, 'Select ID type…');
    fillCountrySelect('country', data.countries, data.default_country || 'Sri Lanka');
    fillPhoneCodes('phone_code', data.phone_countries);
    fillPhoneCodes('phone_alt_code', data.phone_countries);

    const today = new Date().toISOString().split('T')[0];
    document.getElementById('birth_date').max = today;
    document.getElementById('death_date').max = today;
}

function fillSelect(id, items, valueKey, labelKey, placeholder) {
    const select = document.getElementById(id);
    if (!select) return;

    select.innerHTML = `<option value="">${placeholder}</option>`;
    items.forEach(item => {
        const opt = document.createElement('option');
        opt.value = item[valueKey];
        opt.textContent = item[labelKey];
        select.appendChild(opt);
    });
}

function fillSimpleSelect(id, items, placeholder) {
    const select = document.getElementById(id);
    if (!select) return;

    select.innerHTML = `<option value="">${placeholder}</option>`;
    items.forEach(item => {
        const opt = document.createElement('option');
        opt.value = item;
        opt.textContent = item;
        select.appendChild(opt);
    });
}

function fillCountrySelect(id, countries, selectedCountry) {
    const select = document.getElementById(id);
    if (!select) return;

    select.innerHTML = `<option value="">Select country…</option>`;
    countries.forEach(country => {
        const opt = document.createElement('option');
        opt.value = country;
        opt.textContent = country;
        if (country === selectedCountry) opt.selected = true;
        select.appendChild(opt);
    });
}

function fillPhoneCodes(id, items) {
    const select = document.getElementById(id);
    if (!select) return;

    select.innerHTML = `<option value="">Code</option>`;
    items.forEach(item => {
        const opt = document.createElement('option');
        opt.value = item.code;
        opt.textContent = `${item.name} (${item.code})`;
        select.appendChild(opt);
    });

    const sri = items.find(x => x.code === '+94');
    if (sri) select.value = '+94';
}

async function loadCities(countryName) {
    const searchEl = document.getElementById('lived_place_search');
    const listEl = document.getElementById('lived_place_list');
    const hiddenEl = document.getElementById('lived_place');

    if (!countryName) {
        listEl.innerHTML = '<option value="">Select country first</option>';
        listEl.disabled = true;
        searchEl.value = '';
        hiddenEl.value = '';
        return;
    }

    listEl.disabled = true;
    searchEl.disabled = true;
    hiddenEl.value = '';
    searchEl.value = '';
    listEl.innerHTML = '<option value="">Loading cities…</option>';

    try {
        const res = await fetch(`api/cities_get.php?country=${encodeURIComponent(countryName)}`, {
            credentials: 'include'
        });
        const data = await res.json();

        if (!data.ok) {
            listEl.innerHTML = `<option value="">${data.message || 'Failed to load cities'}</option>`;
            listEl.disabled = false;
            searchEl.disabled = false;
            return;
        }

        const cities = Array.isArray(data.cities) ? data.cities : [];
        renderCityOptions(cities);
        listEl.disabled = false;
        searchEl.disabled = false;
    } catch (err) {
        listEl.innerHTML = '<option value="">Failed to load cities</option>';
        listEl.disabled = false;
        searchEl.disabled = false;
    }
}

function renderCityOptions(cities, query = '') {
    const listEl = document.getElementById('lived_place_list');
    const q = query.trim().toLowerCase();

    listEl.innerHTML = '';

    const filtered = q
        ? cities.filter(city => city.toLowerCase().includes(q))
        : cities;

    if (!filtered.length) {
        listEl.innerHTML = '<option value="">No cities found</option>';
        return;
    }

    filtered.forEach(city => {
        const opt = document.createElement('option');
        opt.value = city;
        opt.textContent = city;
        listEl.appendChild(opt);
    });
}

async function verifyCaptcha() {
    const input = document.getElementById('captchaInput');
    const err = document.getElementById('captchaErr');
    const btn = document.getElementById('captchaOkBtn');

    const val = (input.value || '').trim();
    if (!val) {
        err.style.display = 'block';
        err.textContent = 'Please enter the answer.';
        return false;
    }

    btn.disabled = true;
    btn.textContent = 'Checking...';

    try {
        const res = await fetch('api/captcha_check.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            },
            credentials: 'include',
            body: JSON.stringify({ answer: val })
        });

        const data = await res.json();

        if (!data.ok) {
            err.style.display = 'block';
            err.textContent = data.message || 'Captcha incorrect.';
            document.getElementById('captchaQ').textContent = data.question || 'Try again';
            input.value = '';
            btn.disabled = false;
            btn.textContent = 'Continue';
            return false;
        }

        err.style.display = 'none';
        btn.disabled = false;
        btn.textContent = 'Continue';
        return true;
    } catch (e) {
        err.style.display = 'block';
        err.textContent = 'Captcha check failed.';
        btn.disabled = false;
        btn.textContent = 'Continue';
        return false;
    }
}

async function submitMemorialForm() {
    const form = document.getElementById('memorialForm');
    const submitBtn = document.getElementById('finalSubmitBtn');
    const originalText = submitBtn.innerHTML;

    submitBtn.disabled = true;
    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Submitting...';

    try {
        const formData = new FormData(form);

        const res = await fetch('api/memorial_create_submit.php', {
            method: 'POST',
            credentials: 'include',
            body: formData
        });

        const data = await res.json();

        if (!data.ok) {
            showToast(data.message || 'Submit failed.', 'error');
            submitBtn.disabled = false;
            submitBtn.innerHTML = originalText;
            return;
        }

        currentPostId = Number(data.post_id || 0);
        showToast(data.message || 'Submitted successfully.', 'success');

        document.getElementById('captchaPopup').style.display = 'none';
        openOtpPopup();
    } catch (err) {
        showToast('Something went wrong while submitting.', 'error');
        submitBtn.disabled = false;
        submitBtn.innerHTML = originalText;
    }
}

function openOtpPopup() {
    const popup = document.getElementById('otpPopup');
    const timerEl = document.getElementById('otpTimer');
    const verifyBtn = document.getElementById('otpVerifyBtn');
    const errorEl = document.getElementById('otpError');
    const input = document.getElementById('otpCodeInput');

    popup.style.display = 'flex';
    errorEl.style.display = 'none';
    input.value = '';
    verifyBtn.disabled = false;
    verifyBtn.textContent = 'Verify';
    verifyBtn.dataset.mode = 'verify';

    otpRemaining = 60;
    timerEl.textContent = String(otpRemaining);

    if (otpTimerId) clearInterval(otpTimerId);
    otpTimerId = setInterval(() => {
        otpRemaining--;
        timerEl.textContent = String(otpRemaining);

        if (otpRemaining <= 0) {
            clearInterval(otpTimerId);
            verifyBtn.textContent = 'Resend OTP';
            verifyBtn.dataset.mode = 'resend';
            errorEl.style.display = 'block';
            errorEl.textContent = 'Code expired. Tap "Resend OTP" to get a new OTP.';
        }
    }, 1000);
}

async function verifyOtp() {
    const code = (document.getElementById('otpCodeInput').value || '').trim();
    const errorEl = document.getElementById('otpError');
    const btn = document.getElementById('otpVerifyBtn');

    if (code.length !== 6) {
        errorEl.style.display = 'block';
        errorEl.textContent = 'Please enter the 6-digit code.';
        return;
    }

    btn.disabled = true;
    btn.textContent = 'Verifying...';

    try {
        const res = await fetch('verify_otp.php', {
            method: 'POST',
            credentials: 'same-origin',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: JSON.stringify({
                post_id: currentPostId,
                code: code.replace(/\D/g, '')
            })
        });

        const data = await res.json();

        if (!data.ok) {
            errorEl.style.display = 'block';
            errorEl.textContent = data.message || 'Invalid or expired code.';
            btn.disabled = false;
            btn.textContent = 'Verify';
            return;
        }

        showToast('OTP verified. Redirecting...', 'success');
        setTimeout(() => {
            window.location.href = data.redirect || ('pricing.php?post_id=' + currentPostId);
        }, 600);
    } catch (err) {
        errorEl.style.display = 'block';
        errorEl.textContent = 'Something went wrong. Please try again.';
        btn.disabled = false;
        btn.textContent = 'Verify';
    }
}

async function resendOtp() {
    const btn = document.getElementById('otpVerifyBtn');
    const errorEl = document.getElementById('otpError');

    btn.disabled = true;
    btn.textContent = 'Sending...';

    try {
        const res = await fetch('resend_otp.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ post_id: currentPostId })
        });

        const data = await res.json();

        if (!data.ok) {
            errorEl.style.display = 'block';
            errorEl.textContent = data.message || 'Could not resend OTP.';
            btn.disabled = false;
            btn.textContent = 'Resend OTP';
            btn.dataset.mode = 'resend';
            return;
        }

        showToast('New OTP sent. Please check your phone.', 'success');
        openOtpPopup();
    } catch (err) {
        errorEl.style.display = 'block';
        errorEl.textContent = 'Resend failed. Please try again.';
        btn.disabled = false;
        btn.textContent = 'Resend OTP';
        btn.dataset.mode = 'resend';
    }
}

document.addEventListener('DOMContentLoaded', async function () {
    await loadBootstrap();

    document.getElementById('choosePhotoBtn')?.addEventListener('click', () => {
        document.getElementById('photoUpload')?.click();
    });

    document.getElementById('photoUpload')?.addEventListener('change', handlePhotoUpload);
    document.getElementById('removePhotoBtn')?.addEventListener('click', removePhoto);

    document.getElementById('btnNext1')?.addEventListener('click', () => {
        if (!validateStep1()) return;
        showStep(2);
    });

    document.getElementById('btnBack1')?.addEventListener('click', () => {
        showStep(1);
    });

    document.getElementById('id_type')?.addEventListener('change', function () {
        const v = this.value;
        document.getElementById('nicFields').style.display = v === 'NIC' ? 'block' : 'none';
        document.getElementById('passportFields').style.display = v === 'Passport' ? 'block' : 'none';
    });

    document.getElementById('country')?.addEventListener('change', async function () {
        await loadCities(this.value);
    });

    const countryEl = document.getElementById('country');
    const searchEl = document.getElementById('lived_place_search');
    const listEl = document.getElementById('lived_place_list');
    const hiddenEl = document.getElementById('lived_place');

    let loadedCities = [];

    async function refreshCitiesForSelectedCountry() {
        const selectedCountry = countryEl.value;
        if (!selectedCountry) return;

        const res = await fetch(`api/cities_get.php?country=${encodeURIComponent(selectedCountry)}`, {
            credentials: 'include'
        });
        const data = await res.json();
        loadedCities = Array.isArray(data.cities) ? data.cities : [];
        renderCityOptions(loadedCities);
    }

    if (countryEl.value) {
        await refreshCitiesForSelectedCountry();
        listEl.disabled = false;
    }

    searchEl?.addEventListener('focus', () => {
        listEl.style.display = 'block';
    });

    searchEl?.addEventListener('input', () => {
        renderCityOptions(loadedCities, searchEl.value);
        listEl.style.display = 'block';
    });

    listEl?.addEventListener('change', () => {
        const v = listEl.value || '';
        hiddenEl.value = v;
        searchEl.value = v;
        listEl.style.display = 'none';
    });

    document.addEventListener('click', (e) => {
        if (!searchEl.contains(e.target) && !listEl.contains(e.target)) {
            listEl.style.display = 'none';
        }
    });

    document.getElementById('btnCallTeam')?.addEventListener('click', () => {
        window.location.href = 'contact.php';
    });

    document.getElementById('btnFillManual')?.addEventListener('click', () => {
        document.getElementById('startPopup').style.display = 'none';
    });

    document.getElementById('captchaCancelBtn')?.addEventListener('click', () => {
        document.getElementById('captchaPopup').style.display = 'none';
    });

    document.getElementById('captchaOkBtn')?.addEventListener('click', async () => {
        const ok = await verifyCaptcha();
        if (!ok) return;
        await submitMemorialForm();
    });

    document.getElementById('memorialForm')?.addEventListener('submit', async (e) => {
        e.preventDefault();

        if (!validateStep2()) return;

        document.getElementById('captchaInput').value = '';
        document.getElementById('captchaErr').style.display = 'none';
        document.getElementById('captchaPopup').style.display = 'flex';
    });

    document.getElementById('otpCancelBtn')?.addEventListener('click', () => {
        document.getElementById('otpPopup').style.display = 'none';
    });

    document.getElementById('otpVerifyBtn')?.addEventListener('click', async function () {
        const mode = this.dataset.mode || 'verify';
        if (mode === 'resend') {
            await resendOtp();
        } else {
            await verifyOtp();
        }
    });
});