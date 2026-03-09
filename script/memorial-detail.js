// Sponsored Ads Data
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

// Default memorial data (Mrs Thiyakarasa Sivakamiammah)
const defaultMemorial = {
    id: "memorial-default",
    name: "Mrs Thiyakarasa Sivakamiammah",
    birthYear: "1939",
    deathYear: "2026",
    birthDate: "27<br>Jan<br>1939",
    deathDate: "24<br>Jan<br>2026",
    age: "Age 87",
    locations: ["Achchuveli, Jaffna, Sri Lanka"],
    image: "https://ripnews.lk/uploads/posts/76/cover.png",
    lifeStory: `<p><strong>யாழ். வரூணன் அச்சுவேலியைப் பிறப்பிடமாகவும், வதிவிடமாகவும் கொண்ட திருமதி தியாகராசா சிவகாமியம்மா அவர்கள் 24-01-2026 சனிக்கிழமை அன்று இறைபதம் அடைந்தார்.</strong></p>
                <p>காலஞ்சென்றவர்களான நாகலிங்கம் – சரஸ்வதிப்பிள்ளை தம்பதிகளின் அன்பு மகள்.</p>
                <p>காலஞ்சென்றவர்களான முருகேசு – தங்கம்மா தம்பதிகளின் அன்பு மருமகள்.</p>
                <p>காலஞ்சென்ற தியாகராசா அவர்களின் பாசமிகு துணைவியார்.</p>
                <p>காலஞ்சென்றவர்களான நாகராசா, சிவஞானரத்தினம், சிவசூப்பிரமணியம், சிவதாசன், வைகுந்தன் மற்றும் சிவநேசன் ஆகியோரின் அன்புச் சகோதரி.</p>
                <p>காலஞ்சென்றவர்களான கோசலையம்மா, சபாநாயகம், பரமேஸ்வரி மற்றும் தனலட்சுமி ஆகியோரின் அன்பு மைத்துனி.</p>
                <p>குமரகுருபரன் (பிரித்தானியா), ராகினி, நந்தகுமார் (பிரித்தானியா) ஆகியோரின் பாசமிகு தாயார்.</p>
                <p>கீதா (UK), ஜெசி (UK) ஆகியோரின் மாமியார்.</p>
                <p>ஷஜின்தன், ஜெஷின்தன், அக்ஷயா, மயூரி சோபியா, சயுரி இசபெல்லா ஆகியோரின் அன்புப் பேத்தி.</p>
                <p><strong>இறுதிக்கிரியைகள்</strong></p>
                <p>அன்னாரின் இறுதிக்கிரியைகள் 28-01-2026 புதன்கிழமை அன்று முற்பகல் 10:00 மணியளவில் அவரது இல்லத்தில் நடைபெற்று, பின்னர் வரூணன் தீத்தாங்குளம் hindu மயானத்தில் பூதவுடல் தகனம் செய்யப்படும்.</p>
                <p><strong>வீட்டு முகவரி:</strong> வரூணன், அச்சுவேலி.</p>
                <p><strong>தகவல்:</strong> குடும்பத்தினர்.</p>
                <p>அன்னாரின் பிரிவால் துயருற்றிருக்கும் குடும்பத்தினருக்கு எமது ஆழ்ந்த இரங்கல்களைத் தரிவித்துக் கொள்கிறோம்.</p>`,
    birthPlace: "Achchuveli, Jaffna",
    livedPlaces: "Jaffna, Sri Lanka",
    religion: "Hinduism",
    funeralDate: "28 Jan 2026, 10:00 AM",
    firstRemembrance: "24 Jan 2027",
    birthAnniversary: "27 Jan 2027"
};

// Sponsored Ads Carousel
let currentAdIndex = 0;
let adInterval;

// Toast helper function
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

// Wait for assets to load
async function waitForAssets(root) {
    // Wait for fonts
    if (document.fonts && document.fonts.ready) {
        try { await document.fonts.ready; } catch (e) { }
    }

    // Wait for images
    const imgs = Array.from(root.querySelectorAll('img'));
    await Promise.all(imgs.map(img => {
        if (img.complete && img.naturalWidth > 0) return Promise.resolve();
        return new Promise(res => {
            img.addEventListener('load', res, { once: true });
            img.addEventListener('error', res, { once: true });
        });
    }));
}

