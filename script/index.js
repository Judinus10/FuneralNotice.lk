/**
 * index.js - Logic for the homepage (index.html)
 */

// --- DATA ---

const memorials = [
    {
        id: "memorial-1",
        type: "remembrance",
        title: "13th Year Remembrance",
        time: "4 Hours Ago",
        name: "Late Peethamparam Sellammah",
        birthYear: "1936",
        deathYear: "2013",
        locations: ["Mareesanakoodal, Sri Lanka", "Chavakachcheri, Sri Lanka", "Switzerland"],
        tributeCount: "8 Tributes",
        image: "https://cdn.lankasririp.com/memorial/profile/205767/6c67a177-e9cb-40cf-b8b6-e356616af3de/26-6971100cac46f-md.webp",
        hasCustomBorder: true,
        reactions: {
            sad: 5,
            tearful: 3,
            crying: 2,
            heartbroken: 4,
            praying: 6
        },
        userReaction: null
    },
    {
        id: "memorial-2",
        type: "obituary",
        title: "Obituary",
        time: "5 Hours Ago",
        name: "Mrs Kaneswary Sivaloganathan",
        birthYear: "1952",
        deathYear: "2026",
        locations: ["Oddisuddan, Sri Lanka"],
        tributeCount: "Be First To Tribute",
        image: "https://cdn.lankasririp.com/memorial/profile/232978/8d0680df-d3af-487a-b0bb-61bc829858b4/26-69728f54d131a-md.webp",
        hasCustomBorder: true,
        reactions: {
            sad: 3,
            tearful: 1,
            crying: 0,
            heartbroken: 2,
            praying: 4
        },
        userReaction: null
    },
    {
        id: "memorial-3",
        type: "obituary",
        title: "Obituary",
        time: "1 Day Ago",
        name: "Mrs Kanesammah Ponnusamy",
        birthYear: "1939",
        deathYear: "2026",
        locations: ["Madduvil East, Sri Lanka", "Rorschach, Switzerland"],
        tributeCount: "5 Tributes",
        image: "https://cdn.lankasririp.com/memorial/profile/232974/de27a9db-2d1c-4b69-a936-d9c3fccafca9/26-69712d227480d-md.webp",
        hasCustomBorder: true,
        reactions: {
            sad: 7,
            tearful: 4,
            crying: 3,
            heartbroken: 5,
            praying: 8
        },
        userReaction: null
    },
    {
        id: "memorial-4",
        type: "vip",
        title: "Overseas Memorial",
        time: "1 Day Ago",
        name: "Mr Subramaniam Ramasamy",
        birthYear: "1930",
        deathYear: "2026",
        locations: ["Navali, Sri Lanka", "Madduvil South, Sri Lanka", "Toronto, Canada", "Ajax, Canada"],
        tributeCount: "3 Tributes",
        image: "https://cdn.lankasririp.com/memorial/profile/232968/e05ce790-582f-4872-a7bb-b2b959ca8dc3/26-696fb7ce940c0-md.webp",
        hasCustomBorder: true,
        reactions: {
            sad: 4,
            tearful: 2,
            crying: 1,
            heartbroken: 3,
            praying: 5
        },
        userReaction: null
    },
    {
        id: "memorial-5",
        type: "obituary",
        title: "Obituary",
        time: "4 Days Ago",
        name: "Mrs Umathevi Sabapathy",
        birthYear: "1929",
        deathYear: "2026",
        locations: ["Analaitivu, Sri Lanka", "Toronto, Canada"],
        tributeCount: "6 Tributes",
        image: "https://cdn.lankasririp.com/memorial/profile/232915/836be137-e2e6-4f47-a5a6-78f222c69901/26-696aaeda29a13-md.webp",
        hasCustomBorder: true,
        reactions: {
            sad: 6,
            tearful: 3,
            crying: 2,
            heartbroken: 4,
            praying: 7
        },
        userReaction: null
    },
    {
        id: "memorial-6",
        type: "obituary",
        title: "Obituary",
        time: "Recently",
        name: "Mr Kandasamy Sivagnanam",
        birthYear: "1947",
        deathYear: "2026",
        locations: ["Navakkiri, Sri Lanka", "Yogapuram, Sri Lanka"],
        tributeCount: "5 Tributes",
        image: "https://cdn.lankasririp.com/memorial/profile/233005/0818a3cf-8126-457a-9fc4-ece1e194ac2a/26-6975dfeff31dd-md.webp",
        hasCustomBorder: true,
        reactions: {
            sad: 2,
            tearful: 1,
            crying: 0,
            heartbroken: 1,
            praying: 3
        },
        userReaction: null
    }
];

