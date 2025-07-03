@extends('layouts.app')

@section('title', __('admin.dashboard'))

@section('content')
<div class="min-h-screen bg-gray-50 dark:bg-gray-900">
    <!-- Header -->
    <div class="bg-white dark:bg-gray-800 shadow">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="py-6">
                <div class="flex items-center justify-between">
                    <div>
                        <h1 class="text-3xl font-bold text-gray-900 dark:text-white">
                            {{ __('admin.dashboard') }}
                        </h1>
                        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                            {{ __('admin.welcome_back_admin') }}, {{ auth()->user()->name }}
                        </p>
                    </div>
                    
                    <div class="flex items-center space-x-3">
                        <x-ui.button 
                            href="{{ route('admin.reports.generate') }}" 
                            variant="secondary"
                            icon="document-chart-bar"
                        >
                            {{ __('admin.generate_report') }}
                        </x-ui.button>
                        
                        <x-ui.button 
                            href="{{ route('admin.settings') }}" 
                            variant="primary"
                            icon="cog-6-tooth"
                        >
                            {{ __('admin.system_settings') }}
                        </x-ui.button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Quick Stats -->
        <div class="grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-4 mb-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow rounded-lg">
                <div class="p-5">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <x-icon name="users" class="h-6 w-6 text-blue-600 dark:text-blue-400" />
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400 truncate">
                                    {{ __('admin.total_users') }}
                                </dt>
                                <dd class="flex items-baseline">
                                    <div class="text-2xl font-semibold text-gray-900 dark:text-white">
                                        {{ number_format($stats['total_users'] ?? 0) }}
                                    </div>
                                    @if(isset($stats['users_change']))
                                        <div class="ml-2 flex items-baseline text-sm font-semibold {{ $stats['users_change'] >= 0 ? 'text-green-600' : 'text-red-600' }}">
                                            <x-icon name="{{ $stats['users_change'] >= 0 ? 'arrow-trending-up' : 'arrow-trending-down' }}" class="h-4 w-4 mr-1" />
                                            {{ abs($stats['users_change']) }}%
                                        </div>
                                    @endif
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
                            <x-icon name="briefcase" class="h-6 w-6 text-green-600 dark:text-green-400" />
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400 truncate">
                                    {{ __('admin.active_jobs') }}
                                </dt>
                                <dd class="flex items-baseline">
                                    <div class="text-2xl font-semibold text-gray-900 dark:text-white">
                                        {{ number_format($stats['active_jobs'] ?? 0) }}
                                    </div>
                                    @if(isset($stats['jobs_change']))
                                        <div class="ml-2 flex items-baseline text-sm font-semibold {{ $stats['jobs_change'] >= 0 ? 'text-green-600' : 'text-red-600' }}">
                                            <x-icon name="{{ $stats['jobs_change'] >= 0 ? 'arrow-trending-up' : 'arrow-trending-down' }}" class="h-4 w-4 mr-1" />
                                            {{ abs($stats['jobs_change']) }}%
                                        </div>
                                    @endif
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
                            <x-icon name="building-office" class="h-6 w-6 text-purple-600 dark:text-purple-400" />
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400 truncate">
                                    {{ __('admin.total_companies') }}
                                </dt>
                                <dd class="flex items-baseline">
                                    <div class="text-2xl font-semibold text-gray-900 dark:text-white">
                                        {{ number_format($stats['total_companies'] ?? 0) }}
                                    </div>
                                    @if(isset($stats['companies_change']))
                                        <div class="ml-2 flex items-baseline text-sm font-semibold {{ $stats['companies_change'] >= 0 ? 'text-green-600' : 'text-red-600' }}">
                                            <x-icon name="{{ $stats['companies_change'] >= 0 ? 'arrow-trending-up' : 'arrow-trending-down' }}" class="h-4 w-4 mr-1" />
                                            {{ abs($stats['companies_change']) }}%
                                        </div>
                                    @endif
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
                            <x-icon name="currency-dollar" class="h-6 w-6 text-yellow-600 dark:text-yellow-400" />
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400 truncate">
                                    {{ __('admin.monthly_revenue') }}
                                </dt>
                                <dd class="flex items-baseline">
                                    <div class="text-2xl font-semibold text-gray-900 dark:text-white">
                                        ${{ number_format($stats['monthly_revenue'] ?? 0) }}
                                    </div>
                                    @if(isset($stats['revenue_change']))
                                        <div class="ml-2 flex items-baseline text-sm font-semibold {{ $stats['revenue_change'] >= 0 ? 'text-green-600' : 'text-red-600' }}">
                                            <x-icon name="{{ $stats['revenue_change'] >= 0 ? 'arrow-trending-up' : 'arrow-trending-down' }}" class="h-4 w-4 mr-1" />
                                            {{ abs($stats['revenue_change']) }}%
                                        </div>
                                    @endif
                                </dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
            <!-- User Growth Chart -->
            <div class="bg-white dark:bg-gray-800 shadow rounded-lg">
                <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-white">
                        {{ __('admin.user_growth_trend') }}
                    </h3>
                </div>
                <div class="p-6">
                    <div class="h-64" id="user-growth-chart">
                        <!-- Chart will be rendered here -->
                        <div class="flex items-center justify-center h-full text-gray-500 dark:text-gray-400">
                            <div class="text-center">
                                <x-icon name="chart-bar" class="mx-auto h-12 w-12 mb-4" />
                                <p>{{ __('admin.chart_loading') }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Recent Activity -->
            <div class="bg-white dark:bg-gray-800 shadow rounded-lg">
                <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-white">
                        {{ __('admin.recent_activity') }}
                    </h3>
                </div>
                <div class="p-6">
                    @if($recentActivity && count($recentActivity) > 0)
                        <div class="flow-root">
                            <ul class="-mb-8">
                                @foreach($recentActivity as $index => $activity)
                                    <li>
                                        <div class="relative pb-8">
                                            @if($index < count($recentActivity) - 1)
                                                <span class="absolute top-4 left-4 -ml-px h-full w-0.5 bg-gray-200 dark:bg-gray-600" aria-hidden="true"></span>
                                            @endif
                                            <div class="relative flex space-x-3">
                                                <div>
                                                    <span class="h-8 w-8 rounded-full {{ $activity['color'] ?? 'bg-gray-400' }} flex items-center justify-center ring-8 ring-white dark:ring-gray-800">
                                                        <x-icon :name="$activity['icon'] ?? 'bell'" class="h-4 w-4 text-white" />
                                                    </span>
                                                </div>
                                                <div class="min-w-0 flex-1 pt-1.5 flex justify-between space-x-4">
                                                    <div>
                                                        <p class="text-sm text-gray-900 dark:text-white">
                                                            {{ $activity['description'] }}
                                                        </p>
                                                        @if(isset($activity['user']))
                                                            <p class="text-xs text-gray-500 dark:text-gray-400">
                                                                {{ __('admin.by') }} {{ $activity['user'] }}
                                                            </p>
                                                        @endif
                                                    </div>
                                                    <div class="text-right text-sm whitespace-nowrap text-gray-500 dark:text-gray-400">
                                                        {{ $activity['time'] }}
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    @else
                        <div class="text-center py-8">
                            <x-icon name="clock" class="mx-auto h-12 w-12 text-gray-400" />
                            <h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-white">
                                {{ __('admin.no_recent_activity') }}
                            </h3>
                            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                                {{ __('admin.activity_will_appear_here') }}
                            </p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- System Status & Quick Actions -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 mb-8">
            <!-- System Status -->
            <div class="bg-white dark:bg-gray-800 shadow rounded-lg">
                <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-white">
                        {{ __('admin.system_status') }}
                    </h3>
                </div>
                <div class="p-6">
                    <div class="space-y-4">
                        <div class="flex items-center justify-between">
                            <span class="text-sm text-gray-700 dark:text-gray-300">{{ __('admin.database') }}</span>
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $systemStatus['database'] === 'healthy' ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200' : 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200' }}">
                                {{ __('admin.status_' . ($systemStatus['database'] ?? 'unknown')) }}
                            </span>
                        </div>

                        <div class="flex items-center justify-between">
                            <span class="text-sm text-gray-700 dark:text-gray-300">{{ __('admin.cache') }}</span>
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $systemStatus['cache'] === 'healthy' ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200' : 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200' }}">
                                {{ __('admin.status_' . ($systemStatus['cache'] ?? 'unknown')) }}
                            </span>
                        </div>

                        <div class="flex items-center justify-between">
                            <span class="text-sm text-gray-700 dark:text-gray-300">{{ __('admin.queue') }}</span>
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $systemStatus['queue'] === 'healthy' ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200' : 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200' }}">
                                {{ __('admin.status_' . ($systemStatus['queue'] ?? 'unknown')) }}
                            </span>
                        </div>

                        <div class="flex items-center justify-between">
                            <span class="text-sm text-gray-700 dark:text-gray-300">{{ __('admin.storage') }}</span>
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $systemStatus['storage'] === 'healthy' ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200' : 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200' }}">
                                {{ __('admin.status_' . ($systemStatus['storage'] ?? 'unknown')) }}
                            </span>
                        </div>

                        <div class="pt-4 border-t border-gray-200 dark:border-gray-700">
                            <div class="flex items-center justify-between">
                                <span class="text-sm text-gray-700 dark:text-gray-300">{{ __('admin.server_load') }}</span>
                                <span class="text-sm font-medium text-gray-900 dark:text-white">
                                    {{ $systemStatus['server_load'] ?? '0.0' }}%
                                </span>
                            </div>
                            <div class="mt-2 w-full bg-gray-200 rounded-full h-2 dark:bg-gray-700">
                                <div class="bg-blue-600 h-2 rounded-full" style="width: {{ $systemStatus['server_load'] ?? 0 }}%"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="bg-white dark:bg-gray-800 shadow rounded-lg">
                <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-white">
                        {{ __('admin.quick_actions') }}
                    </h3>
                </div>
                <div class="p-6">
                    <div class="space-y-3">
                        <x-ui.button 
                            href="{{ route('admin.users.create') }}" 
                            variant="ghost" 
                            class="w-full justify-start"
                            icon="user-plus"
                        >
                            {{ __('admin.create_user') }}
                        </x-ui.button>

                        <x-ui.button 
                            href="{{ route('admin.jobs.pending') }}" 
                            variant="ghost" 
                            class="w-full justify-start"
                            icon="briefcase"
                        >
                            {{ __('admin.review_pending_jobs') }}
                        </x-ui.button>

                        <x-ui.button 
                            href="{{ route('admin.companies.verification') }}" 
                            variant="ghost" 
                            class="w-full justify-start"
                            icon="shield-check"
                        >
                            {{ __('admin.verify_companies') }}
                        </x-ui.button>

                        <x-ui.button 
                            href="{{ route('admin.reports.index') }}" 
                            variant="ghost" 
                            class="w-full justify-start"
                            icon="document-chart-bar"
                        >
                            {{ __('admin.view_reports') }}
                        </x-ui.button>

                        <x-ui.button 
                            href="{{ route('admin.maintenance') }}" 
                            variant="ghost" 
                            class="w-full justify-start"
                            icon="wrench-screwdriver"
                        >
                            {{ __('admin.maintenance_mode') }}
                        </x-ui.button>

                        <x-ui.button 
                            href="{{ route('admin.cache.clear') }}" 
                            variant="ghost" 
                            class="w-full justify-start"
                            icon="arrow-path"
                        >
                            {{ __('admin.clear_cache') }}
                        </x-ui.button>
                    </div>
                </div>
            </div>

            <!-- Pending Reviews -->
            <div class="bg-white dark:bg-gray-800 shadow rounded-lg">
                <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-white">
                        {{ __('admin.pending_reviews') }}
                    </h3>
                </div>
                <div class="p-6">
                    <div class="space-y-4">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center">
                                <x-icon name="briefcase" class="h-5 w-5 text-gray-400 mr-2" />
                                <span class="text-sm text-gray-700 dark:text-gray-300">{{ __('admin.pending_jobs') }}</span>
                            </div>
                            <div class="flex items-center">
                                <span class="text-sm font-medium text-gray-900 dark:text-white mr-2">
                                    {{ $pendingReviews['jobs'] ?? 0 }}
                                </span>
                                @if(($pendingReviews['jobs'] ?? 0) > 0)
                                    <x-ui.button 
                                        href="{{ route('admin.jobs.pending') }}" 
                                        variant="ghost" 
                                        size="sm"
                                    >
                                        {{ __('admin.review') }}
                                    </x-ui.button>
                                @endif
                            </div>
                        </div>

                        <div class="flex items-center justify-between">
                            <div class="flex items-center">
                                <x-icon name="building-office" class="h-5 w-5 text-gray-400 mr-2" />
                                <span class="text-sm text-gray-700 dark:text-gray-300">{{ __('admin.company_verifications') }}</span>
                            </div>
                            <div class="flex items-center">
                                <span class="text-sm font-medium text-gray-900 dark:text-white mr-2">
                                    {{ $pendingReviews['companies'] ?? 0 }}
                                </span>
                                @if(($pendingReviews['companies'] ?? 0) > 0)
                                    <x-ui.button 
                                        href="{{ route('admin.companies.verification') }}" 
                                        variant="ghost" 
                                        size="sm"
                                    >
                                        {{ __('admin.review') }}
                                    </x-ui.button>
                                @endif
                            </div>
                        </div>

                        <div class="flex items-center justify-between">
                            <div class="flex items-center">
                                <x-icon name="flag" class="h-5 w-5 text-gray-400 mr-2" />
                                <span class="text-sm text-gray-700 dark:text-gray-300">{{ __('admin.reported_content') }}</span>
                            </div>
                            <div class="flex items-center">
                                <span class="text-sm font-medium text-gray-900 dark:text-white mr-2">
                                    {{ $pendingReviews['reports'] ?? 0 }}
                                </span>
                                @if(($pendingReviews['reports'] ?? 0) > 0)
                                    <x-ui.button 
                                        href="{{ route('admin.reports.content') }}" 
                                        variant="ghost" 
                                        size="sm"
                                    >
                                        {{ __('admin.review') }}
                                    </x-ui.button>
                                @endif
                            </div>
                        </div>

                        <div class="flex items-center justify-between">
                            <div class="flex items-center">
                                <x-icon name="user-group" class="h-5 w-5 text-gray-400 mr-2" />
                                <span class="text-sm text-gray-700 dark:text-gray-300">{{ __('admin.user_appeals') }}</span>
                            </div>
                            <div class="flex items-center">
                                <span class="text-sm font-medium text-gray-900 dark:text-white mr-2">
                                    {{ $pendingReviews['appeals'] ?? 0 }}
                                </span>
                                @if(($pendingReviews['appeals'] ?? 0) > 0)
                                    <x-ui.button 
                                        href="{{ route('admin.appeals.index') }}" 
                                        variant="ghost" 
                                        size="sm"
                                    >
                                        {{ __('admin.review') }}
                                    </x-ui.button>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Users & Top Companies -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
            <!-- Recent Users -->
            <div class="bg-white dark:bg-gray-800 shadow rounded-lg">
                <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                    <div class="flex items-center justify-between">
                        <h3 class="text-lg font-medium text-gray-900 dark:text-white">
                            {{ __('admin.recent_users') }}
                        </h3>
                        <x-ui.button 
                            href="{{ route('admin.users.index') }}" 
                            variant="ghost" 
                            size="sm"
                        >
                            {{ __('admin.view_all') }}
                        </x-ui.button>
                    </div>
                </div>
                <div class="overflow-hidden">
                    @if($recentUsers && count($recentUsers) > 0)
                        <ul class="divide-y divide-gray-200 dark:divide-gray-700">
                            @foreach($recentUsers as $user)
                                <li class="px-6 py-4 hover:bg-gray-50 dark:hover:bg-gray-700">
                                    <div class="flex items-center justify-between">
                                        <div class="flex items-center">
                                            <div class="flex-shrink-0 h-10 w-10">
                                                @if($user['avatar'])
                                                    <img class="h-10 w-10 rounded-full" src="{{ $user['avatar'] }}" alt="{{ $user['name'] }}">
                                                @else
                                                    <div class="h-10 w-10 rounded-full bg-gray-300 dark:bg-gray-600 flex items-center justify-center">
                                                        <span class="text-sm font-medium text-gray-700 dark:text-gray-300">
                                                            {{ substr($user['name'], 0, 1) }}
                                                        </span>
                                                    </div>
                                                @endif
                                            </div>
                                            <div class="ml-4">
                                                <div class="text-sm font-medium text-gray-900 dark:text-white">
                                                    {{ $user['name'] }}
                                                </div>
                                                <div class="text-sm text-gray-500 dark:text-gray-400">
                                                    {{ $user['email'] }}
                                                </div>
                                            </div>
                                        </div>
                                        <div class="text-right">
                                            <div class="text-sm text-gray-900 dark:text-white">
                                                {{ $user['role'] }}
                                            </div>
                                            <div class="text-sm text-gray-500 dark:text-gray-400">
                                                {{ $user['joined'] }}
                                            </div>
                                        </div>
                                    </div>
                                </li>
                            @endforeach
                        </ul>
                    @else
                        <div class="px-6 py-8 text-center">
                            <x-icon name="users" class="mx-auto h-12 w-12 text-gray-400" />
                            <h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-white">
                                {{ __('admin.no_recent_users') }}
                            </h3>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Top Companies -->
            <div class="bg-white dark:bg-gray-800 shadow rounded-lg">
                <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                    <div class="flex items-center justify-between">
                        <h3 class="text-lg font-medium text-gray-900 dark:text-white">
                            {{ __('admin.top_companies') }}
                        </h3>
                        <x-ui.button 
                            href="{{ route('admin.companies.index') }}" 
                            variant="ghost" 
                            size="sm"
                        >
                            {{ __('admin.view_all') }}
                        </x-ui.button>
                    </div>
                </div>
                <div class="overflow-hidden">
                    @if($topCompanies && count($topCompanies) > 0)
                        <ul class="divide-y divide-gray-200 dark:divide-gray-700">
                            @foreach($topCompanies as $company)
                                <li class="px-6 py-4 hover:bg-gray-50 dark:hover:bg-gray-700">
                                    <div class="flex items-center justify-between">
                                        <div class="flex items-center">
                                            <div class="flex-shrink-0 h-10 w-10">
                                                @if($company['logo'])
                                                    <img class="h-10 w-10 rounded-lg object-cover" src="{{ $company['logo'] }}" alt="{{ $company['name'] }}">
                                                @else
                                                    <div class="h-10 w-10 rounded-lg bg-gray-300 dark:bg-gray-600 flex items-center justify-center">
                                                        <x-icon name="building-office" class="h-6 w-6 text-gray-500 dark:text-gray-400" />
                                                    </div>
                                                @endif
                                            </div>
                                            <div class="ml-4">
                                                <div class="text-sm font-medium text-gray-900 dark:text-white">
                                                    {{ $company['name'] }}
                                                </div>
                                                <div class="text-sm text-gray-500 dark:text-gray-400">
                                                    {{ $company['industry'] }}
                                                </div>
                                            </div>
                                        </div>
                                        <div class="text-right">
                                            <div class="text-sm font-medium text-gray-900 dark:text-white">
                                                {{ $company['active_jobs'] }} {{ __('admin.jobs') }}
                                            </div>
                                            <div class="text-sm text-gray-500 dark:text-gray-400">
                                                {{ $company['total_applications'] }} {{ __('admin.applications') }}
                                            </div>
                                        </div>
                                    </div>
                                </li>
                            @endforeach
                        </ul>
                    @else
                        <div class="px-6 py-8 text-center">
                            <x-icon name="building-office" class="mx-auto h-12 w-12 text-gray-400" />
                            <h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-white">
                                {{ __('admin.no_companies') }}
                            </h3>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- System Alerts -->
        @if($systemAlerts && count($systemAlerts) > 0)
            <div class="bg-white dark:bg-gray-800 shadow rounded-lg">
                <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-white">
                        {{ __('admin.system_alerts') }}
                    </h3>
                </div>
                <div class="p-6">
                    <div class="space-y-4">
                        @foreach($systemAlerts as $alert)
                            <div class="flex items-start space-x-3 p-4 rounded-lg {{ $alert['type'] === 'error' ? 'bg-red-50 dark:bg-red-900/20' : ($alert['type'] === 'warning' ? 'bg-yellow-50 dark:bg-yellow-900/20' : 'bg-blue-50 dark:bg-blue-900/20') }}">
                                <div class="flex-shrink-0">
                                    <x-icon 
                                        :name="$alert['type'] === 'error' ? 'exclamation-triangle' : ($alert['type'] === 'warning' ? 'exclamation-triangle' : 'information-circle')" 
                                        class="h-5 w-5 {{ $alert['type'] === 'error' ? 'text-red-600 dark:text-red-400' : ($alert['type'] === 'warning' ? 'text-yellow-600 dark:text-yellow-400' : 'text-blue-600 dark:text-blue-400') }}" 
                                    />
                                </div>
                                <div class="flex-1 min-w-0">
                                    <h4 class="text-sm font-medium text-gray-900 dark:text-white">
                                        {{ $alert['title'] }}
                                    </h4>
                                    <p class="mt-1 text-sm text-gray-700 dark:text-gray-300">
                                        {{ $alert['message'] }}
                                    </p>
                                    @if(isset($alert['action_url']))
                                        <div class="mt-2">
                                            <a href="{{ $alert['action_url'] }}" class="text-sm font-medium {{ $alert['type'] === 'error' ? 'text-red-600 hover:text-red-500 dark:text-red-400' : ($alert['type'] === 'warning' ? 'text-yellow-600 hover:text-yellow-500 dark:text-yellow-400' : 'text-blue-600 hover:text-blue-500 dark:text-blue-400') }}">
                                                {{ $alert['action_text'] }} →
                                            </a>
                                        </div>
                                    @endif
                                </div>
                                <div class="flex-shrink-0">
                                    <span class="text-xs text-gray-500 dark:text-gray-400">
                                        {{ $alert['time'] }}
                                    </span>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        @endif
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Initialize charts
    initializeUserGrowthChart();
    
    // Auto-refresh dashboard every 5 minutes
    setInterval(refreshDashboard, 300000);
});