// Enhanced function to ensure portrait fits frame perfectly
function ensurePortraitFit() {
    const portrait = document.querySelector('.portrait');
    const frame = document.querySelector('.frame-img');

    if (portrait) {
        // Ensure portrait is circular and properly sized
        portrait.style.borderRadius = '50%';
        portrait.style.objectFit = 'cover';
        portrait.style.aspectRatio = '1 / 1';
        portrait.style.objectPosition = 'center center';

        // Larger portrait to better fill the frame
        portrait.style.width = '90%';
        portrait.style.height = '90%';

        // Adjust for poster mode separately
        if (document.getElementById('posterArea')?.classList.contains('poster-mode')) {
            portrait.style.width = '85%';
            portrait.style.height = '85%';
        }
    }

    if (frame) {
        // Slightly larger frame to better encompass the portrait
        frame.style.width = '110%';
        frame.style.height = '110%';

        // Center the frame
        frame.style.transform = 'translate(-50%, -50%)';
        frame.style.top = '50%';
        frame.style.left = '50%';

        // Adjust for poster mode
        if (document.getElementById('posterArea')?.classList.contains('poster-mode')) {
            frame.style.width = '120%';
            frame.style.height = '120%';
        }
    }
}

// Function to get memorial data from URL parameter or localStorage
function getMemorialData() {
    const urlParams = new URLSearchParams(window.location.search);
    const memorialId = urlParams.get('id');

    // Get from localStorage (set by index page)
    const memorialData = localStorage.getItem('currentMemorial');

    if (memorialData) {
        try {
            const parsedData = JSON.parse(memorialData);
            console.log("Loaded memorial data from localStorage:", parsedData);
            return parsedData;
        } catch (e) {
            console.error("Error parsing memorial data:", e);
        }
    }

    // If no localStorage but we have ID in URL, check for specific memorial
    if (memorialId) {
        // Try to get from index page memorials (if available)
        try {
            const allMemorials = JSON.parse(localStorage.getItem('allMemorials'));
            if (allMemorials && Array.isArray(allMemorials)) {
                const memorial = allMemorials.find(m => m.id === memorialId);
                if (memorial) {
                    // Enhance with additional data
                    const enhancedMemorial = {
                        ...memorial,
                        birthDate: formatDateString(memorial.birthYear),
                        deathDate: formatDateString(memorial.deathYear),
                        age: calculateAge(memorial.birthYear, memorial.deathYear),
                        birthPlace: memorial.locations && memorial.locations.length > 0 ? memorial.locations[0] : "Not specified",
                        livedPlaces: memorial.locations ? memorial.locations.join(", ") : "Not specified",
                        religion: "Hinduism",
                        funeralDate: getFuneralDate(memorial.deathYear),
                        lifeStory: generateLifeStory(memorial),
                        firstRemembrance: getFirstRemembrance(memorial.deathYear),
                        birthAnniversary: getBirthAnniversary(memorial.birthYear)
                    };
                    return enhancedMemorial;
                }
            }
        } catch (e) {
            console.error("Error loading from allMemorials:", e);
        }
    }

    // Return default memorial if nothing found
    console.log("Using default memorial data");
    return defaultMemorial;
}

// Helper function to format date string
function formatDateString(year) {
    const month = ["Jan", "Feb", "Mar", "Apr", "May", "Jun",
        "Jul", "Aug", "Sep", "Oct", "Nov", "Dec"][Math.floor(Math.random() * 12)];
    const day = Math.floor(Math.random() * 28) + 1;
    return `${day}<br>${month}<br>${year}`;
}

// Helper function to calculate age
function calculateAge(birthYear, deathYear) {
    const age = parseInt(deathYear) - parseInt(birthYear);
    return `Age ${age}`;
}

