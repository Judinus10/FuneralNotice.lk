const pricingState = {
    postId: window.PRICING_POST_ID || 0,
    currency: 'LKR',
    postType: 'obituary',
    selectedPlan: null,
    extras: {
        live: false,
        social: false,
        media: []
    },
    pricing: null
};

function pricingToast(message, type = 'info', timeout = 3000) {
    const stack = document.getElementById('toastStack');
    if (!stack) return;

    const item = document.createElement('div');
    item.className = `toast-pop ${type} show`;
    item.innerHTML = `
        <div>
            <div class="toast-title">${type === 'success' ? 'Success' : type === 'error' ? 'Error' : 'Info'}</div>
            <div class="toast-msg">${message}</div>
        </div>
        <button class="toast-x" type="button">×</button>
    `;

    stack.appendChild(item);

    const close = () => {
        item.classList.remove('show');
        setTimeout(() => item.remove(), 250);
    };

    item.querySelector('.toast-x')?.addEventListener('click', close);
    setTimeout(close, timeout);
}

function moneyText(lkr, usd) {
    if (pricingState.currency === 'LKR') {
        return `LKR ${Number(lkr || 0).toLocaleString()}`;
    }
    return `USD ${Number(usd || 0).toFixed(2)}`;
}

async function loadPricingBootstrap() {
    try {
        const res = await fetch(`api/pricing_bootstrap_get.php?post_id=${encodeURIComponent(pricingState.postId)}`, {
            credentials: 'include'
        });

        const data = await res.json();

        if (!data.ok) {
            pricingToast(data.message || 'Failed to load pricing.', 'error');
            return;
        }

        pricingState.currency = data.currency || 'LKR';
        pricingState.postType = data.post_type || 'obituary';
        pricingState.pricing = data.pricing || {};

        document.getElementById('currencyBadge').textContent = pricingState.currency;
        document.getElementById('pricingSubtitle').textContent =
            pricingState.postType === 'remembrance'
                ? 'Select remembrance duration and additional services.'
                : 'Select obituary duration and additional services.';

        document.getElementById('infoBlock').textContent =
            data.info_text && data.info_text.trim() !== ''
                ? data.info_text
                : 'No additional rules have been published yet.';

        renderPricingOptions();
        renderBasket();
    } catch (err) {
        console.error(err);
        pricingToast('Failed to load pricing.', 'error');
    }
}

