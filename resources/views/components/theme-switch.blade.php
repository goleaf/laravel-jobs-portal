@props(['id' => 'theme-switch'])

<!-- Theme Switch Toggle Button -->
<button 
    id="theme-switch" 
    class="rounded-md p-2 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-offset-gray-100 focus:ring-blue-500"
>
    <!-- Sun icon (visible in light mode) -->
    <x-icons.sun class="w-5 h-5 transform transition-transform dark:scale-0 dark:opacity-0 scale-100 opacity-100" />
    
    <!-- Moon icon (visible in dark mode) -->
    <x-icons.moon class="w-5 h-5 ml-[-1.25rem] transform transition-transform dark:scale-100 dark:opacity-100 scale-0 opacity-0" />
</button>

@once
<script>
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
</script>
@endonce 