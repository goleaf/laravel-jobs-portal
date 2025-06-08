{{-- Context7 Language Switcher Component --}}
@props([
    'type' => 'dropdown', // dropdown, select, flags, compact
    'position' => 'bottom-right',
    'showFlags' => true,
    'showNative' => true,
    'size' => 'medium', // small, medium, large
    'variant' => 'default' // default, minimal, outline
])

@php
    $currentLocale = app()->getLocale();
    $availableLocales = config('app.available_locales', []);
    $currentConfig = $availableLocales[$currentLocale] ?? [];
    
    // Size classes
    $sizeClasses = [
        'small' => 'text-xs px-2 py-1',
        'medium' => 'text-sm px-3 py-2',
        'large' => 'text-base px-4 py-3'
    ];
    
    // Variant classes
    $variantClasses = [
        'default' => 'bg-white border border-gray-300 shadow-sm hover:bg-gray-50',
        'minimal' => 'bg-transparent border-0 hover:bg-gray-100',
        'outline' => 'bg-transparent border border-gray-300 hover:border-gray-400'
    ];
@endphp

<div class="relative inline-block text-left" x-data="languageSwitcher()" x-init="init()">
    @if($type === 'dropdown')
        {{-- Dropdown Language Switcher --}}
        <div>
            <button 
                type="button" 
                class="inline-flex items-center justify-center w-full rounded-md {{ $sizeClasses[$size] }} {{ $variantClasses[$variant] }} font-medium text-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-colors duration-200"
                @click="toggle()"
                :aria-expanded="open"
                aria-haspopup="true"
            >
                @if($showFlags)
                    <span class="mr-2 text-lg">{{ $this->getFlag($currentLocale) }}</span>
                @endif
                
                @if($showNative)
                    <span class="mr-1">{{ $currentConfig['native'] ?? $currentLocale }}</span>
                @else
                    <span class="mr-1">{{ $currentConfig['name'] ?? $currentLocale }}</span>
                @endif
                
                <svg class="ml-2 -mr-1 h-5 w-5 transition-transform duration-200" :class="{ 'transform rotate-180': open }" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                </svg>
            </button>
        </div>

        {{-- Dropdown Menu --}}
        <div 
            class="absolute z-50 mt-2 w-56 rounded-md shadow-lg bg-white ring-1 ring-black ring-opacity-5 focus:outline-none {{ $position === 'bottom-left' ? 'left-0' : 'right-0' }}"
            x-show="open"
            x-transition:enter="transition ease-out duration-100"
            x-transition:enter-start="transform opacity-0 scale-95"
            x-transition:enter-end="transform opacity-100 scale-100"
            x-transition:leave="transition ease-in duration-75"
            x-transition:leave-start="transform opacity-100 scale-100"
            x-transition:leave-end="transform opacity-0 scale-95"
            @click.away="open = false"
            x-cloak
        >
            <div class="py-1" role="menu">
                <div class="px-4 py-2 text-xs font-semibold text-gray-500 uppercase tracking-wider border-b border-gray-100">
                    {{ __('locale.choose_language') }}
                </div>
                
                @foreach($availableLocales as $locale => $config)
                    <button
                        type="button"
                        class="group flex items-center w-full px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 hover:text-gray-900 transition-colors duration-150 {{ $locale === $currentLocale ? 'bg-indigo-50 text-indigo-700' : '' }}"
                        role="menuitem"
                        @click="switchLanguage('{{ $locale }}')"
                        :disabled="loading"
                    >
                        @if($showFlags)
                            <span class="mr-3 text-lg">{{ $this->getFlag($locale) }}</span>
                        @endif
                        
                        <div class="flex-1 text-left">
                            <div class="font-medium">
                                @if($showNative)
                                    {{ $config['native'] ?? $locale }}
                                @else
                                    {{ $config['name'] ?? $locale }}
                                @endif
                            </div>
                            @if($showNative && isset($config['name']))
                                <div class="text-xs text-gray-500">{{ $config['name'] }}</div>
                            @endif
                        </div>
                        
                        @if($locale === $currentLocale)
                            <svg class="ml-2 h-4 w-4 text-indigo-600" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                            </svg>
                        @endif
                    </button>
                @endforeach
            </div>
        </div>

    @elseif($type === 'select')
        {{-- Select Language Switcher --}}
        <div class="relative">
            <select 
                class="block w-full pl-3 pr-10 py-2 text-base border border-gray-300 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 rounded-md {{ $sizeClasses[$size] }}"
                @change="switchLanguage($event.target.value)"
                :disabled="loading"
            >
                @foreach($availableLocales as $locale => $config)
                    <option value="{{ $locale }}" {{ $locale === $currentLocale ? 'selected' : '' }}>
                        @if($showFlags){{ $this->getFlag($locale) }} @endif
                        {{ $showNative ? ($config['native'] ?? $locale) : ($config['name'] ?? $locale) }}
                    </option>
                @endforeach
            </select>
        </div>

    @elseif($type === 'flags')
        {{-- Flag-only Language Switcher --}}
        <div class="flex items-center space-x-2">
            @foreach($availableLocales as $locale => $config)
                <button
                    type="button"
                    class="flex items-center justify-center w-8 h-8 rounded-full border-2 transition-all duration-200 {{ $locale === $currentLocale ? 'border-indigo-500 bg-indigo-50' : 'border-gray-200 hover:border-gray-300' }}"
                    @click="switchLanguage('{{ $locale }}')"
                    :disabled="loading"
                    title="{{ $config['native'] ?? $locale }}"
                >
                    <span class="text-lg">{{ $this->getFlag($locale) }}</span>
                </button>
            @endforeach
        </div>

    @elseif($type === 'compact')
        {{-- Compact Language Switcher --}}
        <div class="relative" x-data="{ open: false }">
            <button 
                type="button"
                class="flex items-center {{ $sizeClasses[$size] }} {{ $variantClasses[$variant] }} rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500"
                @click="open = !open"
            >
                <span class="text-lg">{{ $this->getFlag($currentLocale) }}</span>
                <span class="ml-1 text-xs font-medium uppercase">{{ $currentLocale }}</span>
            </button>
            
            <div 
                class="absolute top-full left-0 mt-1 bg-white border border-gray-200 rounded-md shadow-lg z-50 min-w-max"
                x-show="open"
                @click.away="open = false"
                x-cloak
            >
                @foreach($availableLocales as $locale => $config)
                    @if($locale !== $currentLocale)
                        <button
                            type="button"
                            class="flex items-center w-full px-3 py-2 text-sm hover:bg-gray-50 first:rounded-t-md last:rounded-b-md"
                            @click="switchLanguage('{{ $locale }}'); open = false"
                        >
                            <span class="mr-2 text-lg">{{ $this->getFlag($locale) }}</span>
                            <span class="font-medium uppercase">{{ $locale }}</span>
                        </button>
                    @endif
                @endforeach
            </div>
        </div>
    @endif

    {{-- Loading State --}}
    <div 
        class="absolute inset-0 flex items-center justify-center bg-white bg-opacity-75 rounded-md"
        x-show="loading"
        x-cloak
    >
        <svg class="animate-spin h-5 w-5 text-indigo-600" fill="none" viewBox="0 0 24 24">
            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
        </svg>
    </div>
