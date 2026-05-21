import './bootstrap';
import Alpine from 'alpinejs';

window.Alpine = Alpine;

// Dark mode
Alpine.store('darkMode', {
    on: localStorage.getItem('darkMode') === 'true',
    toggle() {
        this.on = !this.on;
        localStorage.setItem('darkMode', this.on);
        document.documentElement.classList.toggle('dark', this.on);
    },
    init() {
        document.documentElement.classList.toggle('dark', this.on);
    }
});

// Notifications store
Alpine.store('notifications', {
    count: 0,
    items: [],
    async fetch() {
        try {
            const res = await fetch('/notifications/unread');
            const data = await res.json();
            this.count = data.count;
            this.items = data.notifications;
        } catch (e) {}
    }
});

Alpine.start();

// Initialize dark mode on load
document.addEventListener('DOMContentLoaded', () => {
    const darkMode = localStorage.getItem('darkMode') === 'true';
    document.documentElement.classList.toggle('dark', darkMode);

    // Auto-dismiss alerts
    setTimeout(() => {
        document.querySelectorAll('[data-auto-dismiss]').forEach(el => {
            el.style.transition = 'opacity 0.5s';
            el.style.opacity = '0';
            setTimeout(() => el.remove(), 500);
        });
    }, 4000);
});
