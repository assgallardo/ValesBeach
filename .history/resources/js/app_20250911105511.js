import './bootstrap';
import Alpine from 'alpinejs';

// Make Alpine available globally
window.Alpine = Alpine;

// Initialize Alpine.js after DOM is loaded
document.addEventListener('DOMContentLoaded', () => {
    Alpine.start();
});

