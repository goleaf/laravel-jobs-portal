<form action="{{ route('jobs.index') }}" method="GET" {{ $attributes->merge(['class' => 'relative']) }}>
    <div class="flex items-center rounded-full shadow-md bg-white dark:bg-gray-800 focus-within:ring-2 focus-within:ring-blue-500 focus-within:border-blue-500 transition-all duration-200">
        <div class="relative flex-1">
            <x-icon name="search" class="absolute left-4 top-1/2 -translate-y-1/2 h-5 w-5 text-gray-400" />
            <x-input 
                type="search" 
                name="q"
                id="job-search-keyword"
                placeholder="{{ __('jobs.search_keyword_placeholder') }}"
                value="{{ request('q') }}"
                class="w-full pl-12 pr-4 py-3 rounded-full border-none focus:ring-0 dark:bg-gray-800 dark:text-white dark:placeholder-gray-400"
            />
        </div>

        <div class="relative flex-1 border-l border-gray-200 dark:border-gray-700">
            <x-icon name="map-pin" class="absolute left-4 top-1/2 -translate-y-1/2 h-5 w-5 text-gray-400" />
            <x-input 
                type="text" 
                name="location"
                id="job-search-location"
                placeholder="{{ __('jobs.search_location_placeholder') }}"
                value="{{ request('location') }}"
                class="w-full pl-12 pr-4 py-3 rounded-full border-none focus:ring-0 dark:bg-gray-800 dark:text-white dark:placeholder-gray-400"
            />
        </div>

        <button type="submit" class="flex-shrink-0 px-8 py-3 bg-blue-600 text-white font-semibold rounded-full hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition duration-300">
            {{ __('jobs.search') }}
        </button>
    </div>
</form>