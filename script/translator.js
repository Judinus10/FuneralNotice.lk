function initTranslator() {
    console.log("initNavbar called");
  const triggers = document.querySelectorAll('[data-lang-switcher]');

  async function setLanguage(lang) {
    try {
      const fd = new FormData();
      fd.append('lang', lang);

      const res = await fetch('api/set_language.php', {
        method: 'POST',
        body: fd,
        credentials: 'same-origin'
      });

      const json = await res.json();

      if (!json.ok) {
        alert(json.message || 'Failed to switch language.');
        return;
      }

      window.location.reload();
    } catch (err) {
      console.error('Language switch failed:', err);
      alert('Language switch failed.');
    }
  }

  triggers.forEach(trigger => {
    trigger.addEventListener('change', function () {
      const lang = this.value;
      if (lang) setLanguage(lang);
    });
  });
}