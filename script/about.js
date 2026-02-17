/**
 * about.js - Logic for about.html
 */

// Scroll Animations
function initScrollAnimations() {
    const fadeElements = document.querySelectorAll('.fade-in, .stagger-item');

    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.classList.add('visible');
            }
        });
    }, {
        threshold: 0.1,
        rootMargin: '0px 0px -50px 0px'
    });

    fadeElements.forEach(element => {
        observer.observe(element);
    });

    // Add staggered delay to stagger items
    document.querySelectorAll('.stagger-item').forEach((item, index) => {
        item.style.transitionDelay = `${index * 0.1}s`;
    });
}

// Animated Counter
function initCounters() {
    const counters = document.querySelectorAll('.stat-number');

    counters.forEach(counter => {
        const target = parseInt(counter.getAttribute('data-count'));
        const duration = 2000; // 2 seconds
        const increment = target / (duration / 16); // 60fps
        let current = 0;

        const updateCounter = () => {
            current += increment;
            if (current < target) {
                counter.textContent = Math.floor(current);
                requestAnimationFrame(updateCounter);
            } else {
                counter.textContent = target;
            }
        };

        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    updateCounter();
                    observer.unobserve(entry.target);
                }
            });
        });

        observer.observe(counter);
    });
}

// Initialize the about page
document.addEventListener('DOMContentLoaded', function () {
    // Shared components are loaded by common.js via loadComponent in the HTML

    // Initialize about-specific features
    initScrollAnimations();
    initCounters();

    // Create Funeral Notice Button - Redirect to create.html
    document.getElementById('createFuneralNoticeBtn')?.addEventListener('click', function () {
        this.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Creating...';
        this.disabled = true;

        setTimeout(() => {
            window.location.href = 'create.html';
        }, 500);
    });

    // Desktop Language selector (Mobile version is in common.js)
    document.querySelector('.language-selector')?.addEventListener('click', function () {
        const span = this.querySelector('span');
        if (span) {
            const currentLang = span.textContent;
            span.textContent = currentLang === 'English' ? 'தமிழ்' : 'English';
        }
    });

    // Search functionality
    const searchInputs = document.querySelectorAll('.search-box input[type="text"]');
    searchInputs.forEach(input => {
        input.addEventListener('keypress', function (e) {
            if (e.key === 'Enter' && this.value.trim()) {
                alert(`Searching for: ${this.value}`);
            }
        });
    });

    // Handle window resize logic if not fully covered by common.js
    window.addEventListener('resize', function () {
        if (window.innerWidth <= 992) {
            if (typeof initMobileApp === 'function') initMobileApp();
        }
    });
});
