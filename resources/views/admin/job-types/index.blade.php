@extends('layouts.app')

@section('title', __('job_type.titles.index'))

<style>
.table-container {
    overflow-x: auto;
}
</style>

@section('content')
<div class="py-6">
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="md:flex md:items-center md:justify-between">
            <div class="min-w-0 flex-1">
                <h2 class="text-2xl font-bold leading-7 text-gray-900 dark:text-white sm:truncate sm:text-3xl sm:tracking-tight">
                    {{ __('job_type.titles.index') }}
                </h2>
                <div class="mt-1 flex flex-col sm:mt-0 sm:flex-row sm:flex-wrap sm:space-x-6">
                    <div class="mt-2 flex items-center text-sm text-gray-500 dark:text-gray-400">
                        <svg class="mr-1.5 h-5 w-5 flex-shrink-0 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z" clip-rule="evenodd"/>
                        </svg>
                        {{ trans_choice('job_type.count', $jobTypes->total()) }}
                    </div>
                </div>
            </div>
            
            <div class="mt-4 flex md:ml-4 md:mt-0 space-x-3">
                @can('create', App\Models\JobType::class)
                <a href="{{ route('admin.job-types.create') }}"
                   class="inline-flex items-center rounded-md bg-indigo-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
                    <svg class="-ml-0.5 mr-1.5 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                    {{ __('job_type.actions.create') }}
                </a>
                @endcan

                @can('viewAny', App\Models\JobType::class)
                <a href="{{ route('admin.job-types.statistics') }}"
                   class="inline-flex items-center rounded-md bg-gray-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2">
                    <svg class="-ml-0.5 mr-1.5 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                    </svg>
                    {{ __('job_type.actions.statistics') }}
                </a>
                @endcan

                @can('export', App\Models\JobType::class)
                <a href="{{ route('admin.job-types.export') }}"
                   class="inline-flex items-center rounded-md bg-green-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2">
                    <svg class="-ml-0.5 mr-1.5 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                    {{ __('job_type.actions.export') }}
                </a>
                @endcan
            </div>
        </div>

        <!-- Filters -->
        <div class="mt-6">
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-6">
                <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">{{ __('common.filters') }}</h3>
                
                <form method="GET" action="{{ route('admin.job-types.index') }}" class="space-y-4 sm:space-y-0 sm:flex sm:items-center sm:space-x-4">
                    <!-- Search -->
                    <div class="flex-1">
                        <label for="search" class="sr-only">{{ __('common.search') }}</label>
                        <div class="relative">
                            <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                                <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                                </svg>
                            </div>
                            <input 
                                type="text" 
                                name="search" 
                                id="search" 
                                value="{{ request('search') }}"
                                class="block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white pl-10 focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" 
                                placeholder="{{ __('job_type.placeholders.search') }}"
                            >
                        </div>
                    </div>

                    <!-- Status Filter -->
                    <div>
                        <label for="status" class="sr-only">{{ __('job_type.fields.status') }}</label>
                        <select 
                            name="status" 
                            id="status" 
                            class="rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                        >
                            <option value="">{{ __('job_type.filters.all_statuses') }}</option>
                            <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>
                                {{ __('job_type.statuses.active') }}
                            </option>
                            <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>
                                {{ __('job_type.statuses.inactive') }}
                            </option>
                        </select>
                    </div>

                    <!-- Sort By -->
                    <div>
                        <label for="sort" class="sr-only">{{ __('common.sort_by') }}</label>
                        <select 
                            name="sort" 
                            id="sort" 
                            class="rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                        >
                            <option value="name" {{ request('sort', 'name') === 'name' ? 'selected' : '' }}>
                                {{ __('job_type.sorts.name') }}
                            </option>
                            <option value="created_at" {{ request('sort') === 'created_at' ? 'selected' : '' }}>
                                {{ __('job_type.sorts.created_at') }}
                            </option>
                            <option value="jobs_count" {{ request('sort') === 'jobs_count' ? 'selected' : '' }}>
                                {{ __('job_type.sorts.jobs_count') }}
                            </option>
                        </select>
                    </div>

                    <!-- Direction -->
                    <div>
                        <label for="direction" class="sr-only">{{ __('common.direction') }}</label>
                        <select 
                            name="direction" 
                            id="direction" 
                            class="rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                        >
                            <option value="asc" {{ request('direction', 'asc') === 'asc' ? 'selected' : '' }}>
                                {{ __('common.ascending') }}
                            </option>
                            <option value="desc" {{ request('direction') === 'desc' ? 'selected' : '' }}>
                                {{ __('common.descending') }}
                            </option>
                        </select>
                    </div>

                    <!-- Filter Button -->
                    <div class="flex space-x-2">
                        <button 
                            type="submit" 
                            class="inline-flex items-center rounded-md bg-indigo-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2"
                        >
                            {{ __('common.filter') }}
                        </button>
                        
                        <a href="{{ route('admin.job-types.index') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                            {{ __('common.clear') }}
                        </a>
                    </div>
                </form>
            </div>
        </div>

        <!-- Bulk Actions -->
        <div class="flex items-center space-x-2" id="bulk-actions" style="display: none;">
            <button onclick="bulkAction('activate')" class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                {{ __('job_type.actions.activate_selected') }}
            </button>
            <button onclick="bulkAction('deactivate')" class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-white bg-yellow-600 hover:bg-yellow-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-yellow-500">
                {{ __('job_type.actions.deactivate_selected') }}
            </button>
            <button onclick="bulkAction('delete')" class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                {{ __('job_type.actions.delete_selected') }}
            </button>
        </div>

        <!-- Table -->
        @if($jobTypes->count() > 0)
        <div class="mt-6 table-container">
            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700 bg-white dark:bg-gray-800 shadow-sm rounded-lg overflow-hidden">
                <thead class="bg-gray-50 dark:bg-gray-700">
                    <tr>
                        <th scope="col" class="relative px-6 py-3">
                            <input 
                                type="checkbox" 
                                id="select-all"
                                class="absolute left-4 top-1/2 -mt-2 h-4 w-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500"
                            >
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                            {{ __('job_type.fields.name') }}
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                            {{ __('job_type.fields.status') }}
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                            {{ __('job_type.fields.jobs_count') }}
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                            {{ __('common.created_at') }}
                        </th>
                        <th scope="col" class="relative px-6 py-3">
                            <span class="sr-only">{{ __('common.actions') }}</span>
                        </th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                    @foreach($jobTypes as $jobType)
                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                        <td class="relative px-6 py-4 whitespace-nowrap">
                            <input 
                                type="checkbox" 
                                class="job-type-checkbox absolute left-4 top-1/2 -mt-2 h-4 w-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500"
                                value="{{ $jobType->id }}"
                            >
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center">
                                <div class="flex-shrink-0 h-8 w-8 rounded-lg flex items-center justify-center mr-3" style="background-color: {{ $jobType->color ?? '#6B7280' }}20">
                                    <i class="fas fa-{{ $jobType->icon }} text-sm" style="color: {{ $jobType->color ?? '#6B7280' }}"></i>
                                </div>
                                <div>
                                    <div class="text-sm font-medium text-gray-900 dark:text-white">
                                        {{ $jobType->name }}
                                    </div>
                                    @if($jobType->description)
                                    <div class="text-sm text-gray-500 dark:text-gray-400">
                                        {{ Str::limit($jobType->description, 50) }}
                                    </div>
                                    @endif
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <button 
                                onclick="toggleStatus({{ $jobType->id }})"
                                class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium cursor-pointer transition-colors duration-200 
                                    {{ $jobType->is_active 
                                        ? 'bg-green-100 text-green-800 hover:bg-green-200 dark:bg-green-800 dark:text-green-100' 
                                        : 'bg-red-100 text-red-800 hover:bg-red-200 dark:bg-red-800 dark:text-red-100' }}"
                            >
                                <span class="w-1.5 h-1.5 mr-1 rounded-full {{ $jobType->is_active ? 'bg-green-600' : 'bg-red-600' }}"></span>
                                {{ $jobType->is_active ? __('job_type.statuses.active') : __('job_type.statuses.inactive') }}
                            </button>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            <div class="flex items-center">
                                <span class="font-medium">{{ $jobType->jobs_count ?? 0 }}</span>
                                @if($jobType->jobs_count > 0)
                                <a href="{{ route('admin.jobs.index', ['job_type_id' => $jobType->id]) }}" class="ml-2 text-indigo-600 hover:text-indigo-900">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
                                    </svg>
                                </a>
                                @endif
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            {{ $jobType->created_at->format('M j, Y') }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                            <div class="flex items-center justify-end space-x-2">
                                <a href="{{ route('job-types.show', $jobType->slug) }}" class="text-gray-600 hover:text-gray-900" title="{{ __('job_type.actions.view') }}">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                    </svg>
                                </a>
                                
                                @can('update', $jobType)
                                <a href="{{ route('admin.job-types.edit', $jobType) }}" class="text-indigo-600 hover:text-indigo-900" title="{{ __('job_type.actions.edit') }}">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                    </svg>
                                </a>
                                @endcan

                                @can('duplicate', $jobType)
                                <button onclick="duplicateJobType({{ $jobType->id }})" class="text-green-600 hover:text-green-900" title="{{ __('job_type.actions.duplicate') }}">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                                    </svg>
                                </button>
                                @endcan

                                @can('delete', $jobType)
                                <button onclick="deleteJobType({{ $jobType->id }}, '{{ $jobType->name }}')" class="text-red-600 hover:text-red-900" title="{{ __('job_type.actions.delete') }}">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                    </svg>
                                </button>
                                @endcan
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div class="bg-white px-4 py-3 border-t border-gray-200 sm:px-6">
            {{ $jobTypes->appends(request()->query())->links() }}
        </div>
        @else
        <!-- Empty State -->
        <div class="text-center py-12">
            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
            </svg>
            <h3 class="mt-2 text-sm font-medium text-gray-900">{{ __('job_type.empty.no_job_types') }}</h3>
            <p class="mt-1 text-sm text-gray-500">{{ __('job_type.empty.create_first_type') }}</p>
            @can('create', App\Models\JobType::class)
            <div class="mt-6">
                <a href="{{ route('admin.job-types.create') }}" class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                    <svg class="-ml-1 mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                    {{ __('job_type.actions.create') }}
                </a>
            </div>
            @endcan
        </div>
        @endif
    </div>
</div>

<!-- Forms for AJAX actions -->
<form id="bulk-action-form" method="POST" action="{{ route('admin.job-types.bulk-action') }}" style="display: none;">
    @csrf
    <input type="hidden" name="action" id="bulk-action-type">
    <input type="hidden" name="job_type_ids" id="bulk-job-type-ids">
</form>

<form id="delete-form" method="POST" style="display: none;">
    @csrf
    @method('DELETE')
</form>
@endsection

{{-- JavaScript functionality moved to external file for better maintainability --}}
{{-- TODO: Implement external JS file with proper Enhanced patterns --}} 