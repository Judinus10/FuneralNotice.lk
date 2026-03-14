(function () {
    const cfg = window.TRIBUTE_PAGE || {};
    const form = document.getElementById('tributeEntryForm');
    const alertBox = document.getElementById('tributeFormAlert');

    const byName = document.getElementById('by_name');
    const byCountry = document.getElementById('by_country');
    const message = document.getElementById('message');
    const photoLinks = document.getElementById('photo_links');

    const previewName = document.getElementById('previewName');
    const previewCountry = document.getElementById('previewCountry');
    const previewMessage = document.getElementById('previewMessage');
    const previewPhotos = document.getElementById('previewPhotos');

    const btnBackChooser = document.getElementById('btnBackChooser');
    const btnBackBottom = document.getElementById('btnBackBottom');
    const btnCloseTribute = document.getElementById('btnCloseTribute');
    const btnSubmit = document.getElementById('btnSubmitTribute');

    function showAlert(type, msg) {
        if (!alertBox) return;
        alertBox.className = `tribute-alert ${type}`;
        alertBox.textContent = msg || '';
        alertBox.style.display = 'block';
    }

    function updatePreview() {
        if (previewName) {
            previewName.textContent = (byName?.value || '').trim() || 'Your Name';
        }

        if (previewCountry) {
            previewCountry.textContent = (byCountry?.value || '').trim() || 'Country';
        }

        if (previewMessage) {
            previewMessage.textContent = (message?.value || '').trim() || 'Your tribute message will appear here.';
        }

        if (previewPhotos && photoLinks) {
            const lines = photoLinks.value
                .split('\n')
                .map(v => v.trim())
                .filter(Boolean);

            previewPhotos.textContent = `${lines.length} photo link${lines.length === 1 ? '' : 's'} added`;
        }
    }

    function goBackChooser() {
        window.parent.postMessage('back-to-tribute-chooser', '*');
    }

    function closeOverlay() {
        window.parent.postMessage('close-tribute-overlay', '*');
    }

    [byName, byCountry, message, photoLinks].forEach(el => {
        el?.addEventListener('input', updatePreview);
    });

    btnBackChooser?.addEventListener('click', goBackChooser);
    btnBackBottom?.addEventListener('click', goBackChooser);
    btnCloseTribute?.addEventListener('click', closeOverlay);

    form?.addEventListener('submit', async function (e) {
        e.preventDefault();

        if (!form) return;

        btnSubmit.disabled = true;
        btnSubmit.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Submitting...';

        try {
            const fd = new FormData(form);

            if (photoLinks) {
                const lines = photoLinks.value
                    .split('\n')
                    .map(v => v.trim())
                    .filter(Boolean);

                fd.append('extra_json', JSON.stringify({
                    photo_links: lines
                }));
            }

            const res = await fetch('../api/tribute_entry_create.php', {
                method: 'POST',
                body: fd,
                credentials: 'same-origin'
            });

            const json = await res.json();

            if (!json.ok) {
                showAlert('error', json.message || 'Failed to submit tribute.');
                return;
            }

            showAlert('success', json.message || 'Tribute submitted successfully.');

            setTimeout(() => {
                closeOverlay();
            }, 1000);

        } catch (err) {
            showAlert('error', 'Something went wrong while submitting tribute.');
        } finally {
            btnSubmit.disabled = false;
            btnSubmit.innerHTML = '<i class="fa-solid fa-paper-plane"></i> Submit Tribute';
        }
    });

    updatePreview();
})();