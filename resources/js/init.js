// App Initialization

// Initialize theme
if (localStorage.theme === 'dark' || (!('theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
    document.documentElement.classList.add('dark');
} else {
    document.documentElement.classList.remove('dark');
}

// Initialize language and locale
// Data will be set from a data attribute in the HTML
window.App = window.App || {};

// Initialize multilingual system
document.addEventListener('DOMContentLoaded', function() {
    if (window.UniversalI18nSystem) {
        window.i18n = new UniversalI18nSystem();
    }
}); 