function renderPricingOptions() {
    const wrap = document.getElementById('featureList');
    if (!wrap || !pricingState.pricing) return;

    const sections = [];
    const baseKey = pricingState.postType === 'remembrance' ? 'remembrance_days' : 'memorial_time';
    const baseTitle = pricingState.postType === 'remembrance' ? 'Days & Prices' : 'Days & Prices';

    const basePlans = Array.isArray(pricingState.pricing[baseKey]) ? pricingState.pricing[baseKey] : [];
    const mediaSites = Array.isArray(pricingState.pricing.media_website) ? pricingState.pricing.media_website : [];

    let html = '';

    html += `<div class="list-section-label">${baseTitle}</div>`;

    basePlans.forEach((row, index) => {
        const label = row.label || '';
        const days = Number(row.days || 0);
        const lkr = Number(row.lkr || 0);
        const usd = Number(row.usd || 0);

        html += `
            <label class="feature-row plan-card" data-plan-index="${index}">
                <input type="radio" name="memPlan" value="${index}">
                <div class="feature-main">
                    <div class="feature-title">${label}</div>
                    <div class="feature-sub">
                        ${pricingState.postType === 'remembrance'
                ? `Remembrance page visible for ${days === 0 ? 'lifetime' : `${days} days`}.`
                : `Memorial visible for ${days === 0 ? 'lifetime' : `${days} days`}.`
            }
                    </div>
                </div>
                <div class="feature-price">${moneyText(lkr, usd)}</div>
            </label>
        `;
    });

    if (pricingState.pricing.live_arrangement) {
        const row = pricingState.pricing.live_arrangement;
        html += `
            <div class="list-section-label">Live Coverage</div>
            <label class="feature-row">
                <input type="checkbox" id="extraLive">
                <div class="feature-main">
                    <div class="feature-title">${row.label || 'Live Arrangement'}</div>
                    <div class="feature-sub">Include live coverage for the funeral.</div>
                </div>
                <div class="feature-price">${moneyText(row.lkr, row.usd)}</div>
            </label>
        `;
    }

    if (pricingState.pricing.social_media) {
        const row = pricingState.pricing.social_media;
        html += `
            <div class="list-section-label">Social Media Publish</div>
            <label class="feature-row">
                <input type="checkbox" id="extraSocial">
                <div class="feature-main">
                    <div class="feature-title">${row.label || 'Social Media Publish'}</div>
                    <div class="feature-sub">Publish on Facebook and Instagram.</div>
                </div>
                <div class="feature-price">${moneyText(row.lkr, row.usd)}</div>
            </label>
        `;
    }

    if (mediaSites.length) {
        html += `<div class="list-section-label">Media Websites</div>`;

        const defaultMediaSites = [
            'funeralnotice',
            'funeralnews',
            'digitalnotice',
            'ripnotice',
            'ripnews'
        ];

        mediaSites.forEach((row, index) => {

            const label = (row.label || '').toLowerCase();

            const isDefault = defaultMediaSites.some(site =>
                label.includes(site)
            );

            html += `
        <label class="feature-row">
            <input 
                type="checkbox" 
                class="media-option" 
                value="${index}" 
                ${isDefault ? 'checked' : ''}
            >

            <div class="feature-main">
                <div class="feature-title">${row.label || ''}</div>
                <div class="feature-sub">Publish on this external media website.</div>
            </div>

            <div class="feature-price">${moneyText(row.lkr, row.usd)}</div>
        </label>
    `;
        });
    }

    wrap.innerHTML = html;

    bindPricingEvents(basePlans, mediaSites);
    // apply default selections to state
    document.querySelectorAll('.media-option').forEach(chk => {
        if (chk.checked) {
            const idx = Number(chk.value);
            if (!pricingState.extras.media.includes(idx)) {
                pricingState.extras.media.push(idx);
            }
        }
    });

    renderBasket();
}

function bindPricingEvents(basePlans, mediaSites) {
    document.querySelectorAll('input[name="memPlan"]').forEach(radio => {
        radio.addEventListener('change', function () {
            const index = Number(this.value);
            pricingState.selectedPlan = basePlans[index] || null;

            document.querySelectorAll('.plan-card').forEach(card => card.classList.remove('is-selected'));
            this.closest('.plan-card')?.classList.add('is-selected');

            renderBasket();
        });
    });

    document.getElementById('extraLive')?.addEventListener('change', function () {
        pricingState.extras.live = this.checked;
        renderBasket();
    });

    document.getElementById('extraSocial')?.addEventListener('change', function () {
        pricingState.extras.social = this.checked;
        renderBasket();
    });

    document.querySelectorAll('.media-option').forEach(chk => {
        chk.addEventListener('change', function () {
            const idx = Number(this.value);
            if (this.checked) {
                if (!pricingState.extras.media.includes(idx)) {
                    pricingState.extras.media.push(idx);
                }
            } else {
                pricingState.extras.media = pricingState.extras.media.filter(x => x !== idx);
            }
            renderBasket();
        });
    });
}