const recentPosts = [
    { country: "Australia", count: "1 Posts" },
    { country: "Canada", count: "28 Posts" },
    { country: "Switzerland", count: "6 Posts" },
    { country: "China", count: "1 Posts" },
    { country: "Germany", count: "6 Posts" },
    { country: "France", count: "8 Posts" },
    { country: "United Kingdom", count: "16 Posts" },
    { country: "India", count: "5 Posts" },
    { country: "Italy", count: "1 Posts" },
    { country: "Sri Lanka", count: "107 Posts" },
    { country: "Malaysia", count: "2 Posts" },
    { country: "Netherlands", count: "2 Posts" },
    { country: "Singapore", count: "1 Posts" },
    { country: "United States", count: "1 Posts" }
];

const recentTributes = [
    {
        name: "Mrs Vijayalatha Vijayaratnam",
        tributeBy: "Vipulanandan Vallipuram",
        time: "14 Hours Ago",
        more: "+10 MORE",
        image: "https://cdn.lankasririp.com/memorial/profile/232972/57e76f92-4aa8-4ead-afe4-a8ed04f58a79/26-6971c1cbd09de-md.webp"
    },
    {
        name: "Mr Sutharsan Sivapalan",
        tributeBy: "Thanapalasingam Sangeerthanan",
        time: "19 Hours Ago",
        more: "+4 MORE",
        image: "https://cdn.lankasririp.com/memorial/profile/232976/9c6c9214-ac08-4cc9-ba6f-1cbf733c2b18/26-6971cc9112825-md.webp"
    },
    {
        name: "Mrs Kanesammah Ponnusamy",
        tributeBy: "A.r.thayaparan",
        time: "1 Day Ago",
        more: "+4 MORE",
        image: "https://cdn.lankasririp.com/memorial/profile/232974/de27a9db-2d1c-4b69-a936-d9c3fccafca9/26-69712d227480d-md.webp"
    },
    {
        name: "Mr Karthigesan Rasiah",
        tributeBy: "Pithamagan",
        time: "1 Day Ago",
        more: "+11 MORE",
        image: "https://cdn.lankasririp.com/memorial/profile/232973/d242f985-4bca-4626-b4fa-8a386c2fb045/26-6971279d7dba3-md.webp"
    },
    {
        name: "Mr Subramaniam Ramasamy",
        tributeBy: "P M Muthiah",
        time: "1 Day Ago",
        more: "+2 MORE",
        image: "https://cdn.lankasririp.com/memorial/profile/232968/e05ce790-582f-4872-a7bb-b2b959ca8dc3/26-696fb7ce940c0-md.webp"
    },
    {
        name: "Mr Alexander Dunstan Rajkumar",
        tributeBy: "Llewellyn Selvanayagam",
        time: "1 Day Ago",
        more: "+2 MORE",
        image: "https://cdn.lankasririp.com/memorial/profile/232957/c7753958-e50f-4b29-b877-707cc2a7702d/26-696fc338be8b4-md.webp"
    }
];

const sponsoredAds = [
    {
        id: "ad-1",
        title: "Funeral Flower Arrangements",
        description: "Beautiful floral arrangements for funeral services. Same-day delivery available.",
        image: "https://images.unsplash.com/photo-1519681393784-d120267933ba?ixlib=rb-4.0.3&auto=format&fit=crop&w=400&h=200&q=80",
        badge: "Sponsored",
        whatsapp: "https://wa.me/94711234567",
        viewLink: "#",
        whatsappText: "Hi, I'm interested in your funeral flower arrangements."
    },
    {
        id: "ad-2",
        title: "Memorial Video Services",
        description: "Professional memorial video creation to honor your loved ones.",
        image: "https://images.unsplash.com/photo-1542037104857-ffbb0b9155fb?ixlib=rb-4.0.3&auto=format&fit=crop&w=400&h=200&q=80",
        badge: "Featured",
        whatsapp: "https://wa.me/94711234568",
        viewLink: "#",
        whatsappText: "Hi, I need information about memorial video services."
    },
    {
        id: "ad-3",
        title: "Funeral Catering Services",
        description: "Traditional funeral catering for gatherings. Customizable menus available.",
        image: "https://images.unsplash.com/photo-1565958011703-44f9829ba187?ixlib=rb-4.0.3&auto=format&fit=crop&w=400&h=200&q=80",
        badge: "Partner",
        whatsapp: "https://wa.me/94711234569",
        viewLink: "#",
        whatsappText: "Hi, I need catering services for a funeral gathering."
    }
];

