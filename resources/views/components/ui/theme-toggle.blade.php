{{-- Theme Toggle Component --}}
@props([
    'size' => 'md',
    'showLabels' => true,
    'position' => 'relative'
])

@php
$sizes = [
    'sm' => 'w-8 h-8',
    'md' => 'w-10 h-10', 
    'lg' => 'w-12 h-12'
];

$sizeClass = $sizes[$size] ?? $sizes['md'];
@endphp

<div class="theme-toggle-container {{ $position === 'fixed' ? 'fixed top-4 right-4 z-50' : 'relative' }}" x-data="themeToggle()" x-init="init()">
    <div class="flex items-center space-x-2">
        @if($showLabels)
            <span class="text-sm text-gray-600 dark:text-gray-300" x-show="theme === 'light'">
                {{ __('Light') }}
            </span>
            <span class="text-sm text-gray-600 dark:text-gray-300" x-show="theme === 'dark'">
                {{ __('Dark') }}
            </span>
            <span class="text-sm text-gray-600 dark:text-gray-300" x-show="theme === 'system'">
                {{ __('System') }}
            </span>
        @endif

        {{-- Three-way toggle button --}}
        <div class="relative">
            <button
                type="button"
                @click="toggleTheme()"
                class="{{ $sizeClass }} rounded-lg bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 flex items-center justify-center transition-all duration-200 hover:bg-gray-50 dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 shadow-sm"
                :title="getThemeLabel()"
                aria-label="Toggle theme"
            >
                {{-- Light theme icon --}}
                <svg 
                    x-show="theme === 'light'" 
                    x-transition:enter="transition-opacity duration-200"
                    x-transition:enter-start="opacity-0"
                    x-transition:enter-end="opacity-100"
                    class="w-5 h-5 text-yellow-500" 
                    fill="currentColor" 
                    viewBox="0 0 20 20"
                >
                    <path fill-rule="evenodd" d="M10 2a1 1 0 011 1v1a1 1 0 11-2 0V3a1 1 0 011-1zm4 8a4 4 0 11-8 0 4 4 0 018 0zm-.464 4.95l.707.707a1 1 0 001.414-1.414l-.707-.707a1 1 0 00-1.414 1.414zm2.12-10.607a1 1 0 010 1.414l-.706.707a1 1 0 11-1.414-1.414l.707-.707a1 1 0 011.414 0zM17 11a1 1 0 100-2h-1a1 1 0 100 2h1zm-7 4a1 1 0 011 1v1a1 1 0 11-2 0v-1a1 1 0 011-1zM5.05 6.464A1 1 0 106.465 5.05l-.708-.707a1 1 0 00-1.414 1.414l.707.707zm1.414 8.486l-.707.707a1 1 0 01-1.414-1.414l.707-.707a1 1 0 011.414 1.414zM4 11a1 1 0 100-2H3a1 1 0 000 2h1z" clip-rule="evenodd" />
                </svg>

                {{-- Dark theme icon --}}
                <svg 
                    x-show="theme === 'dark'" 
                    x-transition:enter="transition-opacity duration-200"
                    x-transition:enter-start="opacity-0"
                    x-transition:enter-end="opacity-100"
                    class="w-5 h-5 text-blue-400" 
                    fill="currentColor" 
                    viewBox="0 0 20 20"
                >
                    <path d="M17.293 13.293A8 8 0 016.707 2.707a8.001 8.001 0 1010.586 10.586z" />
                </svg>

                {{-- System theme icon --}}
                <svg 
                    x-show="theme === 'system'" 
                    x-transition:enter="transition-opacity duration-200"
                    x-transition:enter-start="opacity-0"
                    x-transition:enter-end="opacity-100"
                    class="w-5 h-5 text-gray-500" 
                    fill="none" 
                    stroke="currentColor" 
                    viewBox="0 0 24 24"
                >
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                </svg>
            </button>

            {{-- Theme indicator badge --}}
            <div class="absolute -top-1 -right-1 w-3 h-3 rounded-full border-2 border-white dark:border-gray-800" 
                 :class="{
                     'bg-yellow-500': theme === 'light',
                     'bg-blue-500': theme === 'dark', 
                     'bg-gray-500': theme === 'system'
                 }">
            </div>
        </div>

        {{-- Dropdown menu for larger screens --}}
        <div class="relative hidden md:block" x-data="{ open: false }">
            <button
                type="button"
                @click="open = !open"
                @click.away="open = false"
                class="text-sm text-gray-600 dark:text-gray-300 hover:text-gray-900 dark:hover:text-white focus:outline-none"
                aria-label="Select theme"
            >
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                </svg>
            </button>

            <div x-show="open" 
                 x-transition:enter="transition ease-out duration-200"
                 x-transition:enter-start="opacity-0 scale-95"
                 x-transition:enter-end="opacity-100 scale-100"
                 x-transition:leave="transition ease-in duration-75"
                 x-transition:leave-start="opacity-100 scale-100"
                 x-transition:leave-end="opacity-0 scale-95"
                 class="absolute right-0 mt-2 w-36 bg-white dark:bg-gray-800 rounded-md shadow-lg border border-gray-200 dark:border-gray-700 py-1 z-50">
                
                <button @click="setTheme('light'); open = false" 
                        class="flex items-center w-full px-3 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700"
                        :class="{ 'bg-gray-100 dark:bg-gray-700': theme === 'light' }">
                    <svg class="w-4 h-4 mr-2 text-yellow-500" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 2a1 1 0 011 1v1a1 1 0 11-2 0V3a1 1 0 011-1zm4 8a4 4 0 11-8 0 4 4 0 018 0zm-.464 4.95l.707.707a1 1 0 001.414-1.414l-.707-.707a1 1 0 00-1.414 1.414zm2.12-10.607a1 1 0 010 1.414l-.706.707a1 1 0 11-1.414-1.414l.707-.707a1 1 0 011.414 0zM17 11a1 1 0 100-2h-1a1 1 0 100 2h1zm-7 4a1 1 0 011 1v1a1 1 0 11-2 0v-1a1 1 0 011-1zM5.05 6.464A1 1 0 106.465 5.05l-.708-.707a1 1 0 00-1.414 1.414l.707.707zm1.414 8.486l-.707.707a1 1 0 01-1.414-1.414l.707-.707a1 1 0 011.414 1.414zM4 11a1 1 0 100-2H3a1 1 0 000 2h1z" clip-rule="evenodd" />
                    </svg>
                    {{ __('Light') }}
                </button>
                
                <button @click="setTheme('dark'); open = false" 
                        class="flex items-center w-full px-3 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700"
                        :class="{ 'bg-gray-100 dark:bg-gray-700': theme === 'dark' }">
                    <svg class="w-4 h-4 mr-2 text-blue-400" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M17.293 13.293A8 8 0 016.707 2.707a8.001 8.001 0 1010.586 10.586z" />
                    </svg>
                    {{ __('Dark') }}
                </button>
                
                <button @click="setTheme('system'); open = false" 
                        class="flex items-center w-full px-3 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700"
                        :class="{ 'bg-gray-100 dark:bg-gray-700': theme === 'system' }">
                    <svg class="w-4 h-4 mr-2 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                    </svg>
                    {{ __('System') }}
                </button>
            </div>
        </div>
    </div>
