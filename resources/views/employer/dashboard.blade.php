@extends('layouts.app')

@section('title', __('dashboard.employer_dashboard'))

@section('content')
<div class="min-h-screen bg-gray-50 dark:bg-gray-900 py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Welcome Header -->
        <div class="mb-8">
            <div class="bg-white dark:bg-gray-800 shadow rounded-lg p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">
                            {{ __('dashboard.welcome_back') }}, {{ auth()->user()->name }}!
                        </h1>
                        <p class="mt-1 text-gray-600 dark:text-gray-400">
                            {{ __('dashboard.employer_welcome_message') }}
                        </p>
                    </div>
                    
                    <div class="flex space-x-3">
                        <x-ui.button 
                            href="{{ route('jobs.create') }}" 
                            variant="primary"
                            icon="plus"
                        >
                            {{ __('dashboard.post_new_job') }}
                        </x-ui.button>
                        
                        <x-ui.button 
                            href="{{ route('employer.candidates.search') }}" 
                            variant="secondary"
                            icon="magnifying-glass"
                        >
                            {{ __('dashboard.search_candidates') }}
                        </x-ui.button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Key Metrics -->
        <div class="grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-4 mb-8">
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
                        <a href="{{ route('employer.jobs.index', ['status' => 'active']) }}" class="font-medium text-blue-600 hover:text-blue-500 dark:text-blue-400 dark:hover:text-blue-300">
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
                        <a href="{{ route('employer.applications.index') }}" class="font-medium text-green-600 hover:text-green-500 dark:text-green-400 dark:hover:text-green-300">
                            {{ __('dashboard.view_all') }}
                        </a>
                    </div>
                </div>
            </div>

            <!-- New Applications -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow rounded-lg">
                <div class="p-5">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <x-icon name="bell" class="h-6 w-6 text-yellow-600 dark:text-yellow-400" />
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400 truncate">
                                    {{ __('dashboard.new_applications') }}
                                </dt>
                                <dd class="text-lg font-medium text-gray-900 dark:text-white">
                                    {{ $stats['new_applications'] ?? 0 }}
                                </dd>
                            </dl>
                        </div>
                    </div>
                </div>
                <div class="bg-gray-50 dark:bg-gray-700 px-5 py-3">
                    <div class="text-sm">
                        <a href="{{ route('employer.applications.index', ['status' => 'pending']) }}" class="font-medium text-yellow-600 hover:text-yellow-500 dark:text-yellow-400 dark:hover:text-yellow-300">
                            {{ __('dashboard.review_now') }}
                        </a>
                    </div>
                </div>
            </div>

            <!-- Profile Views -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow rounded-lg">
                <div class="p-5">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <x-icon name="eye" class="h-6 w-6 text-purple-600 dark:text-purple-400" />
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
                        <a href="{{ route('employer.company.analytics') }}" class="font-medium text-purple-600 hover:text-purple-500 dark:text-purple-400 dark:hover:text-purple-300">
                            {{ __('dashboard.view_analytics') }}
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Main Content -->
            <div class="lg:col-span-2 space-y-8">
                <!-- Recent Applications -->
                <div class="bg-white dark:bg-gray-800 shadow rounded-lg">
                    <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                        <div class="flex items-center justify-between">
                            <h3 class="text-lg font-medium text-gray-900 dark:text-white">
                                {{ __('dashboard.recent_applications') }}
                            </h3>
                            <x-ui.button 
                                href="{{ route('employer.applications.index') }}" 
                                variant="ghost" 
                                size="sm"
                            >
                                {{ __('dashboard.view_all') }}
                            </x-ui.button>
                        </div>
                    </div>
                    
                    <div class="divide-y divide-gray-200 dark:divide-gray-700">
                        @forelse($recentApplications ?? [] as $application)
                            <div class="p-6 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center space-x-4">
                                        <!-- Candidate Avatar -->
                                        <div class="flex-shrink-0">
                                            @if($application->candidate->avatar)
                                                <img class="h-10 w-10 rounded-full" src="{{ $application->candidate->avatar }}" alt="{{ $application->candidate->full_name }}">
                                            @else
                                                <div class="h-10 w-10 rounded-full bg-gray-300 dark:bg-gray-600 flex items-center justify-center">
                                                    <x-icon name="user" class="h-5 w-5 text-gray-500 dark:text-gray-400" />
                                                </div>
                                            @endif
                                        </div>
                                        
                                        <div class="flex-1 min-w-0">
                                            <p class="text-sm font-medium text-gray-900 dark:text-white truncate">
                                                {{ $application->candidate->full_name }}
                                            </p>
                                            <p class="text-sm text-gray-500 dark:text-gray-400 truncate">
                                                {{ __('dashboard.applied_for') }} 
                                                <a href="{{ route('jobs.show', $application->job) }}" class="text-blue-600 hover:text-blue-500 dark:text-blue-400 dark:hover:text-blue-300">
                                                    {{ $application->job->title }}
                                                </a>
                                            </p>
                                            <p class="text-xs text-gray-500 dark:text-gray-400">
                                                {{ $application->created_at->diffForHumans() }}
                                            </p>
                                        </div>
                                    </div>
                                    
                                    <div class="flex items-center space-x-2">
                                        <!-- Status Badge -->
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                            {{ $application->status === 'pending' ? 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200' : '' }}
                                            {{ $application->status === 'reviewing' ? 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200' : '' }}
                                            {{ $application->status === 'interview' ? 'bg-purple-100 text-purple-800 dark:bg-purple-900 dark:text-purple-200' : '' }}
                                            {{ $application->status === 'hired' ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200' : '' }}
                                            {{ $application->status === 'rejected' ? 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200' : '' }}
                                        ">
                                            {{ __('applications.status.' . $application->status) }}
                                        </span>
                                        
                                        <!-- Actions -->
                                        <x-ui.button 
                                            href="{{ route('employer.applications.show', $application) }}" 
                                            variant="ghost" 
                                            size="sm"
                                        >
                                            {{ __('dashboard.review') }}
                                        </x-ui.button>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="p-6 text-center">
                                <x-icon name="document-text" class="mx-auto h-12 w-12 text-gray-400" />
                                <h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-white">
                                    {{ __('dashboard.no_recent_applications') }}
                                </h3>
                                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                                    {{ __('dashboard.applications_will_appear_here') }}
                                </p>
                            </div>
                        @endforelse
                    </div>
                </div>

                <!-- Job Performance -->
                <div class="bg-white dark:bg-gray-800 shadow rounded-lg">
                    <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                        <div class="flex items-center justify-between">
                            <h3 class="text-lg font-medium text-gray-900 dark:text-white">
                                {{ __('dashboard.job_performance') }}
                            </h3>
                            <x-ui.button 
                                href="{{ route('employer.jobs.analytics') }}" 
                                variant="ghost" 
                                size="sm"
                            >
                                {{ __('dashboard.detailed_analytics') }}
                            </x-ui.button>
                        </div>
                    </div>
                    
                    <div class="p-6">
                        @if($topJobs ?? false)
                            <div class="space-y-4">
                                @foreach($topJobs as $job)
                                    <div class="flex items-center justify-between">
                                        <div class="flex-1 min-w-0">
                                            <h4 class="text-sm font-medium text-gray-900 dark:text-white truncate">
                                                <a href="{{ route('jobs.show', $job) }}" class="hover:text-blue-600 dark:hover:text-blue-400">
                                                    {{ $job->title }}
                                                </a>
                                            </h4>
                                            <p class="text-sm text-gray-500 dark:text-gray-400">
                                                {{ $job->location }} • {{ __('dashboard.posted') }} {{ $job->created_at->diffForHumans() }}
                                            </p>
                                        </div>
                                        
                                        <div class="flex items-center space-x-4 text-sm">
                                            <div class="text-center">
                                                <div class="font-medium text-gray-900 dark:text-white">{{ $job->views_count ?? 0 }}</div>
                                                <div class="text-gray-500 dark:text-gray-400">{{ __('dashboard.views') }}</div>
                                            </div>
                                            
                                            <div class="text-center">
                                                <div class="font-medium text-gray-900 dark:text-white">{{ $job->applications_count ?? 0 }}</div>
                                                <div class="text-gray-500 dark:text-gray-400">{{ __('dashboard.applications') }}</div>
                                            </div>
                                            
                                            <div class="text-center">
                                                <div class="font-medium text-green-600 dark:text-green-400">
                                                    {{ $job->applications_count > 0 ? number_format(($job->views_count / $job->applications_count) * 100, 1) : 0 }}%
                                                </div>
                                                <div class="text-gray-500 dark:text-gray-400">{{ __('dashboard.conversion') }}</div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="text-center py-8">
                                <x-icon name="chart-bar" class="mx-auto h-12 w-12 text-gray-400" />
                                <h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-white">
                                    {{ __('dashboard.no_job_data') }}
                                </h3>
                                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                                    {{ __('dashboard.post_jobs_to_see_analytics') }}
                                </p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Sidebar -->
            <div class="space-y-8">
                <!-- Quick Actions -->
                <div class="bg-white dark:bg-gray-800 shadow rounded-lg">
                    <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                        <h3 class="text-lg font-medium text-gray-900 dark:text-white">
                            {{ __('dashboard.quick_actions') }}
                        </h3>
                    </div>
                    
                    <div class="p-6 space-y-3">
                        <x-ui.button 
                            href="{{ route('jobs.create') }}" 
                            variant="primary" 
                            class="w-full justify-center"
                            icon="plus"
                        >
                            {{ __('dashboard.post_new_job') }}
                        </x-ui.button>
                        
                        <x-ui.button 
                            href="{{ route('employer.candidates.search') }}" 
                            variant="secondary" 
                            class="w-full justify-center"
                            icon="magnifying-glass"
                        >
                            {{ __('dashboard.search_candidates') }}
                        </x-ui.button>
                        
                        <x-ui.button 
                            href="{{ route('employer.company.edit') }}" 
                            variant="ghost" 
                            class="w-full justify-center"
                            icon="building-office"
                        >
                            {{ __('dashboard.update_company_profile') }}
                        </x-ui.button>
                        
                        <x-ui.button 
                            href="{{ route('employer.subscription.plans') }}" 
                            variant="ghost" 
                            class="w-full justify-center"
                            icon="credit-card"
                        >
                            {{ __('dashboard.manage_subscription') }}
                        </x-ui.button>
                    </div>
                </div>

                <!-- Company Profile Completion -->
                <div class="bg-white dark:bg-gray-800 shadow rounded-lg">
                    <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                        <h3 class="text-lg font-medium text-gray-900 dark:text-white">
                            {{ __('dashboard.profile_completion') }}
                        </h3>
                    </div>
                    
                    <div class="p-6">
                        <div class="flex items-center justify-between mb-4">
                            <span class="text-sm font-medium text-gray-700 dark:text-gray-300">
                                {{ __('dashboard.completion_progress') }}
                            </span>
                            <span class="text-sm font-medium text-gray-900 dark:text-white">
                                {{ $profileCompletion ?? 0 }}%
                            </span>
                        </div>
                        
                        <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-2 mb-4">
                            <div class="bg-blue-600 h-2 rounded-full" style="width: {{ $profileCompletion ?? 0 }}%"></div>
                        </div>
                        
                        @if(($profileCompletion ?? 0) < 100)
                            <div class="space-y-2 text-sm">
                                <p class="text-gray-600 dark:text-gray-400">
                                    {{ __('dashboard.complete_profile_benefits') }}
                                </p>
                                
                                @if(!($company->logo ?? false))
                                    <div class="flex items-center text-gray-500 dark:text-gray-400">
                                        <x-icon name="x-circle" class="h-4 w-4 mr-2 text-red-500" />
                                        {{ __('dashboard.add_company_logo') }}
                                    </div>
                                @endif
                                
                                @if(!($company->description ?? false))
                                    <div class="flex items-center text-gray-500 dark:text-gray-400">
                                        <x-icon name="x-circle" class="h-4 w-4 mr-2 text-red-500" />
                                        {{ __('dashboard.add_company_description') }}
                                    </div>
                                @endif
                                
                                @if(!($company->website ?? false))
                                    <div class="flex items-center text-gray-500 dark:text-gray-400">
                                        <x-icon name="x-circle" class="h-4 w-4 mr-2 text-red-500" />
                                        {{ __('dashboard.add_company_website') }}
                                    </div>
                                @endif
                            </div>
                            
                            <div class="mt-4">
                                <x-ui.button 
                                    href="{{ route('employer.company.edit') }}" 
                                    variant="primary" 
                                    size="sm"
                                    class="w-full justify-center"
                                >
                                    {{ __('dashboard.complete_profile') }}
                                </x-ui.button>
                            </div>
                        @else
                            <div class="flex items-center text-green-600 dark:text-green-400">
                                <x-icon name="check-circle" class="h-5 w-5 mr-2" />
                                <span class="text-sm font-medium">
                                    {{ __('dashboard.profile_complete') }}
                                </span>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Recent Activity -->
                <div class="bg-white dark:bg-gray-800 shadow rounded-lg">
                    <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                        <h3 class="text-lg font-medium text-gray-900 dark:text-white">
                            {{ __('dashboard.recent_activity') }}
                        </h3>
                    </div>
                    
                    <div class="p-6">
                        @if($recentActivity ?? false)
                            <div class="flow-root">
                                <ul class="-mb-8">
                                    @foreach($recentActivity as $activity)
                                        <li>
                                            <div class="relative pb-8">
                                                @if(!$loop->last)
                                                    <span class="absolute top-4 left-4 -ml-px h-full w-0.5 bg-gray-200 dark:bg-gray-600" aria-hidden="true"></span>
                                                @endif
                                                
                                                <div class="relative flex space-x-3">
                                                    <div>
                                                        <span class="h-8 w-8 rounded-full bg-blue-500 flex items-center justify-center ring-8 ring-white dark:ring-gray-800">
                                                            <x-icon :name="$activity['icon']" class="h-4 w-4 text-white" />
                                                        </span>
                                                    </div>
                                                    
                                                    <div class="min-w-0 flex-1 pt-1.5">
                                                        <div>
                                                            <p class="text-sm text-gray-900 dark:text-white">
                                                                {{ $activity['description'] }}
                                                            </p>
                                                            <p class="text-xs text-gray-500 dark:text-gray-400">
                                                                {{ $activity['time'] }}
                                                            </p>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                        @else
                            <div class="text-center py-4">
                                <x-icon name="clock" class="mx-auto h-8 w-8 text-gray-400" />
                                <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">
                                    {{ __('dashboard.no_recent_activity') }}
                                </p>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Subscription Status -->
                @if($subscription ?? false)
                    <div class="bg-white dark:bg-gray-800 shadow rounded-lg">
                        <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                            <h3 class="text-lg font-medium text-gray-900 dark:text-white">
                                {{ __('dashboard.subscription_status') }}
                            </h3>
                        </div>
                        
                        <div class="p-6">
                            <div class="flex items-center justify-between mb-4">
                                <div>
                                    <h4 class="text-sm font-medium text-gray-900 dark:text-white">
                                        {{ $subscription->plan->name }}
                                    </h4>
                                    <p class="text-sm text-gray-500 dark:text-gray-400">
                                        {{ __('dashboard.expires_on') }} {{ $subscription->expires_at->format('M d, Y') }}
                                    </p>
                                </div>
                                
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                    {{ $subscription->is_active ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200' : 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200' }}
                                ">
                                    {{ $subscription->is_active ? __('dashboard.active') : __('dashboard.expired') }}
                                </span>
                            </div>
                            
                            <div class="space-y-2 text-sm">
                                <div class="flex justify-between">
                                    <span class="text-gray-500 dark:text-gray-400">{{ __('dashboard.job_posts_remaining') }}</span>
                                    <span class="font-medium text-gray-900 dark:text-white">{{ $subscription->job_posts_remaining }}</span>
                                </div>
                                
                                <div class="flex justify-between">
                                    <span class="text-gray-500 dark:text-gray-400">{{ __('dashboard.featured_jobs_remaining') }}</span>
                                    <span class="font-medium text-gray-900 dark:text-white">{{ $subscription->featured_jobs_remaining }}</span>
                                </div>
                            </div>
                            
                            <div class="mt-4">
                                <x-ui.button 
                                    href="{{ route('employer.subscription.plans') }}" 
                                    variant="primary" 
                                    size="sm"
                                    class="w-full justify-center"
                                >
                                    {{ __('dashboard.manage_subscription') }}
                                </x-ui.button>
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Auto-refresh dashboard data every 5 minutes
    setInterval(function() {
        // Refresh new applications count
        fetch('{{ route("employer.dashboard.refresh") }}')
            .then(response => response.json())
            .then(data => {
                if (data.new_applications !== undefined) {
                    const newAppElement = document.querySelector('[data-stat="new_applications"]');
                    if (newAppElement) {
                        newAppElement.textContent = data.new_applications;
                    }
                }
            })
            .catch(error => console.error('Error refreshing dashboard:', error));
    }, 300000); // 5 minutes
});
</script>
@endpush 