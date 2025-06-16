@extends('layouts.app')

@section('title', __('jobs.manage_jobs'))

@section('content')
<div class="min-h-screen bg-gray-50 dark:bg-gray-900 py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="mb-8">
            <div class="sm:flex sm:items-center sm:justify-between">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900 dark:text-white">
                        {{ __('jobs.manage_jobs') }}
                    </h1>
                    <p class="mt-2 text-gray-600 dark:text-gray-400">
                        {{ __('jobs.manage_your_job_postings') }}
                    </p>
                </div>
                
                <div class="mt-4 sm:mt-0 flex space-x-3">
                    <x-ui.button 
                        href="{{ route('employer.jobs.analytics') }}" 
                        variant="secondary"
                        icon="chart-bar"
                    >
                        {{ __('jobs.view_analytics') }}
                    </x-ui.button>
                    
                    <x-ui.button 
                        href="{{ route('jobs.create') }}" 
                        variant="primary"
                        icon="plus"
                    >
                        {{ __('jobs.post_new_job') }}
                    </x-ui.button>
                </div>
            </div>
        </div>

        <!-- Job Stats -->
        <div class="grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-4 mb-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow rounded-lg">
                <div class="p-5">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <x-icon name="briefcase" class="h-6 w-6 text-blue-600 dark:text-blue-400" />
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400 truncate">
                                    {{ __('jobs.total_jobs') }}
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
                            <x-icon name="check-circle" class="h-6 w-6 text-green-600 dark:text-green-400" />
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400 truncate">
                                    {{ __('jobs.active_jobs') }}
                                </dt>
                                <dd class="text-lg font-medium text-gray-900 dark:text-white">
                                    {{ $stats['active'] ?? 0 }}
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
                                    {{ __('jobs.draft_jobs') }}
                                </dt>
                                <dd class="text-lg font-medium text-gray-900 dark:text-white">
                                    {{ $stats['draft'] ?? 0 }}
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
                            <x-icon name="document-text" class="h-6 w-6 text-purple-600 dark:text-purple-400" />
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400 truncate">
                                    {{ __('jobs.total_applications') }}
                                </dt>
                                <dd class="text-lg font-medium text-gray-900 dark:text-white">
                                    {{ $stats['applications'] ?? 0 }}
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
                <form method="GET" action="{{ route('employer.jobs.index') }}" class="space-y-4 lg:space-y-0 lg:flex lg:items-center lg:space-x-4">
                    <!-- Search -->
                    <div class="flex-1">
                        <x-ui.input
                            name="search"
                            id="search"
                            :placeholder="__('jobs.search_jobs')"
                            :value="request('search')"
                            icon="magnifying-glass"
                        />
                    </div>

                    <!-- Status Filter -->
                    <div class="w-full lg:w-48">
                        <x-ui.select
                            name="status"
                            id="status"
                            :options="[
                                '' => __('jobs.all_statuses'),
                                'active' => __('jobs.active'),
                                'draft' => __('jobs.draft'),
                                'paused' => __('jobs.paused'),
                                'expired' => __('jobs.expired'),
                                'closed' => __('jobs.closed')
                            ]"
                            :selected="request('status')"
                        />
                    </div>

                    <!-- Category Filter -->
                    <div class="w-full lg:w-48">
                        <x-ui.select
                            name="category"
                            id="category"
                            :options="$categories ?? []"
                            :selected="request('category')"
                        />
                    </div>

                    <!-- Date Filter -->
                    <div class="w-full lg:w-48">
                        <x-ui.select
                            name="date_range"
                            id="date_range"
                            :options="[
                                '' => __('jobs.all_dates'),
                                'today' => __('jobs.today'),
                                'week' => __('jobs.this_week'),
                                'month' => __('jobs.this_month'),
                                '3months' => __('jobs.last_3_months')
                            ]"
                            :selected="request('date_range')"
                        />
                    </div>

                    <!-- Sort -->
                    <div class="w-full lg:w-48">
                        <x-ui.select
                            name="sort"
                            id="sort"
                            :options="[
                                'created_at_desc' => __('jobs.newest_first'),
                                'created_at_asc' => __('jobs.oldest_first'),
                                'title_asc' => __('jobs.title_a_z'),
                                'title_desc' => __('jobs.title_z_a'),
                                'applications_desc' => __('jobs.most_applications'),
                                'views_desc' => __('jobs.most_views')
                            ]"
                            :selected="request('sort', 'created_at_desc')"
                        />
                    </div>

                    <!-- Filter Button -->
                    <x-ui.button type="submit" variant="secondary">
                        {{ __('jobs.filter') }}
                    </x-ui.button>

                    <!-- Clear Filters -->
                    @if(request()->hasAny(['search', 'status', 'category', 'date_range', 'sort']))
                        <x-ui.button 
                            href="{{ route('employer.jobs.index') }}" 
                            variant="ghost"
                        >
                            {{ __('jobs.clear') }}
                        </x-ui.button>
                    @endif
                </form>
            </div>
        </div>

        <!-- Bulk Actions -->
        <div class="bg-white dark:bg-gray-800 shadow rounded-lg mb-8" id="bulk-actions" style="display: none;">
            <div class="px-6 py-4">
                <div class="flex items-center justify-between">
                    <div class="flex items-center">
                        <span class="text-sm text-gray-700 dark:text-gray-300 mr-4">
                            <span id="selected-count">0</span> {{ __('jobs.jobs_selected') }}
                        </span>
                    </div>
                    
                    <div class="flex space-x-2">
                        <x-ui.button 
                            type="button" 
                            variant="secondary" 
                            size="sm"
                            onclick="bulkAction('activate')"
                        >
                            {{ __('jobs.activate_selected') }}
                        </x-ui.button>
                        
                        <x-ui.button 
                            type="button" 
                            variant="secondary" 
                            size="sm"
                            onclick="bulkAction('pause')"
                        >
                            {{ __('jobs.pause_selected') }}
                        </x-ui.button>
                        
                        <x-ui.button 
                            type="button" 
                            variant="secondary" 
                            size="sm"
                            onclick="bulkAction('close')"
                        >
                            {{ __('jobs.close_selected') }}
                        </x-ui.button>
                        
                        <x-ui.button 
                            type="button" 
                            variant="ghost" 
                            size="sm"
                            class="text-red-600 hover:text-red-500 dark:text-red-400 dark:hover:text-red-300"
                            onclick="bulkAction('delete')"
                        >
                            {{ __('jobs.delete_selected') }}
                        </x-ui.button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Jobs List -->
        <div class="bg-white dark:bg-gray-800 shadow rounded-lg">
            @if($jobs && $jobs->count() > 0)
                <div class="divide-y divide-gray-200 dark:divide-gray-700">
                    @foreach($jobs as $job)
                        <div class="p-6 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                            <div class="flex items-start">
                                <!-- Checkbox -->
                                <div class="flex items-center h-5 mr-4">
                                    <input 
                                        type="checkbox" 
                                        class="job-checkbox h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded" 
                                        value="{{ $job->id }}"
                                        onchange="updateBulkActions()"
                                    >
                                </div>

                                <div class="flex-1 min-w-0">
                                    <div class="flex items-start justify-between">
                                        <div class="flex-1 min-w-0">
                                            <!-- Job Title -->
                                            <h3 class="text-lg font-medium text-gray-900 dark:text-white">
                                                <a href="{{ route('jobs.show', $job) }}" class="hover:text-blue-600 dark:hover:text-blue-400">
                                                    {{ $job->title }}
                                                </a>
                                            </h3>

                                            <!-- Job Details -->
                                            <div class="mt-1 flex items-center space-x-2 text-sm text-gray-500 dark:text-gray-400">
                                                <span>{{ $job->category->name ?? __('jobs.uncategorized') }}</span>
                                                <span>•</span>
                                                <span>{{ $job->location }}</span>
                                                @if($job->remote_option !== 'no')
                                                    <span>•</span>
                                                    <span class="text-green-600 dark:text-green-400">
                                                        {{ $job->remote_option === 'yes' ? __('jobs.remote') : __('jobs.hybrid') }}
                                                    </span>
                                                @endif
                                                <span>•</span>
                                                <span>{{ $job->job_type->name ?? __('jobs.full_time') }}</span>
                                            </div>

                                            <!-- Job Meta -->
                                            <div class="mt-2 flex items-center space-x-4 text-sm text-gray-500 dark:text-gray-400">
                                                <span>{{ __('jobs.posted') }}: {{ $job->created_at->format('M d, Y') }}</span>
                                                
                                                @if($job->deadline)
                                                    <span>•</span>
                                                    <span class="{{ $job->deadline->isPast() ? 'text-red-600 dark:text-red-400' : '' }}">
                                                        {{ __('jobs.deadline') }}: {{ $job->deadline->format('M d, Y') }}
                                                    </span>
                                                @endif

                                                @if($job->is_featured)
                                                    <span>•</span>
                                                    <span class="text-yellow-600 dark:text-yellow-400 font-medium">
                                                        <x-icon name="star" class="h-4 w-4 inline mr-1" />
                                                        {{ __('jobs.featured') }}
                                                    </span>
                                                @endif
                                            </div>

                                            <!-- Job Stats -->
                                            <div class="mt-3 flex items-center space-x-6 text-sm">
                                                <div class="flex items-center text-gray-500 dark:text-gray-400">
                                                    <x-icon name="eye" class="h-4 w-4 mr-1" />
                                                    <span>{{ $job->views_count ?? 0 }} {{ __('jobs.views') }}</span>
                                                </div>
                                                
                                                <div class="flex items-center text-blue-600 dark:text-blue-400">
                                                    <x-icon name="document-text" class="h-4 w-4 mr-1" />
                                                    <a href="{{ route('employer.applications.index', ['job' => $job->id]) }}" class="hover:underline">
                                                        {{ $job->applications_count ?? 0 }} {{ __('jobs.applications') }}
                                                    </a>
                                                </div>
                                                
                                                @if($job->applications_count > 0)
                                                    <div class="flex items-center text-green-600 dark:text-green-400">
                                                        <x-icon name="chart-bar" class="h-4 w-4 mr-1" />
                                                        <span>{{ number_format(($job->applications_count / ($job->views_count ?: 1)) * 100, 1) }}% {{ __('jobs.conversion') }}</span>
                                                    </div>
                                                @endif
                                            </div>
                                        </div>

                                        <!-- Status and Actions -->
                                        <div class="flex flex-col items-end space-y-3 ml-4">
                                            <!-- Status Badge -->
                                            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium
                                                {{ $job->status === 'active' ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200' : '' }}
                                                {{ $job->status === 'draft' ? 'bg-gray-100 text-gray-800 dark:bg-gray-900 dark:text-gray-200' : '' }}
                                                {{ $job->status === 'paused' ? 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200' : '' }}
                                                {{ $job->status === 'expired' ? 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200' : '' }}
                                                {{ $job->status === 'closed' ? 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200' : '' }}
                                            ">
                                                <x-icon 
                                                    :name="$job->status === 'active' ? 'check-circle' : ($job->status === 'draft' ? 'document' : ($job->status === 'paused' ? 'pause' : ($job->status === 'expired' ? 'x-circle' : 'stop')))" 
                                                    class="h-4 w-4 mr-1" 
                                                />
                                                {{ __('jobs.status.' . $job->status) }}
                                            </span>

                                            <!-- Actions Dropdown -->
                                            <div class="relative" x-data="{ open: false }">
                                                <button @click="open = !open" class="p-2 rounded-full text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                                                    <x-icon name="ellipsis-vertical" class="h-5 w-5" />
                                                </button>
                                                
                                                <div x-show="open" @click.away="open = false" x-transition class="absolute right-0 mt-2 w-48 bg-white dark:bg-gray-700 rounded-md shadow-lg z-10">
                                                    <div class="py-1">
                                                        <a href="{{ route('jobs.show', $job) }}" class="block px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-600">
                                                            <x-icon name="eye" class="h-4 w-4 mr-2 inline" />
                                                            {{ __('jobs.view_job') }}
                                                        </a>
                                                        
                                                        <a href="{{ route('employer.jobs.edit', $job) }}" class="block px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-600">
                                                            <x-icon name="pencil" class="h-4 w-4 mr-2 inline" />
                                                            {{ __('jobs.edit_job') }}
                                                        </a>
                                                        
                                                        <a href="{{ route('employer.applications.index', ['job' => $job->id]) }}" class="block px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-600">
                                                            <x-icon name="document-text" class="h-4 w-4 mr-2 inline" />
                                                            {{ __('jobs.view_applications') }}
                                                        </a>
                                                        
                                                        <a href="{{ route('employer.jobs.duplicate', $job) }}" class="block px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-600">
                                                            <x-icon name="document-duplicate" class="h-4 w-4 mr-2 inline" />
                                                            {{ __('jobs.duplicate_job') }}
                                                        </a>
                                                        
                                                        <div class="border-t border-gray-100 dark:border-gray-600"></div>
                                                        
                                                        @if($job->status === 'active')
                                                            <form action="{{ route('employer.jobs.pause', $job) }}" method="POST" class="inline">
                                                                @csrf
                                                                <button type="submit" class="w-full text-left block px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-600">
                                                                    <x-icon name="pause" class="h-4 w-4 mr-2 inline" />
                                                                    {{ __('jobs.pause_job') }}
                                                                </button>
                                                            </form>
                                                        @elseif($job->status === 'paused')
                                                            <form action="{{ route('employer.jobs.activate', $job) }}" method="POST" class="inline">
                                                                @csrf
                                                                <button type="submit" class="w-full text-left block px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-600">
                                                                    <x-icon name="play" class="h-4 w-4 mr-2 inline" />
                                                                    {{ __('jobs.activate_job') }}
                                                                </button>
                                                            </form>
                                                        @endif
                                                        
                                                        @if($job->status !== 'closed')
                                                            <form action="{{ route('employer.jobs.close', $job) }}" method="POST" class="inline">
                                                                @csrf
                                                                <button type="submit" class="w-full text-left block px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-600">
                                                                    <x-icon name="stop" class="h-4 w-4 mr-2 inline" />
                                                                    {{ __('jobs.close_job') }}
                                                                </button>
                                                            </form>
                                                        @endif
                                                        
                                                        <div class="border-t border-gray-100 dark:border-gray-600"></div>
                                                        
                                                        <form action="{{ route('employer.jobs.destroy', $job) }}" method="POST" class="inline" onsubmit="return confirm('{{ __('jobs.confirm_delete') }}')">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" class="w-full text-left block px-4 py-2 text-sm text-red-700 dark:text-red-400 hover:bg-red-50 dark:hover:bg-red-900">
                                                                <x-icon name="trash" class="h-4 w-4 mr-2 inline" />
                                                                {{ __('jobs.delete_job') }}
                                                            </button>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <!-- Pagination -->
                @if($jobs->hasPages())
                    <div class="px-6 py-4 border-t border-gray-200 dark:border-gray-700">
                        {{ $jobs->appends(request()->query())->links() }}
                    </div>
                @endif
            @else
                <!-- Empty State -->
                <div class="text-center py-12">
                    <x-icon name="briefcase" class="mx-auto h-12 w-12 text-gray-400" />
                    <h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-white">
                        {{ __('jobs.no_jobs_found') }}
                    </h3>
                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                        {{ __('jobs.get_started_by_posting_job') }}
                    </p>
                    <div class="mt-6">
                        <x-ui.button 
                            href="{{ route('jobs.create') }}" 
                            variant="primary"
                        >
                            {{ __('jobs.post_your_first_job') }}
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
    const filterSelects = ['status', 'category', 'date_range', 'sort'];
    
    filterSelects.forEach(selectId => {
        const select = document.getElementById(selectId);
        if (select) {
            select.addEventListener('change', function() {
                this.form.submit();
            });
        }
    });
    
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

function updateBulkActions() {
    const checkboxes = document.querySelectorAll('.job-checkbox:checked');
    const bulkActions = document.getElementById('bulk-actions');
    const selectedCount = document.getElementById('selected-count');
    
    if (checkboxes.length > 0) {
        bulkActions.style.display = 'block';
        selectedCount.textContent = checkboxes.length;
    } else {
        bulkActions.style.display = 'none';
    }
}

function bulkAction(action) {
    const checkboxes = document.querySelectorAll('.job-checkbox:checked');
    const jobIds = Array.from(checkboxes).map(cb => cb.value);
    
    if (jobIds.length === 0) {
        alert('{{ __("jobs.please_select_jobs") }}');
        return;
    }
    
    let confirmMessage = '';
    switch (action) {
        case 'activate':
            confirmMessage = '{{ __("jobs.confirm_bulk_activate") }}';
            break;
        case 'pause':
            confirmMessage = '{{ __("jobs.confirm_bulk_pause") }}';
            break;
        case 'close':
            confirmMessage = '{{ __("jobs.confirm_bulk_close") }}';
            break;
        case 'delete':
            confirmMessage = '{{ __("jobs.confirm_bulk_delete") }}';
            break;
    }
    
    if (confirm(confirmMessage.replace(':count', jobIds.length))) {
        // Create form and submit
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = '{{ route("employer.jobs.bulk-action") }}';
        
        // CSRF token
        const csrfInput = document.createElement('input');
        csrfInput.type = 'hidden';
        csrfInput.name = '_token';
        csrfInput.value = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        form.appendChild(csrfInput);
        
        // Action
        const actionInput = document.createElement('input');
        actionInput.type = 'hidden';
        actionInput.name = 'action';
        actionInput.value = action;
        form.appendChild(actionInput);
        
        // Job IDs
        jobIds.forEach(id => {
            const idInput = document.createElement('input');
            idInput.type = 'hidden';
            idInput.name = 'job_ids[]';
            idInput.value = id;
            form.appendChild(idInput);
        });
        
        document.body.appendChild(form);
        form.submit();
    }
}
</script>
@endpush 