// Helper function to generate funeral date
function getFuneralDate(deathYear) {
    const month = ["Jan", "Feb", "Mar", "Apr", "May", "Jun",
        "Jul", "Aug", "Sep", "Oct", "Nov", "Dec"][Math.floor(Math.random() * 12)];
    const day = Math.floor(Math.random() * 28) + 1;
    return `${day} ${month} ${parseInt(deathYear) + 1}, 10:00 AM`;
}

// Helper function to generate first remembrance date
function getFirstRemembrance(deathYear) {
    const month = ["Jan", "Feb", "Mar", "Apr", "May", "Jun",
        "Jul", "Aug", "Sep", "Oct", "Nov", "Dec"][Math.floor(Math.random() * 12)];
    const day = Math.floor(Math.random() * 28) + 1;
    return `${day} ${month} ${parseInt(deathYear) + 1}`;
}

// Helper function to generate birth anniversary
function getBirthAnniversary(birthYear) {
    const month = ["Jan", "Feb", "Mar", "Apr", "May", "Jun",
        "Jul", "Aug", "Sep", "Oct", "Nov", "Dec"][Math.floor(Math.random() * 12)];
    const day = Math.floor(Math.random() * 28) + 1;
    return `${day} ${month} ${parseInt(birthYear) + 88}`;
}

// Helper function to generate life story
function generateLifeStory(memorial) {
    return `
        <p><strong>In loving memory of ${memorial.name}.</strong></p>
        <p>Born in ${memorial.birthYear} and passed away in ${memorial.deathYear}.</p>
        ${memorial.locations ? `<p>Lived in: ${memorial.locations.join(", ")}</p>` : ''}
        <p>A beloved family member and friend who will be dearly missed by all who knew them.</p>
        <p>Their memory will live on in the hearts of family and friends.</p>
        <p><strong>Funeral arrangements:</strong> Will be announced by the family.</p>
        <p>Our deepest condolences to the grieving family.</p>
    `;
}

// Function to populate memorial data
function populateMemorialData() {
    const memorial = getMemorialData();

    if (!memorial) {
        console.error("No memorial data found");
        return;
    }

    console.log("Populating with memorial:", memorial);

    // Update page title
    document.title = `${memorial.name} - FuneralNotice.lk`;

    // Update OG meta tags for social sharing
    updateMetaTags(memorial);

    // Update hero section
    const memorialName = document.getElementById('memorialName');
    const memorialAge = document.getElementById('memorialAge');
    const memorialLocation = document.getElementById('memorialLocation');
    const birthDate = document.getElementById('birthDate');
    const deathDate = document.getElementById('deathDate');

    if (memorialName) memorialName.textContent = memorial.name;
    if (memorialAge) memorialAge.textContent = memorial.age || `Age ${parseInt(memorial.deathYear) - parseInt(memorial.birthYear)}`;
    if (memorialLocation) {
        const location = memorial.locations && memorial.locations.length > 0 ? memorial.locations[0] : "Location information";
        memorialLocation.textContent = location;
    }

    // Update dates
    if (birthDate) {
        birthDate.innerHTML = memorial.birthDate || `27<br>Jan<br>${memorial.birthYear}`;
    }

    if (deathDate) {
        deathDate.innerHTML = memorial.deathDate || `24<br>Jan<br>${memorial.deathYear}`;
    }

    // Update portrait image
    const portraitImg = document.getElementById('memorialPortrait');
    if (portraitImg) {
        portraitImg.src = memorial.image || 'https://images.unsplash.com/photo-1542736667-069246bdbc6d?ixlib=rb-4.0.3&auto=format&fit=crop&w=1080&q=80';
        portraitImg.alt = memorial.name;

        // Add error handler
        portraitImg.onerror = function () {
            this.src = 'https://images.unsplash.com/photo-1542736667-069246bdbc6d?ixlib=rb-4.0.3&auto=format&fit=crop&w=1080&q=80';
            // Re-apply sizing after image loads
            setTimeout(ensurePortraitFit, 100);
        };
    }

    // Update life story
    const lifeStoryEl = document.getElementById('lifeStory');
    if (lifeStoryEl) {
        lifeStoryEl.innerHTML = memorial.lifeStory ||
            `<p>No life story available for ${memorial.name}.</p>`;
    }

    // Update information card
    const birthPlaceEl = document.getElementById('birthPlace');
    const livedPlacesEl = document.getElementById('livedPlaces');
    const religionEl = document.getElementById('religion');
    const funeralDateEl = document.getElementById('funeralDate');
    const firstRemembranceEl = document.getElementById('firstRemembrance');
    const birthAnniversaryEl = document.getElementById('birthAnniversary');

    if (birthPlaceEl) {
        birthPlaceEl.textContent = memorial.birthPlace ||
            (memorial.locations && memorial.locations.length > 0 ? memorial.locations[0] : "Not specified");
    }

    if (livedPlacesEl) {
        livedPlacesEl.textContent = memorial.livedPlaces ||
            (memorial.locations ? memorial.locations.join(", ") : "Not specified");
    }

    if (religionEl) {
        religionEl.textContent = memorial.religion || "Hinduism";
    }

    if (funeralDateEl) {
        funeralDateEl.textContent = memorial.funeralDate || "Will be announced by family";
    }

    if (firstRemembranceEl) {
        firstRemembranceEl.textContent = memorial.firstRemembrance || getFirstRemembrance(memorial.deathYear);
    }

    if (birthAnniversaryEl) {
        birthAnniversaryEl.textContent = memorial.birthAnniversary || getBirthAnniversary(memorial.birthYear);
    }
}

