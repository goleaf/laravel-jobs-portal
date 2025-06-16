@extends('layouts.app')

@section('title', __('dashboard.employer_dashboard'))

@section('content')
<div class="min-h-screen bg-gray-50 dark:bg-gray-900">
    <!-- Header Section -->
    <div class="bg-white dark:bg-gray-800 shadow-sm border-b border-gray-200 dark:border-gray-700">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
            <div class="lg:flex lg:items-center lg:justify-between">
                <div class="flex-1 min-w-0">
                    <h1 class="text-2xl font-bold leading-7 text-gray-900 dark:text-white sm:text-3xl sm:truncate">
                        {{ __('dashboard.welcome_back') }}, {{ auth()->user()->first_name }}!
                    </h1>
                    <div class="mt-1 flex flex-col sm:flex-row sm:flex-wrap sm:mt-0 sm:space-x-6">
                        <div class="mt-2 flex items-center text-sm text-gray-500 dark:text-gray-400">
                            <x-icon name="building-office" class="flex-shrink-0 mr-1.5 h-5 w-5" />
                            {{ auth()->user()->company->name ?? __('dashboard.employer') }}
                        </div>
                        <div class="mt-2 flex items-center text-sm text-gray-500 dark:text-gray-400">
                            <x-icon name="calendar" class="flex-shrink-0 mr-1.5 h-5 w-5" />
                            {{ __('dashboard.member_since') }} {{ auth()->user()->created_at->format('M Y') }}
                        </div>
                    </div>
                </div>
                <div class="mt-5 flex lg:mt-0 lg:ml-4">
                    <span class="hidden sm:block">
                        <x-ui.button 
                            href="{{ route('employer.company.edit') }}" 
                            variant="secondary"
                            icon="pencil"
                        >
                            {{ __('dashboard.edit_company') }}
                        </x-ui.button>
                    </span>
                    
                    <span class="ml-3">
                        <x-ui.button 
                            href="{{ route('jobs.create') }}" 
                            variant="primary"
                            icon="plus"
                        >
                            {{ __('dashboard.post_job') }}
                        </x-ui.button>
                    </span>
                </div>
            </div>
        </div>
    </div>

    <!-- Stats Overview -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
        <div class="grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-4">
            <!-- Active Jobs -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow rounded-lg">
                <div class="p-5">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <x-icon name="briefcase" class="h-6 w-6 text-blue-600 dark:text-blue-400" />
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400 truncate">
                                    {{ __('dashboard.active_jobs') }}
                                </dt>
                                <dd class="text-lg font-medium text-gray-900 dark:text-white">
                                    {{ $stats['active_jobs'] ?? 0 }}
                                </dd>
                            </dl>
                        </div>
                    </div>
                </div>
                <div class="bg-gray-50 dark:bg-gray-700 px-5 py-3">
                    <div class="text-sm">
                        <a href="{{ route('employer.jobs') }}" class="font-medium text-blue-600 hover:text-blue-500 dark:text-blue-400 dark:hover:text-blue-300">
                            {{ __('dashboard.view_all') }}
                        </a>
                    </div>
                </div>
            </div>

            <!-- Total Applications -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow rounded-lg">
                <div class="p-5">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <x-icon name="document-text" class="h-6 w-6 text-green-600 dark:text-green-400" />
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400 truncate">
                                    {{ __('dashboard.total_applications') }}
                                </dt>
                                <dd class="text-lg font-medium text-gray-900 dark:text-white">
                                    {{ $stats['total_applications'] ?? 0 }}
                                </dd>
                            </dl>
                        </div>
                    </div>
                </div>
                <div class="bg-gray-50 dark:bg-gray-700 px-5 py-3">
                    <div class="text-sm">
                        <a href="{{ route('employer.applications') }}" class="font-medium text-blue-600 hover:text-blue-500 dark:text-blue-400 dark:hover:text-blue-300">
                            {{ __('dashboard.view_all') }}
                        </a>
                    </div>
                </div>
            </div>

            <!-- Company Views -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow rounded-lg">
                <div class="p-5">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <x-icon name="eye" class="h-6 w-6 text-purple-600 dark:text-purple-400" />
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400 truncate">
                                    {{ __('dashboard.company_views') }}
                                </dt>
                                <dd class="text-lg font-medium text-gray-900 dark:text-white">
                                    {{ $stats['company_views'] ?? 0 }}
                                </dd>
                            </dl>
                        </div>
                    </div>
                </div>
                <div class="bg-gray-50 dark:bg-gray-700 px-5 py-3">
                    <div class="text-sm">
                        <span class="text-gray-500 dark:text-gray-400">{{ __('dashboard.this_month') }}</span>
                    </div>
                </div>
            </div>

            <!-- Hired Candidates -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow rounded-lg">
                <div class="p-5">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <x-icon name="user-plus" class="h-6 w-6 text-emerald-600 dark:text-emerald-400" />
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400 truncate">
                                    {{ __('dashboard.hired_candidates') }}
                                </dt>
                                <dd class="text-lg font-medium text-gray-900 dark:text-white">
                                    {{ $stats['hired_candidates'] ?? 0 }}
                                </dd>
                            </dl>
                        </div>
                    </div>
                </div>
                <div class="bg-gray-50 dark:bg-gray-700 px-5 py-3">
                    <div class="text-sm">
                        <span class="text-gray-500 dark:text-gray-400">{{ __('dashboard.all_time') }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pb-12">
        <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">
            <!-- Recent Applications -->
            <div class="bg-white dark:bg-gray-800 shadow rounded-lg">
                <div class="p-6">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-lg leading-6 font-medium text-gray-900 dark:text-white">
                            {{ __('dashboard.recent_applications') }}
                        </h3>
                        <x-ui.button 
                            href="{{ route('employer.applications') }}" 
                            variant="ghost" 
                            size="sm"
                        >
                            {{ __('dashboard.view_all') }}
                        </x-ui.button>
                    </div>
                    
                    @if($recentApplications && $recentApplications->count() > 0)
                        <div class="space-y-4">
                            @foreach($recentApplications as $application)
                                <div class="flex items-start space-x-4 p-4 bg-gray-50 dark:bg-gray-700 rounded-lg">
                                    <div class="flex-shrink-0">
                                        @if($application->candidate->avatar)
                                            <img class="h-10 w-10 rounded-full object-cover" src="{{ $application->candidate->avatar }}" alt="{{ $application->candidate->full_name }}">
                                        @else
                                            <div class="h-10 w-10 rounded-full bg-gray-300 dark:bg-gray-600 flex items-center justify-center">
                                                <x-icon name="user" class="h-6 w-6 text-gray-500 dark:text-gray-400" />
                                            </div>
                                        @endif
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <div class="flex items-center justify-between">
                                            <h4 class="text-sm font-medium text-gray-900 dark:text-white truncate">
                                                {{ $application->candidate->full_name }}
                                            </h4>
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                                {{ $application->status === 'pending' ? 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200' : '' }}
                                                {{ $application->status === 'reviewing' ? 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200' : '' }}
                                                {{ $application->status === 'interview' ? 'bg-purple-100 text-purple-800 dark:bg-purple-900 dark:text-purple-200' : '' }}
                                                {{ $application->status === 'hired' ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200' : '' }}
                                                {{ $application->status === 'rejected' ? 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200' : '' }}
                                            ">
                                                {{ __('application.status.' . $application->status) }}
                                            </span>
                                        </div>
                                        <p class="text-sm text-gray-500 dark:text-gray-400">
                                            {{ $application->job->title }} • {{ $application->created_at->diffForHumans() }}
                                        </p>
                                        <div class="mt-2 flex space-x-2">
                                            <x-ui.button 
                                                href="{{ route('employer.applications.show', $application) }}" 
                                                variant="ghost" 
                                                size="xs"
                                            >
                                                {{ __('dashboard.view') }}
                                            </x-ui.button>
                                            
                                            @if($application->status === 'pending')
                                                <x-ui.button 
                                                    href="{{ route('employer.applications.update-status', [$application, 'reviewing']) }}" 
                                                    variant="primary" 
                                                    size="xs"
                                                >
                                                    {{ __('dashboard.review') }}
                                                </x-ui.button>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-6">
                            <x-icon name="document-text" class="mx-auto h-12 w-12 text-gray-400" />
                            <h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-white">
                                {{ __('dashboard.no_applications') }}
                            </h3>
                            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                                {{ __('dashboard.post_jobs_to_receive_applications') }}
                            </p>
                            <div class="mt-6">
                                <x-ui.button 
                                    href="{{ route('jobs.create') }}" 
                                    variant="primary"
                                >
                                    {{ __('dashboard.post_first_job') }}
                                </x-ui.button>
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Active Jobs -->
            <div class="bg-white dark:bg-gray-800 shadow rounded-lg">
                <div class="p-6">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-lg leading-6 font-medium text-gray-900 dark:text-white">
                            {{ __('dashboard.active_jobs') }}
                        </h3>
                        <x-ui.button 
                            href="{{ route('employer.jobs') }}" 
                            variant="ghost" 
                            size="sm"
                        >
                            {{ __('dashboard.view_all') }}
                        </x-ui.button>
                    </div>
                    
                    @if($activeJobs && $activeJobs->count() > 0)
                        <div class="space-y-4">
                            @foreach($activeJobs as $job)
                                <div class="flex items-start space-x-4 p-4 bg-gray-50 dark:bg-gray-700 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-600 transition-colors">
                                    <div class="flex-1 min-w-0">
                                        <div class="flex items-center justify-between">
                                            <h4 class="text-sm font-medium text-gray-900 dark:text-white truncate">
                                                <a href="{{ route('jobs.show', $job) }}" class="hover:text-blue-600 dark:hover:text-blue-400">
                                                    {{ $job->title }}
                                                </a>
                                            </h4>
                                            <div class="flex items-center space-x-2">
                                                @if($job->is_featured)
                                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200">
                                                        {{ __('jobs.featured') }}
                                                    </span>
                                                @endif
                                                
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200">
                                                    {{ __('jobs.active') }}
                                                </span>
                                            </div>
                                        </div>
                                        <p class="text-sm text-gray-500 dark:text-gray-400">
                                            {{ $job->location }} • {{ $job->created_at->diffForHumans() }}
                                        </p>
                                        <div class="mt-2 flex items-center justify-between">
                                            <div class="flex items-center space-x-4 text-sm text-gray-500 dark:text-gray-400">
                                                <span class="flex items-center">
                                                    <x-icon name="eye" class="h-4 w-4 mr-1" />
                                                    {{ $job->views_count ?? 0 }} {{ __('dashboard.views') }}
                                                </span>
                                                <span class="flex items-center">
                                                    <x-icon name="document-text" class="h-4 w-4 mr-1" />
                                                    {{ $job->applications_count ?? 0 }} {{ __('dashboard.applications') }}
                                                </span>
                                            </div>
                                            
                                            <div class="flex space-x-2">
                                                <x-ui.button 
                                                    href="{{ route('employer.jobs.edit', $job) }}" 
                                                    variant="ghost" 
                                                    size="xs"
                                                    icon="pencil"
                                                >
                                                    {{ __('dashboard.edit') }}
                                                </x-ui.button>
                                                
                                                <x-ui.button 
                                                    href="{{ route('employer.jobs.applications', $job) }}" 
                                                    variant="primary" 
                                                    size="xs"
                                                >
                                                    {{ __('dashboard.view_applications') }}
                                                </x-ui.button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-6">
                            <x-icon name="briefcase" class="mx-auto h-12 w-12 text-gray-400" />
                            <h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-white">
                                {{ __('dashboard.no_active_jobs') }}
                            </h3>
                            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                                {{ __('dashboard.start_hiring_by_posting_jobs') }}
                            </p>
                            <div class="mt-6">
                                <x-ui.button 
                                    href="{{ route('jobs.create') }}" 
                                    variant="primary"
                                >
                                    {{ __('dashboard.post_job') }}
                                </x-ui.button>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="mt-8 bg-white dark:bg-gray-800 shadow rounded-lg">
            <div class="p-6">
                <h3 class="text-lg leading-6 font-medium text-gray-900 dark:text-white mb-4">
                    {{ __('dashboard.quick_actions') }}
                </h3>
                
                <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">
                    <x-ui.button 
                        href="{{ route('jobs.create') }}" 
                        variant="primary" 
                        class="justify-center"
                        icon="plus"
                    >
                        {{ __('dashboard.post_new_job') }}
                    </x-ui.button>
                    
                    <x-ui.button 
                        href="{{ route('candidates.index') }}" 
                        variant="secondary" 
                        class="justify-center"
                        icon="users"
                    >
                        {{ __('dashboard.browse_candidates') }}
                    </x-ui.button>
                    
                    <x-ui.button 
                        href="{{ route('employer.company.edit') }}" 
                        variant="secondary" 
                        class="justify-center"
                        icon="building-office"
                    >
                        {{ __('dashboard.update_company') }}
                    </x-ui.button>
                    
                    <x-ui.button 
                        href="{{ route('employer.reports') }}" 
                        variant="secondary" 
                        class="justify-center"
                        icon="chart-bar"
                    >
                        {{ __('dashboard.view_reports') }}
                    </x-ui.button>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 