function initializeUserGrowthChart() {
    const ctx = document.getElementById('user-growth-chart');
    if (!ctx) return;
    
    // Sample data - replace with actual data from backend
    const chartData = @json($userGrowthData ?? []);
    
    new Chart(ctx, {
        type: 'line',
        data: {
            labels: chartData.labels || [],
            datasets: [
                {
                    label: '{{ __("admin.candidates") }}',
                    data: chartData.candidates || [],
                    borderColor: 'rgb(59, 130, 246)',
                    backgroundColor: 'rgba(59, 130, 246, 0.1)',
                    tension: 0.4
                },
                {
                    label: '{{ __("admin.employers") }}',
                    data: chartData.employers || [],
                    borderColor: 'rgb(16, 185, 129)',
                    backgroundColor: 'rgba(16, 185, 129, 0.1)',
                    tension: 0.4
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'top',
                },
                title: {
                    display: false
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    grid: {
                        color: 'rgba(156, 163, 175, 0.2)'
                    }
                },
                x: {
                    grid: {
                        color: 'rgba(156, 163, 175, 0.2)'
                    }
                }
            }
        }
    });
}

function refreshDashboard() {
    // Refresh dashboard data without page reload
    fetch(window.location.href, {
        headers: {
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(response => response.json())
    .then(data => {
        // Update stats
        updateStats(data.stats);
        
        // Update charts
        updateCharts(data.chartData);
        
        // Update system status
        updateSystemStatus(data.systemStatus);
        
        console.log('Dashboard data refreshed');
    })
    .catch(error => {
        console.error('Error refreshing dashboard:', error);
    });
}

function updateStats(stats) {
    // Update stat values in the UI
    Object.keys(stats).forEach(key => {
        const element = document.querySelector(`[data-stat="${key}"]`);
        if (element) {
            element.textContent = stats[key];
        }
    });
}

function updateCharts(chartData) {
    // Update chart data
    if (window.userGrowthChart) {
        window.userGrowthChart.data.labels = chartData.labels;
        window.userGrowthChart.data.datasets[0].data = chartData.candidates;
        window.userGrowthChart.data.datasets[1].data = chartData.employers;
        window.userGrowthChart.update();
    }
}

function updateSystemStatus(systemStatus) {
    // Update system status indicators
    Object.keys(systemStatus).forEach(key => {
        const element = document.querySelector(`[data-status="${key}"]`);
        if (element) {
            // Update status badge colors and text
            element.className = systemStatus[key] === 'healthy' 
                ? 'inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200'
                : 'inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200';
            element.textContent = systemStatus[key];
        }
    });
}
</script>
@endpush 