// Function to update meta tags for social sharing
function updateMetaTags(memorial) {
    // Update Open Graph tags
    const ogTitle = document.querySelector('meta[property="og:title"]');
    const ogDescription = document.querySelector('meta[property="og:description"]');
    const ogImage = document.querySelector('meta[property="og:image"]');
    const ogUrl = document.querySelector('meta[property="og:url"]');

    if (ogTitle) {
        ogTitle.setAttribute('content', `${memorial.name} – FuneralNotice.lk`);
    }

    if (ogDescription) {
        const description = `View memorial for ${memorial.name} (${memorial.birthYear} – ${memorial.deathYear}) on FuneralNotice.lk.`;
        ogDescription.setAttribute('content', description);
    }

    if (ogImage) {
        ogImage.setAttribute('content', memorial.image || 'https://ripnews.lk/uploads/posts/76/cover.png');
    }

    if (ogUrl) {
        const currentUrl = window.location.href;
        ogUrl.setAttribute('content', currentUrl);
    }

    // Update Twitter cards
    const twitterTitle = document.querySelector('meta[name="twitter:title"]');
    const twitterDescription = document.querySelector('meta[name="twitter:description"]');
    const twitterImage = document.querySelector('meta[name="twitter:image"]');

    if (twitterTitle) {
        twitterTitle.setAttribute('content', `${memorial.name} – FuneralNotice.lk`);
    }

    if (twitterDescription) {
        const description = `View memorial for ${memorial.name} (${memorial.birthYear} – ${memorial.deathYear}) on FuneralNotice.lk.`;
        twitterDescription.setAttribute('content', description);
    }

    if (twitterImage) {
        twitterImage.setAttribute('content', memorial.image || 'https://ripnews.lk/uploads/posts/76/cover.png');
    }

    // Update page meta description
    const metaDescription = document.querySelector('meta[name="description"]');
    if (metaDescription) {
        metaDescription.setAttribute('content',
            `View memorial for ${memorial.name} (${memorial.birthYear} – ${memorial.deathYear}) on FuneralNotice.lk.`);
    }
}

