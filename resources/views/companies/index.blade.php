@extends('layouts.app')

@section('title', __('companies.page_title'))
@section('description', __('companies.page_description'))

@section('content')
<!-- Page Header -->
<div class="bg-white dark:bg-gray-800 shadow-sm">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="md:flex md:items-center md:justify-between">
            <div class="flex-1 min-w-0">
                <h1 class="text-2xl font-bold leading-7 text-gray-900 dark:text-white sm:text-3xl sm:truncate">
                    {{ __('companies.all_companies') }}
                </h1>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                    {{ trans_choice('companies.companies_count', $companies->total(), ['count' => number_format($companies->total())]) }}
                </p>
            </div>
            

        </div>
    </div>
</div>

<!-- Search and Filters -->
<div class="bg-gray-50 dark:bg-gray-900 border-b border-gray-200 dark:border-gray-700">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
        <x-companies.search-filters 
            :industries="$industries"
            :companySizes="$companySizes"
            :locations="$locations"
            :filters="$filters"
        />
    </div>
</div>

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="lg:grid lg:grid-cols-4 lg:gap-8">
        <!-- Sidebar Filters -->
        <div class="hidden lg:block lg:col-span-1">
            <div class="sticky top-20">
                <x-companies.sidebar-filters 
                    :industries="$industries"
                    :companySizes="$companySizes"
                    :locations="$locations"
                    :filters="$filters"
                />
            </div>
        </div>

        <!-- Company Listings -->
        <div class="lg:col-span-3">
            <!-- Sort and View Options -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 mb-6">
                <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center space-x-4">
                            <x-ui.select 
                                name="sort" 
                                id="company-sort"
                                class="w-auto"
                            >
                                <option value="name_asc" {{ request('sort') === 'name_asc' ? 'selected' : '' }}>
                                    {{ __('companies.sort_name_asc') }}
                                </option>
                                <option value="name_desc" {{ request('sort') === 'name_desc' ? 'selected' : '' }}>
                                    {{ __('companies.sort_name_desc') }}
                                </option>
                                <option value="created_at_desc" {{ request('sort') === 'created_at_desc' ? 'selected' : '' }}>
                                    {{ __('companies.sort_newest') }}
                                </option>
                                <option value="jobs_count_desc" {{ request('sort') === 'jobs_count_desc' ? 'selected' : '' }}>
                                    {{ __('companies.sort_most_jobs') }}
                                </option>
                                <option value="employees_desc" {{ request('sort') === 'employees_desc' ? 'selected' : '' }}>
                                    {{ __('companies.sort_largest') }}
                                </option>
                            </x-ui.select>

                            <div class="flex items-center space-x-2">
                                <span class="text-sm text-gray-500 dark:text-gray-400">{{ __('companies.view') }}:</span>
                                <button 
                                    type="button" 
                                    class="p-2 rounded-md {{ request('view') !== 'grid' ? 'bg-blue-100 text-blue-600 dark:bg-blue-900 dark:text-blue-300' : 'text-gray-400 hover:text-gray-600' }}"
                                    data-action="companies-update-view" data-view="list"
                                    title="{{ __('companies.list_view') }}"
                                >
                                    <x-icon name="list" class="h-4 w-4" />
                                </button>
                                <button 
                                    type="button" 
                                    class="p-2 rounded-md {{ request('view') === 'grid' ? 'bg-blue-100 text-blue-600 dark:bg-blue-900 dark:text-blue-300' : 'text-gray-400 hover:text-gray-600' }}"
                                    data-action="companies-update-view" data-view="grid"
                                    title="{{ __('companies.grid_view') }}"
                                >
                                    <x-icon name="grid" class="h-4 w-4" />
                                </button>
                            </div>
                        </div>

                        <!-- Mobile Filter Toggle -->
                        <button 
                            type="button" 
                            class="lg:hidden inline-flex items-center px-3 py-2 border border-gray-300 dark:border-gray-600 text-sm leading-4 font-medium rounded-md text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-700"
                            data-action="companies-toggle-mobile-filters"
                        >
                            <x-icon name="filter" class="h-4 w-4 mr-2" />
                            {{ __('companies.filters') }}
                        </button>
                    </div>
                </div>
            </div>

            <!-- Active Filters -->
            @if($activeFilters && count($activeFilters) > 0)
                <div class="mb-6">
                    <x-companies.active-filters :filters="$activeFilters" />
                </div>
            @endif

            <!-- Companies Grid/List -->
            @if($companies->count() > 0)
                <div class="{{ request('view') === 'grid' ? 'grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6' : 'space-y-6' }}" id="companies-container">
                    @foreach($companies as $company)
                        @if(request('view') === 'grid')
                            <x-companies.company-card :company="$company" layout="grid" />
                        @else
                            <x-companies.company-card :company="$company" layout="list" />
                        @endif
                    @endforeach
                </div>

                <!-- Pagination -->
                <div class="mt-8">
                    {{ $companies->links('components.ui.pagination') }}
                </div>
            @else
                <!-- No Companies Found -->
                <x-ui.empty-state 
                    icon="building-office"
                    :title="__('companies.no_companies_found')"
                    :description="__('companies.no_companies_description')"
                >
                    <x-ui.button 
                        href="{{ route('companies.index') }}" 
                        variant="primary"
                        data-action="companies-clear-filters"
                    >
                        {{ __('companies.clear_filters') }}
                    </x-ui.button>
                </x-ui.empty-state>
            @endif
        </div>
    </div>
</div>

<!-- Featured Companies Section -->
@if($featuredCompanies && $featuredCompanies->count() > 0)
    <section class="bg-gray-50 dark:bg-gray-900 border-t border-gray-200 dark:border-gray-700">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
            <div class="text-center mb-12">
                <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-4">
                    {{ __('companies.featured_companies') }}
                </h2>
                <p class="text-lg text-gray-600 dark:text-gray-400 max-w-2xl mx-auto">
                    {{ __('companies.featured_companies_subtitle') }}
                </p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
                @foreach($featuredCompanies as $company)
                    <x-companies.featured-company-card :company="$company" />
                @endforeach
            </div>
        </div>
    </section>
@endif

<!-- Mobile Filters Modal -->
<x-ui.modal id="mobile-company-filters-modal" title="{{ __('companies.filter_companies') }}">
    <x-companies.mobile-filters 
        :industries="$industries"
        :companySizes="$companySizes"
        :locations="$locations"
        :filters="$filters"
    />
</x-ui.modal>
@endsection

@push('scripts')
    {{-- Scripts handled globally via Vite/app.js; page-specific hooks use data-action attributes --}}
@endpush
