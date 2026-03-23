/**
 * index.js - Real homepage logic using API
 */

// --------------------
// STATE
// --------------------
const state = {
    page: 1,
    limit: 25,
    displayedCount: 0,
    hasMore: true,
    type: 'all',
    district: '',
    ads: [],
    currentAdIndex: 0,
    adInterval: null,
    feedItems: []
};

// Match your UI reactions
const reactionTypes = {
    pray: { emoji: "🙏", label: "Praying" },
    wow: { emoji: "🥺", label: "Emotional" },
    sad: { emoji: "😢", label: "Sad" },
    heart: { emoji: "❤️", label: "Heart" },
    flower: { emoji: "🌸", label: "Flower" },
    candle: { emoji: "🕯️", label: "Candle" }
};

// --------------------
// HELPERS
// --------------------
function esc(str = '') {
    return String(str)
        .replace(/&/g, '&amp;')
        .replace(/</g, '&lt;')
        .replace(/>/g, '&gt;')
        .replace(/"/g, '&quot;')
        .replace(/'/g, '&#039;');
}

async function getJSON(url, options = {}) {
    const res = await fetch(url, {
        credentials: 'same-origin',
        ...options
    });

    if (!res.ok) {
        throw new Error(`Request failed: ${res.status}`);
    }

    return await res.json();
}

function getTypeLabel(item) {
    if (item.type === 'remembrance') {
        return item.type_label || 'Remembrance';
    }
    return 'Obituary';
}

function getReactionEmoji(myReaction) {
    return reactionTypes[myReaction]?.emoji || '🙏';
}

function getReactionLabel(myReaction) {
    return reactionTypes[myReaction]?.label || 'React';
}

function getCommentLabel(count) {
    if (!count || count < 1) return 'Be First To Comment';
    if (count === 1) return '1 Comment';
    return `${count} Comments`;
}

function imageOrFallback(src) {
    return src && src.trim() !== '' ? src : 'assets/defaultavt.png';
}

function buildFeedParams() {
    const params = new URLSearchParams({
        page: state.page,
        limit: state.limit,
        type: state.type
    });

    if (state.district) {
        params.set('district', state.district);
    }

    return params.toString();
}

function updateCount() {
    const countEl = document.getElementById('currentCount');
    if (countEl) {
        countEl.textContent = String(state.displayedCount);
    }
}

function setLoadMoreVisibility() {
    const btn = document.getElementById('loadMoreBtn');
    if (!btn) return;

    btn.style.display = state.hasMore ? 'inline-flex' : 'none';
}

function closeAllReactionPopups() {
    document.querySelectorAll('.fb-reaction-popup').forEach(popup => {
        popup.classList.remove('show');
        popup.classList.remove('active');
    });
}

// --------------------
// DISTRICTS
// --------------------
async function loadDistricts() {
    const container = document.getElementById('recentPosts');
    if (!container) return;

    try {
        const data = await getJSON('api/districts_get.php');
        const districts = data.districts || [];

        if (!districts.length) {
            container.innerHTML = `
                <li>
                    <a href="#">
                        <span class="info">
                            <span class="label-main">No districts found</span>
                            <span class="label-secondary">0 Posts</span>
                        </span>
                    </a>
                </li>
            `;
            return;
        }

        container.innerHTML = districts.map(d => `
            <li>
                <a href="#" class="district-link" data-district="${esc(d.district)}">
                    <span class="info">
                        <span class="label-main">${esc(d.district)}</span>
                        <span class="label-secondary">${Number(d.total)} Posts</span>
                    </span>
                </a>
            </li>
        `).join('');

        document.querySelectorAll('.district-link').forEach(link => {
            link.addEventListener('click', function (e) {
                e.preventDefault();

                const clickedDistrict = this.dataset.district || '';
                if (state.district === clickedDistrict) {
                    state.district = '';
                } else {
                    state.district = clickedDistrict;
                }

                state.page = 1;
                state.displayedCount = 0;
                loadFeed(false);

                document.querySelectorAll('.district-link').forEach(x => x.classList.remove('active'));
                if (state.district) {
                    this.classList.add('active');
                }
            });
        });

    } catch (err) {
        console.error('District load failed:', err);
        container.innerHTML = `
            <li>
                <a href="#">
                    <span class="info">
                        <span class="label-main">Failed to load districts</span>
                        <span class="label-secondary">Try again</span>
                    </span>
                </a>
            </li>
        `;
    }
}

// --------------------
// RECENT COMMENTS
// --------------------
async function loadRecentComments() {
    const container = document.getElementById('recentTributes');
    if (!container) return;

    try {
        const data = await getJSON('api/recent_comments_get.php');
        const items = data.recent_comments || [];

        if (!items.length) {
            container.innerHTML = `
                <li>
                    <a href="#">
                        <div class="tribute-info">
                            <span class="label-main">No recent comments</span>
                            <span class="label-secondary">Nothing yet</span>
                        </div>
                    </a>
                </li>
            `;
            return;
        }

        container.innerHTML = items.map(item => `
            <li>
                <a href="memorial-detail.php?id=${Number(item.id)}">
                    <img src="${esc(imageOrFallback(item.cover_image))}" 
                         alt="${esc(item.full_name)}" 
                         loading="lazy"
                         onerror="this.onerror=null;this.src='assets/defaultavt.png';">
                    <div class="tribute-info">
                        <span class="label-main">${esc(item.full_name)}</span>
                        <span class="label-secondary">${Number(item.tribute_count)} Comments</span>
                        <div class="tributes-footer">
                            <span class="upd-at">${esc(item.last_tribute_ago || '')}</span>
                            <span class="lnk-see-more">View</span>
                        </div>
                    </div>
                </a>
            </li>
        `).join('');

    } catch (err) {
        console.error('Recent comments load failed:', err);
        container.innerHTML = `
            <li>
                <a href="#">
                    <div class="tribute-info">
                        <span class="label-main">Failed to load comments</span>
                        <span class="label-secondary">Try again</span>
                    </div>
                </a>
            </li>
        `;
    }
}

// --------------------
// ORG WHATSAPP
// --------------------
async function loadOrgWhatsapp() {
    try {
        const data = await getJSON('api/org_whatsapp_get.php');

        const phoneText = document.getElementById('orgPhoneText');
        const whatsappBtn = document.getElementById('orgWhatsappBtn');
        const placeAdBtn = document.getElementById('placeAdBtn');

        if (phoneText) {
            phoneText.textContent = data.phone_raw || '+94';
        }

        if (whatsappBtn) {
            whatsappBtn.href = data.whatsapp_link || '#';
        }

        if (placeAdBtn) {
            placeAdBtn.href = data.whatsapp_link || '#';
        }
    } catch (err) {
        console.error('WhatsApp info load failed:', err);
    }
}

// --------------------
// ADS
// --------------------
function renderDesktopAds() {
    const carousel = document.getElementById('sponsoredCarousel');
    const dotsContainer = document.getElementById('adDots');

    if (!carousel || !dotsContainer) return;

    const ads = state.ads || [];

    if (!ads.length) {
        carousel.innerHTML = `
            <div class="ad-slide active">
                <div class="ad-content">
                    <span class="ad-badge">Sponsored</span>
                    <h3 class="ad-title">No active ads right now</h3>
                    <p class="ad-description">Nothing to show at the moment.</p>
                </div>
            </div>
        `;
        dotsContainer.innerHTML = '';
        return;
    }

    carousel.innerHTML = ads.map((ad, index) => `
        <div class="ad-slide ${index === 0 ? 'active' : ''}" data-index="${index}">
            <div style="position: relative;">
                <img src="${esc(ad.image)}" alt="${esc(ad.title)}" class="ad-image" loading="lazy">
                ${ad.whatsapp_link ? `
                    <a href="${esc(ad.whatsapp_link)}"
                       target="_blank"
                       rel="noopener"
                       class="float-wa-btn"
                       aria-label="Contact on WhatsApp">
                        <i class="fab fa-whatsapp"></i>
                    </a>
                ` : ''}
            </div>
            <div class="ad-content">
                <span class="ad-badge">Sponsored</span>
                <h3 class="ad-title">${esc(ad.title || 'Sponsored')}</h3>
                <p class="ad-description">Click to contact advertiser on WhatsApp.</p>
                <div class="ad-actions">
                    ${ad.whatsapp_link ? `
                        <a href="${esc(ad.whatsapp_link)}"
                           target="_blank"
                           rel="noopener"
                           class="ad-btn ad-btn-whatsapp">
                            <i class="fab fa-whatsapp"></i> WhatsApp
                        </a>
                    ` : ''}
                    <a href="${esc(ad.image)}"
                       target="_blank"
                       rel="noopener"
                       class="ad-btn ad-btn-view">
                        <i class="fas fa-eye"></i> View More
                    </a>
                </div>
            </div>
        </div>
    `).join('');

    dotsContainer.innerHTML = ads.map((_, index) => `
        <div class="ad-dot ${index === 0 ? 'active' : ''}" data-index="${index}"></div>
    `).join('');

    dotsContainer.querySelectorAll('.ad-dot').forEach(dot => {
        dot.addEventListener('click', function () {
            showAd(Number(this.dataset.index));
        });
    });

    const arrows = document.createElement('div');
    arrows.className = 'ad-arrows';
    arrows.innerHTML = `
        <button class="ad-arrow ad-prev" id="adPrevBtn" aria-label="Previous ad">
            <i class="fas fa-chevron-left"></i>
        </button>
        <button class="ad-arrow ad-next" id="adNextBtn" aria-label="Next ad">
            <i class="fas fa-chevron-right"></i>
        </button>
    `;
    carousel.appendChild(arrows);

    document.getElementById('adPrevBtn')?.addEventListener('click', prevAd);
    document.getElementById('adNextBtn')?.addEventListener('click', nextAd);

    addTouchSupport(carousel);
    startAdRotation();
}

function renderMobileAds() {
    const mobileContainer = document.getElementById('mobileSponsoredSection');
    if (!mobileContainer) return;

    const ads = state.ads || [];

    if (window.innerWidth > 992) {
        mobileContainer.innerHTML = '';
        return;
    }

    if (!ads.length) {
        mobileContainer.innerHTML = `
            <div class="list-multiple-item">
                <div class="header" style="background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);">
                    <h2><i class="fas fa-ad"></i> Sponsored Ads</h2>
                </div>
                <div class="list">
                    <div class="ad-content">
                        <span class="ad-badge">Sponsored</span>
                        <h3 class="ad-title">No active ads right now</h3>
                    </div>
                </div>
            </div>
        `;
        return;
    }

    mobileContainer.innerHTML = `
        <div class="list-multiple-item">
            <div class="header" style="background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);">
                <h2><i class="fas fa-ad"></i> Sponsored Ads</h2>
                <a href="${document.getElementById('placeAdBtn')?.href || '#'}"
                   target="_blank"
                   rel="noopener"
                   class="whatsapp-btn-small">
                    <i class="fab fa-whatsapp"></i>
                    Place Ad
                </a>
            </div>
            <div class="list">
                <div id="mobileSponsoredCarousel" class="mobile-sponsored-carousel">
                    ${ads.map((ad, index) => `
                        <div class="ad-slide ${index === 0 ? 'active' : ''}" data-index="${index}">
                            <div style="position: relative;">
                                <img src="${esc(ad.image)}" alt="${esc(ad.title)}" class="ad-image" loading="lazy">
                                ${ad.whatsapp_link ? `
                                    <a href="${esc(ad.whatsapp_link)}"
                                       target="_blank"
                                       rel="noopener"
                                       class="float-wa-btn"
                                       aria-label="Contact on WhatsApp">
                                        <i class="fab fa-whatsapp"></i>
                                    </a>
                                ` : ''}
                            </div>
                            <div class="ad-content">
                                <span class="ad-badge">Sponsored</span>
                                <h3 class="ad-title">${esc(ad.title || 'Sponsored')}</h3>
                                <p class="ad-description">Click to contact advertiser on WhatsApp.</p>
                                <div class="ad-actions">
                                    ${ad.whatsapp_link ? `
                                        <a href="${esc(ad.whatsapp_link)}"
                                           target="_blank"
                                           rel="noopener"
                                           class="ad-btn ad-btn-whatsapp">
                                            <i class="fab fa-whatsapp"></i> WhatsApp
                                        </a>
                                    ` : ''}
                                    <a href="${esc(ad.image)}"
                                       target="_blank"
                                       rel="noopener"
                                       class="ad-btn ad-btn-view">
                                        <i class="fas fa-eye"></i> View More
                                    </a>
                                </div>
                            </div>
                        </div>
                    `).join('')}
                </div>
                <div class="ad-dots" id="mobileAdDots">
                    ${ads.map((_, index) => `
                        <div class="ad-dot ${index === 0 ? 'active' : ''}" data-index="${index}"></div>
                    `).join('')}
                </div>
            </div>
        </div>
    `;

    initMobileCarousel();
}

async function loadAds() {
    try {
        const data = await getJSON('api/ads_get.php');
        state.ads = data.ads || [];
        state.currentAdIndex = 0;

        renderDesktopAds();
        renderMobileAds();
    } catch (err) {
        console.error('Ads load failed:', err);
    }
}

function showAd(index) {
    const slides = document.querySelectorAll('#sponsoredCarousel .ad-slide');
    const dots = document.querySelectorAll('#adDots .ad-dot');

    if (!slides.length) return;
    if (index < 0 || index >= slides.length) return;

    slides.forEach(slide => slide.classList.remove('active'));
    dots.forEach(dot => dot.classList.remove('active'));

    slides[index].classList.add('active');
    if (dots[index]) dots[index].classList.add('active');

    state.currentAdIndex = index;
    resetAdRotation();
}

function nextAd() {
    if (!state.ads.length) return;
    const nextIndex = (state.currentAdIndex + 1) % state.ads.length;
    showAd(nextIndex);
}

function prevAd() {
    if (!state.ads.length) return;
    const prevIndex = (state.currentAdIndex - 1 + state.ads.length) % state.ads.length;
    showAd(prevIndex);
}

function startAdRotation() {
    clearInterval(state.adInterval);
    if (state.ads.length <= 1) return;

    state.adInterval = setInterval(() => {
        nextAd();
    }, 5000);
}

function resetAdRotation() {
    startAdRotation();
}

function addTouchSupport(carousel) {
    let touchStartX = 0;
    let touchEndX = 0;

    carousel.addEventListener('touchstart', e => {
        touchStartX = e.changedTouches[0].screenX;
    }, { passive: true });

    carousel.addEventListener('touchend', e => {
        touchEndX = e.changedTouches[0].screenX;
        const diff = touchStartX - touchEndX;
        const threshold = 50;

        if (Math.abs(diff) > threshold) {
            if (diff > 0) nextAd();
            else prevAd();
        }
    }, { passive: true });
}

function initMobileCarousel() {
    const dots = document.querySelectorAll('#mobileAdDots .ad-dot');
    const slides = document.querySelectorAll('#mobileSponsoredCarousel .ad-slide');

    if (!dots.length || !slides.length) return;

    let mobileIndex = 0;
    let mobileInterval = null;

    function showMobileAd(index) {
        if (index < 0 || index >= slides.length) return;

        slides.forEach(slide => slide.classList.remove('active'));
        dots.forEach(dot => dot.classList.remove('active'));

        slides[index].classList.add('active');
        dots[index].classList.add('active');
        mobileIndex = index;
    }

    dots.forEach((dot, index) => {
        dot.addEventListener('click', () => {
            showMobileAd(index);
        });
    });

    mobileInterval = setInterval(() => {
        showMobileAd((mobileIndex + 1) % slides.length);
    }, 5000);

    const mobileCarousel = document.getElementById('mobileSponsoredCarousel');
    if (mobileCarousel) {
        mobileCarousel.addEventListener('mouseenter', () => clearInterval(mobileInterval));
        mobileCarousel.addEventListener('mouseleave', () => {
            mobileInterval = setInterval(() => {
                showMobileAd((mobileIndex + 1) % slides.length);
            }, 5000);
        });
    }
}

// --------------------
// FEED
// --------------------
function renderFeedItem(item) {
    const years = (item.years_range || '— - —').split(' - ');
    const birthYear = years[0] || '—';
    const deathYear = years[1] || '—';
    const coverImage = imageOrFallback(item.cover_image);
    const reactEmoji = getReactionEmoji(item.my_reaction);
    const reactLabel = getReactionLabel(item.my_reaction);
    const commentText = getCommentLabel(Number(item.tribute_count || 0));

    const title = getTypeLabel(item);

    return `
        <div class="feed-card ${item.type === 'remembrance' ? 'premium' : ''}" data-id="${Number(item.id)}">
            <div class="feed-header">
                <span class="head"><span>${esc(title)}</span></span>
                <span class="actions"><span class="minago">${esc(item.time_ago || '')}</span></span>
            </div>

            <a href="${esc(item.details_url)}" class="card-body">
                <div class="avatar">
                    <span class="yearfrom">${esc(birthYear)}</span>
                    <div class="image-container">
                        <div class="photo-wrapper">
                            <img src="${esc(coverImage)}"
                                 alt="${esc(item.full_name)}"
                                 class="photo"
                                 loading="lazy"
                                 onerror="this.onerror=null;this.src='assets/defaultavt.png';">
                        </div>
                        <div class="floral-decoration">
                            <img src="assets/floral.png" alt="Floral" onerror="this.style.display='none';">
                        </div>
                    </div>
                    <span class="yearto">${esc(deathYear)}</span>
                </div>

                <div class="info">
                    <span class="head">${esc(item.full_name)}</span>
                    ${item.birth_place ? `<span class="subhead">${esc(item.birth_place)}</span>` : ''}
                    ${item.lived_place ? `<span class="subhead">${esc(item.lived_place)}</span>` : ''}
                    ${item.country ? `<span class="subhead">${esc(item.country)}</span>` : ''}
                </div>
            </a>

            <div class="facebook-actions">
                <div class="action-row">
                    <div class="reaction-summary">
                        <div class="reaction-icons-small">
                            <span>${reactEmoji}</span>
                        </div>
                        <div class="reaction-count-small">
                            <span class="count">${Number(item.react_total || 0)}</span>
                        </div>
                    </div>

                    <div class="tribute-summary">
                        <a href="${esc(item.details_url)}#comments"
                           class="tribute-link ${Number(item.tribute_count || 0) < 1 ? 'orange' : ''}">
                            <i class="fas fa-heart"></i> ${esc(commentText)}
                        </a>
                    </div>

                    <div class="facebook-buttons">
                        <button class="fb-action-btn fb-reaction-btn ${item.my_reaction ? 'active' : ''}"
                                onclick="showFacebookReactionPopup(${Number(item.id)}, event)">
                            <span class="fb-icon">${reactEmoji}</span>
                            <span>${esc(reactLabel)}</span>

                            <div class="fb-reaction-popup" id="fbReactionPopup-${Number(item.id)}">
                                ${Object.entries(reactionTypes).map(([key, val]) => `
                                    <div class="fb-reaction-option"
                                         data-label="${esc(val.label)}"
                                         data-reaction="${esc(key)}"
                                         onclick="handleReaction(${Number(item.id)}, '${esc(key)}', event)">
                                        ${val.emoji}
                                    </div>
                                `).join('')}
                            </div>
                        </button>

                        <a href="javascript:void(0)" class="fb-action-btn fb-share-btn"
                           onclick="handleShareButton(${Number(item.id)}, event)">
                            <i class="fas fa-share fb-icon"></i> <span>Share</span>
                        </a>

                        ${item.rip_video_link ? `
                            <a href="${esc(item.rip_video_link)}"
                               class="fb-action-btn"
                               target="_blank"
                               rel="noopener">
                                <i class="fas fa-play fb-icon"></i> <span>Video</span>
                            </a>
                        ` : ''}
                    </div>
                </div>
            </div>
        </div>
    `;
}

async function loadFeed(append = false) {
    const container = document.getElementById('feedsContainer');
    const btn = document.getElementById('loadMoreBtn');

    if (!container) return;

    try {
        if (!append) {
            container.innerHTML = `
                <div class="feed-card">
                    <div class="feed-header">
                        <span class="head">Loading memorials...</span>
                        <span class="actions">Please wait</span>
                    </div>
                </div>
            `;
        }

        if (btn && append) {
            btn.disabled = true;
            btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Loading...';
        }

        const query = buildFeedParams();
        const data = await getJSON(`api/home_feed_get.php?${query}`);
        const items = data.items || [];

        if (!append) {
            state.feedItems = items;
            state.displayedCount = items.length;

            if (!items.length) {
                container.innerHTML = `
                    <div class="feed-card">
                        <div class="feed-header">
                            <span class="head">No memorials found</span>
                            <span class="actions">Empty</span>
                        </div>
                    </div>
                `;
            } else {
                container.innerHTML = items.map(renderFeedItem).join('');
            }
        } else {
            state.feedItems = state.feedItems.concat(items);
            state.displayedCount += items.length;
            container.insertAdjacentHTML('beforeend', items.map(renderFeedItem).join(''));
        }

        state.hasMore = !!data.has_more;
        updateCount();
        setLoadMoreVisibility();

    } catch (err) {
        console.error('Feed load failed:', err);

        if (!append) {
            container.innerHTML = `
                <div class="feed-card">
                    <div class="feed-header">
                        <span class="head">Failed to load memorials</span>
                        <span class="actions">Error</span>
                    </div>
                </div>
            `;
        }
    } finally {
        if (btn) {
            btn.disabled = false;
            btn.innerHTML = '<i class="fas fa-spinner"></i> Load More Memorials';
        }
    }
}

// --------------------
// REACTIONS
// --------------------
function showFacebookReactionPopup(id, event) {
    event.preventDefault();
    event.stopPropagation();

    const popup = document.getElementById(`fbReactionPopup-${id}`);
    if (!popup) return;

    document.querySelectorAll('.fb-reaction-popup').forEach(p => {
        if (p !== popup) {
            p.classList.remove('show');
            p.classList.remove('active');
        }
    });

    popup.classList.toggle('show');
    popup.classList.toggle('active');
}

async function handleReaction(postId, reaction, event) {
    event.preventDefault();
    event.stopPropagation();

    try {
        const formData = new FormData();
        formData.append('post_id', postId);
        formData.append('reaction', reaction);

        // This assumes you already have a reaction API.
        // If you don't, stop pretending reactions are done.
        const res = await fetch('api/post_reaction_toggle.php', {
            method: 'POST',
            body: formData,
            credentials: 'same-origin'
        });

        if (!res.ok) {
            throw new Error('Reaction request failed');
        }

        const data = await res.json();

        if (data.ok) {
            const item = state.feedItems.find(x => Number(x.id) === Number(postId));
            if (item) {
                item.my_reaction = data.my_reaction || '';
                item.react_total = Number(data.react_total || 0);

                const card = document.querySelector(`.feed-card[data-id="${postId}"]`);
                if (card && item) {
                    const replacement = document.createElement('div');
                    replacement.innerHTML = renderFeedItem(item);
                    card.replaceWith(replacement.firstElementChild);
                }
            }
        }
    } catch (err) {
        console.error('Reaction failed:', err);
    } finally {
        closeAllReactionPopups();
    }
}

function handleShareButton(id, event) {
    event.preventDefault();
    event.stopPropagation();

    const item = state.feedItems.find(x => Number(x.id) === Number(id));
    if (!item) return;

    const shareUrl = `${window.location.origin}/${item.details_url}`;
    const shareTitle = `In Memory of ${item.full_name}`;

    if (navigator.share) {
        navigator.share({
            title: shareTitle,
            text: shareTitle,
            url: shareUrl
        }).catch(err => console.log('Share cancelled:', err));
    } else {
        navigator.clipboard.writeText(shareUrl)
            .then(() => alert('Link copied to clipboard'))
            .catch(() => alert(shareUrl));
    }
}

// --------------------
// MAINTENANCE
// --------------------
async function loadMaintenance() {
    try {
        const data = await getJSON('api/maintenance_get.php');

        if (!data.maintenance) return;

        const m = data.maintenance;
        const wrapper = document.createElement('div');
        wrapper.id = 'maintenancePopup';
        wrapper.innerHTML = `
            <div style="
                position: fixed;
                inset: 0;
                background: rgba(0,0,0,0.7);
                display: flex;
                align-items: center;
                justify-content: center;
                z-index: 99999;
                padding: 20px;">
                <div style="
                    background: white;
                    border-radius: 16px;
                    max-width: 420px;
                    width: 100%;
                    position: relative;
                    padding: 16px;
                    box-shadow: 0 20px 40px rgba(0,0,0,0.25);">
                    <button id="closeMaintenancePopup" style="
                        position:absolute;
                        top:10px;
                        right:10px;
                        width:36px;
                        height:36px;
                        border:none;
                        border-radius:50%;
                        cursor:pointer;
                        font-size:18px;">×</button>
                    <img src="notification.php?id=${Number(m.id)}"
                         alt="Maintenance"
                         style="width:100%;border-radius:12px;">
                    ${m.label ? `<div style="margin-top:12px;font-weight:700;text-align:center;">${esc(m.label)}</div>` : ''}
                </div>
            </div>
        `;

        document.body.appendChild(wrapper);
        document.getElementById('closeMaintenancePopup')?.addEventListener('click', () => {
            wrapper.remove();
        });

    } catch (err) {
        console.error('Maintenance load failed:', err);
    }
}

// --------------------
// TABS
// --------------------
function bindTabs() {
    document.querySelectorAll('.tab-link').forEach(link => {
        link.addEventListener('click', function (e) {
            e.preventDefault();

            document.querySelectorAll('.tab-link').forEach(x => x.classList.remove('active'));
            this.classList.add('active');

            state.type = this.dataset.type || 'all';
            state.page = 1;
            state.displayedCount = 0;

            loadFeed(false);
        });
    });
}

// --------------------
// LOAD MORE
// --------------------
function bindLoadMore() {
    const btn = document.getElementById('loadMoreBtn');
    if (!btn) return;

    btn.addEventListener('click', function () {
        if (!state.hasMore) return;
        state.page += 1;
        loadFeed(true);
    });
}

// --------------------
// INIT SESSION
// --------------------
async function initSession() {
    try {
        await getJSON('api/session_sid.php');
    } catch (err) {
        console.error('Session init failed:', err);
    }
}

// --------------------
// GLOBALS
// --------------------
window.showFacebookReactionPopup = showFacebookReactionPopup;
window.handleReaction = handleReaction;
window.handleShareButton = handleShareButton;
window.nextAd = nextAd;
window.prevAd = prevAd;

// --------------------
// INIT
// --------------------
document.addEventListener('click', function () {
    closeAllReactionPopups();
});

document.addEventListener('DOMContentLoaded', async function () {
    bindTabs();
    bindLoadMore();

    await initSession();

    await Promise.all([
        loadOrgWhatsapp(),
        loadDistricts(),
        loadRecentComments(),
        loadAds(),
        loadFeed(false),
        loadMaintenance()
    ]);

    window.addEventListener('resize', function () {
        renderMobileAds();
    });
});