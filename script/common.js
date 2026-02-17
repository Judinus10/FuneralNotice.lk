/**
 * common.js - Shared utility functions for FuneralNotice.lk
 */

/**
 * Loads an external HTML component into a placeholder element.
 * @param {string} url - URL of the HTML component.
 * @param {string} elementId - ID of the placeholder element.
 */
function loadComponent(url, elementId) {
    fetch(url)
        .then(response => response.text())
        .then(data => {
            const element = document.getElementById(elementId);
            if (element) {
                element.innerHTML = data;
                // Re-initialize event listeners after navbar loads
                if (elementId === 'navbar-placeholder') {
                    if (typeof initMobileMenu === 'function') initMobileMenu();
                    if (typeof initMobileSearch === 'function') initMobileSearch();
                    if (typeof initMobileApp === 'function') initMobileApp();
                }
            }
        })
        .catch(error => console.error('Error loading component:', error));
}

/**
 * Initializes the mobile menu functionality.
 */
function initMobileMenu() {
    const mobileMenuBtn = document.getElementById('mobileMenuBtn');
    const mobileMenuClose = document.getElementById('mobileMenuClose');
    const mobileMenuContainer = document.getElementById('mobileMenuContainer');
    const mobileMenuOverlay = document.getElementById('mobileMenuOverlay');

    if (!mobileMenuBtn || !mobileMenuContainer) return;

    // Open mobile menu
    mobileMenuBtn.addEventListener('click', function (e) {
        e.stopPropagation();
        mobileMenuContainer.style.display = 'block';
        if (mobileMenuOverlay) mobileMenuOverlay.style.display = 'block';

        // Prevent body scroll
        const scrollBarWidth = window.innerWidth - document.documentElement.clientWidth;
        document.body.style.overflow = 'hidden';
        document.body.style.paddingRight = scrollBarWidth + 'px';

        setTimeout(() => {
            if (mobileMenuOverlay) mobileMenuOverlay.style.opacity = '1';
            mobileMenuContainer.classList.add('active');
        }, 10);
    });

    // Close function
    const closeMobileMenu = () => {
        mobileMenuContainer.classList.remove('active');
        if (mobileMenuOverlay) mobileMenuOverlay.style.opacity = '0';

        setTimeout(() => {
            mobileMenuContainer.style.display = 'none';
            if (mobileMenuOverlay) mobileMenuOverlay.style.display = 'none';
            document.body.style.overflow = '';
            document.body.style.paddingRight = '';
        }, 300);
    };

    if (mobileMenuClose) mobileMenuClose.addEventListener('click', closeMobileMenu);
    if (mobileMenuOverlay) mobileMenuOverlay.addEventListener('click', closeMobileMenu);

    // Escape key to close
    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape' && mobileMenuContainer.classList.contains('active')) {
            closeMobileMenu();
        }
    });

    // Export internal close function if needed by page scripts
    window.closeMobileMenu = closeMobileMenu;
}

/**
 * Initializes the mobile search functionality.
 */
function initMobileSearch() {
    const mobileSearchIcon = document.getElementById('mobileSearchIcon');
    const searchContainer = document.getElementById('mobileSearchContainer');
    const mobileSearchInput = document.getElementById('mobileSearchInput');

    if (!mobileSearchIcon || !searchContainer) return;

    mobileSearchIcon.addEventListener('click', function () {
        searchContainer.classList.toggle('active');
        if (searchContainer.classList.contains('active') && mobileSearchInput) {
            setTimeout(() => {
                mobileSearchInput.focus();
            }, 100);
        }
    });

    if (mobileSearchInput) {
        mobileSearchInput.addEventListener('keypress', function (e) {
            if (e.key === 'Enter' && this.value.trim()) {
                if (typeof performSearch === 'function') {
                    performSearch(this.value);
                } else {
                    alert(`Searching for: ${this.value}`);
                }
                searchContainer.classList.remove('active');
            }
        });
    }

    // Close search when clicking outside
    document.addEventListener('click', function (e) {
        if (!mobileSearchIcon.contains(e.target) && !searchContainer.contains(e.target)) {
            searchContainer.classList.remove('active');
        }
    });
}

/**
 * Initializes common mobile app features (language selector, bottom nav).
 */
function initMobileApp() {
    // Mobile language selector
    const mobileLangSelector = document.getElementById('mobileLanguageSelector');
    if (mobileLangSelector) {
        mobileLangSelector.addEventListener('click', function () {
            const span = this.querySelector('span');
            if (span) {
                const currentLang = span.textContent;
                span.textContent = currentLang === 'EN' ? 'த' : 'EN';
            }
        });
    }

    // Mobile bottom navigation
    document.querySelectorAll('.mobile-nav-item').forEach(item => {
        item.addEventListener('click', function (e) {
            const page = this.getAttribute('data-page');

            // Highlight active item (except menu)
            if (page !== 'menu') {
                document.querySelectorAll('.mobile-nav-item').forEach(nav => nav.classList.remove('active'));
                this.classList.add('active');
            }

            // Handle page-specific actions or navigation
            if (typeof handleMobilePageNavigation === 'function') {
                handleMobilePageNavigation(page);
            } else {
                // Default navigation
                switch (page) {
                    case 'home': window.location.href = 'index.html'; break;
                    case 'about': window.location.href = 'about.html'; break;
                    case 'contact': window.location.href = 'contact.html'; break;
                    case 'whatsapp': window.open('https://wa.me/94711234567', '_blank'); break;
                    case 'menu': document.getElementById('mobileMenuBtn')?.click(); break;
                }
            }
        });
    });

    // Mobile Create Notice Button
    const mobileCreateNoticeBtn = document.getElementById('mobileCreateNoticeBtn');
    if (mobileCreateNoticeBtn) {
        mobileCreateNoticeBtn.addEventListener('click', function () {
            window.location.href = 'create.html';
        });
    }
}
