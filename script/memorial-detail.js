let pageData = null;
let currentAdIndex = 0;
let adTimer = null;

function notify(type, title, message, ttl = 3500) {
    const stack = document.getElementById('toastStack');
    if (!stack) return;

    const el = document.createElement('div');
    el.className = `toast-pop ${type || 'info'}`;
    el.innerHTML = `
        <div>
            <p class="toast-title">${title || ''}</p>
            <p class="toast-msg">${message || ''}</p>
        </div>
        <button type="button" class="toast-x" aria-label="Close">×</button>
    `;
    stack.appendChild(el);

    requestAnimationFrame(() => el.classList.add('show'));

    const close = () => {
        el.classList.remove('show');
        setTimeout(() => el.remove(), 180);
    };

    el.querySelector('.toast-x')?.addEventListener('click', close);
    setTimeout(close, ttl);
}

function getId() {
    const qs = new URLSearchParams(window.location.search);
    return qs.get('id') || '';
}

function escapeHtml(v) {
    const div = document.createElement('div');
    div.textContent = v == null ? '' : String(v);
    return div.innerHTML;
}

function nl2brSafe(v) {
    return escapeHtml(v || '').replace(/\n/g, '<br>');
}

async function apiGet(url) {
    const res = await fetch(url, { credentials: 'same-origin' });
    return res.json();
}

async function apiPost(url, formData) {
    const res = await fetch(url, {
        method: 'POST',
        body: formData,
        credentials: 'same-origin'
    });
    return res.json();
}

function updateMetaTags(data) {
    document.title = data.seo_title || 'Memorial - FuneralNotice.lk';

    const setMeta = (selector, value) => {
        const el = document.querySelector(selector);
        if (el) el.setAttribute('content', value || '');
    };

    setMeta('meta[name="description"]', data.share_desc || '');
    setMeta('meta[property="og:url"]', data.share_url || window.location.href);
    setMeta('meta[property="og:title"]', data.share_title || '');
    setMeta('meta[property="og:description"]', data.share_desc || '');
    setMeta('meta[property="og:image"]', data.share_image || '');
    setMeta('meta[name="twitter:title"]', data.share_title || '');
    setMeta('meta[name="twitter:description"]', data.share_desc || '');
    setMeta('meta[name="twitter:image"]', data.share_image || '');
}

function renderSummary(data) {
    const box = document.getElementById('summaryList');
    if (!box) return;

    const items = [];

    if (data.summary?.born_place) {
        items.push(`
            <li class="summary-item">
                <div class="summary-ico"><i class="fa-solid fa-baby"></i></div>
                <div class="summary-text">
                    <div class="label">பிறந்த இடம்</div>
                    <div class="value">${escapeHtml(data.summary.born_place)}</div>
                </div>
            </li>
        `);
    }

    if (data.summary?.lived_place) {
        items.push(`
            <li class="summary-item">
                <div class="summary-ico"><i class="fa-solid fa-location-dot"></i></div>
                <div class="summary-text">
                    <div class="label">வாழ்ந்த இடம்</div>
                    <div class="value">${escapeHtml(data.summary.lived_place)}</div>
                </div>
            </li>
        `);
    }

    if (data.summary?.religion) {
        items.push(`
            <li class="summary-item">
                <div class="summary-ico"><i class="fa-solid fa-hands-praying"></i></div>
                <div class="summary-text">
                    <div class="label">Religion</div>
                    <div class="value">${escapeHtml(data.summary.religion)}</div>
                </div>
            </li>
        `);
    }

    box.innerHTML = items.join('');
}

