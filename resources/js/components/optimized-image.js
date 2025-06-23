document.addEventListener('DOMContentLoaded', function() {
// Component-specific JavaScript
document.addEventListener('lazyImageLoaded', function(e) {
            const img = e.detail.img;
            const loadTime = performance.now();
            
            // Add performance indicator
            const indicator = document.createElement('div');
            indicator.className = 'perf-indicator ' + (loadTime < 500 ? 'fast' : loadTime < 1000 ? 'medium' : 'slow');
            indicator.textContent = Math.round(loadTime) + 'ms';
            
            const wrapper = img.closest('.optimized-image-wrapper');
            if (wrapper) {
                wrapper.style.position = 'relative';
                wrapper.appendChild(indicator);
                
                // Remove indicator after 3 seconds
                setTimeout(() => {
                    indicator.remove();
                }, 3000);
            }
        });


});