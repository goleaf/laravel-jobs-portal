@extends('layouts.app')

@section('title', __('dashboard.candidate_dashboard'))

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
                            <x-icon name="briefcase" class="flex-shrink-0 mr-1.5 h-5 w-5" />
                            {{ __('dashboard.job_seeker') }}
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
                            href="{{ route('candidate.profile.edit') }}" 
                            variant="secondary"
                            icon="pencil"
                        >
                            {{ __('dashboard.edit_profile') }}
                        </x-ui.button>
                    </span>
                    
                    <span class="ml-3">
                        <x-ui.button 
                            href="{{ route('jobs.index') }}" 
                            variant="primary"
                            icon="search"
                        >
                            {{ __('dashboard.browse_jobs') }}
                        </x-ui.button>
                    </span>
                </div>
            </div>
        </div>
    </div>

    <!-- Stats Overview -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
        <div class="grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-4">
            <!-- Applications -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow rounded-lg">
                <div class="p-5">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <x-icon name="document-text" class="h-6 w-6 text-blue-600 dark:text-blue-400" />
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400 truncate">
                                    {{ __('dashboard.applications') }}
                                </dt>
                                <dd class="text-lg font-medium text-gray-900 dark:text-white">
                                    {{ $stats['applications'] ?? 0 }}
                                </dd>
                            </dl>
                        </div>
                    </div>
                </div>
                <div class="bg-gray-50 dark:bg-gray-700 px-5 py-3">
                    <div class="text-sm">
                        <a href="{{ route('candidate.applications') }}" class="font-medium text-blue-600 hover:text-blue-500 dark:text-blue-400 dark:hover:text-blue-300">
                            {{ __('dashboard.view_all') }}
                        </a>
                    </div>
                </div>
            </div>

            <!-- Saved Jobs -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow rounded-lg">
                <div class="p-5">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <x-icon name="heart" class="h-6 w-6 text-red-600 dark:text-red-400" />
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400 truncate">
                                    {{ __('dashboard.saved_jobs') }}
                                </dt>
                                <dd class="text-lg font-medium text-gray-900 dark:text-white">
                                    {{ $stats['saved_jobs'] ?? 0 }}
                                </dd>
                            </dl>
                        </div>
                    </div>
                </div>
                <div class="bg-gray-50 dark:bg-gray-700 px-5 py-3">
                    <div class="text-sm">
                        <a href="{{ route('candidate.saved-jobs') }}" class="font-medium text-blue-600 hover:text-blue-500 dark:text-blue-400 dark:hover:text-blue-300">
                            {{ __('dashboard.view_all') }}
                        </a>
                    </div>
                </div>
            </div>

            <!-- Profile Views -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow rounded-lg">
                <div class="p-5">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <x-icon name="eye" class="h-6 w-6 text-green-600 dark:text-green-400" />
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400 truncate">
                                    {{ __('dashboard.profile_views') }}
                                </dt>
                                <dd class="text-lg font-medium text-gray-900 dark:text-white">
                                    {{ $stats['profile_views'] ?? 0 }}
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

            <!-- Profile Completion -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow rounded-lg">
                <div class="p-5">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <x-icon name="chart-pie" class="h-6 w-6 text-purple-600 dark:text-purple-400" />
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400 truncate">
                                    {{ __('dashboard.profile_completion') }}
                                </dt>
                                <dd class="text-lg font-medium text-gray-900 dark:text-white">
                                    {{ $stats['profile_completion'] ?? 0 }}%
                                </dd>
                            </dl>
                        </div>
                    </div>
                </div>
                <div class="bg-gray-50 dark:bg-gray-700 px-5 py-3">
                    <div class="text-sm">
                        <a href="{{ route('candidate.profile.edit') }}" class="font-medium text-blue-600 hover:text-blue-500 dark:text-blue-400 dark:hover:text-blue-300">
                            {{ __('dashboard.complete_profile') }}
                        </a>
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
                            href="{{ route('candidate.applications') }}" 
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
                                        @if($application->job->company->logo)
                                            <img class="h-10 w-10 rounded-lg object-cover" src="{{ $application->job->company->logo }}" alt="{{ $application->job->company->name }}">
                                        @else
                                            <div class="h-10 w-10 rounded-lg bg-gray-300 dark:bg-gray-600 flex items-center justify-center">
                                                <x-icon name="building-office" class="h-6 w-6 text-gray-500 dark:text-gray-400" />
                                            </div>
                                        @endif
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <div class="flex items-center justify-between">
                                            <h4 class="text-sm font-medium text-gray-900 dark:text-white truncate">
                                                {{ $application->job->title }}
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
                                            {{ $application->job->company->name }} • {{ $application->created_at->diffForHumans() }}
                                        </p>
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
                                {{ __('dashboard.start_applying_jobs') }}
                            </p>
                            <div class="mt-6">
                                <x-ui.button 
                                    href="{{ route('jobs.index') }}" 
                                    variant="primary"
                                >
                                    {{ __('dashboard.browse_jobs') }}
                                </x-ui.button>
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Recommended Jobs -->
            <div class="bg-white dark:bg-gray-800 shadow rounded-lg">
                <div class="p-6">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-lg leading-6 font-medium text-gray-900 dark:text-white">
                            {{ __('dashboard.recommended_jobs') }}
                        </h3>
                        <x-ui.button 
                            href="{{ route('jobs.index') }}" 
                            variant="ghost" 
                            size="sm"
                        >
                            {{ __('dashboard.view_all') }}
                        </x-ui.button>
                    </div>
                    
                    @if($recommendedJobs && $recommendedJobs->count() > 0)
                        <div class="space-y-4">
                            @foreach($recommendedJobs as $job)
                                <div class="flex items-start space-x-4 p-4 bg-gray-50 dark:bg-gray-700 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-600 transition-colors">
                                    <div class="flex-shrink-0">
                                        @if($job->company->logo)
                                            <img class="h-10 w-10 rounded-lg object-cover" src="{{ $job->company->logo }}" alt="{{ $job->company->name }}">
                                        @else
                                            <div class="h-10 w-10 rounded-lg bg-gray-300 dark:bg-gray-600 flex items-center justify-center">
                                                <x-icon name="building-office" class="h-6 w-6 text-gray-500 dark:text-gray-400" />
                                            </div>
                                        @endif
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <div class="flex items-center justify-between">
                                            <h4 class="text-sm font-medium text-gray-900 dark:text-white truncate">
                                                <a href="{{ route('jobs.show', $job) }}" class="hover:text-blue-600 dark:hover:text-blue-400">
                                                    {{ $job->title }}
                                                </a>
                                            </h4>
                                            @if($job->is_featured)
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200">
                                                    {{ __('jobs.featured') }}
                                                </span>
                                            @endif
                                        </div>
                                        <p class="text-sm text-gray-500 dark:text-gray-400">
                                            {{ $job->company->name }} • {{ $job->location }} • {{ $job->created_at->diffForHumans() }}
                                        </p>
                                        @if($job->salary_min && $job->salary_max)
                                            <p class="text-sm text-green-600 dark:text-green-400 font-medium">
                                                ${{ number_format($job->salary_min) }} - ${{ number_format($job->salary_max) }}
                                            </p>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-6">
                            <x-icon name="briefcase" class="mx-auto h-12 w-12 text-gray-400" />
                            <h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-white">
                                {{ __('dashboard.no_recommendations') }}
                            </h3>
                            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                                {{ __('dashboard.complete_profile_for_recommendations') }}
                            </p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Profile Completion Alert -->
        @if(($stats['profile_completion'] ?? 0) < 80)
            <div class="mt-6 bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-md p-4">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <x-icon name="information-circle" class="h-5 w-5 text-blue-400" />
                    </div>
                    <div class="ml-3">
                        <h3 class="text-sm font-medium text-blue-800 dark:text-blue-200">
                            {{ __('dashboard.complete_your_profile') }}
                        </h3>
                        <div class="mt-2 text-sm text-blue-700 dark:text-blue-300">
                            <p>{{ __('dashboard.profile_completion_benefits') }}</p>
                        </div>
                        <div class="mt-4">
                            <div class="-mx-2 -my-1.5 flex">
                                <x-ui.button 
                                    href="{{ route('candidate.profile.edit') }}" 
                                    variant="primary" 
                                    size="sm"
                                >
                                    {{ __('dashboard.complete_now') }}
                                </x-ui.button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    </div>
</div>
@endsection 