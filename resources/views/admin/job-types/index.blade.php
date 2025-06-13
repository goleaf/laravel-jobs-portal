@extends('layouts.admin')

@section('title', __('job_type.pages.index'))

@push('styles')
<style>
    .job-type-card {
        transition: all 0.3s ease;
    }
    .job-type-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
    }
    .status-toggle {
        transition: all 0.2s ease;
    }
</style>
@endpush

@section('content')
<div class="container-fluid px-4 py-6">
    <!-- Header Section -->
    <div class="mb-8">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">{{ __('job_type.title') }}</h1>
                <p class="text-gray-600 mt-1">{{ __('job_type.pages.index') }}</p>
            </div>
            <div class="flex flex-wrap gap-2">
                @can('create', App\Models\JobType::class)
                <a href="{{ route('admin.job-types.create') }}" 
                   class="inline-flex items-center px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                    {{ __('job_type.actions.create') }}
                </a>
                @endcan
                
                @can('viewStatistics', App\Models\JobType::class)
                <a href="{{ route('admin.job-types.statistics') }}" 
                   class="inline-flex items-center px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition-colors">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                    </svg>
                    {{ __('job_type.actions.statistics') }}
                </a>
                @endcan
                
                @can('export', App\Models\JobType::class)
                <a href="{{ route('admin.job-types.export') }}" 
                   class="inline-flex items-center px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                    {{ __('job_type.actions.export') }}
                </a>
                @endcan
            </div>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <div class="flex items-center">
                <div class="p-2 bg-blue-100 rounded-lg">
                    <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-500">{{ __('job_type.statistics.total_job_types') }}</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $statistics['total'] ?? 0 }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <div class="flex items-center">
                <div class="p-2 bg-green-100 rounded-lg">
                    <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-500">{{ __('job_type.statistics.active_job_types') }}</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $statistics['active'] ?? 0 }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <div class="flex items-center">
                <div class="p-2 bg-yellow-100 rounded-lg">
                    <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"/>
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-500">{{ __('job_type.statistics.featured_job_types') }}</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $statistics['featured'] ?? 0 }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <div class="flex items-center">
                <div class="p-2 bg-purple-100 rounded-lg">
                    <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2-2v2m8 0V6a2 2 0 012 2v6a2 2 0 01-2 2H8a2 2 0 01-2-2V8a2 2 0 012-2V6"/>
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-500">{{ __('job_type.statistics.with_jobs') }}</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $statistics['with_jobs'] ?? 0 }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters and Search -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 mb-6">
        <form method="GET" action="{{ route('admin.job-types.index') }}" class="space-y-4 sm:space-y-0 sm:flex sm:items-center sm:space-x-4">
            <!-- Search -->
            <div class="flex-1">
                <label for="search" class="sr-only">{{ __('job_type.actions.search') }}</label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                        </svg>
                    </div>
                    <input type="text" name="search" id="search" value="{{ request('search') }}" 
                           placeholder="{{ __('job_type.placeholders.search') }}"
                           class="block w-full pl-10 pr-3 py-2 border border-gray-300 rounded-md leading-5 bg-white placeholder-gray-500 focus:outline-none focus:placeholder-gray-400 focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500">
                </div>
            </div>

            <!-- Status Filter -->
            <div>
                <select name="status" class="block w-full py-2 px-3 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500">
                    <option value="">{{ __('job_type.filters.all') }}</option>
                    <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>{{ __('job_type.filters.active') }}</option>
                    <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>{{ __('job_type.filters.inactive') }}</option>
                </select>
            </div>

            <!-- Type Filter -->
            <div>
                <select name="type" class="block w-full py-2 px-3 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500">
                    <option value="">{{ __('job_type.filters.all') }}</option>
                    <option value="default" {{ request('type') === 'default' ? 'selected' : '' }}>{{ __('job_type.filters.default') }}</option>
                    <option value="custom" {{ request('type') === 'custom' ? 'selected' : '' }}>{{ __('job_type.filters.custom') }}</option>
                    <option value="featured" {{ request('type') === 'featured' ? 'selected' : '' }}>{{ __('job_type.filters.featured') }}</option>
                </select>
            </div>

            <!-- Sort Filter -->
            <div>
                <select name="sort" class="block w-full py-2 px-3 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500">
                    <option value="name" {{ request('sort') === 'name' ? 'selected' : '' }}>{{ __('job_type.sorting.name_asc') }}</option>
                    <option value="recent" {{ request('sort') === 'recent' ? 'selected' : '' }}>{{ __('job_type.sorting.created_newest') }}</option>
                    <option value="popular" {{ request('sort') === 'popular' ? 'selected' : '' }}>{{ __('job_type.sorting.most_popular') }}</option>
                    <option value="usage" {{ request('sort') === 'usage' ? 'selected' : '' }}>{{ __('job_type.sorting.usage_high') }}</option>
                </select>
            </div>

            <!-- Submit -->
            <div>
                <button type="submit" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                    {{ __('job_type.actions.filter') }}
                </button>
            </div>

            @if(request()->hasAny(['search', 'status', 'type', 'sort']))
            <div>
                <a href="{{ route('admin.job-types.index') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                    {{ __('common.clear') }}
                </a>
            </div>
            @endif
        </form>
    </div>

    <!-- Job Types Table -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
        @if($jobTypes->count() > 0)
        <!-- Bulk Actions -->
        <div class="px-6 py-3 bg-gray-50 border-b border-gray-200">
            <div class="flex items-center justify-between">
                <div class="flex items-center">
                    <input type="checkbox" id="select-all" class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded">
                    <label for="select-all" class="ml-2 text-sm text-gray-700">{{ __('common.select_all') }}</label>
                </div>
                
                @can('bulkUpdate', App\Models\JobType::class)
                <div class="flex items-center space-x-2" id="bulk-actions" style="display: none;">
                    <button type="button" onclick="bulkAction('activate')" class="inline-flex items-center px-3 py-1 bg-green-100 text-green-800 rounded-md text-xs font-medium hover:bg-green-200">
                        {{ __('job_type.actions.activate') }}
                    </button>
                    <button type="button" onclick="bulkAction('deactivate')" class="inline-flex items-center px-3 py-1 bg-yellow-100 text-yellow-800 rounded-md text-xs font-medium hover:bg-yellow-200">
                        {{ __('job_type.actions.deactivate') }}
                    </button>
                    <button type="button" onclick="bulkAction('feature')" class="inline-flex items-center px-3 py-1 bg-purple-100 text-purple-800 rounded-md text-xs font-medium hover:bg-purple-200">
                        {{ __('job_type.actions.feature') }}
                    </button>
                    @can('delete', App\Models\JobType::class)
                    <button type="button" onclick="bulkAction('delete')" class="inline-flex items-center px-3 py-1 bg-red-100 text-red-800 rounded-md text-xs font-medium hover:bg-red-200">
                        {{ __('job_type.actions.delete') }}
                    </button>
                    @endcan
                </div>
                @endcan
            </div>
        </div>

        <!-- Table -->
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            <input type="checkbox" class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded">
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            {{ __('job_type.table.name') }}
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            {{ __('job_type.table.status') }}
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            {{ __('job_type.table.jobs_count') }}
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            {{ __('job_type.table.created_at') }}
                        </th>
                        <th scope="col" class="relative px-6 py-3">
                            <span class="sr-only">{{ __('job_type.table.actions') }}</span>
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($jobTypes as $jobType)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <input type="checkbox" name="selected_job_types[]" value="{{ $jobType->id }}" class="job-type-checkbox h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded">
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center">
                                @if($jobType->icon)
                                <div class="flex-shrink-0 h-8 w-8 rounded-lg flex items-center justify-center mr-3" style="background-color: {{ $jobType->color ?? '#6B7280' }}20">
                                    <i class="fas fa-{{ $jobType->icon }} text-sm" style="color: {{ $jobType->color ?? '#6B7280' }}"></i>
                                </div>
                                @endif
                                <div>
                                    <div class="text-sm font-medium text-gray-900">{{ $jobType->name }}</div>
                                    @if($jobType->description)
                                    <div class="text-sm text-gray-500">{{ Str::limit($jobType->description, 50) }}</div>
                                    @endif
                                    <div class="flex items-center space-x-2 mt-1">
                                        @if($jobType->is_default)
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-blue-100 text-blue-800">
                                            {{ __('job_type.badges.default') }}
                                        </span>
                                        @endif
                                        @if($jobType->is_featured)
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-yellow-100 text-yellow-800">
                                            {{ __('job_type.badges.featured') }}
                                        </span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @can('manageStatus', $jobType)
                            <button onclick="toggleStatus({{ $jobType->id }})" class="status-toggle inline-flex items-center px-2.5 py-1.5 rounded-full text-xs font-medium {{ $jobType->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                {{ $jobType->is_active ? __('job_type.status.active') : __('job_type.status.inactive') }}
                            </button>
                            @else
                            <span class="inline-flex items-center px-2.5 py-1.5 rounded-full text-xs font-medium {{ $jobType->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                {{ $jobType->is_active ? __('job_type.status.active') : __('job_type.status.inactive') }}
                            </span>
                            @endcan
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

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Bulk selection
    const selectAll = document.getElementById('select-all');
    const checkboxes = document.querySelectorAll('.job-type-checkbox');
    const bulkActions = document.getElementById('bulk-actions');

    selectAll?.addEventListener('change', function() {
        checkboxes.forEach(checkbox => {
            checkbox.checked = this.checked;
        });
        toggleBulkActions();
    });

    checkboxes.forEach(checkbox => {
        checkbox.addEventListener('change', toggleBulkActions);
    });

    function toggleBulkActions() {
        const checkedBoxes = document.querySelectorAll('.job-type-checkbox:checked');
        if (checkedBoxes.length > 0) {
            bulkActions?.style.setProperty('display', 'flex');
        } else {
            bulkActions?.style.setProperty('display', 'none');
        }
    }
});