function renderBasket() {
    const summaryEl = document.getElementById('basketSummary');
    const totalEl = document.getElementById('basketTotal');
    const desktopBtn = document.getElementById('desktopSubmitBtn');
    const mobileBtn = document.getElementById('mobileSubmitBtn');

    const hiddenDays = document.getElementById('duration_days');
    const hiddenLive = document.getElementById('has_live_coverage');
    const hiddenSocial = document.getElementById('has_social_media');
    const hiddenMedia = document.getElementById('has_media_website');

    const lines = [];
    let totalLkr = 0;
    let totalUsd = 0;

    if (pricingState.selectedPlan) {
        lines.push(`<strong>Duration:</strong> ${pricingState.selectedPlan.label}`);
        totalLkr += Number(pricingState.selectedPlan.lkr || 0);
        totalUsd += Number(pricingState.selectedPlan.usd || 0);
        hiddenDays.value = pricingState.selectedPlan.days || 0;
    } else {
        hiddenDays.value = '';
    }

    if (pricingState.extras.live && pricingState.pricing.live_arrangement) {
        lines.push(`<strong>Extra:</strong> ${pricingState.pricing.live_arrangement.label}`);
        totalLkr += Number(pricingState.pricing.live_arrangement.lkr || 0);
        totalUsd += Number(pricingState.pricing.live_arrangement.usd || 0);
    }

    if (pricingState.extras.social && pricingState.pricing.social_media) {
        lines.push(`<strong>Extra:</strong> ${pricingState.pricing.social_media.label}`);
        totalLkr += Number(pricingState.pricing.social_media.lkr || 0);
        totalUsd += Number(pricingState.pricing.social_media.usd || 0);
    }

    if (pricingState.extras.media.length && Array.isArray(pricingState.pricing.media_website)) {
        pricingState.extras.media.forEach(idx => {
            const row = pricingState.pricing.media_website[idx];
            if (!row) return;
            lines.push(`<strong>Media:</strong> ${row.label}`);
            totalLkr += Number(row.lkr || 0);
            totalUsd += Number(row.usd || 0);
        });
    }

    hiddenLive.value = pricingState.extras.live ? '1' : '0';
    hiddenSocial.value = pricingState.extras.social ? '1' : '0';
    hiddenMedia.value = pricingState.extras.media.length ? '1' : '0';

    summaryEl.innerHTML = lines.length ? lines.join('<br>') : '<span class="basket-empty">No services selected yet.</span>';
    totalEl.textContent = moneyText(totalLkr, totalUsd);

    const canSubmit = !!pricingState.selectedPlan;
    desktopBtn.disabled = !canSubmit;
    mobileBtn.disabled = !canSubmit;

    setStepper(canSubmit ? 5 : 4);
}

function setStepper(step) {
    const fill = document.getElementById('stepperFill');
    const steps = document.querySelectorAll('.stepper-step');

    steps.forEach(el => {
        const n = Number(el.dataset.step || 0);
        el.classList.remove('is-active', 'is-done', 'is-disabled');

        if (n < step) {
            el.classList.add('is-done');
        } else if (n === step) {
            el.classList.add('is-active');
        } else {
            el.classList.add('is-disabled');
        }
    });

    if (fill) {
        const total = steps.length;
        const pct = step <= 1 ? 0 : ((step - 1) / (total - 1)) * 100;
        fill.style.width = `${pct}%`;
    }
}

async function submitPricingForm(e) {
    e.preventDefault();

    if (!pricingState.selectedPlan) {
        pricingToast('Please select a duration plan.', 'error');
        return;
    }

    const form = document.getElementById('pricingForm');
    const formData = new FormData(form);

    formData.append('media_sites', JSON.stringify(
        pricingState.extras.media.map(idx => pricingState.pricing.media_website[idx]?.label).filter(Boolean)
    ));

    const desktopBtn = document.getElementById('desktopSubmitBtn');
    const mobileBtn = document.getElementById('mobileSubmitBtn');
    const oldDesktop = desktopBtn.innerHTML;
    const oldMobile = mobileBtn.innerHTML;

    desktopBtn.disabled = true;
    mobileBtn.disabled = true;
    desktopBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Submitting...';
    mobileBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Submitting...';

    try {
        const res = await fetch('api/pricing_save.php', {
            method: 'POST',
            credentials: 'include',
            body: formData
        });

        const data = await res.json();

        if (!data.ok) {
            pricingToast(data.message || 'Could not save pricing.', 'error');
            desktopBtn.disabled = false;
            mobileBtn.disabled = false;
            desktopBtn.innerHTML = oldDesktop;
            mobileBtn.innerHTML = oldMobile;
            return;
        }

        pricingToast('Pricing saved successfully. Redirecting...', 'success');

        setTimeout(() => {
            window.location.href = data.redirect || `memorial-detail.php?id=${pricingState.postId}`;
        }, 700);
    } catch (err) {
        console.error(err);
        pricingToast('Something went wrong while saving pricing.', 'error');
        desktopBtn.disabled = false;
        mobileBtn.disabled = false;
        desktopBtn.innerHTML = oldDesktop;
        mobileBtn.innerHTML = oldMobile;
    }
}

document.addEventListener('DOMContentLoaded', async function () {
    await loadPricingBootstrap();
    document.getElementById('pricingForm')?.addEventListener('submit', submitPricingForm);
});