const reactionTypes = {
    sad: { emoji: "😔", label: "Sad" },
    tearful: { emoji: "😢", label: "Tearful" },
    crying: { emoji: "😭", label: "Crying" },
    heartbroken: { emoji: "💔", label: "Heartbroken" },
    praying: { emoji: "🙏", label: "Praying" }
};

// --- STATE ---

let currentAdIndex = 0;
let adInterval;
let displayedMemorialsCount = 6;
const memorialsPerLoad = 3;

// --- FUNCTIONS ---

function renderRecentPosts() {
    const container = document.getElementById('recentPosts');
    if (!container) return;

    container.innerHTML = '';

    recentPosts.forEach(post => {
        const listItem = document.createElement('li');
        listItem.innerHTML = `
            <a href="#">
                <span class="info">
                    <span class="label-main">${post.country}</span>
                    <span class="label-secondary">${post.count}</span>
                </span>
            </a>
        `;
        container.appendChild(listItem);
    });
}

function renderRecentTributes() {
    const container = document.getElementById('recentTributes');
    if (!container) return;

    container.innerHTML = '';

    recentTributes.forEach(tribute => {
        const listItem = document.createElement('li');
        listItem.innerHTML = `
            <a href="#">
                <img src="${tribute.image}" alt="${tribute.name}" loading="lazy">
                <div class="tribute-info">
                    <span class="label-main">${tribute.name}</span>
                    <span class="label-secondary">${tribute.tributeBy}</span>
                    <div class="tributes-footer">
                        <span class="upd-at">${tribute.time}</span>
                        <span class="lnk-see-more">${tribute.more}</span>
                    </div>
                </div>
            </a>
        `;
        container.appendChild(listItem);
    });
}

function renderMobileSponsoredAds() {
    const mobileSponsoredSection = document.getElementById('mobileSponsoredSection');
    if (!mobileSponsoredSection) return;

    if (window.innerWidth <= 992) {
        mobileSponsoredSection.innerHTML = `
            <div class="list-multiple-item">
                <div class="header" style="background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);">
                    <h2><i class="fas fa-ad"></i> Sponsored Ads</h2>
                    <a href="https://api.whatsapp.com/send?phone=94769988123&text=Hello%20WEBbuilders.lk%20%F0%9F%91%8B%2C" 
                       target="_blank" 
                       rel="noopener"
                       class="whatsapp-btn-small" 
                       style="background: linear-gradient(135deg, #25D366 0%, #128C7E 100%); color: white; padding: 6px 12px; border-radius: 6px; text-decoration: none; display: inline-flex; align-items: center; gap: 5px; font-size: 0.8rem; font-weight: 600; transition: var(--transition); white-space: nowrap;">
                        <i class="fab fa-whatsapp"></i>
                        Place Ad
                    </a>
                </div>
                <div class="list">
                    <div id="mobileSponsoredCarousel" class="mobile-sponsored-carousel">
                        ${sponsoredAds.map((ad, index) => `
                            <div class="ad-slide ${index === 0 ? 'active' : ''}" data-index="${index}">
                                <div style="position: relative;">
                                    <img src="${ad.image}" alt="${ad.title}" class="ad-image" loading="lazy">
                                    <a href="${ad.whatsapp}?text=${encodeURIComponent(ad.whatsappText)}" 
                                       target="_blank" 
                                       rel="noopener"
                                       class="float-wa-btn" 
                                       aria-label="Contact on WhatsApp">
                                        <i class="fab fa-whatsapp"></i>
                                    </a>
                                </div>
                                <div class="ad-content">
                                    <span class="ad-badge">${ad.badge}</span>
                                    <h3 class="ad-title">${ad.title}</h3>
                                    <p class="ad-description">${ad.description}</p>
                                    <div class="ad-actions">
                                        <a href="${ad.whatsapp}?text=${encodeURIComponent(ad.whatsappText)}" 
                                           target="_blank" 
                                           rel="noopener"
                                           class="ad-btn ad-btn-whatsapp">
                                            <i class="fab fa-whatsapp"></i> WhatsApp
                                        </a>
                                        <a href="${ad.viewLink}" 
                                           class="ad-btn ad-btn-view">
                                            <i class="fas fa-eye"></i> View More
                                        </a>
                                    </div>
                                </div>
                            </div>
                        `).join('')}
                    </div>
                    <div class="ad-dots" id="mobileAdDots">
                        ${sponsoredAds.map((ad, index) => `
                            <div class="ad-dot ${index === 0 ? 'active' : ''}" data-index="${index}"></div>
                        `).join('')}
                    </div>
                </div>
            </div>
        `;
        initMobileCarousel();
    } else {
        mobileSponsoredSection.innerHTML = '';
    }
}

