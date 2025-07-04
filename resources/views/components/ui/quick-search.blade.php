{{-- Quick Search Component --}}
@props([
    'placeholder' => __('Search jobs, companies, or locations...'),
    'categories' => [],
    'locations' => [],
    'showFilters' => true,
    'showCategories' => true,
    'showLocations' => true,
    'action' => route('front.job.listing'),
    'method' => 'GET'
])

<div class="quick-search-container bg-white rounded-lg shadow-lg p-6">
    <form action="{{ $action }}" method="{{ $method }}" class="space-y-4">
        {{-- Main Search Input --}}
        <div class="relative">
            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                <svg-icon name="search" class="h-5 w-5 text-gray-400" />
            </div>
            <input
                type="text"
                name="search"
                id="search"
                class="block w-full pl-10 pr-3 py-3 border border-gray-300 rounded-md leading-5 bg-white placeholder-gray-500 focus:outline-none focus:placeholder-gray-400 focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500 text-sm"
                placeholder="{{ $placeholder }}"
                value="{{ request('search') }}"
            >
        </div>

        @if($showFilters)
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                {{-- Category Filter --}}
                @if($showCategories && count($categories) > 0)
                    <div>
                        <label for="category" class="block text-sm font-medium text-gray-700 mb-1">
                            {{ __('Category') }}
                        </label>
                        <select
                            name="category"
                            id="category"
                            class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 text-sm"
                        >
                            <option value="">{{ __('All Categories') }}</option>
                            @foreach($categories as $category)
                                <option value="{{ $category->id }}" {{ request('category') == $category->id ? 'selected' : '' }}>
                                    {{ $category->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                @endif

                {{-- Location Filter --}}
                @if($showLocations && count($locations) > 0)
                    <div>
                        <label for="location" class="block text-sm font-medium text-gray-700 mb-1">
                            {{ __('Location') }}
                        </label>
                        <select
                            name="location"
                            id="location"
                            class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 text-sm"
                        >
                            <option value="">{{ __('All Locations') }}</option>
                            @foreach($locations as $location)
                                <option value="{{ $location->id }}" {{ request('location') == $location->id ? 'selected' : '' }}>
                                    {{ $location->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                @endif

                {{-- Job Type Filter --}}
                <div>
                    <label for="job_type" class="block text-sm font-medium text-gray-700 mb-1">
                        {{ __('Job Type') }}
                    </label>
                    <select
                        name="job_type"
                        id="job_type"
                        class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 text-sm"
                    >
                        <option value="">{{ __('All Types') }}</option>
                        <option value="full-time" {{ request('job_type') == 'full-time' ? 'selected' : '' }}>{{ __('Full Time') }}</option>
                        <option value="part-time" {{ request('job_type') == 'part-time' ? 'selected' : '' }}>{{ __('Part Time') }}</option>
                        <option value="contract" {{ request('job_type') == 'contract' ? 'selected' : '' }}>{{ __('Contract') }}</option>
                        <option value="freelance" {{ request('job_type') == 'freelance' ? 'selected' : '' }}>{{ __('Freelance') }}</option>
                        <option value="internship" {{ request('job_type') == 'internship' ? 'selected' : '' }}>{{ __('Internship') }}</option>
                    </select>
                </div>
            </div>
        @endif

        {{-- Search Button --}}
        <div class="flex justify-center">
            <button
                type="submit"
                class="inline-flex items-center px-6 py-3 border border-transparent text-base font-medium rounded-md shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition duration-150 ease-in-out"
            >
                <svg-icon name="search" class="w-5 h-5 mr-2" />
                {{ __('Search Jobs') }}
            </button>
        </div>
    </form>

    {{-- Quick Search Suggestions --}}
    <div class="mt-4 pt-4 border-t border-gray-200">
        <div class="flex flex-wrap gap-2">
            <span class="text-sm text-gray-600 mr-2">{{ __('Popular searches:') }}</span>
            <a href="{{ route('front.job.listing', ['search' => 'developer']) }}" class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800 hover:bg-gray-200 transition duration-150 ease-in-out">
                {{ __('Developer') }}
            </a>
            <a href="{{ route('front.job.listing', ['search' => 'designer']) }}" class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800 hover:bg-gray-200 transition duration-150 ease-in-out">
                {{ __('Designer') }}
            </a>
            <a href="{{ route('front.job.listing', ['search' => 'manager']) }}" class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800 hover:bg-gray-200 transition duration-150 ease-in-out">
                {{ __('Manager') }}
            </a>
            <a href="{{ route('front.job.listing', ['search' => 'marketing']) }}" class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800 hover:bg-gray-200 transition duration-150 ease-in-out">
                {{ __('Marketing') }}
            </a>
        </div>
    </div>
</div>

{{-- Enhanced Search JavaScript --}}
<script>
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('search');
    const searchForm = searchInput.closest('form');
    
    // Auto-submit form on Enter key
    searchInput.addEventListener('keypress', function(e) {
        if (e.key === 'Enter') {
            e.preventDefault();
            searchForm.submit();
        }
    });
    
    // Enhanced search with debouncing for future AJAX implementation
    let searchTimeout;
    searchInput.addEventListener('input', function() {
        clearTimeout(searchTimeout);
        searchTimeout = setTimeout(() => {
            // Future: implement live search suggestions
            console.log('Search query:', this.value);
        }, 300);
    });
});
</script>

<style>
.quick-search-container {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    background: white;
    border: 1px solid #e5e7eb;
}

.quick-search-container input:focus,
.quick-search-container select:focus {
    box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.1);
}

.quick-search-container button:hover {
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(99, 102, 241, 0.4);
}
</style> 