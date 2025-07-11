@extends('layouts.app')

@section('title', __('jobs.page_title'))
@section('description', __('jobs.page_description'))

@section('content')
<!-- Page Header -->
<div class="bg-white dark:bg-gray-800 shadow-sm">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
        <div class="md:flex md:items-center md:justify-between">
            <div class="flex-1 min-w-0">
                <h1 class="text-4xl font-extrabold leading-tight text-gray-900 dark:text-white sm:text-5xl sm:truncate mb-2">
                    {{ __('jobs.all_jobs') }}
                </h1>
                <p class="mt-1 text-lg text-gray-500 dark:text-gray-400 max-w-3xl">
                    {{ trans_choice('jobs.jobs_count', $jobs->total(), ['count' => number_format($jobs->total())]) }}
                </p>
            </div>
            

        </div>
    </div>
</div>

<!-- Search and Filters -->
<div class="bg-gray-50 dark:bg-gray-900 border-b border-gray-200 dark:border-gray-700">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
        <x-jobs.search-filters 
            :categories="$categories"
            :locations="$locations"
            :jobTypes="$jobTypes"
            :salaryRanges="$salaryRanges"
            :filters="$filters"
        />
    </div>
</div>

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="lg:grid lg:grid-cols-4 lg:gap-8">
        <!-- Sidebar Filters -->
        <div class="hidden lg:block lg:col-span-1">
            <div class="sticky top-20">
                <x-jobs.sidebar-filters 
                    :categories="$categories"
                    :locations="$locations"
                    :companies="$companies"
                    :filters="$filters"
                />
            </div>
        </div>

        <!-- Job Listings -->
        <div class="lg:col-span-3">
            <!-- Sort and View Options -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 mb-6">
                <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center space-x-4">
                            <x-ui.select 
                                name="sort" 
                                id="job-sort"
                                class="w-auto px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                            >
                                <option value="created_at_desc" {{ request('sort') === 'created_at_desc' ? 'selected' : '' }}>
                                    {{ __('jobs.sort_newest') }}
                                </option>
                                <option value="created_at_asc" {{ request('sort') === 'created_at_asc' ? 'selected' : '' }}>
                                    {{ __('jobs.sort_oldest') }}
                                </option>
                                <option value="salary_desc" {{ request('sort') === 'salary_desc' ? 'selected' : '' }}>
                                    {{ __('jobs.sort_salary_high') }}
                                </option>
                                <option value="salary_asc" {{ request('sort') === 'salary_asc' ? 'selected' : '' }}>
                                    {{ __('jobs.sort_salary_low') }}
                                </option>
                                <option value="title_asc" {{ request('sort') === 'title_asc' ? 'selected' : '' }}>
                                    {{ __('jobs.sort_title') }}
                                </option>
                            </x-ui.select>

                            <div class="flex items-center space-x-2">
                                <span class="text-sm text-gray-500 dark:text-gray-400">{{ __('jobs.view') }}:</span>
                                <button 
                                    type="button" 
                                    class="p-2 rounded-md transition duration-150 ease-in-out {{ request('view') !== 'grid' ? 'bg-blue-600 text-white shadow-md' : 'text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700' }}"
                                    onclick="window.JobFilters.updateView('list')"
                                    title="{{ __('jobs.list_view') }}"
                                >
                                    <x-icon name="list" class="h-5 w-5" />
                                </button>
                                <button 
                                    type="button" 
                                    class="p-2 rounded-md transition duration-150 ease-in-out {{ request('view') === 'grid' ? 'bg-blue-600 text-white shadow-md' : 'text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700' }}"
                                    onclick="window.JobFilters.updateView('grid')"
                                    title="{{ __('jobs.grid_view') }}"
                                >
                                    <x-icon name="grid" class="h-5 w-5" />
                                </button>
                            </div>
                        </div>

                        <!-- Mobile Filter Toggle -->
                        <button 
                            type="button" 
                            class="lg:hidden inline-flex items-center px-3 py-2 border border-gray-300 dark:border-gray-600 text-sm leading-4 font-medium rounded-md text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-700"
                            onclick="window.JobFilters.toggleMobileFilters()"
                        >
                            <x-icon name="filter" class="h-4 w-4 mr-2" />
                            {{ __('jobs.filters') }}
                        </button>
                    </div>
                </div>
            </div>

            <!-- Active Filters -->
            @if($activeFilters && count($activeFilters) > 0)
                <div class="mb-6">
                    <x-jobs.active-filters :filters="$activeFilters" />
                </div>
            @endif

            <!-- Jobs Grid/List -->
            @if($jobs->count() > 0)
                <div class="{{ request('view') === 'grid' ? 'grid grid-cols-1 md:grid-cols-2 gap-6' : 'space-y-4' }}" id="jobs-container">
                    @foreach($jobs as $job)
                        @if(request('view') === 'grid')
                            <x-jobs.job-card :job="$job" layout="grid" />
                        @else
                            <x-jobs.job-card :job="$job" layout="list" />
                        @endif
                    @endforeach
                </div>

                <!-- Pagination -->
                <div class="mt-8">
                    {{ $jobs->links('components.ui.pagination') }}
                </div>
            @else
                <!-- No Jobs Found -->
                <x-ui.empty-state 
                    icon="briefcase"
                    :title="__('jobs.no_jobs_found')"
                    :description="__('jobs.no_jobs_description')"
                >
                    <x-ui.button 
                        href="{{ route('jobs.index') }}" 
                        variant="primary"
                        onclick="window.JobFilters.clearFilters()"
                    >
                        {{ __('jobs.clear_filters') }}
                    </x-ui.button>
                </x-ui.empty-state>
            @endif
        </div>
    </div>
</div>

<!-- Mobile Filters Modal -->
<x-ui.modal id="mobile-filters-modal" title="{{ __('jobs.filter_jobs') }}">
    <x-jobs.mobile-filters 
        :categories="$categories"
        :locations="$locations"
        :companies="$companies"
        :filters="$filters"
    />
</x-ui.modal>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Initialize job filters
    window.JobFilters?.init({
        baseUrl: '{{ route('jobs.index') }}',
        currentFilters: @json($filters),
        currentSort: '{{ request('sort', 'created_at_desc') }}',
        currentView: '{{ request('view', 'list') }}'
    });

    // Initialize job interactions (save, apply, etc.)
    window.JobInteractions?.init();

    // Track jobs page visit
    if (window.Analytics) {
        window.Analytics.track('jobs_page_view', {
            total_jobs: {{ $jobs->total() }},
            current_page: {{ $jobs->currentPage() }},
            filters: @json($filters)
        });
    }
});
</script>
@endpush