function initMobileCarousel() {
    let mobileAdIndex = 0;
    const mobileCarousel = document.getElementById('mobileSponsoredCarousel');
    if (!mobileCarousel) return;

    const mobileSlides = mobileCarousel.querySelectorAll('.ad-slide');
    const mobileDots = document.querySelectorAll('#mobileAdDots .ad-dot');

    function showMobileAd(index) {
        if (index >= mobileSlides.length || index < 0) return;

        mobileSlides.forEach(slide => slide.classList.remove('active'));
        mobileDots.forEach(dot => dot.classList.remove('active'));

        mobileSlides[index].classList.add('active');
        mobileDots[index].classList.add('active');

        mobileAdIndex = index;
    }

    mobileDots.forEach((dot, index) => {
        dot.addEventListener('click', () => showMobileAd(index));
    });

    let mobileAdInterval = setInterval(() => {
        const nextIndex = (mobileAdIndex + 1) % sponsoredAds.length;
        showMobileAd(nextIndex);
    }, 5000);

    mobileCarousel.addEventListener('mouseenter', () => clearInterval(mobileAdInterval));
    mobileCarousel.addEventListener('mouseleave', () => {
        mobileAdInterval = setInterval(() => {
            const nextIndex = (mobileAdIndex + 1) % sponsoredAds.length;
            showMobileAd(nextIndex);
        }, 5000);
    });
}

function renderSponsoredAds() {
    const carousel = document.getElementById('sponsoredCarousel');
    const dotsContainer = document.getElementById('adDots');

    if (!carousel || !dotsContainer) return;

    carousel.innerHTML = '';
    dotsContainer.innerHTML = '';

    sponsoredAds.forEach((ad, index) => {
        const slide = document.createElement('div');
        slide.className = `ad-slide ${index === 0 ? 'active' : ''}`;
        slide.setAttribute('data-index', index);

        slide.innerHTML = `
            <div style="position: relative;">
                <img src="${ad.image}" alt="${ad.title}" class="ad-image" loading="lazy">
                <a href="${ad.whatsapp}?text=${encodeURIComponent(ad.whatsappText)}" 
                   target="_blank" 
                   rel="noopener"
                   class="float-wa-btn" 
                   aria-label="Contact on WhatsApp">
                    <i class="fab fa-whatsapp"></i>
                </a>
            </div>
            <div class="ad-content">
                <span class="ad-badge">${ad.badge}</span>
                <h3 class="ad-title">${ad.title}</h3>
                <p class="ad-description">${ad.description}</p>
                <div class="ad-actions">
                    <a href="${ad.whatsapp}?text=${encodeURIComponent(ad.whatsappText)}" 
                       target="_blank" 
                       rel="noopener"
                       class="ad-btn ad-btn-whatsapp">
                        <i class="fab fa-whatsapp"></i> WhatsApp
                    </a>
                    <a href="${ad.viewLink}" 
                       class="ad-btn ad-btn-view">
                        <i class="fas fa-eye"></i> View More
                    </a>
                </div>
            </div>
        `;

        carousel.appendChild(slide);

        const dot = document.createElement('div');
        dot.className = `ad-dot ${index === 0 ? 'active' : ''}`;
        dot.setAttribute('data-index', index);
        dot.addEventListener('click', () => showAd(index));
        dotsContainer.appendChild(dot);
    });

    const arrows = document.createElement('div');
    arrows.className = 'ad-arrows';
    arrows.innerHTML = `
        <button class="ad-arrow ad-prev" onclick="prevAd()" aria-label="Previous ad">
            <i class="fas fa-chevron-left"></i>
        </button>
        <button class="ad-arrow ad-next" onclick="nextAd()" aria-label="Next ad">
            <i class="fas fa-chevron-right"></i>
        </button>
    `;
    carousel.appendChild(arrows);

    startAdRotation();
    addTouchSupport(carousel);
}

