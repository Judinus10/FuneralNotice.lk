/**
 * contact.js - Logic for contact.html
 */

// Contact Form Submission
function initContactForm() {
    const contactForm = document.getElementById('contactForm');
    if (!contactForm) return;

    contactForm.addEventListener('submit', function (e) {
        e.preventDefault();

        // Get form values
        const name = document.getElementById('name').value;
        const email = document.getElementById('email').value;
        const phone = document.getElementById('phone').value;
        const subject = document.getElementById('subject').value;
        const message = document.getElementById('message').value;

        // Simple validation
        if (!name || !email || !phone || !subject || !message) {
            alert('Please fill in all required fields.');
            return;
        }

        // Show success message
        alert(`Thank you, ${name}! Your message has been sent successfully. We will contact you at ${email} or ${phone} within 2 hours.`);

        // Reset form
        this.reset();

        // In a real application, you would send the data to a server here
        console.log('Form submitted:', { name, email, phone, subject, message });
    });
}

// Initialize the contact page
document.addEventListener('DOMContentLoaded', function () {
    // Shared components are loaded by common.js via loadComponent in the HTML

    // Initialize contact-specific features
    initContactForm();

    // Create Funeral Notice Button - Redirect to create.html
    document.getElementById('createFuneralNoticeBtn')?.addEventListener('click', function () {
        this.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Creating...';
        this.disabled = true;

        setTimeout(() => {
            window.location.href = 'create.html';
        }, 500);
    });

    // Language selector
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

    // Phone number click handlers with confirmation for desktop
    document.querySelectorAll('.contact-detail').forEach(detail => {
        if (detail.href && detail.href.includes('tel:')) {
            detail.addEventListener('click', function (e) {
                if (window.innerWidth > 768) { // Desktop
                    e.preventDefault();
                    const phoneNumber = this.textContent;
                    if (confirm(`Call: ${phoneNumber}?`)) {
                        window.location.href = this.href;
                    }
                }
            });
        }
    });

    // Highlight active menu item in mobile menu
    const currentPage = window.location.pathname.split('/').pop();
    const menuLinks = document.querySelectorAll('.mobile-menu-nav a');
    menuLinks.forEach(link => {
        link.classList.remove('active');
        if (link.getAttribute('href') === currentPage ||
            (currentPage === '' && link.getAttribute('href') === 'index.html')) {
            link.classList.add('active');
        }
    });

    // Handle initial mobile app state if needed
    if (window.innerWidth <= 992) {
        if (typeof initMobileApp === 'function') initMobileApp();
    }
});
