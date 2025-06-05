document.addEventListener('DOMContentLoaded', function() {
// Component-specific JavaScript
document.addEventListener('DOMContentLoaded', function() {
        // Check for user's preferred theme in localStorage or system preference
        const themeSwitch = document.getElementById('theme-switch');
        
        // Check if the user's preference is stored in localStorage
        if (localStorage.theme === 'dark' || (!('theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
            document.documentElement.classList.add('dark');
        } else {
            document.documentElement.classList.remove('dark');
        }
        
        // Handle theme switch button click
        themeSwitch.addEventListener('click', function() {
            if (document.documentElement.classList.contains('dark')) {
                document.documentElement.classList.remove('dark');
                localStorage.theme = 'light';
            } else {
                document.documentElement.classList.add('dark');
                localStorage.theme = 'dark';
            }
        });
    });


});