// 404 page enhancements
document.addEventListener('DOMContentLoaded', () => {
  // Go back button
  document.body.addEventListener('click', (e) => {
    const target = e.target.closest('[data-action="go-back"]');
    if (!target) return;
    e.preventDefault();
    if (window.history.length > 1) window.history.back();
    else window.location.href = '/';
  });
});

