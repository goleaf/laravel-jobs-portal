<div x-data="{ open: false }" class="relative">
    <button @click="open = !open" class="flex items-center text-sm font-medium text-gray-500 hover:text-gray-700 focus:outline-none">
        <span>{{ config('app.available_locales')[app()->getLocale()] ?? 'English' }}</span>
        <svg class="ml-1 w-5 h-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
            <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
        </svg>
    </button>
    
    <div 
        x-show="open" 
        @click.away="open = false"
        class="absolute right-0 mt-2 py-2 w-48 bg-white rounded-md shadow-xl z-20"
        x-transition:enter="transition ease-out duration-100"
        x-transition:enter-start="transform opacity-0 scale-95"
        x-transition:enter-end="transform opacity-100 scale-100"
        x-transition:leave="transition ease-in duration-75"
        x-transition:leave-start="transform opacity-100 scale-100"
        x-transition:leave-end="transform opacity-0 scale-95"
    >
        @foreach(config('app.available_locales') as $locale => $name)
            <a 
                href="{{ route('language.change', $locale) }}" 
                class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 hover:text-gray-900 {{ app()->getLocale() === $locale ? 'bg-gray-100' : '' }}"
            >
                {{ $name }}
            </a>
        @endforeach
    </div>
</div> 