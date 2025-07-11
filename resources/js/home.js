document.addEventListener('DOMContentLoaded', function() {
    // Initialize job search functionality
    window.JobSearch?.init();
    
    // Initialize stats counter animation
    window.StatsCounter?.init();
    
    // Track home page visit
    if (window.Analytics) {
        window.Analytics.track('page_view', {
            page: 'home',
            title: 'Home Page'
        });
    }
}); 