function renderInlineTributeForm(postId, csrf) {
    return `
        <div class="tribute-inline-form"
             style="background:#fff;border:1px solid #e2e8f0;border-radius:16px;padding:20px;box-shadow:0 4px 12px rgba(0,0,0,0.04);margin-top:16px;">
            <h4 style="margin-top:0;font-size:18px;margin-bottom:14px;">Share Your Feelings</h4>

            <form id="inlineTributeForm">
                <input type="hidden" name="csrf" value="${escapeHtml(csrf)}">
                <input type="hidden" name="post_id" value="${escapeHtml(postId)}">

                <div style="margin-bottom:12px;text-align:left;">
                    <label style="display:block;font-size:14px;font-weight:600;margin-bottom:6px;color:#475569;">Your Name</label>
                    <input type="text" name="sender_name" required
                           style="width:100%;padding:10px 12px;border-radius:10px;border:1px solid #cbd5e1;font-size:15px;">
                </div>

                <div style="margin-bottom:16px;text-align:left;">
                    <label style="display:block;font-size:14px;font-weight:600;margin-bottom:6px;color:#475569;">Message</label>
                    <textarea name="message" required placeholder="Share your memories, prayers or condolences"
                              style="width:100%;padding:10px 12px;border-radius:10px;border:1px solid #cbd5e1;font-size:15px;min-height:80px;font-family:inherit;"></textarea>
                </div>

                <div style="text-align:right;">
                    <button class="btn btn-primary" type="submit">Post Tribute</button>
                </div>
            </form>
        </div>
    `;
}

function renderTributes(data) {
    const list = document.getElementById('tributesList');
    const inlineWrap = document.getElementById('inlineTributeWrap');
    const badge = document.getElementById('tributeCountBadge');
    const countText = document.getElementById('tributeCountText');
    const btnWrite = document.getElementById('btnWriteTribute');
    const btnViewMore = document.getElementById('btnViewMoreTributes');

    if (!list || !inlineWrap) return;

    const tributes = data.tributes || [];
    const maxShow = data.max_show || 10;
    const total = data.total_tributes || 0;

    countText.textContent = String(total);
    badge.style.display = total > 0 ? 'inline-flex' : 'none';

    if (total === 0) {
        inlineWrap.innerHTML = renderInlineTributeForm(data.post.id, data.csrf);
        list.innerHTML = '';
        btnWrite.style.display = 'none';
        btnViewMore.style.display = 'none';
        bindInlineTributeForm();
        return;
    }

    inlineWrap.innerHTML = '';
    btnWrite.style.display = 'inline-flex';

    const html = tributes.slice(0, maxShow).map(t => {
        if (t.kind === 'banner') {
            return `
                <div class="tribute-banner">
                    <img src="${escapeHtml(t.banner_image)}" alt="">
                    <div class="tribute-banner-overlay">
                        <div>
                            <div class="tb-heading">${escapeHtml(t.heading || 'Tribute')}</div>
                            <div class="tb-message">
                                <span class="tb-quote-icon-inline">❝</span>
                                <span class="tb-message-text">
                                    <span class="tb-message-inner">
                                        ${escapeHtml(t.short_message || '')}
                                        ${t.has_more ? `<a class="read-more-inline" href="full_tribute.php?post_id=${encodeURIComponent(data.post.id)}&entry_id=${encodeURIComponent(t.id)}">Read more</a>` : ''}
                                    </span>
                                </span>
                            </div>
                        </div>
                        <div>
                            <div class="tb-from">
                                ${t.by_name ? `<strong>${escapeHtml(t.by_name)}</strong>` : ''}
                                ${t.by_org ? `<br>${escapeHtml(t.by_org)}` : ''}
                            </div>
                            <div class="tb-meta">${escapeHtml(t.meta || '')}</div>
                        </div>
                    </div>
                </div>
            `;
        }

        return `
            <div class="tribute-text">
                <div class="tt-message">
                    <span class="tt-quote-icon-inline">❝</span>
                    <span class="tt-message-text">
                        ${nl2brSafe(t.short_message || '')}
                        ${t.has_more ? `<a class="read-more-link" href="full_tribute.php?post_id=${encodeURIComponent(data.post.id)}&entry_id=${encodeURIComponent(t.id)}">Read more</a>` : ''}
                    </span>
                </div>
                <div class="tt-from"><strong>${escapeHtml(t.by_name || 'Anonymous')}</strong>${t.by_org ? `<br>${escapeHtml(t.by_org)}` : ''}</div>
                <div class="tt-meta">${escapeHtml(t.meta || '')}</div>
            </div>
        `;
    }).join('');

    list.innerHTML = html;
    btnViewMore.style.display = total > maxShow ? 'inline-flex' : 'none';
    btnViewMore.href = `view_tribute.php?post_id=${encodeURIComponent(data.post.id)}`;
}