function showAd(index) {
    const slides = document.querySelectorAll('#sponsoredCarousel .ad-slide');
    const dots = document.querySelectorAll('#adDots .ad-dot');

    if (index >= slides.length || index < 0) return;

    slides.forEach(slide => slide.classList.remove('active'));
    dots.forEach(dot => dot.classList.remove('active'));

    slides[index].classList.add('active');
    dots[index].classList.add('active');

    currentAdIndex = index;
    resetAdRotation();
}

function nextAd() {
    const nextIndex = (currentAdIndex + 1) % sponsoredAds.length;
    showAd(nextIndex);
}

function prevAd() {
    const prevIndex = (currentAdIndex - 1 + sponsoredAds.length) % sponsoredAds.length;
    showAd(prevIndex);
}

window.nextAd = nextAd;
window.prevAd = prevAd;

function startAdRotation() {
    adInterval = setInterval(nextAd, 5000);
}

function resetAdRotation() {
    clearInterval(adInterval);
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
        handleSwipe();
    }, { passive: true });

    function handleSwipe() {
        const swipeThreshold = 50;
        const diff = touchStartX - touchEndX;

        if (Math.abs(diff) > swipeThreshold) {
            if (diff > 0) nextAd();
            else prevAd();
        }
    }
}

function getTotalReactions(reactions) {
    return Object.values(reactions).reduce((sum, count) => sum + count, 0);
}

function getTopReactions(reactions) {
    const sorted = Object.entries(reactions)
        .sort(([, a], [, b]) => b - a)
        .slice(0, 3)
        .filter(([, count]) => count > 0);

    return sorted.map(([emotion]) => reactionTypes[emotion].emoji);
}

function renderFeeds(limit = displayedMemorialsCount) {
    const container = document.getElementById('feedsContainer');
    if (!container) return;

    container.innerHTML = '';
    const memorialsToShow = memorials.slice(0, limit);

    memorialsToShow.forEach(memorial => {
        const totalReactions = getTotalReactions(memorial.reactions);
        const topReactions = getTopReactions(memorial.reactions);
        const feedCard = document.createElement('div');
        feedCard.className = `feed-card ${memorial.type === 'vip' ? 'premium' : ''}`;
        feedCard.setAttribute('data-id', memorial.id);

        feedCard.innerHTML = `
            <div class="feed-header">
                <span class="head"><span>${memorial.title}</span></span>
                <span class="actions"><span class="minago">${memorial.time}</span></span>
            </div>
            
            <a href="memorial-detail.html?id=${memorial.id}" class="card-body" onclick="handleCardClick('${memorial.id}', event)">
                <div class="avatar">
                    <span class="yearfrom">${memorial.birthYear}</span>
                    <div class="image-container">
                        <div class="photo-wrapper">
                            <img src="${memorial.image}" alt="${memorial.name}" class="photo" />
                        </div>
                        <div class="floral-decoration">
                            ${memorial.hasCustomBorder ? `<img src="floral.png" alt="Floral" />` : `<i class="sprite floral"></i>`}
                        </div>
                    </div>
                    <span class="yearto">${memorial.deathYear}</span>
                </div>
                <div class="info">
                    ${memorial.locations.map(loc => `<span class="subhead">${loc}</span>`).join('')}
                </div>
            </a>
            
            <div class="facebook-actions">
                <div class="action-row">
                    ${totalReactions > 0 ? `
                        <div class="reaction-summary">
                            <div class="reaction-icons-small">${topReactions.map(emoji => `<span>${emoji}</span>`).join('')}</div>
                            <div class="reaction-count-small"><span class="count">${totalReactions}</span></div>
                        </div>
                    ` : ''}
                    
                    <div class="tribute-summary">
                        <a href="memorial-detail.html?id=${memorial.id}#tributes" 
                           class="tribute-link ${memorial.tributeCount.includes('First') ? 'orange' : ''}"
                           onclick="handleCardClick('${memorial.id}', event)">
                            <i class="fas fa-heart"></i> ${memorial.tributeCount}
                        </a>
                    </div>
                    
                    <div class="facebook-buttons">
                        <button class="fb-action-btn fb-reaction-btn ${memorial.userReaction ? 'active' : ''}" 
                                onclick="showFacebookReactionPopup('${memorial.id}', event)">
                            <span class="fb-icon">${memorial.userReaction ? reactionTypes[memorial.userReaction].emoji : '😔'}</span>
                            <span>${memorial.userReaction ? reactionTypes[memorial.userReaction].label : 'React'}</span>
                            
                            <div class="fb-reaction-popup" id="fbReactionPopup-${memorial.id}">
                                ${Object.entries(reactionTypes).map(([key, val]) => `
                                    <div class="fb-reaction-option" 
                                         data-reaction="${key}"
                                         onclick="handleReaction('${memorial.id}', '${key}', event)">
                                        ${val.emoji}
                                    </div>
                                `).join('')}
                            </div>
                        </button>
                        
                        <a href="javascript:;" class="fb-action-btn fb-share-btn" onclick="handleShareButton('${memorial.id}', event)">
                            <i class="fas fa-share fb-icon"></i> <span>Share</span>
                        </a>
                    </div>
                </div>
            </div>
        `;
        container.appendChild(feedCard);
    });

    updateMemorialCount();
}

