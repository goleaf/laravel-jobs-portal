@props(['id' => 'theme-switch'])

<!-- Theme Switch Toggle Button -->
<div x-data="{ darkMode: localStorage.getItem('darkMode') === 'true' }" 
    x-init="$watch('darkMode', val => { 
        localStorage.setItem('darkMode', val); 
        if (val) {
            document.documentElement.classList.add('dark');
        } else {
            document.documentElement.classList.remove('dark');
        }
    })" 
    class="flex items-center">
    <button @click="darkMode = !darkMode" 
        class="rounded-full p-1 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary">
        <span x-show="!darkMode" class="text-yellow-500">
            <x-icons.sun class="w-5 h-5" />
        </span>
        <span x-show="darkMode" class="text-gray-300">
            <x-icons.moon class="w-5 h-5" />
        </span>
    </button>
</div>

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