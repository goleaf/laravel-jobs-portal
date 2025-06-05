@php
    $currentLocale = app()->getLocale();
    $currentLanguage = config('app.available_locales')[$currentLocale] ?? config('app.available_locales')['en'];
@endphp

<div x-data="{ open: false }" class="relative">
    <!-- Language Selector Button -->
    <button @click="open = !open" 
            class="flex items-center px-3 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition duration-150 ease-in-out">
        <span class="mr-2">
            {{ $currentLanguage['native'] ?? $currentLanguage['name'] }}
        </span>
        <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
        </svg>
    </button>
    
    <!-- Language Dropdown -->
    <div x-show="open" 
         @click.away="open = false"
         x-transition:enter="transition ease-out duration-100"
         x-transition:enter-start="transform opacity-0 scale-95"
         x-transition:enter-end="transform opacity-100 scale-100"
         x-transition:leave="transition ease-in duration-75"
         x-transition:leave-start="transform opacity-100 scale-100"
         x-transition:leave-end="transform opacity-0 scale-95"
         class="absolute right-0 mt-2 w-56 bg-white rounded-md shadow-lg ring-1 ring-black ring-opacity-5 z-50">
        <div class="py-1" role="menu">
            @foreach(config('app.available_locales') as $locale => $details)
                <a href="{{ route('language.switch', $locale) }}" 
                   class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-indigo-50 hover:text-indigo-900 transition duration-150 ease-in-out {{ app()->getLocale() === $locale ? 'bg-indigo-100 text-indigo-900 font-medium' : '' }}"
                   role="menuitem">
                    <span class="flex-1 {{ isset($details['rtl']) && $details['rtl'] ? 'text-right' : 'text-left' }}">
                        {{ $details['native'] ?? $details['name'] }}
                    </span>
                    @if(app()->getLocale() === $locale)
                        <svg class="w-4 h-4 text-indigo-600 ml-2" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                        </svg>
                    @endif
                </a>
            @endforeach
        </div>
    </div>
</div> 