function handleCardClick(id, event) {
    // Analytics or tracking could go here
    console.log(`Memorial clicked: ${id}`);
}

window.handleCardClick = handleCardClick;

function showFacebookReactionPopup(id, event) {
    event.preventDefault();
    event.stopPropagation();

    // Close all other popups
    document.querySelectorAll('.fb-reaction-popup').forEach(p => {
        if (p.id !== `fbReactionPopup-${id}`) p.classList.remove('active');
    });

    const popup = document.getElementById(`fbReactionPopup-${id}`);
    if (popup) popup.classList.toggle('active');
}

window.showFacebookReactionPopup = showFacebookReactionPopup;

function handleReaction(memorialId, reactionKey, event) {
    event.preventDefault();
    event.stopPropagation();

    const memorial = memorials.find(m => m.id === memorialId);
    if (!memorial) return;

    if (memorial.userReaction === reactionKey) {
        memorial.reactions[reactionKey]--;
        memorial.userReaction = null;
    } else {
        if (memorial.userReaction) memorial.reactions[memorial.userReaction]--;
        memorial.reactions[reactionKey]++;
        memorial.userReaction = reactionKey;
    }

    renderFeeds();
}

window.handleReaction = handleReaction;

function handleShareButton(id, event) {
    event.preventDefault();
    event.stopPropagation();

    const memorial = memorials.find(m => m.id === id);
    if (!memorial) return;

    if (navigator.share) {
        navigator.share({
            title: `In Memory of ${memorial.name}`,
            text: `View the memorial of ${memorial.name} on FuneralNotice.lk`,
            url: window.location.href
        }).catch(err => console.log('Error sharing:', err));
    } else {
        alert(`Sharing memorial: ${memorial.name}\nLink: ${window.location.origin}/memorial-detail.html?id=${id}`);
    }
}

window.handleShareButton = handleShareButton;

function loadMoreMemorials() {
    const btn = document.getElementById('loadMoreBtn');
    if (!btn) return;

    btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Loading...';
    btn.disabled = true;

    setTimeout(() => {
        displayedMemorialsCount += memorialsPerLoad;
        renderFeeds(displayedMemorialsCount);

        btn.innerHTML = '<i class="fas fa-spinner"></i> Load More Memorials';
        btn.disabled = false;

        if (displayedMemorialsCount >= memorials.length) {
            btn.style.display = 'none';
        }
    }, 800);
}

function updateMemorialCount() {
    const countEl = document.getElementById('currentCount');
    if (countEl) countEl.textContent = Math.min(displayedMemorialsCount, memorials.length);
}

function filterMemorials(type) {
    console.log(`Filtering by type: ${type}`);
    // In a real app, this would filter the array and re-render
}

// Sidebars initialization 
function initSidebarToggle() {
    // This logic is mostly handled by CSS and simple event listeners
    // but we can add more robust handling here if needed.
}

// Global click handler to close popups
document.addEventListener('click', function () {
    document.querySelectorAll('.fb-reaction-popup').forEach(p => p.classList.remove('active'));
});

// Initialize the homepage
document.addEventListener('DOMContentLoaded', function () {
    renderRecentPosts();
    renderRecentTributes();
    renderFeeds();
    renderSponsoredAds();
    renderMobileSponsoredAds();

    const loadMoreBtn = document.getElementById('loadMoreBtn');
    if (loadMoreBtn) loadMoreBtn.addEventListener('click', loadMoreMemorials);

    window.addEventListener('resize', function () {
        if (window.innerWidth > 992) {
            document.getElementById('leftSidebar')?.classList.remove('active');
            document.getElementById('rightSidebar')?.classList.remove('active');
        }
        renderMobileSponsoredAds();
    });
});