// Function to render sponsored ads
function renderSponsoredAds() {
    const carousel = document.getElementById('sponsoredCarousel');
    const dotsContainer = document.getElementById('adDots');

    if (!carousel || !dotsContainer) return;

    // Clear existing content
    carousel.innerHTML = '';
    dotsContainer.innerHTML = '';

    // Create ad slides
    sponsoredAds.forEach((ad, index) => {
        // Create slide
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

        // Create dot
        const dot = document.createElement('div');
        dot.className = `ad-dot ${index === 0 ? 'active' : ''}`;
        dot.setAttribute('data-index', index);
        dot.addEventListener('click', () => showAd(index));
        dotsContainer.appendChild(dot);
    });

    // Add arrows for desktop
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

    // Start auto-rotation
    startAdRotation();

    // Add touch support for mobile
    addTouchSupport(carousel);
}

function showAd(index) {
    const slides = document.querySelectorAll('.ad-slide');
    const dots = document.querySelectorAll('.ad-dot');

    if (index >= slides.length || index < 0) return;

    // Hide all slides
    slides.forEach(slide => slide.classList.remove('active'));
    dots.forEach(dot => dot.classList.remove('active'));

    // Show selected slide
    slides[index].classList.add('active');
    dots[index].classList.add('active');

    currentAdIndex = index;

    // Reset auto-rotation timer
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

function startAdRotation() {
    adInterval = setInterval(nextAd, 5000); // Change ad every 5 seconds
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
            if (diff > 0) {
                // Swipe left - next ad
                nextAd();
            } else {
                // Swipe right - previous ad
                prevAd();
            }
        }
    }
}

// Poster download functionality
const btnDownloadPoster = document.getElementById('btnDownloadPoster');
const posterArea = document.getElementById('posterArea');

btnDownloadPoster?.addEventListener('click', async () => {
    if (!posterArea || typeof html2canvas === 'undefined') return;

    // Show loading notification
    notify('info', 'Generating Poster', 'Please wait while we prepare your poster...');

    // Add poster mode class to hide buttons and show watermark
    posterArea.classList.add('poster-mode');

    // Ensure portrait and frame are properly sized before capture
    ensurePortraitFit();

    // Wait for next animation frame
    await new Promise(r => requestAnimationFrame(r));

    // Wait for all images to load
    await waitForAssets(posterArea);

    try {
        // Generate canvas with higher quality
        const canvas = await html2canvas(posterArea, {
            useCORS: true,
            backgroundColor: '#261921',
            scale: 3,
            scrollX: 0,
            scrollY: 0,
            logging: false,
            allowTaint: true,
            foreignObjectRendering: false,
            onclone: function (clonedDoc) {
                const clonedPoster = clonedDoc.getElementById('posterArea');
                if (clonedPoster) {
                    clonedPoster.classList.add('poster-mode');
                    // Ensure circular portrait and larger frame in cloned document
                    const clonedPortrait = clonedPoster.querySelector('.portrait');
                    const clonedFrame = clonedPoster.querySelector('.frame-img');

                    if (clonedPortrait) {
                        clonedPortrait.style.borderRadius = '50%';
                        clonedPortrait.style.objectFit = 'cover';
                        clonedPortrait.style.aspectRatio = '1 / 1';
                        clonedPortrait.style.width = '85%';
                        clonedPortrait.style.height = '85%';
                    }

                    if (clonedFrame) {
                        clonedFrame.style.width = '120%';
                        clonedFrame.style.height = '120%';
                        clonedFrame.style.transform = 'translate(-50%, -50%)';
                        clonedFrame.style.top = '50%';
                        clonedFrame.style.left = '50%';
                    }
                }
            }
        });

        // Remove poster mode
        posterArea.classList.remove('poster-mode');

        // Create download link
        const link = document.createElement('a');
        link.href = canvas.toDataURL('image/png', 1.0);

        // Get memorial name for filename
        const memorialName = document.getElementById('memorialName').textContent;
        const fileName = memorialName.replace(/[^a-z0-9]/gi, '-').toLowerCase();
        link.download = `${fileName}-memorial-poster.png`;

        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);

        // Show success message
        notify('success', 'Poster Downloaded', 'Memorial poster saved successfully.');

    } catch (err) {
        console.error('Poster generation failed:', err);
        posterArea.classList.remove('poster-mode');
        notify('error', 'Download Failed', 'Unable to generate poster. Please try again.');
    }
});

