@props(['id' => 'theme-switch'])

<button {{ $attributes->merge(['id' => $id, 'class' => 'relative inline-flex items-center p-2 rounded-full focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500']) }}>
    <span class="sr-only">Toggle dark mode</span>
    
    <!-- Sun Icon (Light mode) -->
    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-5 h-5 transform transition-transform dark:scale-0 dark:opacity-0 scale-100 opacity-100">
        <path d="M12 2.25a.75.75 0 01.75.75v2.25a.75.75 0 01-1.5 0V3a.75.75 0 01.75-.75zM7.5 12a4.5 4.5 0 119 0 4.5 4.5 0 01-9 0zM18.894 6.166a.75.75 0 00-1.06-1.06l-1.591 1.59a.75.75 0 101.06 1.061l1.591-1.59zM21.75 12a.75.75 0 01-.75.75h-2.25a.75.75 0 010-1.5H21a.75.75 0 01.75.75zM17.834 18.894a.75.75 0 001.06-1.06l-1.59-1.591a.75.75 0 10-1.061 1.06l1.59 1.591zM12 18a.75.75 0 01.75.75V21a.75.75 0 01-1.5 0v-2.25A.75.75 0 0112 18zM7.758 17.303a.75.75 0 00-1.061-1.06l-1.591 1.59a.75.75 0 001.06 1.061l1.591-1.59zM6 12a.75.75 0 01-.75.75H3a.75.75 0 010-1.5h2.25A.75.75 0 016 12zM6.697 7.757a.75.75 0 001.06-1.06l-1.59-1.591a.75.75 0 00-1.061 1.06l1.59 1.591z" />
    </svg>
    
    <!-- Moon Icon (Dark mode) -->
    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-5 h-5 ml-[-1.25rem] transform transition-transform dark:scale-100 dark:opacity-100 scale-0 opacity-0">
        <path fill-rule="evenodd" d="M9.528 1.718a.75.75 0 01.162.819A8.97 8.97 0 009 6a9 9 0 009 9 8.97 8.97 0 003.463-.69.75.75 0 01.981.98 10.503 10.503 0 01-9.694 6.46c-5.799 0-10.5-4.701-10.5-10.5 0-4.368 2.667-8.112 6.46-9.694a.75.75 0 01.818.162z" clip-rule="evenodd" />
    </svg>
</button>

@once
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Check for saved theme preference or use the system preference
        const themeToggleBtn = document.getElementById('{{ $id }}');
        
        if (!themeToggleBtn) return;
        
        function getThemePreference() {
            if (localStorage.theme === 'dark' || (!('theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
                return 'dark';
            }
            return 'light';
        }
        
        function setTheme(theme) {
            if (theme === 'dark') {
                document.documentElement.classList.add('dark');
                localStorage.theme = 'dark';
            } else {
                document.documentElement.classList.remove('dark');
                localStorage.theme = 'light';
            }
        }
        
        // Set the initial theme
        setTheme(getThemePreference());
        
        // Toggle theme when the button is clicked
        themeToggleBtn.addEventListener('click', function() {
            if (localStorage.theme === 'dark') {
                setTheme('light');
            } else {
                setTheme('dark');
            }
        });
    });
</script>
@endonce 