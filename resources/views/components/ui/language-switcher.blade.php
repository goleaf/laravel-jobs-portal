{{-- Universal Language Switcher Component --}}
@props([
    'type' => 'dropdown', // dropdown, select, flags
    'showFlags' => true,
    'showNames' => true,
    'position' => 'right', // left, right, center
    'size' => 'md' // sm, md, lg
])

@php
    $languages = [
        'en' => ['name' => 'English', 'flag' => '🇺🇸', 'native' => 'English'],
        'ar' => ['name' => 'Arabic', 'flag' => '🇸🇦', 'native' => 'العربية'],
        'de' => ['name' => 'German', 'flag' => '🇩🇪', 'native' => 'Deutsch'],
        'es' => ['name' => 'Spanish', 'flag' => '🇪🇸', 'native' => 'Español'],
        'fr' => ['name' => 'French', 'flag' => '🇫🇷', 'native' => 'Français'],
        'pt' => ['name' => 'Portuguese', 'flag' => '🇵🇹', 'native' => 'Português'],
        'ru' => ['name' => 'Russian', 'flag' => '🇷🇺', 'native' => 'Русский'],
        'tr' => ['name' => 'Turkish', 'flag' => '🇹🇷', 'native' => 'Türkçe'],
        'zh' => ['name' => 'Chinese', 'flag' => '🇨🇳', 'native' => '中文']
    ];
    
    $sizeClasses = [
        'sm' => 'text-sm px-2 py-1',
        'md' => 'text-sm px-3 py-2', 
        'lg' => 'text-base px-4 py-3'
    ];
    
    $positionClasses = [
        'left' => 'left-0',
        'right' => 'right-0',
        'center' => 'left-1/2 transform -translate-x-1/2'
    ];
@endphp

@if($type === 'dropdown')
    {{-- Dropdown Language Switcher --}}
    <div class="dropdown relative inline-block" data-language-dropdown>
        <button 
            type="button"
            data-dropdown-toggle="language-menu"
            class="inline-flex items-center justify-center {{ $sizeClasses[$size] }} bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-md text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2 transition-all duration-200"
            aria-label="Select language"
            title="Change language"
        >
            {{-- Current Language Flag --}}
            @if($showFlags)
                <span class="language-flag text-lg mr-2" data-current-flag>🇺🇸</span>
            @endif
            
            {{-- Current Language Name --}}
            @if($showNames)
                <span class="language-name font-medium" data-current-name>English</span>
            @endif
            
            {{-- Dropdown Arrow --}}
            <svg class="w-4 h-4 ml-2 transition-transform duration-200" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd"></path>
            </svg>
        </button>

        {{-- Dropdown Menu --}}
        <div 
            id="language-menu"
            class="dropdown-menu absolute {{ $positionClasses[$position] }} mt-2 w-56 origin-top-right bg-white dark:bg-gray-800 rounded-md shadow-lg border border-gray-200 dark:border-gray-700 py-1 z-50 hidden"
        >
            @foreach($languages as $code => $language)
                <button
                    type="button"
                    data-language-option="{{ $code }}"
                    class="dropdown-item w-full text-left px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors duration-150 flex items-center"
                    data-language-code="{{ $code }}"
                    data-language-flag="{{ $language['flag'] }}"
                    data-language-name="{{ $language['name'] }}"
                    data-language-native="{{ $language['native'] }}"
                >
                    @if($showFlags)
                        <span class="text-lg mr-3">{{ $language['flag'] }}</span>
                    @endif
                    
                    <div class="flex-1">
                        @if($showNames)
                            <div class="font-medium">{{ $language['native'] }}</div>
                            <div class="text-xs text-gray-500 dark:text-gray-400">{{ $language['name'] }}</div>
                        @else
                            <div class="font-medium">{{ $language['native'] }}</div>
                        @endif
                    </div>
                    
                    {{-- Active Indicator --}}
                    <svg class="w-4 h-4 text-primary-600 dark:text-primary-400 opacity-0 transition-opacity duration-200" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                    </svg>
                </button>
            @endforeach
        </div>
    </div>

@elseif($type === 'select')
    {{-- Select Language Switcher --}}
    <div class="relative">
        <select 
            data-language-switcher
            class="form-select {{ $sizeClasses[$size] }} bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-md text-gray-700 dark:text-gray-300 focus:ring-primary-500 focus:border-primary-500 transition-colors duration-200"
            aria-label="Select language"
        >
            @foreach($languages as $code => $language)
                <option value="{{ $code }}" data-flag="{{ $language['flag'] }}">
                    @if($showFlags && $showNames)
                        {{ $language['flag'] }} {{ $language['native'] }}
                    @elseif($showFlags)
                        {{ $language['flag'] }}
                    @else
                        {{ $language['native'] }}
                    @endif
                </option>
            @endforeach
        </select>
    </div>

