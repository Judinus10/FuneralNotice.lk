document.addEventListener("DOMContentLoaded", () => {
  const mobileMenuBtn = document.getElementById("mobileMenuBtn");
  const mobileMenuClose = document.getElementById("mobileMenuClose");
  const mobileMenuOverlay = document.getElementById("mobileMenuOverlay");
  const mobileMenuContainer = document.getElementById("mobileMenuContainer");

  const mobileSearchIcon = document.getElementById("mobileSearchIcon");
  const mobileSearchToggle = document.getElementById("mobileSearchToggle");
  const mobileSearchContainer = document.getElementById("mobileSearchContainer");

  const desktopSearchInput = document.getElementById("desktopSearchInput");
  const mobileSearchInput = document.getElementById("mobileSearchInput");
  const desktopSearchResults = document.getElementById("desktopSearchResults");
  const mobileSearchResults = document.getElementById("mobileSearchResults");

  let liveSearchTimer = null;

  function openMobileMenu() {
    if (!mobileMenuOverlay || !mobileMenuContainer) return;
    mobileMenuOverlay.style.display = "block";
    mobileMenuContainer.style.display = "block";
    requestAnimationFrame(() => {
      mobileMenuContainer.classList.add("active");
    });
    document.body.style.overflow = "hidden";
  }

  function closeMobileMenu() {
    if (!mobileMenuOverlay || !mobileMenuContainer) return;
    mobileMenuContainer.classList.remove("active");
    mobileMenuOverlay.style.display = "none";
    setTimeout(() => {
      mobileMenuContainer.style.display = "none";
    }, 300);
    document.body.style.overflow = "";
  }

  mobileMenuBtn?.addEventListener("click", openMobileMenu);
  mobileMenuClose?.addEventListener("click", closeMobileMenu);
  mobileMenuOverlay?.addEventListener("click", closeMobileMenu);

  function toggleMobileSearch() {
    if (!mobileSearchContainer) return;
    mobileSearchContainer.classList.toggle("active");
  }

  mobileSearchIcon?.addEventListener("click", toggleMobileSearch);
  mobileSearchToggle?.addEventListener("click", toggleMobileSearch);

  function renderResults(container, results) {
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
  }

  function clearResults(container) {
    if (!container) return;
    container.innerHTML = "";
    container.classList.remove("show");
  }

  function escapeHtml(str) {
    return String(str || "")
      .replace(/&/g, "&amp;")
      .replace(/</g, "&lt;")
      .replace(/>/g, "&gt;")
      .replace(/"/g, "&quot;")
      .replace(/'/g, "&#039;");
  }

  async function runLiveSearch(query, container) {
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
  }

  function bindLiveSearch(input, container) {
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
  }

  bindLiveSearch(desktopSearchInput, desktopSearchResults);
  bindLiveSearch(mobileSearchInput, mobileSearchResults);

  document.addEventListener("click", (e) => {
    const desktopWrap = desktopSearchInput?.closest(".live-search-wrap");
    const mobileWrap = mobileSearchInput?.closest(".live-search-wrap");

    if (desktopWrap && !desktopWrap.contains(e.target)) {
      clearResults(desktopSearchResults);
    }

    if (mobileWrap && !mobileWrap.contains(e.target)) {
      clearResults(mobileSearchResults);
    }
  });
});