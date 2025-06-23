@extends('layouts.app')

@section('title', __('applications.my_applications'))

@section('content')
<div class="min-h-screen bg-gray-50 dark:bg-gray-900 py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="mb-8">
            <div class="sm:flex sm:items-center sm:justify-between">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900 dark:text-white">
                        {{ __('applications.my_applications') }}
                    </h1>
                    <p class="mt-2 text-gray-600 dark:text-gray-400">
                        {{ __('applications.track_your_job_applications') }}
                    </p>
                </div>
                
                <div class="mt-4 sm:mt-0">
                    <x-ui.button 
                        href="{{ route('jobs.index') }}" 
                        variant="primary"
                        icon="search"
                    >
                        {{ __('applications.find_more_jobs') }}
                    </x-ui.button>
                </div>
            </div>
        </div>

        <!-- Stats Overview -->
        <div class="grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-4 mb-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow rounded-lg">
                <div class="p-5">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <x-icon name="document-text" class="h-6 w-6 text-blue-600 dark:text-blue-400" />
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400 truncate">
                                    {{ __('applications.total_applications') }}
                                </dt>
                                <dd class="text-lg font-medium text-gray-900 dark:text-white">
                                    {{ $stats['total'] ?? 0 }}
                                </dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow rounded-lg">
                <div class="p-5">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <x-icon name="clock" class="h-6 w-6 text-yellow-600 dark:text-yellow-400" />
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400 truncate">
                                    {{ __('applications.pending') }}
                                </dt>
                                <dd class="text-lg font-medium text-gray-900 dark:text-white">
                                    {{ $stats['pending'] ?? 0 }}
                                </dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow rounded-lg">
                <div class="p-5">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <x-icon name="eye" class="h-6 w-6 text-purple-600 dark:text-purple-400" />
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400 truncate">
                                    {{ __('applications.under_review') }}
                                </dt>
                                <dd class="text-lg font-medium text-gray-900 dark:text-white">
                                    {{ $stats['reviewing'] ?? 0 }}
                                </dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow rounded-lg">
                <div class="p-5">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <x-icon name="check-circle" class="h-6 w-6 text-green-600 dark:text-green-400" />
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400 truncate">
                                    {{ __('applications.interviews') }}
                                </dt>
                                <dd class="text-lg font-medium text-gray-900 dark:text-white">
                                    {{ $stats['interview'] ?? 0 }}
                                </dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filters and Search -->
        <div class="bg-white dark:bg-gray-800 shadow rounded-lg mb-8">
            <div class="px-6 py-4">
                <form method="GET" action="{{ route('candidate.applications') }}" class="space-y-4 sm:space-y-0 sm:flex sm:items-center sm:space-x-4">
                    <!-- Search -->
                    <div class="flex-1">
                        <x-ui.input
                            name="search"
                            id="search"
                            :placeholder="__('applications.search_applications')"
                            :value="request('search')"
                            icon="magnifying-glass"
                        />
                    </div>

                    <!-- Status Filter -->
                    <div class="w-full sm:w-48">
                        <x-ui.select
                            name="status"
                            id="status"
                            :options="[
                                '' => __('applications.all_statuses'),
                                'pending' => __('applications.pending'),
                                'reviewing' => __('applications.under_review'),
                                'interview' => __('applications.interview'),
                                'hired' => __('applications.hired'),
                                'rejected' => __('applications.rejected')
                            ]"
                            :selected="request('status')"
                        />
                    </div>

                    <!-- Date Filter -->
                    <div class="w-full sm:w-48">
                        <x-ui.select
                            name="date_range"
                            id="date_range"
                            :options="[
                                '' => __('applications.all_dates'),
                                'today' => __('applications.today'),
                                'week' => __('applications.this_week'),
                                'month' => __('applications.this_month'),
                                '3months' => __('applications.last_3_months'),
                                '6months' => __('applications.last_6_months')
                            ]"
                            :selected="request('date_range')"
                        />
                    </div>

                    <!-- Filter Button -->
                    <x-ui.button type="submit" variant="secondary">
                        {{ __('applications.filter') }}
                    </x-ui.button>

                    <!-- Clear Filters -->
                    @if(request()->hasAny(['search', 'status', 'date_range']))
                        <x-ui.button 
                            href="{{ route('candidate.applications') }}" 
                            variant="ghost"
                        >
                            {{ __('applications.clear') }}
                        </x-ui.button>
                    @endif
                </form>
            </div>
        </div>

        <!-- Applications List -->
        <div class="bg-white dark:bg-gray-800 shadow rounded-lg">
            @if($applications && $applications->count() > 0)
                <div class="divide-y divide-gray-200 dark:divide-gray-700">
                    @foreach($applications as $application)
                        <div class="p-6 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                            <div class="flex items-start justify-between">
                                <div class="flex-1 min-w-0">
                                    <div class="flex items-center space-x-3">
                                        <!-- Company Logo -->
                                        <div class="flex-shrink-0">
                                            @if($application->job->company->logo)
                                                <img class="h-12 w-12 rounded-lg object-cover" src="{{ $application->job->company->logo }}" alt="{{ $application->job->company->name }}">
                                            @else
                                                <div class="h-12 w-12 rounded-lg bg-gray-200 dark:bg-gray-600 flex items-center justify-center">
                                                    <x-icon name="building-office" class="h-6 w-6 text-gray-500 dark:text-gray-400" />
                                                </div>
                                            @endif
                                        </div>

                                        <div class="flex-1 min-w-0">
                                            <!-- Job Title -->
                                            <h3 class="text-lg font-medium text-gray-900 dark:text-white truncate">
                                                <a href="{{ route('jobs.show', $application->job) }}" class="hover:text-blue-600 dark:hover:text-blue-400">
                                                    {{ $application->job->title }}
                                                </a>
                                            </h3>

                                            <!-- Company and Location -->
                                            <div class="mt-1 flex items-center space-x-2 text-sm text-gray-500 dark:text-gray-400">
                                                <span class="font-medium">{{ $application->job->company->name }}</span>
                                                <span>•</span>
                                                <span>{{ $application->job->location }}</span>
                                                @if($application->job->remote_option !== 'no')
                                                    <span>•</span>
                                                    <span class="text-green-600 dark:text-green-400">
                                                        {{ $application->job->remote_option === 'yes' ? __('jobs.remote') : __('jobs.hybrid') }}
                                                    </span>
                                                @endif
                                            </div>

                                            <!-- Application Details -->
                                            <div class="mt-2 flex items-center space-x-4 text-sm text-gray-500 dark:text-gray-400">
                                                <span>{{ __('applications.applied') }}: {{ $application->created_at->format('M d, Y') }}</span>
                                                
                                                @if($application->job->deadline)
                                                    <span>•</span>
                                                    <span>{{ __('applications.deadline') }}: {{ $application->job->deadline->format('M d, Y') }}</span>
                                                @endif

                                                @if($application->expected_salary)
                                                    <span>•</span>
                                                    <span>{{ __('applications.expected_salary') }}: ${{ number_format($application->expected_salary) }}</span>
                                                @endif
                                            </div>

                                            <!-- Application Notes -->
                                            @if($application->notes)
                                                <div class="mt-3">
                                                    <p class="text-sm text-gray-700 dark:text-gray-300 line-clamp-2">
                                                        {{ $application->notes }}
                                                    </p>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                </div>

                                <!-- Status and Actions -->
                                <div class="flex flex-col items-end space-y-3 ml-4">
                                    <!-- Status Badge -->
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium
                                        {{ $application->status === 'pending' ? 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200' : '' }}
                                        {{ $application->status === 'reviewing' ? 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200' : '' }}
                                        {{ $application->status === 'interview' ? 'bg-purple-100 text-purple-800 dark:bg-purple-900 dark:text-purple-200' : '' }}
                                        {{ $application->status === 'hired' ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200' : '' }}
                                        {{ $application->status === 'rejected' ? 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200' : '' }}
                                    ">
                                        <x-icon 
                                            :name="$application->status === 'pending' ? 'clock' : ($application->status === 'reviewing' ? 'eye' : ($application->status === 'interview' ? 'user-group' : ($application->status === 'hired' ? 'check-circle' : 'x-circle')))" 
                                            class="h-4 w-4 mr-1" 
                                        />
                                        {{ __('applications.status.' . $application->status) }}
                                    </span>

                                    <!-- Actions -->
                                    <div class="flex space-x-2">
                                        <x-ui.button 
                                            href="{{ route('candidate.applications.show', $application) }}" 
                                            variant="ghost" 
                                            size="sm"
                                        >
                                            {{ __('applications.view_details') }}
                                        </x-ui.button>

                                        @if($application->status === 'pending')
                                            <x-ui.button 
                                                href="{{ route('candidate.applications.withdraw', $application) }}" 
                                                variant="ghost" 
                                                size="sm"
                                                class="text-red-600 hover:text-red-500 dark:text-red-400 dark:hover:text-red-300"
                                                onclick="return confirm('{{ __('applications.confirm_withdraw') }}')"
                                            >
                                                {{ __('applications.withdraw') }}
                                            </x-ui.button>
                                        @endif
                                    </div>

                                    <!-- Last Update -->
                                    @if($application->updated_at->ne($application->created_at))
                                        <p class="text-xs text-gray-500 dark:text-gray-400">
                                            {{ __('applications.updated') }}: {{ $application->updated_at->diffForHumans() }}
                                        </p>
                                    @endif
                                </div>
                            </div>

                            <!-- Progress Timeline (for interview/hired status) -->
                            @if(in_array($application->status, ['interview', 'hired']) && $application->timeline)
                                <div class="mt-4 pt-4 border-t border-gray-200 dark:border-gray-600">
                                    <h4 class="text-sm font-medium text-gray-900 dark:text-white mb-3">
                                        {{ __('applications.application_timeline') }}
                                    </h4>
                                    
                                    <div class="flow-root">
                                        <ul class="-mb-8">
                                            @foreach($application->timeline as $index => $event)
                                                <li>
                                                    <div class="relative pb-8">
                                                        @if(!$loop->last)
                                                            <span class="absolute top-4 left-4 -ml-px h-full w-0.5 bg-gray-200 dark:bg-gray-600" aria-hidden="true"></span>
                                                        @endif
                                                        
                                                        <div class="relative flex space-x-3">
                                                            <div>
                                                                <span class="h-8 w-8 rounded-full bg-blue-500 flex items-center justify-center ring-8 ring-white dark:ring-gray-800">
                                                                    <x-icon name="check" class="h-4 w-4 text-white" />
                                                                </span>
                                                            </div>
                                                            
                                                            <div class="min-w-0 flex-1 pt-1.5 flex justify-between space-x-4">
                                                                <div>
                                                                    <p class="text-sm text-gray-900 dark:text-white">
                                                                        {{ $event['title'] }}
                                                                    </p>
                                                                    @if(isset($event['description']))
                                                                        <p class="text-sm text-gray-500 dark:text-gray-400">
                                                                            {{ $event['description'] }}
                                                                        </p>
                                                                    @endif
                                                                </div>
                                                                
                                                                <div class="text-right text-sm whitespace-nowrap text-gray-500 dark:text-gray-400">
                                                                    {{ $event['date'] }}
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </li>
                                            @endforeach
                                        </ul>
                                    </div>
                                </div>
                            @endif
                        </div>
                    @endforeach
                </div>

                <!-- Pagination -->
                @if($applications->hasPages())
                    <div class="px-6 py-4 border-t border-gray-200 dark:border-gray-700">
                        {{ $applications->appends(request()->query())->links() }}
                    </div>
                @endif
            @else
                <!-- Empty State -->
                <div class="text-center py-12">
                    <x-icon name="document-text" class="mx-auto h-12 w-12 text-gray-400" />
                    <h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-white">
                        {{ __('applications.no_applications') }}
                    </h3>
                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                        {{ __('applications.start_applying_to_jobs') }}
                    </p>
                    <div class="mt-6">
                        <x-ui.button 
                            href="{{ route('jobs.index') }}" 
                            variant="primary"
                        >
                            {{ __('applications.browse_jobs') }}
                        </x-ui.button>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Auto-submit form on filter changes
    const statusSelect = document.getElementById('status');
    const dateRangeSelect = document.getElementById('date_range');
    
    if (statusSelect) {
        statusSelect.addEventListener('change', function() {
            this.form.submit();
        });
    }
    
    if (dateRangeSelect) {
        dateRangeSelect.addEventListener('change', function() {
            this.form.submit();
        });
    }
    
    // Search with debounce
    const searchInput = document.getElementById('search');
    let searchTimeout;
    
    if (searchInput) {
        searchInput.addEventListener('input', function() {
            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(() => {
                this.form.submit();
            }, 500);
        });
    }
});
</script>
@endpush 