</div>

{{-- Alpine.js component --}}
<script>
document.addEventListener('alpine:init', () => {
    Alpine.data('themeToggle', () => ({
        theme: 'system',
        
        init() {
            // Get saved theme or default to system
            this.theme = localStorage.getItem('theme') || 'system';
            this.applyTheme();
            
            // Listen for system theme changes
            if (window.matchMedia) {
                window.matchMedia('(prefers-color-scheme: dark)').addEventListener('change', () => {
                    if (this.theme === 'system') {
                        this.applyTheme();
                    }
                });
            }
        },
        
        toggleTheme() {
            // Cycle through: light -> dark -> system -> light
            const themes = ['light', 'dark', 'system'];
            const currentIndex = themes.indexOf(this.theme);
            const nextIndex = (currentIndex + 1) % themes.length;
            this.setTheme(themes[nextIndex]);
        },
        
        setTheme(newTheme) {
            this.theme = newTheme;
            localStorage.setItem('theme', newTheme);
            this.applyTheme();
            
            // Dispatch custom event for other components
            window.dispatchEvent(new CustomEvent('theme-changed', { 
                detail: { theme: newTheme } 
            }));
        },
        
        applyTheme() {
            const html = document.documentElement;
            
            if (this.theme === 'dark' || 
                (this.theme === 'system' && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
                html.classList.add('dark');
            } else {
                html.classList.remove('dark');
            }
            
            // Update meta theme-color for mobile browsers
            const metaThemeColor = document.querySelector('meta[name="theme-color"]');
            if (metaThemeColor) {
                metaThemeColor.setAttribute('content', 
                    html.classList.contains('dark') ? '#1f2937' : '#ffffff'
                );
            }
        },
        
        getThemeLabel() {
            const labels = {
                'light': '{{ __("Switch to light theme") }}',
                'dark': '{{ __("Switch to dark theme") }}', 
                'system': '{{ __("Switch to system theme") }}'
            };
            return labels[this.theme] || labels['system'];
        }
    }));
});
</script>

{{-- CSS for smooth transitions --}}
<style>
.theme-toggle-container {
    transition: all 0.2s ease-in-out;
}

.theme-toggle-container button {
    transition: all 0.2s ease-in-out;
}

.theme-toggle-container button:hover {
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
}

.theme-toggle-container .dark button:hover {
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.3);
}

/* Theme transition for entire document */
html {
    transition: background-color 0.3s ease, color 0.3s ease;
}

* {
    transition: border-color 0.3s ease, background-color 0.3s ease, color 0.3s ease;
}
</style> 