function renderAds(data) {
    const box = document.getElementById('sponsoredBox');
    const track = document.getElementById('adTrack');
    const dots = document.getElementById('adDots');
    const addAdBtn = document.getElementById('addAdBtn');
    const btnPrev = document.getElementById('adPrev');
    const btnNext = document.getElementById('adNext');

    if (!box || !track || !dots) return;

    const ads = data.ads || [];
    if (!ads.length) {
        box.style.display = 'none';
        return;
    }

    box.style.display = 'block';

    if (data.add_ad_link) {
        addAdBtn.href = data.add_ad_link;
        addAdBtn.style.display = 'inline-flex';
    } else {
        addAdBtn.style.display = 'none';
    }

    track.innerHTML = ads.map(ad => `
        <div class="ad-slide">
            <div class="ad-card-inner">
                <img class="ad-img" src="${escapeHtml(ad.image)}" alt="${escapeHtml(ad.title || 'Sponsored')}" loading="lazy">
                <div class="ad-actions">
                    ${ad.whatsapp ? `
                        <a class="ad-btn ad-btn-wa" href="${escapeHtml(ad.whatsapp)}" target="_blank" rel="noopener">
                            <i class="fa-brands fa-whatsapp"></i> Contact
                        </a>
                    ` : ''}
                    <a class="ad-btn ad-btn-view" href="${escapeHtml(ad.view_url)}" target="_blank" rel="noopener">
                        View more
                    </a>
                </div>
            </div>
        </div>
    `).join('');

    dots.innerHTML = ads.map((_, i) => `<span class="ad-dot ${i === 0 ? 'active' : ''}"></span>`).join('');

    if (ads.length > 2) {
        btnPrev.style.display = 'grid';
        btnNext.style.display = 'grid';
    } else {
        btnPrev.style.display = 'none';
        btnNext.style.display = 'none';
    }

    initAdsCarousel(ads.length);
}

function initAdsCarousel(count) {
    const track = document.getElementById('adTrack');
    const dots = document.getElementById('adDots');
    const box = document.getElementById('sponsoredBox');
    const btnPrev = document.getElementById('adPrev');
    const btnNext = document.getElementById('adNext');

    if (!track || count <= 1) return;

    currentAdIndex = 0;
    clearInterval(adTimer);

    const setDots = (i) => {
        dots.querySelectorAll('.ad-dot').forEach((d, idx) => {
            d.classList.toggle('active', idx === i);
        });
    };

    const go = (i) => {
        currentAdIndex = i;
        track.style.transform = `translateX(-${i * 100}%)`;
        setDots(i);
    };

    const next = () => go((currentAdIndex + 1) % count);
    const prev = () => go((currentAdIndex - 1 + count) % count);

    btnNext.onclick = next;
    btnPrev.onclick = prev;

    adTimer = setInterval(next, 2000);

    box.addEventListener('mouseenter', () => clearInterval(adTimer));
    box.addEventListener('mouseleave', () => {
        clearInterval(adTimer);
        adTimer = setInterval(next, 2000);
    });

    dots.querySelectorAll('.ad-dot').forEach((d, i) => {
        d.addEventListener('click', () => go(i));
    });
}

function fillPhoneOptions(countries, defaultCode) {
    const targets = [
        { optionsId: 'flowersPhoneOptions', selectId: 'flowers_phone_code' },
        { optionsId: 'donatePhoneOptions', selectId: 'donate_phone_code' }
    ];

    targets.forEach(({ optionsId, selectId }) => {
        const optionsBox = document.getElementById(optionsId);
        const select = document.getElementById(selectId);
        if (!optionsBox || !select) return;

        optionsBox.innerHTML = '';
        select.innerHTML = '';

        countries.forEach(pc => {
            const code = pc.code || '';
            const name = pc.name || '';
            const sel = code === defaultCode;

            const div = document.createElement('div');
            div.className = 'custom-option' + (sel ? ' is-selected' : '');
            div.dataset.value = code;
            div.dataset.display = code;
            div.textContent = `${name} (${code})`;
            optionsBox.appendChild(div);

            const opt = document.createElement('option');
            opt.value = code;
            opt.textContent = code;
            if (sel) opt.selected = true;
            select.appendChild(opt);
        });

        const txt = select.closest('.phone-row')?.querySelector('.custom-select-text');
        if (txt) txt.textContent = defaultCode;
    });
}

