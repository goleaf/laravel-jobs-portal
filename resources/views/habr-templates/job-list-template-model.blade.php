<div class="job-list-container">
    <!-- Header -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 mb-6">
        <div class="flex items-start justify-between mb-4">
            <div>
                <h1 class="text-2xl font-bold text-gray-900 mb-2">{{ $title }}</h1>
                @if($description)
                    <p class="text-gray-600">{{ $description }}</p>
                @endif
            </div>
            
            <!-- View Type Toggle -->
            <div class="flex items-center space-x-2">
                <button class="p-2 rounded-md {{ $viewType === 'list' ? 'bg-blue-100 text-blue-700' : 'text-gray-400 hover:text-gray-600' }}" 
                        onclick="toggleViewType('list')">
                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M3 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zM3 10a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zM3 16a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1z"/>
                    </svg>
                </button>
                <button class="p-2 rounded-md {{ $viewType === 'grid' ? 'bg-blue-100 text-blue-700' : 'text-gray-400 hover:text-gray-600' }}" 
                        onclick="toggleViewType('grid')">
                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M5 3a2 2 0 00-2 2v2a2 2 0 002 2h2a2 2 0 002-2V5a2 2 0 00-2-2H5zM5 11a2 2 0 00-2 2v2a2 2 0 002 2h2a2 2 0 002-2v-2a2 2 0 00-2-2H5zM11 5a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V5zM11 13a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/>
                    </svg>
                </button>
            </div>
        </div>

        <!-- Statistics -->
        <div class="grid grid-cols-2 md:grid-cols-6 gap-4 mb-4">
            @foreach($statistics() as $key => $value)
                <div class="text-center">
                    <div class="text-lg font-semibold text-gray-900">{{ $formatNumber($value) }}</div>
                    <div class="text-xs text-gray-500 capitalize">{{ str_replace('_', ' ', $key) }}</div>
                </div>
            @endforeach
        </div>

        <!-- Filters Summary -->
        @if(!empty($filters))
            <div class="flex items-center justify-between">
                <div class="text-sm text-gray-600">
                    <span class="font-medium">{{ __('jobs.filters') }}:</span> {{ $filterSummary() }}
                </div>
                
                <button class="text-sm text-blue-600 hover:text-blue-800 transition-colors" 
                        onclick="clearFilters()">
                    {{ __('jobs.clear_filters') }}
                </button>
            </div>
        @endif
    </div>

    <!-- Featured Jobs Section -->
    @if(!empty($featuredJobs()))
        <div class="mb-8">
            <h2 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                ⭐ {{ __('jobs.featured_jobs') }}
                <span class="ml-2 inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                    {{ count($featuredJobs()) }}
                </span>
            </h2>
            
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-6">
                @foreach($featuredJobs() as $job)
                    {!! $job !!}
                @endforeach
            </div>
        </div>
    @endif

    <!-- Urgent Jobs Section -->
    @if(!empty($urgentJobs()) && count($urgentJobs()) !== count($featuredJobs()))
        <div class="mb-8">
            <h2 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                🚨 {{ __('jobs.urgent_positions') }}
                <span class="ml-2 inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-red-100 text-red-800">
                    {{ count($urgentJobs()) }}
                </span>
            </h2>
            
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-6">
                @foreach(array_slice($urgentJobs(), 0, 6) as $job)
                    @if(!$job->isFeatured)
                        {!! $job !!}
                    @endif
                @endforeach
            </div>
        </div>
    @endif

    <!-- All Jobs Section -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
        <div class="flex items-center justify-between mb-6">
            <h2 class="text-lg font-semibold text-gray-900">
                {{ __('jobs.all_jobs') }} ({{ $formatNumber($totalCount) }})
            </h2>
            
            <!-- Sort Options -->
            <div class="flex items-center space-x-2">
                <label class="text-sm text-gray-600">{{ __('jobs.sort_by') }}:</label>
                <select class="border border-gray-300 rounded-md px-3 py-1 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
                        onchange="sortJobs(this.value)">
                    <option value="created_at" {{ $sortBy === 'created_at' ? 'selected' : '' }}>{{ __('jobs.sort_newest') }}</option>
                    <option value="deadline" {{ $sortBy === 'deadline' ? 'selected' : '' }}>{{ __('jobs.sort_deadline') }}</option>
                    <option value="salary_from" {{ $sortBy === 'salary_from' ? 'selected' : '' }}>{{ __('jobs.sort_salary') }}</option>
                    <option value="title" {{ $sortBy === 'title' ? 'selected' : '' }}>{{ __('jobs.sort_title') }}</option>
                </select>
            </div>
        </div>

        <!-- Jobs Grid/List -->
        <div class="{{ $viewTypeClass() }}" id="jobs-container">
            @if(!empty($jobs))
                @foreach($jobs as $job)
                    {!! $job !!}
                @endforeach
            @else
                <div class="text-center py-12">
                    <svg class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 48 48">
                        <path d="M34 40h10v-4a6 6 0 00-10.712-3.714M34 40H14m20 0v-4a9.971 9.971 0 00-.712-3.714M14 40H4v-4a6 6 0 0110.713-3.714M14 40v-4c0-1.313.253-2.566.713-3.714m0 0A10.003 10.003 0 0124 26c4.21 0 7.813 2.602 9.288 6.286M30 14a6 6 0 11-12 0 6 6 0 0112 0zm12 6a4 4 0 11-8 0 4 4 0 018 0zm-28 0a4 4 0 11-8 0 4 4 0 018 0z" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                    <h3 class="mt-2 text-sm font-medium text-gray-900">{{ __('jobs.no_jobs_found') }}</h3>
                    <p class="mt-1 text-sm text-gray-500">{{ __('jobs.try_adjusting_filters') }}</p>
                </div>
            @endif
        </div>

        <!-- Pagination -->
        @if($showPagination && !empty($pagination()))
            <div class="mt-8 flex items-center justify-between border-t border-gray-200 pt-6">
                <div class="flex-1 flex justify-between sm:hidden">
                    @if($pagination()['has_previous'])
                        <a href="?page={{ $pagination()['previous_page'] }}" 
                           class="relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                            {{ __('pagination.previous') }}
                        </a>
                    @endif
                    
                    @if($pagination()['has_next'])
                        <a href="?page={{ $pagination()['next_page'] }}" 
                           class="ml-3 relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                            {{ __('pagination.next') }}
                        </a>
                    @endif
                </div>
                
                <div class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between">
                    <div>
                        <p class="text-sm text-gray-700">
                            {{ __('pagination.showing') }} 
                            <span class="font-medium">{{ ($pagination()['current_page'] - 1) * $pagination()['per_page'] + 1 }}</span>
                            {{ __('pagination.to') }} 
                            <span class="font-medium">{{ min($pagination()['current_page'] * $pagination()['per_page'], $pagination()['total']) }}</span>
                            {{ __('pagination.of') }} 
                            <span class="font-medium">{{ $formatNumber($pagination()['total']) }}</span>
                            {{ __('pagination.results') }}
                        </p>
                    </div>
                    
                    <div>
                        <nav class="relative z-0 inline-flex rounded-md shadow-sm -space-x-px">
                            @if($pagination()['has_previous'])
                                <a href="?page={{ $pagination()['previous_page'] }}" 
                                   class="relative inline-flex items-center px-2 py-2 rounded-l-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50">
                                    <span class="sr-only">{{ __('pagination.previous') }}</span>
                                    <svg class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                    </svg>
                                </a>
                            @endif
                            
                            <span class="relative inline-flex items-center px-4 py-2 border border-gray-300 bg-white text-sm font-medium text-gray-700">
                                {{ __('pagination.page') }} {{ $pagination()['current_page'] }} {{ __('pagination.of') }} {{ $pagination()['total_pages'] }}
                            </span>
                            
                            @if($pagination()['has_next'])
                                <a href="?page={{ $pagination()['next_page'] }}" 
                                   class="relative inline-flex items-center px-2 py-2 rounded-r-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50">
                                    <span class="sr-only">{{ __('pagination.next') }}</span>
                                    <svg class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"/>
                                    </svg>
                                </a>
                            @endif
                        </nav>
                    </div>
                </div>
            </div>
        @endif
    </div>
</div>

<script>
function toggleViewType(type) {
    const container = document.getElementById('jobs-container');
    if (type === 'grid') {
        container.className = 'grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6';
    } else {
        container.className = 'space-y-4';
    }
    
    // Update active button states
    document.querySelectorAll('button[onclick^="toggleViewType"]').forEach(btn => {
        btn.className = btn.className.replace(/bg-blue-100 text-blue-700|text-gray-400 hover:text-gray-600/g, '');
        if (btn.onclick.toString().includes(type)) {
            btn.className += ' bg-blue-100 text-blue-700';
        } else {
            btn.className += ' text-gray-400 hover:text-gray-600';
        }
    });
}

function sortJobs(sortBy) {
    const url = new URL(window.location);
    url.searchParams.set('sort', sortBy);
    window.location.href = url.toString();
}

function clearFilters() {
    const url = new URL(window.location);
    ['category', 'location', 'salary_range', 'experience'].forEach(param => {
        url.searchParams.delete(param);
    });
    window.location.href = url.toString();
}
</script> 