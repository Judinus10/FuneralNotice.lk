(function () {
    const config = window.TRIBUTE_POPUP_CONFIG || {};
    const postId = config.postId || 0;
    const basePath = config.basePath || 'tributes/';

    const chooserModal = document.getElementById('tributeChooserModal');
    const frameOverlay = document.getElementById('tributeFrameOverlay');
    const tributeFrame = document.getElementById('tributeFrame');
    const tributeFrameClose = document.getElementById('tributeFrameClose');

    const openChooser = () => {
        if (!chooserModal) return;
        chooserModal.style.display = 'flex';
        document.body.style.overflow = 'hidden';
    };

    const closeChooser = () => {
        if (!chooserModal) return;
        chooserModal.style.display = 'none';
        document.body.style.overflow = '';
    };

    const openFrame = (fileName) => {
        if (!frameOverlay || !tributeFrame || !fileName) return;

        const url = `${basePath}${fileName}?post_id=${encodeURIComponent(postId)}`;
        tributeFrame.src = url;
        frameOverlay.style.display = 'flex';
        document.body.style.overflow = 'hidden';
    };

    const closeFrame = () => {
        if (!frameOverlay || !tributeFrame) return;
        frameOverlay.style.display = 'none';
        tributeFrame.src = 'about:blank';
        document.body.style.overflow = '';
    };

    // Open chooser from button
    const triggerBtn = document.getElementById('btnTributeNow');
    triggerBtn?.addEventListener('click', function (e) {
        e.preventDefault();
        openChooser();
    });

    // Choose tribute type
    chooserModal?.addEventListener('click', function (e) {
        const card = e.target.closest('.tribute-chooser-card[data-tribute-file]');
        if (card) {
            const file = card.getAttribute('data-tribute-file');
            closeChooser();
            openFrame(file);
            return;
        }

        if (e.target === chooserModal) {
            closeChooser();
        }
    });

    // Close chooser buttons
    document.body.addEventListener('click', function (e) {
        const btn = e.target.closest('[data-close-tribute]');
        if (!btn) return;

        const selector = btn.getAttribute('data-close-tribute');
        const el = document.querySelector(selector);
        if (el) {
            el.style.display = 'none';
            document.body.style.overflow = '';
        }
    });

    // Close iframe popup
    tributeFrameClose?.addEventListener('click', closeFrame);

    frameOverlay?.addEventListener('click', function (e) {
        if (e.target === frameOverlay) {
            closeFrame();
        }
    });

    // Listen from iframe pages
    window.addEventListener('message', function (e) {
        if (e.data === 'close-tribute-overlay') {
            closeFrame();
        } else if (e.data === 'back-to-tribute-chooser') {
            closeFrame();
            openChooser();
        }
    });

    // ESC key
    window.addEventListener('keydown', function (e) {
        if (e.key !== 'Escape') return;

        if (frameOverlay && frameOverlay.style.display === 'flex') {
            closeFrame();
            return;
        }

        if (chooserModal && chooserModal.style.display === 'flex') {
            closeChooser();
        }
    });
})();