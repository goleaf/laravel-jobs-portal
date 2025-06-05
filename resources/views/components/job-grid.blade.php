
@push('styles')
    @vite('resources/css/components/job-grid.css')
@endpush
@props([
    'jobs' => collect(),
    'layout' => 'default', // default, compact, detailed
    'columns' => 'auto', // auto, 1, 2, 3, 4
    'showPagination' => true,
    'emptyMessage' => null
])

@php
    $gridClasses = [
        'auto' => 'grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6',
        '1' => 'grid grid-cols-1 gap-6',
        '2' => 'grid grid-cols-1 md:grid-cols-2 gap-6',
        '3' => 'grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6',
        '4' => 'grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6'
    ];
    
    $gridClass = $gridClasses[$columns] ?? $gridClasses['auto'];
@endphp

<div class="space-y-6">
    @if($jobs->count() > 0)
        <!-- Job Grid -->
        <div class="{{ $gridClass }}">
            @foreach($jobs as $job)
                <x-job-card 
                    :job="$job" 
                    :layout="$layout"
                    :featured="$job->activeFeatured ?? false"
                />
            @endforeach
        </div>
        
        <!-- Pagination -->
        @if($showPagination && method_exists($jobs, 'links'))
            <div class="flex justify-center">
                <div class="pagination-wrapper">
                    {{ $jobs->links() }}
                </div>
            </div>
        @endif
    @else
        <!-- Empty State -->
        <div class="text-center py-12">
            <div class="mx-auto w-24 h-24 bg-gray-100 dark:bg-gray-800 rounded-full flex items-center justify-center mb-4">
                <svg class="w-12 h-12 text-gray-400 dark:text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2-2v2m8 0V6a2 2 0 012 2v6a2 2 0 01-2 2H8a2 2 0 01-2-2V8a2 2 0 012-2V6"></path>
                </svg>
            </div>
            <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-2">
                {{ __('jobs.no_jobs_found') }}
            </h3>
            <p class="text-gray-500 dark:text-gray-400 max-w-md mx-auto">
                {{ $emptyMessage ?? __('jobs.no_jobs_description') }}
            </p>
            <div class="mt-6">
                <a href="{{ route('front.') }}" 
                   class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-blue-600 hover:bg-blue-700 dark:bg-blue-500 dark:hover:bg-blue-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors duration-200">
                    {{ __('jobs.browse_all_jobs') }}
                </a>
            </div>
        </div>
    @endif
</div>

 