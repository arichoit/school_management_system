import Alpine from 'alpinejs';

window.Alpine = Alpine;

// Global helper for toast notifications
window.showToast = function(message, type = 'success') {
    window.dispatchEvent(new CustomEvent('notify', { detail: { message, type } }));
};

// Global CSRF helper for fetch requests
window.csrfToken = function() {
    const meta = document.querySelector('meta[name="csrf-token"]');
    return meta ? meta.getAttribute('content') : '';
};

// Audio / visual subtle click feedback if needed
document.addEventListener('alpine:init', () => {
    // Alpine components can be registered here if needed
});

Alpine.start();