function initCustomSelects() {
    document.querySelectorAll('.custom-select').forEach(wrapper => {
        const selectId = wrapper.dataset.selectId;
        const selectEl = document.getElementById(selectId);
        if (!selectEl) return;

        const trigger = wrapper.querySelector('.custom-select-trigger');
        const textSpan = wrapper.querySelector('.custom-select-text');
        const optionEls = wrapper.querySelectorAll('.custom-option');

        function syncFromSelect() {
            const current = selectEl.value;
            let match = null;

            optionEls.forEach(opt => {
                const isMatch = opt.dataset.value === current;
                opt.classList.toggle('is-selected', isMatch);
                if (isMatch) match = opt;
            });

            if (match && textSpan) textSpan.textContent = match.dataset.display || match.textContent.trim();
        }

        trigger?.addEventListener('click', (e) => {
            e.preventDefault();
            e.stopPropagation();
            wrapper.classList.toggle('open');
        });

        optionEls.forEach(opt => {
            opt.addEventListener('click', (e) => {
                e.preventDefault();
                e.stopPropagation();
                selectEl.value = opt.dataset.value || '';
                syncFromSelect();
                wrapper.classList.remove('open');
            });
        });

        document.addEventListener('click', (e) => {
            if (!wrapper.contains(e.target)) wrapper.classList.remove('open');
        });

        syncFromSelect();
    });
}

function initCurrencyDropdown() {
    const dd = document.getElementById('currencyDD');
    const btn = document.getElementById('currencyBtn');
    const hidden = document.getElementById('currencyValue');
    if (!dd || !btn || !hidden) return;

    dd.addEventListener('click', (e) => {
        const item = e.target.closest('.dd-item');
        const toggle = e.target.closest('.dd-toggle');

        if (toggle) {
            dd.classList.toggle('open');
            return;
        }

        if (item) {
            const val = item.dataset.value || 'LKR';
            hidden.value = val;
            btn.textContent = val;

            dd.querySelectorAll('.dd-item').forEach(x => x.classList.remove('active'));
            item.classList.add('active');
            dd.classList.remove('open');
        }
    });

    document.addEventListener('click', (e) => {
        if (!dd.contains(e.target)) dd.classList.remove('open');
    });
}

function bindModalControls() {
    document.body.addEventListener('click', (e) => {
        const t = e.target.closest('[data-close]');
        if (!t) return;
        const sel = t.getAttribute('data-close');
        const el = document.querySelector(sel);
        if (el) el.style.display = 'none';
        document.body.style.overflow = '';
    });

    ['modalTributeType', 'modalFlowers', 'modalDonation'].forEach(id => {
        const modal = document.getElementById(id);
        modal?.addEventListener('click', (e) => {
            if (e.target === modal) {
                modal.style.display = 'none';
                document.body.style.overflow = '';
            }
        });
    });
}

function openModal(id) {
    const modal = document.getElementById(id);
    if (!modal) return;
    modal.style.display = 'flex';
    document.body.style.overflow = 'hidden';
}

function bindActions(data) {
    document.getElementById('btnTributeNow')?.addEventListener('click', (e) => {
        e.preventDefault();
        document.getElementById('tributes')?.scrollIntoView({ behavior: 'smooth', block: 'start' });
    });

    document.getElementById('btnWriteTribute')?.addEventListener('click', (e) => {
        e.preventDefault();
        openModal('modalTributeType');
    });

    document.getElementById('btnSendFlowers')?.addEventListener('click', (e) => {
        e.preventDefault();
        openModal('modalFlowers');
    });

    document.getElementById('btnDonate')?.addEventListener('click', (e) => {
        e.preventDefault();
        openModal('modalDonation');
    });

    document.getElementById('btnShare')?.addEventListener('click', async () => {
        try {
            if (navigator.share) {
                await navigator.share({ title: document.title, url: data.share_url || window.location.href });
            } else {
                await navigator.clipboard.writeText(data.share_url || window.location.href);
                notify('success', 'Copied', 'Memorial link copied to clipboard.');
            }
        } catch (_) {}
    });
}

