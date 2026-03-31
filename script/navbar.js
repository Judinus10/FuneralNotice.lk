function initNavbar() {
    console.log("initNavbar called");

    // Global helper for elements
    const getPopupElements = () => ({
        overlay: document.getElementById("mobilePagesOverlay"),
        sheet: document.getElementById("mobilePagesSheet"),
        closeBtn: document.getElementById("closeMobilePages"),
        triggerBtn: document.getElementById("mobileBottomMenuBtn")
    });

    const openPagesPopup = () => {
        const els = getPopupElements();
        if (!els.overlay || !els.sheet) return;
        els.overlay.style.display = "block";
        els.sheet.style.display = "block";
        requestAnimationFrame(() => {
            els.overlay.style.opacity = "1";
            els.sheet.classList.add("active");
        });
        document.body.style.overflow = "hidden";
    };

    const closePagesPopup = () => {
        const els = getPopupElements();
        if (!els.overlay || !els.sheet) return;
        els.sheet.classList.remove("active");
        els.overlay.style.opacity = "0";
        setTimeout(() => {
            els.overlay.style.display = "none";
            els.sheet.style.display = "none";
        }, 400);
        document.body.style.overflow = "";
    };

    const clearResults = (container) => {
        if (!container) return;
        container.innerHTML = "";
        container.classList.remove("show");
    };

    // Global listeners should only be attached once
    if (!window.navbarGlobalListenersAttached) {
        window.navbarGlobalListenersAttached = true;
        
        document.addEventListener("click", (e) => {
            // Trigger Button
            if (e.target.closest("#mobileBottomMenuBtn")) {
                e.preventDefault();
                openPagesPopup();
            }
            // Close Button
            if (e.target.closest("#closeMobilePages")) {
                closePagesPopup();
            }
            // Overlay
            if (e.target === document.getElementById("mobilePagesOverlay")) {
                closePagesPopup();
            }

            // Search result clear
            const desktopSearchInput = document.getElementById("desktopSearchInput");
            const mobileSearchInput = document.getElementById("mobileSearchInput");
            if (desktopSearchInput && !desktopSearchInput.closest(".live-search-wrap")?.contains(e.target)) {
                clearResults(document.getElementById("desktopSearchResults"));
            }
            if (mobileSearchInput && !mobileSearchInput.closest(".live-search-wrap")?.contains(e.target)) {
                clearResults(document.getElementById("mobileSearchResults"));
            }
        });
    }

    // Direct Element Setup
    const mobileMenuBtn = document.getElementById("mobileMenuBtn");
    const mobileMenuClose = document.getElementById("mobileMenuClose");
    const mobileMenuOverlay = document.getElementById("mobileMenuOverlay");
    const mobileMenuContainer = document.getElementById("mobileMenuContainer");

    const openMobileMenu = () => {
        if (!mobileMenuOverlay || !mobileMenuContainer) return;
        mobileMenuOverlay.style.display = "block";
        mobileMenuContainer.style.display = "block";
        requestAnimationFrame(() => {
            mobileMenuContainer.classList.add("active");
        });
        document.body.style.overflow = "hidden";
    };

    const closeMobileMenu = () => {
        if (!mobileMenuOverlay || !mobileMenuContainer) return;
        mobileMenuContainer.classList.remove("active");
        mobileMenuOverlay.style.display = "none";
        setTimeout(() => {
            mobileMenuContainer.style.display = "none";
        }, 300);
        document.body.style.overflow = "";
    };

    mobileMenuBtn?.addEventListener("click", openMobileMenu);
    mobileMenuClose?.addEventListener("click", closeMobileMenu);
    mobileMenuOverlay?.addEventListener("click", closeMobileMenu);

    // Search Logic
    const mobileSearchIcon = document.getElementById("mobileSearchIcon");
    const mobileSearchToggle = document.getElementById("mobileSearchToggle");
    const mobileSearchContainer = document.getElementById("mobileSearchContainer");

    const toggleMobileSearch = () => {
        if (!mobileSearchContainer) return;
        mobileSearchContainer.classList.toggle("active");
    };

    mobileSearchIcon?.addEventListener("click", toggleMobileSearch);
    mobileSearchToggle?.addEventListener("click", toggleMobileSearch);

    const renderResults = (container, results) => {
        if (!container) return;
        if (!results.length) {
            container.innerHTML = `<div class="live-search-empty">No results found</div>`;
            container.classList.add("show");
            return;
        }
        container.innerHTML = results.map(item => `
            <a href="${item.url}" class="live-search-item">
                <div class="live-search-title">${escapeHtml(item.title || '')}</div>
                <div class="live-search-meta">
                    ${escapeHtml(item.type || '')}
                    ${item.location ? ' • ' + escapeHtml(item.location) : ''}
                    ${item.country ? ' • ' + escapeHtml(item.country) : ''}
                    ${item.status ? ' • ' + escapeHtml(item.status) : ''}
                </div>
            </a>
        `).join("");
        container.classList.add("show");
    };

    const escapeHtml = (str) => {
        return String(str || "")
            .replace(/&/g, "&amp;")
            .replace(/</g, "&lt;")
            .replace(/>/g, "&gt;")
            .replace(/"/g, "&quot;")
            .replace(/'/g, "&#039;");
    };

    let liveSearchTimer = null;
    const runLiveSearch = async (query, container) => {
        if (!container) return;
        if (!query.trim()) {
            clearResults(container);
            return;
        }
        container.innerHTML = `<div class="live-search-empty">Searching...</div>`;
        container.classList.add("show");
        try {
            const res = await fetch(`api/live_search.php?q=${encodeURIComponent(query)}`, {
                credentials: "same-origin"
            });
            const json = await res.json();
            if (!json.ok) {
                container.innerHTML = `<div class="live-search-empty">Search failed</div>`;
                return;
            }
            renderResults(container, json.results || []);
        } catch (err) {
            console.error("Live search failed:", err);
            container.innerHTML = `<div class="live-search-empty">Search failed</div>`;
        }
    };

    const bindLiveSearch = (input, container) => {
        if (!input || !container) return;
        input.addEventListener("input", () => {
            const query = input.value;
            clearTimeout(liveSearchTimer);
            liveSearchTimer = setTimeout(() => {
                runLiveSearch(query, container);
            }, 250);
        });
        input.addEventListener("focus", () => {
            if (input.value.trim()) {
                runLiveSearch(input.value, container);
            }
        });
    };

    bindLiveSearch(document.getElementById("desktopSearchInput"), document.getElementById("desktopSearchResults"));
    bindLiveSearch(document.getElementById("mobileSearchInput"), document.getElementById("mobileSearchResults"));
}