</div>

@push('scripts')
<script>
function languageSwitcher() {
    return {
        open: false,
        loading: false,
        
        init() {
            // Initialize language switcher
            console.log('🌍 Language Switcher initialized');
        },
        
        toggle() {
            this.open = !this.open;
        },
        
        async switchLanguage(locale) {
            if (this.loading) return;
            
            this.loading = true;
            this.open = false;
            
            try {
                // Show loading state
                const response = await fetch('/locale/switch', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({ locale: locale })
                });
                
                const data = await response.json();
                
                if (data.success) {
                    // Show success message if available
                    if (window.showToast) {
                        window.showToast(data.message, 'success');
                    }
                    
                    // Reload page to apply language changes
                    setTimeout(() => {
                        window.location.reload();
                    }, 500);
                } else {
                    throw new Error(data.message || 'Failed to switch language');
                }
                
            } catch (error) {
                console.error('Language switch error:', error);
                
                if (window.showToast) {
                    window.showToast('Failed to switch language', 'error');
                }
                
                this.loading = false;
            }
        }
    }
}
</script>
@endpush

@php
function getFlag($locale) {
    $flags = [
        'en' => '🇺🇸',
        'ar' => '🇸🇦',
        'de' => '🇩🇪',
        'es' => '🇪🇸',
        'fr' => '🇫🇷',
        'pt' => '🇵🇹',
        'ru' => '🇷🇺',
        'tr' => '🇹🇷',
        'zh' => '🇨🇳'
    ];
    
    return $flags[$locale] ?? '🌐';
}
@endphp 