async function bindForms() {
    const tributeForm = document.getElementById('tributeForm');
    const flowersForm = document.getElementById('flowersForm');
    const donationForm = document.getElementById('donationForm');

    tributeForm?.addEventListener('submit', async (e) => {
        e.preventDefault();
        const fd = new FormData(tributeForm);
        const json = await apiPost('api/tribute_add_comment.php', fd);

        if (!json.ok) {
            notify('error', 'Error', json.message || 'Unable to submit tribute.');
            return;
        }

        notify('success', 'Success', json.message || 'Tribute submitted.');
        document.getElementById('modalTributeType').style.display = 'none';
        document.body.style.overflow = '';
        tributeForm.reset();
        await loadPageData();
    });

    flowersForm?.addEventListener('submit', async (e) => {
        e.preventDefault();
        const fd = new FormData(flowersForm);
        const json = await apiPost('api/flower_request_create.php', fd);

        if (!json.ok) {
            notify('error', 'Error', json.message || 'Unable to send flower request.');
            return;
        }

        notify('success', 'Success', json.message || 'Flower request submitted.');
        document.getElementById('modalFlowers').style.display = 'none';
        document.body.style.overflow = '';
        flowersForm.reset();
    });

    donationForm?.addEventListener('submit', async (e) => {
        e.preventDefault();
        const fd = new FormData(donationForm);
        const json = await apiPost('api/donation_request_create.php', fd);

        if (!json.ok) {
            notify('error', 'Error', json.message || 'Unable to send donation request.');
            return;
        }

        notify('success', 'Success', json.message || 'Donation request submitted.');
        document.getElementById('modalDonation').style.display = 'none';
        document.body.style.overflow = '';
        donationForm.reset();
    });
}

function bindInlineTributeForm() {
    const form = document.getElementById('inlineTributeForm');
    if (!form) return;

    form.addEventListener('submit', async (e) => {
        e.preventDefault();
        const fd = new FormData(form);
        const json = await apiPost('api/tribute_add_comment.php', fd);

        if (!json.ok) {
            notify('error', 'Error', json.message || 'Unable to submit tribute.');
            return;
        }

        notify('success', 'Success', json.message || 'Tribute submitted.');
        form.reset();
        await loadPageData();
    });
}

async function waitForAssets(root) {
    if (document.fonts && document.fonts.ready) {
        try { await document.fonts.ready; } catch (_) {}
    }

    const imgs = Array.from(root.querySelectorAll('img'));
    await Promise.all(imgs.map(img => {
        if (img.complete && img.naturalWidth > 0) return Promise.resolve();
        return new Promise(res => {
            img.addEventListener('load', res, { once: true });
            img.addEventListener('error', res, { once: true });
        });
    }));
}

function bindPosterDownload() {
    const btn = document.getElementById('btnDownloadPoster');
    const posterArea = document.getElementById('posterArea');

    btn?.addEventListener('click', async () => {
        if (!posterArea || typeof html2canvas === 'undefined') return;

        posterArea.classList.add('poster-mode');
        await new Promise(r => requestAnimationFrame(r));
        await waitForAssets(posterArea);

        try {
            const canvas = await html2canvas(posterArea, {
                useCORS: true,
                backgroundColor: null,
                scale: 2,
                scrollX: 0,
                scrollY: 0
            });

            posterArea.classList.remove('poster-mode');

            const link = document.createElement('a');
            link.href = canvas.toDataURL('image/png');
            link.download = `${(pageData?.post?.full_name || 'memorial').replace(/[^a-z0-9]+/gi, '-').toLowerCase()}_poster.png`;
            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);

            notify('success', 'Downloaded', 'Memorial poster downloaded successfully.');
        } catch (err) {
            posterArea.classList.remove('poster-mode');
            notify('error', 'Download Failed', 'Poster generation failed.');
        }
    });
}

