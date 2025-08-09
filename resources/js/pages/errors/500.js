// 500 page scripts
document.addEventListener('DOMContentLoaded', () => {
  const errorIdMeta = document.querySelector('meta[name="error-id"]');
  const errorId = errorIdMeta ? errorIdMeta.getAttribute('content') : null;

  function getSystemInfo() {
    const browser = navigator.userAgent;
    const screenSize = `${window.screen.width}x${window.screen.height}`;
    const url = window.location.href;
    const timestamp = new Date().toISOString();
    const set = (id, val) => { const el = document.getElementById(id); if (el) el.textContent = val; };
    set('browser-info', browser);
    set('screen-info', screenSize);
    set('url-info', url);
    set('timestamp-info', timestamp);
  }

  document.body.addEventListener('click', (e) => {
    const refreshBtn = e.target.closest('[data-action="refresh-page"]');
    if (refreshBtn) {
      e.preventDefault();
      window.location.reload();
      return;
    }
    const reportBtn = e.target.closest('[data-action="report-error"]');
    if (reportBtn) {
      e.preventDefault();
      const modal = document.getElementById('error-report-modal');
      if (modal) modal.style.display = 'block';
    }
    const openSupport = e.target.closest('[data-action="open-support-modal"]');
    if (openSupport) {
      e.preventDefault();
      const modal = document.getElementById('support-modal');
      if (modal) modal.style.display = 'block';
    }
    const closeModal = e.target.closest('[data-action="close-modal"]');
    if (closeModal) {
      e.preventDefault();
      const modalId = closeModal.getAttribute('data-modal-id');
      const modal = modalId ? document.getElementById(modalId) : closeModal.closest('[id$="-modal"]');
      if (modal) modal.style.display = 'none';
    }
  });

  getSystemInfo();
});

