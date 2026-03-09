/**
 * create.js - Logic for create.php
 */

let uploadedPhoto = null;

// Photo upload functionality
function handlePhotoUpload(event) {
    const file = event.target.files[0];
    if (file) {
        // Check file size (max 5MB)
        if (file.size > 5 * 1024 * 1024) {
            alert('File size exceeds 5MB limit. Please choose a smaller image.');
            return;
        }

        // Check file type
        if (!file.type.match('image.*')) {
            alert('Please select an image file (JPEG, PNG, etc.)');
            return;
        }

        uploadedPhoto = file;

        // Display uploaded photo
        const reader = new FileReader();
        reader.onload = function (e) {
            const uploadedImage = document.getElementById('uploadedImage');
            const photoName = document.getElementById('photoName');
            const photoSize = document.getElementById('photoSize');
            const uploadContainer = document.getElementById('uploadContainer');
            const uploadedPhotoContainer = document.getElementById('uploadedPhotoContainer');

            if (uploadedImage) uploadedImage.src = e.target.result;
            if (photoName) photoName.textContent = file.name;
            if (photoSize) photoSize.textContent = formatFileSize(file.size);

            if (uploadContainer) uploadContainer.style.display = 'none';
            if (uploadedPhotoContainer) uploadedPhotoContainer.style.display = 'block';
        };
        reader.readAsDataURL(file);
    }
}

function removePhoto() {
    uploadedPhoto = null;
    const uploadContainer = document.getElementById('uploadContainer');
    const uploadedPhotoContainer = document.getElementById('uploadedPhotoContainer');
    const photoUpload = document.getElementById('photoUpload');

    if (uploadContainer) uploadContainer.style.display = 'block';
    if (uploadedPhotoContainer) uploadedPhotoContainer.style.display = 'none';
    if (photoUpload) photoUpload.value = '';
}

function formatFileSize(bytes) {
    if (bytes < 1024) return bytes + ' bytes';
    else if (bytes < 1048576) return (bytes / 1024).toFixed(1) + ' KB';
    else return (bytes / 1048576).toFixed(1) + ' MB';
}

function createMemorial(event) {
    event.preventDefault();

    // Basic form validation
    const form = document.getElementById('memorialForm');
    if (form && !form.checkValidity()) {
        alert('Please fill in all required fields (marked with *)');
        return;
    }

    if (!uploadedPhoto) {
        if (!confirm('You haven\'t uploaded a funeral notice photo. Continue without a photo?')) {
            return;
        }
    }

    // Show loading state
    const submitBtn = event.target.closest('button') || event.target;
    const originalText = submitBtn.innerHTML;
    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Creating...';
    submitBtn.disabled = true;

    // Simulate API call
    setTimeout(() => {
        // Success message
        alert('Funeral notice created successfully! Redirecting to home page...');

        // Reset button
        submitBtn.innerHTML = originalText;
        submitBtn.disabled = false;

        // Redirect to index page
        window.location.href = 'index.php';
    }, 2000);
}

// Initialize the create page
document.addEventListener('DOMContentLoaded', function () {
    // Shared components are loaded by common.js via loadComponent in the HTML

    // Set default dates for the form
    const today = new Date().toISOString().split('T')[0];
    document.querySelectorAll('input[type="date"]').forEach(input => {
        input.value = today;
    });

    // Add subtle animation to cards on load
    const cards = document.querySelectorAll('.create-memorial-card');
    cards.forEach((card, index) => {
        card.style.animationDelay = `${index * 0.1}s`;
        card.style.animation = 'fadeIn 0.6s ease-out forwards';
    });

    // Handle Create Funeral Notice Button in navbar (link to self or logic)
    document.getElementById('createFuneralNoticeBtn')?.addEventListener('click', function () {
        window.scrollTo({ top: 0, behavior: 'smooth' });
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

    // Highlight active menu item in mobile menu
    const currentPath = window.location.pathname;
    const menuLinks = document.querySelectorAll('.mobile-menu-nav a');
    menuLinks.forEach(link => {
        link.classList.remove('active');
        if (link.getAttribute('href') === 'create.php' || currentPath.includes('create')) {
            if (link.getAttribute('href').includes('create')) {
                link.classList.add('active');
            }
        }
    });
});