function applyPageData(data) {
    pageData = data;

    updateMetaTags(data);

    document.getElementById('noticeHeading').textContent = data.notice_heading || 'In Loving Memory';
    document.getElementById('memorialName').textContent = data.post.full_name || 'Memorial';
    document.getElementById('birthDate').innerHTML = data.post.birth_date_multi || '-';
    document.getElementById('deathDate').innerHTML = data.post.death_date_multi || '-';
    document.getElementById('memorialLocation').textContent = data.post.location_text || '';

    const ageBadge = document.getElementById('memorialAge');
    if (data.post.age !== null && data.post.age !== undefined) {
        ageBadge.textContent = `Age ${data.post.age}`;
        ageBadge.style.display = 'inline-block';
    } else {
        ageBadge.style.display = 'none';
    }

    const portrait = document.getElementById('memorialPortrait');
    portrait.src = data.post.portrait || 'assets/defaultavt.png';

    document.getElementById('lifeStory').innerHTML = data.post.bio_html || '<em style="color:#64748b">No biography added.</em>';

    document.getElementById('tribute_post_id').value = data.post.id;
    document.getElementById('flowers_post_id').value = data.post.id;
    document.getElementById('donation_post_id').value = data.post.id;

    const btnPoster = document.getElementById('btnDownloadPoster');
    const btnShare = document.getElementById('btnShare');
    const btnFlowers = document.getElementById('btnSendFlowers');
    const btnRipVideo = document.getElementById('btnRipVideo');

    btnPoster.style.display = data.post.is_pending ? 'none' : 'inline-flex';
    btnShare.style.display = data.post.status === 'published' ? 'inline-flex' : 'inline-flex';

    if (data.post.type && String(data.post.type).toLowerCase() === 'obituary') {
        btnFlowers.style.display = 'inline-flex';
    } else {
        btnFlowers.style.display = 'none';
    }

    if (data.live_link) {
        btnRipVideo.href = data.live_link;
        btnRipVideo.style.display = 'inline-flex';
    } else {
        btnRipVideo.style.display = 'none';
    }

    const flowersInfoBox = document.getElementById('flowersInfoBox');
    const donationHotlineBox = document.getElementById('donationHotlineBox');

    if (data.org_phone) {
        flowersInfoBox.innerHTML = `
            <div class="flowers-hotline-label">Hotline Number</div>
            <div class="flowers-hotline-number">${escapeHtml(data.org_phone)}</div>
            <p class="flowers-text">
                For urgent arrangements you can call this number directly.<br>
                You may also leave your details and our team will contact you shortly.
            </p>
        `;

        donationHotlineBox.innerHTML = `
            <div style="margin-top:10px;">
                <div class="flowers-hotline-label">Hotline Number</div>
                <div class="flowers-hotline-number">${escapeHtml(data.org_phone)}</div>
            </div>
        `;
    } else {
        donationHotlineBox.innerHTML = '';
    }

    fillPhoneOptions(data.phone_countries || [], data.default_phone_code || '+94');
    initCustomSelects();
    renderSummary(data);
    renderTributes(data);
    renderAds(data);
    bindActions(data);
}

async function loadPageData() {
    const id = getId();
    if (!id) {
        notify('error', 'Not Found', 'Missing memorial id.');
        return;
    }

    const json = await apiGet(`api/memorial_detail_get.php?id=${encodeURIComponent(id)}`);

    if (!json.ok) {
        document.getElementById('memorialName').textContent = 'Memorial not found';
        document.getElementById('lifeStory').innerHTML = `<p>${escapeHtml(json.message || 'Unable to load memorial.')}</p>`;
        notify('error', 'Error', json.message || 'Unable to load memorial.');
        return;
    }

    applyPageData(json);
}

document.addEventListener('DOMContentLoaded', async () => {
    bindModalControls();
    initCurrencyDropdown();
    bindPosterDownload();
    await loadPageData();
    await bindForms();
});