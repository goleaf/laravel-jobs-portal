@extends('layouts.app')

@section('title', __('applications.manage_applications'))

@section('content')
<div class="min-h-screen bg-gray-50 dark:bg-gray-900 py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="mb-8">
            <div class="sm:flex sm:items-center sm:justify-between">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900 dark:text-white">
                        {{ __('applications.manage_applications') }}
                    </h1>
                    <p class="mt-2 text-gray-600 dark:text-gray-400">
                        {{ __('applications.review_and_manage_job_applications') }}
                    </p>
                </div>
                
                <div class="mt-4 sm:mt-0 flex space-x-3">
                    <x-ui.button 
                        href="{{ route('employer.applications.export') }}" 
                        variant="secondary"
                        icon="arrow-down-tray"
                    >
                        {{ __('applications.export_applications') }}
                    </x-ui.button>
                    
                    <x-ui.button 
                        href="{{ route('employer.candidates.search') }}" 
                        variant="primary"
                        icon="magnifying-glass"
                    >
                        {{ __('applications.find_candidates') }}
                    </x-ui.button>
                </div>
            </div>
        </div>

        <!-- Application Stats -->
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
                                    {{ __('applications.pending_review') }}
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
                            <x-icon name="user-group" class="h-6 w-6 text-purple-600 dark:text-purple-400" />
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

            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow rounded-lg">
                <div class="p-5">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <x-icon name="check-circle" class="h-6 w-6 text-green-600 dark:text-green-400" />
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400 truncate">
                                    {{ __('applications.hired') }}
                                </dt>
                                <dd class="text-lg font-medium text-gray-900 dark:text-white">
                                    {{ $stats['hired'] ?? 0 }}
                                </dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Advanced Filters -->
        <div class="bg-white dark:bg-gray-800 shadow rounded-lg mb-8">
            <div class="px-6 py-4">
                <form method="GET" action="{{ route('employer.applications.index') }}" class="space-y-4 lg:space-y-0 lg:flex lg:items-end lg:space-x-4">
                    <!-- Search -->
                    <div class="flex-1">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                            {{ __('applications.search') }}
                        </label>
                        <x-ui.input
                            name="search"
                            id="search"
                            :placeholder="__('applications.search_candidates_jobs')"
                            :value="request('search')"
                            icon="magnifying-glass"
                        />
                    </div>

                    <!-- Job Filter -->
                    <div class="w-full lg:w-48">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                            {{ __('applications.job') }}
                        </label>
                        <x-ui.select
                            name="job_id"
                            id="job_id"
                            :options="$jobs ?? []"
                            :selected="request('job_id')"
                            searchable="true"
                        />
                    </div>

                    <!-- Status Filter -->
                    <div class="w-full lg:w-48">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                            {{ __('applications.status') }}
                        </label>
                        <x-ui.select
                            name="status"
                            id="status"
                            :options="[
                                '' => __('applications.all_statuses'),
                                'pending' => __('applications.pending'),
                                'reviewing' => __('applications.reviewing'),
                                'shortlisted' => __('applications.shortlisted'),
                                'interview' => __('applications.interview'),
                                'hired' => __('applications.hired'),
                                'rejected' => __('applications.rejected')
                            ]"
                            :selected="request('status')"
                        />
                    </div>

                    <!-- Rating Filter -->
                    <div class="w-full lg:w-48">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                            {{ __('applications.rating') }}
                        </label>
                        <x-ui.select
                            name="rating"
                            id="rating"
                            :options="[
                                '' => __('applications.all_ratings'),
                                '5' => '5 ' . __('applications.stars'),
                                '4' => '4+ ' . __('applications.stars'),
                                '3' => '3+ ' . __('applications.stars'),
                                '2' => '2+ ' . __('applications.stars'),
                                '1' => '1+ ' . __('applications.stars')
                            ]"
                            :selected="request('rating')"
                        />
                    </div>

                    <!-- Date Filter -->
                    <div class="w-full lg:w-48">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                            {{ __('applications.applied_date') }}
                        </label>
                        <x-ui.select
                            name="date_range"
                            id="date_range"
                            :options="[
                                '' => __('applications.all_dates'),
                                'today' => __('applications.today'),
                                'week' => __('applications.this_week'),
                                'month' => __('applications.this_month'),
                                '3months' => __('applications.last_3_months')
                            ]"
                            :selected="request('date_range')"
                        />
                    </div>

                    <!-- Sort -->
                    <div class="w-full lg:w-48">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                            {{ __('applications.sort_by') }}
                        </label>
                        <x-ui.select
                            name="sort"
                            id="sort"
                            :options="[
                                'created_at_desc' => __('applications.newest_first'),
                                'created_at_asc' => __('applications.oldest_first'),
                                'rating_desc' => __('applications.highest_rated'),
                                'rating_asc' => __('applications.lowest_rated'),
                                'name_asc' => __('applications.name_a_z'),
                                'name_desc' => __('applications.name_z_a')
                            ]"
                            :selected="request('sort', 'created_at_desc')"
                        />
                    </div>

                    <!-- Actions -->
                    <div class="flex space-x-2">
                        <x-ui.button type="submit" variant="secondary">
                            {{ __('applications.filter') }}
                        </x-ui.button>

                        @if(request()->hasAny(['search', 'job_id', 'status', 'rating', 'date_range', 'sort']))
                            <x-ui.button 
                                href="{{ route('employer.applications.index') }}" 
                                variant="ghost"
                            >
                                {{ __('applications.clear') }}
                            </x-ui.button>
                        @endif
                    </div>
                </form>
            </div>
        </div>

        <!-- Bulk Actions -->
        <div class="bg-white dark:bg-gray-800 shadow rounded-lg mb-8" id="bulk-actions" style="display: none;">
            <div class="px-6 py-4">
                <div class="flex items-center justify-between">
                    <div class="flex items-center">
                        <span class="text-sm text-gray-700 dark:text-gray-300 mr-4">
                            <span id="selected-count">0</span> {{ __('applications.applications_selected') }}
                        </span>
                    </div>
                    
                    <div class="flex space-x-2">
                        <x-ui.button 
                            type="button" 
                            variant="secondary" 
                            size="sm"
                            onclick="bulkAction('shortlist')"
                        >
                            {{ __('applications.shortlist_selected') }}
                        </x-ui.button>
                        
                        <x-ui.button 
                            type="button" 
                            variant="secondary" 
                            size="sm"
                            onclick="bulkAction('interview')"
                        >
                            {{ __('applications.interview_selected') }}
                        </x-ui.button>
                        
                        <x-ui.button 
                            type="button" 
                            variant="secondary" 
                            size="sm"
                            onclick="bulkAction('reject')"
                        >
                            {{ __('applications.reject_selected') }}
                        </x-ui.button>
                        
                        <x-ui.button 
                            type="button" 
                            variant="primary" 
                            size="sm"
                            onclick="showBulkEmailModal()"
                        >
                            {{ __('applications.send_email') }}
                        </x-ui.button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Applications List -->
        <div class="bg-white dark:bg-gray-800 shadow rounded-lg">
            @if($applications && $applications->count() > 0)
                <div class="divide-y divide-gray-200 dark:divide-gray-700">
                    @foreach($applications as $application)
                        <div class="p-6 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                            <div class="flex items-start">
                                <!-- Checkbox -->
                                <div class="flex items-center h-5 mr-4">
                                    <input 
                                        type="checkbox" 
                                        class="application-checkbox h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded" 
                                        value="{{ $application->id }}"
                                        onchange="updateBulkActions()"
                                    >
                                </div>

                                <div class="flex-1 min-w-0">
                                    <div class="flex items-start justify-between">
                                        <div class="flex items-center space-x-4">
                                            <!-- Candidate Avatar -->
                                            <div class="flex-shrink-0">
                                                @if($application->candidate->avatar)
                                                    <img class="h-12 w-12 rounded-full" src="{{ $application->candidate->avatar }}" alt="{{ $application->candidate->full_name }}">
                                                @else
                                                    <div class="h-12 w-12 rounded-full bg-gray-300 dark:bg-gray-600 flex items-center justify-center">
                                                        <x-icon name="user" class="h-6 w-6 text-gray-500 dark:text-gray-400" />
                                                    </div>
                                                @endif
                                            </div>
                                            
                                            <div class="flex-1 min-w-0">
                                                <!-- Candidate Name -->
                                                <h3 class="text-lg font-medium text-gray-900 dark:text-white">
                                                    <a href="{{ route('employer.applications.show', $application) }}" class="hover:text-blue-600 dark:hover:text-blue-400">
                                                        {{ $application->candidate->full_name }}
                                                    </a>
                                                </h3>

                                                <!-- Job Title -->
                                                <p class="text-sm text-blue-600 dark:text-blue-400 truncate">
                                                    {{ __('applications.applied_for') }} 
                                                    <a href="{{ route('jobs.show', $application->job) }}" class="hover:underline">
                                                        {{ $application->job->title }}
                                                    </a>
                                                </p>

                                                <!-- Application Details -->
                                                <div class="mt-1 flex items-center space-x-4 text-sm text-gray-500 dark:text-gray-400">
                                                    <span>{{ __('applications.applied') }}: {{ $application->created_at->format('M d, Y') }}</span>
                                                    
                                                    @if($application->expected_salary)
                                                        <span>•</span>
                                                        <span>{{ __('applications.expected_salary') }}: ${{ number_format($application->expected_salary) }}</span>
                                                    @endif

                                                    @if($application->candidate->experience_years)
                                                        <span>•</span>
                                                        <span>{{ $application->candidate->experience_years }} {{ __('applications.years_experience') }}</span>
                                                    @endif
                                                </div>

                                                <!-- Skills -->
                                                @if($application->candidate->skills && $application->candidate->skills->count() > 0)
                                                    <div class="mt-2 flex flex-wrap gap-1">
                                                        @foreach($application->candidate->skills->take(5) as $skill)
                                                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200">
                                                                {{ $skill->name }}
                                                            </span>
                                                        @endforeach
                                                        @if($application->candidate->skills->count() > 5)
                                                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-gray-100 text-gray-800 dark:bg-gray-900 dark:text-gray-200">
                                                                +{{ $application->candidate->skills->count() - 5 }} {{ __('applications.more') }}
                                                            </span>
                                                        @endif
                                                    </div>
                                                @endif

                                                <!-- Application Notes -->
                                                @if($application->notes)
                                                    <div class="mt-2">
                                                        <p class="text-sm text-gray-700 dark:text-gray-300 line-clamp-2">
                                                            {{ $application->notes }}
                                                        </p>
                                                    </div>
                                                @endif
                                            </div>
                                        </div>

                                        <!-- Status and Actions -->
                                        <div class="flex flex-col items-end space-y-3 ml-4">
                                            <!-- Rating -->
                                            @if($application->rating)
                                                <div class="flex items-center">
                                                    @for($i = 1; $i <= 5; $i++)
                                                        <x-icon 
                                                            name="star" 
                                                            class="h-4 w-4 {{ $i <= $application->rating ? 'text-yellow-400' : 'text-gray-300 dark:text-gray-600' }}" 
                                                        />
                                                    @endfor
                                                    <span class="ml-2 text-sm text-gray-500 dark:text-gray-400">
                                                        ({{ $application->rating }}/5)
                                                    </span>
                                                </div>
                                            @endif

                                            <!-- Status Badge -->
                                            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium
                                                {{ $application->status === 'pending' ? 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200' : '' }}
                                                {{ $application->status === 'reviewing' ? 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200' : '' }}
                                                {{ $application->status === 'shortlisted' ? 'bg-purple-100 text-purple-800 dark:bg-purple-900 dark:text-purple-200' : '' }}
                                                {{ $application->status === 'interview' ? 'bg-indigo-100 text-indigo-800 dark:bg-indigo-900 dark:text-indigo-200' : '' }}
                                                {{ $application->status === 'hired' ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200' : '' }}
                                                {{ $application->status === 'rejected' ? 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200' : '' }}
                                            ">
                                                <x-icon 
                                                    :name="$application->status === 'pending' ? 'clock' : ($application->status === 'reviewing' ? 'eye' : ($application->status === 'shortlisted' ? 'star' : ($application->status === 'interview' ? 'user-group' : ($application->status === 'hired' ? 'check-circle' : 'x-circle'))))" 
                                                    class="h-4 w-4 mr-1" 
                                                />
                                                {{ __('applications.status.' . $application->status) }}
                                            </span>

                                            <!-- Quick Actions -->
                                            <div class="flex space-x-2">
                                                <x-ui.button 
                                                    href="{{ route('employer.applications.show', $application) }}" 
                                                    variant="primary" 
                                                    size="sm"
                                                >
                                                    {{ __('applications.review') }}
                                                </x-ui.button>

                                                @if($application->candidate->resume_url)
                                                    <x-ui.button 
                                                        href="{{ $application->candidate->resume_url }}" 
                                                        target="_blank"
                                                        variant="secondary" 
                                                        size="sm"
                                                        icon="document-text"
                                                    >
                                                        {{ __('applications.resume') }}
                                                    </x-ui.button>
                                                @endif

                                                <!-- Status Actions Dropdown -->
                                                <div class="relative" x-data="{ open: false }">
                                                    <button @click="open = !open" class="p-2 rounded-full text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                                                        <x-icon name="ellipsis-vertical" class="h-5 w-5" />
                                                    </button>
                                                    
                                                    <div x-show="open" @click.away="open = false" x-transition class="absolute right-0 mt-2 w-48 bg-white dark:bg-gray-700 rounded-md shadow-lg z-10">
                                                        <div class="py-1">
                                                            @if($application->status === 'pending')
                                                                <form action="{{ route('employer.applications.update-status', $application) }}" method="POST" class="inline">
                                                                    @csrf
                                                                    <input type="hidden" name="status" value="reviewing">
                                                                    <button type="submit" class="w-full text-left block px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-600">
                                                                        <x-icon name="eye" class="h-4 w-4 mr-2 inline" />
                                                                        {{ __('applications.mark_reviewing') }}
                                                                    </button>
                                                                </form>
                                                            @endif

                                                            @if(in_array($application->status, ['pending', 'reviewing']))
                                                                <form action="{{ route('employer.applications.update-status', $application) }}" method="POST" class="inline">
                                                                    @csrf
                                                                    <input type="hidden" name="status" value="shortlisted">
                                                                    <button type="submit" class="w-full text-left block px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-600">
                                                                        <x-icon name="star" class="h-4 w-4 mr-2 inline" />
                                                                        {{ __('applications.shortlist') }}
                                                                    </button>
                                                                </form>
                                                            @endif

                                                            @if(in_array($application->status, ['reviewing', 'shortlisted']))
                                                                <form action="{{ route('employer.applications.update-status', $application) }}" method="POST" class="inline">
                                                                    @csrf
                                                                    <input type="hidden" name="status" value="interview">
                                                                    <button type="submit" class="w-full text-left block px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-600">
                                                                        <x-icon name="user-group" class="h-4 w-4 mr-2 inline" />
                                                                        {{ __('applications.interview') }}
                                                                    </button>
                                                                </form>
                                                            @endif

                                                            @if($application->status === 'interview')
                                                                <form action="{{ route('employer.applications.update-status', $application) }}" method="POST" class="inline">
                                                                    @csrf
                                                                    <input type="hidden" name="status" value="hired">
                                                                    <button type="submit" class="w-full text-left block px-4 py-2 text-sm text-green-700 dark:text-green-400 hover:bg-green-50 dark:hover:bg-green-900">
                                                                        <x-icon name="check-circle" class="h-4 w-4 mr-2 inline" />
                                                                        {{ __('applications.hire') }}
                                                                    </button>
                                                                </form>
                                                            @endif

                                                            <div class="border-t border-gray-100 dark:border-gray-600"></div>

                                                            <a href="{{ route('employer.applications.contact', $application) }}" class="block px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-600">
                                                                <x-icon name="envelope" class="h-4 w-4 mr-2 inline" />
                                                                {{ __('applications.contact_candidate') }}
                                                            </a>

                                                            @if(!in_array($application->status, ['rejected', 'hired']))
                                                                <div class="border-t border-gray-100 dark:border-gray-600"></div>

                                                                <form action="{{ route('employer.applications.update-status', $application) }}" method="POST" class="inline">
                                                                    @csrf
                                                                    <input type="hidden" name="status" value="rejected">
                                                                    <button type="submit" class="w-full text-left block px-4 py-2 text-sm text-red-700 dark:text-red-400 hover:bg-red-50 dark:hover:bg-red-900" onclick="return confirm('{{ __('applications.confirm_reject') }}')">
                                                                        <x-icon name="x-circle" class="h-4 w-4 mr-2 inline" />
                                                                        {{ __('applications.reject') }}
                                                                    </button>
                                                                </form>
                                                            @endif
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- Last Update -->
                                            @if($application->updated_at->ne($application->created_at))
                                                <p class="text-xs text-gray-500 dark:text-gray-400">
                                                    {{ __('applications.updated') }}: {{ $application->updated_at->diffForHumans() }}
                                                </p>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
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
                        {{ __('applications.no_applications_found') }}
                    </h3>
                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                        {{ __('applications.no_applications_match_filters') }}
                    </p>
                    <div class="mt-6">
                        <x-ui.button 
                            href="{{ route('employer.applications.index') }}" 
                            variant="primary"
                        >
                            {{ __('applications.view_all_applications') }}
                        </x-ui.button>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>

<!-- Bulk Email Modal -->
<div id="bulk-email-modal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
    <div class="relative top-20 mx-auto p-5 border w-full max-w-2xl shadow-lg rounded-md bg-white dark:bg-gray-800">
        <div class="mt-3">
            <!-- Modal Header -->
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-medium text-gray-900 dark:text-white">
                    {{ __('applications.send_bulk_email') }}
                </h3>
                <button onclick="hideBulkEmailModal()" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                    <x-icon name="x-mark" class="h-6 w-6" />
                </button>
            </div>

            <!-- Email Form -->
            <form action="{{ route('employer.applications.bulk-email') }}" method="POST" id="bulk-email-form">
                @csrf
                <input type="hidden" name="application_ids" id="selected-application-ids">
                
                <div class="space-y-4">
                    <!-- Email Template -->
                    <x-ui.select
                        name="template"
                        id="email_template"
                        :label="__('applications.email_template')"
                        :options="[
                            '' => __('applications.select_template'),
                            'interview_invitation' => __('applications.interview_invitation'),
                            'rejection_notice' => __('applications.rejection_notice'),
                            'application_update' => __('applications.application_update'),
                            'custom' => __('applications.custom_message')
                        ]"
                        required
                    />

                    <!-- Subject -->
                    <x-ui.input
                        name="subject"
                        id="email_subject"
                        :label="__('applications.email_subject')"
                        required
                    />

                    <!-- Message -->
                    <x-ui.textarea
                        name="message"
                        id="email_message"
                        :label="__('applications.email_message')"
                        rows="8"
                        required
                        :hint="__('applications.available_placeholders')"
                    />
                </div>

                <!-- Modal Actions -->
                <div class="flex items-center justify-end space-x-3 mt-6">
                    <x-ui.button 
                        type="button" 
                        variant="secondary"
                        onclick="hideBulkEmailModal()"
                    >
                        {{ __('applications.cancel') }}
                    </x-ui.button>
                    
                    <x-ui.button 
                        type="submit" 
                        variant="primary"
                        id="send-email-button"
                    >
                        {{ __('applications.send_emails') }}
                    </x-ui.button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Auto-submit form on filter changes
    const filterSelects = ['job_id', 'status', 'rating', 'date_range', 'sort'];
    
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
    const checkboxes = document.querySelectorAll('.application-checkbox:checked');
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
    const checkboxes = document.querySelectorAll('.application-checkbox:checked');
    const applicationIds = Array.from(checkboxes).map(cb => cb.value);
    
    if (applicationIds.length === 0) {
        alert('{{ __("applications.please_select_applications") }}');
        return;
    }
    
    let confirmMessage = '';
    switch (action) {
        case 'shortlist':
            confirmMessage = '{{ __("applications.confirm_bulk_shortlist") }}';
            break;
        case 'interview':
            confirmMessage = '{{ __("applications.confirm_bulk_interview") }}';
            break;
        case 'reject':
            confirmMessage = '{{ __("applications.confirm_bulk_reject") }}';
            break;
    }
    
    if (confirm(confirmMessage.replace(':count', applicationIds.length))) {
        // Create form and submit
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = '{{ route("employer.applications.bulk-action") }}';
        
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
        
        // Application IDs
        applicationIds.forEach(id => {
            const idInput = document.createElement('input');
            idInput.type = 'hidden';
            idInput.name = 'application_ids[]';
            idInput.value = id;
            form.appendChild(idInput);
        });
        
        document.body.appendChild(form);
        form.submit();
    }
}

function showBulkEmailModal() {
    const checkboxes = document.querySelectorAll('.application-checkbox:checked');
    const applicationIds = Array.from(checkboxes).map(cb => cb.value);
    
    if (applicationIds.length === 0) {
        alert('{{ __("applications.please_select_applications") }}');
        return;
    }
    
    document.getElementById('selected-application-ids').value = applicationIds.join(',');
    document.getElementById('bulk-email-modal').classList.remove('hidden');
}

function hideBulkEmailModal() {
    document.getElementById('bulk-email-modal').classList.add('hidden');
}

// Email template handling
document.getElementById('email_template').addEventListener('change', function() {
    const templates = {
        'interview_invitation': {
            subject: '{{ __("applications.interview_invitation_subject") }}',
            message: '{{ __("applications.interview_invitation_message") }}'
        },
        'rejection_notice': {
            subject: '{{ __("applications.rejection_notice_subject") }}',
            message: '{{ __("applications.rejection_notice_message") }}'
        },
        'application_update': {
            subject: '{{ __("applications.application_update_subject") }}',
            message: '{{ __("applications.application_update_message") }}'
        }
    };
    
    const selectedTemplate = templates[this.value];
    if (selectedTemplate) {
        document.getElementById('email_subject').value = selectedTemplate.subject;
        document.getElementById('email_message').value = selectedTemplate.message;
    }
});

// Form submission with loading state
document.getElementById('bulk-email-form').addEventListener('submit', function() {
    const submitButton = document.getElementById('send-email-button');
    submitButton.disabled = true;
    submitButton.innerHTML = `
        <div class="flex items-center">
            <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
            {{ __('applications.sending') }}...
        </div>
    `;
});
</script>
@endpush 