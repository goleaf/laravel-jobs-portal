// Main entry point for JavaScript
// Global UI behaviors for layout and basic interactions
document.addEventListener('DOMContentLoaded', () => {
  const sidebar = document.getElementById('sidebar');
  const overlay = document.getElementById('mobile-menu-overlay');
  const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

  // Delegate clicks for actions
  document.body.addEventListener('click', (event) => {
    const target = event.target.closest('[data-action]');
    if (!target) return;

    const action = target.getAttribute('data-action');
    switch (action) {
      case 'toggle-mobile-menu':
        if (sidebar && overlay) {
          sidebar.classList.toggle('-translate-x-full');
          overlay.classList.toggle('hidden');
        }
        break;
      case 'toggle-theme': {
        const html = document.documentElement;
        const isDark = html.classList.contains('dark');
        const newTheme = isDark ? 'light' : 'dark';
        html.classList.toggle('dark');
        fetch('/locale/switch', {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': csrfToken,
          },
          body: JSON.stringify({ theme: newTheme }),
        }).catch(() => {});
        break;
      }
      case 'close-toast':
        const toast = target.closest('#success-toast, #error-toast');
        if (toast) toast.style.display = 'none';
        break;
      // Companies page
      case 'companies-update-view': {
        const view = target.getAttribute('data-view') || 'list';
        const url = new URL(window.location.href);
        url.searchParams.set('view', view);
        window.location.href = url.toString();
        break;
      }
      case 'companies-toggle-mobile-filters': {
        const modal = document.getElementById('mobile-company-filters-modal');
        if (modal) modal.classList.toggle('hidden');
        break;
      }
      case 'companies-clear-filters': {
        const url = new URL(window.location.href);
        ['sort','view','industry','company_size','location'].forEach(p => url.searchParams.delete(p));
        window.location.href = url.toString();
        break;
      }
      // Jobs page
      case 'jobs-update-view': {
        const view = target.getAttribute('data-view') || 'list';
        const url = new URL(window.location.href);
        url.searchParams.set('view', view);
        window.location.href = url.toString();
        break;
      }
      case 'jobs-toggle-mobile-filters': {
        const modal = document.getElementById('mobile-filters-modal');
        if (modal) modal.classList.toggle('hidden');
        break;
      }
      case 'jobs-clear-filters': {
        const url = new URL(window.location.href);
        ['sort','view','category','location','company'].forEach(p => url.searchParams.delete(p));
        window.location.href = url.toString();
        break;
      }
      default:
        break;
    }
  });

  // Auto-hide flash messages after 5 seconds
  setTimeout(() => {
    const toast = document.getElementById('success-toast') || document.getElementById('error-toast');
    if (toast) toast.style.display = 'none';
  }, 5000);

  // Hide initial loader once page is loaded
  window.addEventListener('load', () => {
    const loader = document.getElementById('initial-loader');
    if (loader) loader.style.display = 'none';
  });

  // Auto-submit selects/checkboxes with data-auto-submit
  document.body.addEventListener('change', (e) => {
    const el = e.target;
    if (el && el.hasAttribute('data-auto-submit')) {
      const form = el.closest('form');
      if (form) form.submit();
    }
  });
  // Register service worker if available
  if ('serviceWorker' in navigator) {
    navigator.serviceWorker.register('/sw.js').catch(() => {});
  }
});

// Keep page-specific enhancements separate
import './home.js';
// Page-specific modules (centralized imports)
import './pages/messaging/index.js';
import './pages/errors/404.js';
import './pages/errors/500.js';
import './pages/maintenance.js';
import './pages/search/advanced-search.js';
import './pages/admin/analytics.js';
import './pages/employer/analytics.js';

// Initialize global app functionalities
console.log('App JS loaded'); 