// Bulk actions
function bulkAction(action) {
    const checkedBoxes = document.querySelectorAll('.job-type-checkbox:checked');
    if (checkedBoxes.length === 0) {
        alert('{{ __("common.select_items_first") }}');
        return;
    }

    const jobTypeIds = Array.from(checkedBoxes).map(cb => cb.value);
    
    if (confirm('{{ __("job_type.confirmations.bulk_action") }}')) {
        document.getElementById('bulk-action-type').value = action;
        document.getElementById('bulk-job-type-ids').value = JSON.stringify(jobTypeIds);
        document.getElementById('bulk-action-form').submit();
    }
}

// Toggle status
function toggleStatus(jobTypeId) {
    fetch(`/api/v1/job-types/${jobTypeId}/toggle-status`, {
        method: 'PATCH',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            location.reload();
        } else {
            alert(data.message || '{{ __("common.error_occurred") }}');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('{{ __("common.error_occurred") }}');
    });
}

// Delete job type
function deleteJobType(id, name) {
    if (confirm(`{{ __('job_type.confirmations.delete') }}\n\n${name}`)) {
        const form = document.getElementById('delete-form');
        form.action = `/admin/job-types/${id}`;
        form.submit();
    }
}

// Duplicate job type
function duplicateJobType(id) {
    if (confirm('{{ __("job_type.confirmations.duplicate") }}')) {
        fetch(`/api/v1/job-types/${id}/duplicate`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            } else {
                alert(data.message || '{{ __("common.error_occurred") }}');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('{{ __("common.error_occurred") }}');
        });
    }
}
</script>
@endpush 