// Action button functions
document.getElementById('btnTributeNow')?.addEventListener('click', function () {
    document.getElementById('tributes')?.scrollIntoView({ behavior: 'smooth', block: 'start' });
});

document.getElementById('btnWriteTribute')?.addEventListener('click', function () {
    notify('info', 'Write Tribute', 'Tribute submission feature will be available soon.');
});

document.getElementById('btnSendFlowers')?.addEventListener('click', function () {
    notify('info', 'Send Flowers', 'Flower sending feature will be available soon.');
});

document.getElementById('btnDonate')?.addEventListener('click', function () {
    notify('info', 'Donate', 'Donation feature will be available soon.');
});

document.getElementById('btnShare')?.addEventListener('click', async function () {
    try {
        const memorial = getMemorialData();
        if (navigator.share) {
            await navigator.share({
                title: `${memorial.name} Memorial`,
                text: `Remembering ${memorial.name} on FuneralNotice.lk`,
                url: window.location.href
            });
        } else {
            await navigator.clipboard.writeText(window.location.href);
            notify('success', 'Link Copied', 'Memorial link copied to clipboard');
        }
    } catch (err) {
        console.error('Share failed:', err);
    }
});

// Initialize the page
document.addEventListener('DOMContentLoaded', function () {
    // Load and populate memorial data FIRST
    populateMemorialData();

    // Ensure portrait and frame are properly sized
    ensurePortraitFit();

    // Set frame image fallback
    const frameImg = document.querySelector('.frame-img');
    if (frameImg) {
        frameImg.onerror = function () {
            this.src = 'https://i.ibb.co/0nLbfWj/floral-frame-circle.png';
        };
    }

    // Check for portrait image error
    const portraitImg = document.querySelector('.portrait');
    if (portraitImg) {
        portraitImg.onerror = function () {
            this.src = 'https://images.unsplash.com/photo-1542736667-069246bdbc6d?ixlib=rb-4.0.3&auto=format&fit=crop&w=1080&q=80';
            // Re-apply sizing after image loads
            setTimeout(ensurePortraitFit, 100);
        };
    }

    // Re-apply sizing after image loads completely
    if (portraitImg && portraitImg.complete) {
        ensurePortraitFit();
    } else if (portraitImg) {
        portraitImg.addEventListener('load', ensurePortraitFit);
    }

    // Also apply to frame image
    if (frameImg && frameImg.complete) {
        ensurePortraitFit();
    } else if (frameImg) {
        frameImg.addEventListener('load', ensurePortraitFit);
    }

    // Render sponsored ads
    renderSponsoredAds();

    // UPDATED: Create Funeral Notice Button - Redirect to create.php
    document.getElementById('createFuneralNoticeBtn')?.addEventListener('click', function () {
        // Show loading state
        this.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Creating...';
        this.disabled = true;

        // Redirect after short delay to show loading animation
        setTimeout(() => {
            window.location.href = 'create.php';
        }, 500);
    });

    // Language selector
    document.querySelector('.language-selector')?.addEventListener('click', function () {
        const currentLang = this.querySelector('span').textContent;
        const newLang = currentLang === 'English' ? 'தமிழ்' : 'English';
        this.querySelector('span').textContent = newLang;
    });

    // Search functionality
    const searchInputs = document.querySelectorAll('input[type="text"]');
    searchInputs.forEach(input => {
        input.addEventListener('keypress', function (e) {
            if (e.key === 'Enter' && this.value.trim()) {
                alert(`Searching for: ${this.value}`);
            }
        });
    });

    // Initial toast notification
    setTimeout(() => {
        const memorial = getMemorialData();
        notify('info', 'Memorial Page', `Viewing memorial for ${memorial.name}.`);
    }, 1000);

    // Initialize mobile app on mobile devices
    if (window.innerWidth <= 992) {
        if (typeof initMobileApp === 'function') initMobileApp();
    }

    // Re-initialize on resize
    window.addEventListener('resize', function () {
        if (window.innerWidth <= 992) {
            if (typeof initMobileApp === 'function') initMobileApp();
        }
    });
});