@elseif($type === 'flags')
    {{-- Flag-only Language Switcher --}}
    <div class="flex items-center space-x-2" data-language-flags>
        @foreach($languages as $code => $language)
            <button
                type="button"
                data-language-option="{{ $code }}"
                class="language-flag-rounded-md px-4 py-2 text-sm font-semibold focus:outline-none w-8 h-8 rounded-full flex items-center justify-center text-lg hover:bg-gray-100 dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2 transition-all duration-200"
                title="{{ $language['native'] }} ({{ $language['name'] }})"
                aria-label="Switch to {{ $language['name'] }}"
                data-language-code="{{ $code }}"
                data-language-flag="{{ $language['flag'] }}"
                data-language-name="{{ $language['name'] }}"
                data-language-native="{{ $language['native'] }}"
            >
                {{ $language['flag'] }}
            </button>
        @endforeach
    </div>
@endif

{{-- Language Switcher Styles --}}
<style>
/* Language switcher specific styles */
.language-switcher-loading {
    opacity: 0.6;
    pointer-events: none;
}

.language-switcher-loading::after {
    content: '';
    position: absolute;
    top: 50%;
    left: 50%;
    width: 16px;
    height: 16px;
    margin: -8px 0 0 -8px;
    border: 2px solid transparent;
    border-top: 2px solid currentColor;
    border-radius: 50%;
    animation: spin 1s linear infinite;
}

@keyframes spin {
    0% { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
}

/* Active language indicator */
[data-language-option].active {
    background-color: rgba(59, 130, 246, 0.1);
    color: rgb(59, 130, 246);
}

.dark [data-language-option].active {
    background-color: rgba(59, 130, 246, 0.2);
    color: rgb(147, 197, 253);
}

[data-language-option].active svg {
    opacity: 1;
}

/* RTL support */
.rtl .dropdown-menu {
    left: 0;
    right: auto;
}

.rtl .dropdown-menu.right-0 {
    left: 0;
    right: auto;
}

.rtl .language-flag {
    margin-left: 0.5rem;
    margin-right: 0;
}

.rtl .dropdown-item svg {
    margin-left: 0;
    margin-right: 0.75rem;
}

/* Flag hover effects */
.language-flag-btn:hover {
    transform: scale(1.1);
}

.language-flag-btn.active {
    background-color: rgba(59, 130, 246, 0.1);
    transform: scale(1.1);
}

/* Dropdown animation */
.dropdown-menu.active {
    animation: slideDown 0.2s ease-out;
}

@keyframes slideDown {
    from {
        opacity: 0;
        transform: translateY(-10px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

/* Mobile responsive */
@media (max-width: 640px) {
    .dropdown-menu {
        width: 100vw;
        max-width: 280px;
        left: 50%;
        transform: translateX(-50%);
        right: auto;
    }
    
    .rtl .dropdown-menu {
        left: 50%;
        transform: translateX(-50%);
        right: auto;
    }
}
</style>

{{-- Language Switcher JavaScript --}}
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Initialize language switcher state
    function updateLanguageSwitcherState() {
        if (!window.UniversalI18n) return;
        
        const currentLocale = window.UniversalI18n.getCurrentLocale();
        
        // Update dropdown current language display
        const currentFlag = document.querySelector('[data-current-flag]');
        const currentName = document.querySelector('[data-current-name]');
        
        document.querySelectorAll('[data-language-option]').forEach(option => {
            const code = option.getAttribute('data-language-option');
            const isActive = code === currentLocale;
            
            option.classList.toggle('active', isActive);
            
            if (isActive && currentFlag && currentName) {
                currentFlag.textContent = option.getAttribute('data-language-flag');
                currentName.textContent = option.getAttribute('data-language-native');
            }
        });
        
        // Update select value
        document.querySelectorAll('[data-language-switcher]').forEach(select => {
            select.value = currentLocale;
        });
        
        // Update flag buttons
        document.querySelectorAll('.language-flag-btn').forEach(btn => {
            const code = btn.getAttribute('data-language-option');
            btn.classList.toggle('active', code === currentLocale);
        });
    }
    
    // Handle language change events
    window.addEventListener('language-changed', function(e) {
        updateLanguageSwitcherState();
        
        // Show loading state briefly
        document.querySelectorAll('[data-language-dropdown]').forEach(dropdown => {
            dropdown.classList.add('language-switcher-loading');
            setTimeout(() => {
                dropdown.classList.remove('language-switcher-loading');
            }, 300);
        });
    });
    
    // Initialize state when UniversalI18n is ready
    if (window.UniversalI18n) {
        updateLanguageSwitcherState();
    } else {
        // Wait for UniversalI18n to be initialized
        const checkI18n = setInterval(() => {
            if (window.UniversalI18n) {
                updateLanguageSwitcherState();
                clearInterval(checkI18n);
            }
        }, 100);
    }
});
</script> 