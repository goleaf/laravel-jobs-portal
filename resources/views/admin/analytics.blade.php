@extends('layouts.app')

@section('title', __('admin.analytics_dashboard'))

@section('content')
<div class="min-h-screen bg-gray-50 dark:bg-gray-900 py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="mb-8">
            <div class="sm:flex sm:items-center sm:justify-between">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900 dark:text-white">
                        {{ __('admin.analytics_dashboard') }}
                    </h1>
                    <p class="mt-2 text-gray-600 dark:text-gray-400">
                        {{ __('admin.comprehensive_system_insights') }}
                    </p>
                </div>
                
                <div class="mt-4 sm:mt-0 flex space-x-3">
                    <x-ui.button 
                        href="{{ route('admin.analytics.export') }}" 
                        variant="secondary"
                        icon="arrow-down-tray"
                    >
                        {{ __('admin.export_analytics') }}
                    </x-ui.button>
                    
                    <x-ui.button 
                        href="{{ route('admin.dashboard') }}" 
                        variant="primary"
                        icon="home"
                    >
                        {{ __('admin.back_to_dashboard') }}
                    </x-ui.button>
                </div>
            </div>
        </div>

        <!-- Time Period Filter -->
        <div class="bg-white dark:bg-gray-800 shadow rounded-lg mb-8">
            <div class="px-6 py-4">
                <form method="GET" action="{{ route('admin.analytics') }}" class="flex flex-wrap items-end gap-4">
                    <!-- Date Range -->
                    <div class="flex-1 min-w-0">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                            {{ __('admin.date_range') }}
                        </label>
                        <x-ui.select
                            name="period"
                            id="period"
                            :options="[
                                'today' => __('admin.today'),
                                'week' => __('admin.this_week'),
                                'month' => __('admin.this_month'),
                                '3months' => __('admin.last_3_months'),
                                '6months' => __('admin.last_6_months'),
                                'year' => __('admin.this_year'),
                                'custom' => __('admin.custom_range')
                            ]"
                            :selected="request('period', 'month')"
                            data-auto-submit
                        />
                    </div>

                    <!-- Metric Type -->
                    <div class="w-48">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                            {{ __('admin.metric_type') }}
                        </label>
                        <x-ui.select
                            name="metric_type"
                            id="metric_type"
                            :options="[
                                'all' => __('admin.all_metrics'),
                                'users' => __('admin.user_metrics'),
                                'jobs' => __('admin.job_metrics'),
                                'revenue' => __('admin.revenue_metrics'),
                                'performance' => __('admin.performance_metrics')
                            ]"
                            :selected="request('metric_type', 'all')"
                            data-auto-submit
                        />
                    </div>

                    <!-- Compare Toggle -->
                    <div class="flex items-center">
                        <input 
                            id="compare_previous" 
                            name="compare_previous" 
                            type="checkbox" 
                            value="1"
                            {{ request('compare_previous') ? 'checked' : '' }}
                            class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded"
                            data-auto-submit
                        >
                        <label for="compare_previous" class="ml-2 block text-sm text-gray-900 dark:text-gray-300">
                            {{ __('admin.compare_previous_period') }}
                        </label>
                    </div>
                </form>
            </div>
        </div>

        <!-- Key Performance Indicators -->
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
                                    {{ __('admin.daily_active_users') }}
                                </dt>
                                <dd class="flex items-baseline">
                                    <div class="text-2xl font-semibold text-gray-900 dark:text-white">
                                        {{ number_format($kpis['daily_active_users'] ?? 0) }}
                                    </div>
                                    @if(isset($kpis['dau_change']))
                                        <div class="ml-2 flex items-baseline text-sm font-semibold {{ $kpis['dau_change'] >= 0 ? 'text-green-600' : 'text-red-600' }}">
                                            <x-icon name="{{ $kpis['dau_change'] >= 0 ? 'arrow-trending-up' : 'arrow-trending-down' }}" class="h-4 w-4 mr-1" />
                                            {{ abs($kpis['dau_change']) }}%
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
                            <x-icon name="chart-bar" class="h-6 w-6 text-green-600 dark:text-green-400" />
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400 truncate">
                                    {{ __('admin.conversion_rate') }}
                                </dt>
                                <dd class="flex items-baseline">
                                    <div class="text-2xl font-semibold text-gray-900 dark:text-white">
                                        {{ number_format($kpis['conversion_rate'] ?? 0, 1) }}%
                                    </div>
                                    @if(isset($kpis['conversion_change']))
                                        <div class="ml-2 flex items-baseline text-sm font-semibold {{ $kpis['conversion_change'] >= 0 ? 'text-green-600' : 'text-red-600' }}">
                                            <x-icon name="{{ $kpis['conversion_change'] >= 0 ? 'arrow-trending-up' : 'arrow-trending-down' }}" class="h-4 w-4 mr-1" />
                                            {{ abs($kpis['conversion_change']) }}%
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
                            <x-icon name="currency-dollar" class="h-6 w-6 text-purple-600 dark:text-purple-400" />
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400 truncate">
                                    {{ __('admin.average_revenue_per_user') }}
                                </dt>
                                <dd class="flex items-baseline">
                                    <div class="text-2xl font-semibold text-gray-900 dark:text-white">
                                        ${{ number_format($kpis['arpu'] ?? 0, 2) }}
                                    </div>
                                    @if(isset($kpis['arpu_change']))
                                        <div class="ml-2 flex items-baseline text-sm font-semibold {{ $kpis['arpu_change'] >= 0 ? 'text-green-600' : 'text-red-600' }}">
                                            <x-icon name="{{ $kpis['arpu_change'] >= 0 ? 'arrow-trending-up' : 'arrow-trending-down' }}" class="h-4 w-4 mr-1" />
                                            {{ abs($kpis['arpu_change']) }}%
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
                            <x-icon name="clock" class="h-6 w-6 text-yellow-600 dark:text-yellow-400" />
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400 truncate">
                                    {{ __('admin.average_session_duration') }}
                                </dt>
                                <dd class="flex items-baseline">
                                    <div class="text-2xl font-semibold text-gray-900 dark:text-white">
                                        {{ $kpis['avg_session_duration'] ?? '0m' }}
                                    </div>
                                    @if(isset($kpis['session_change']))
                                        <div class="ml-2 flex items-baseline text-sm font-semibold {{ $kpis['session_change'] >= 0 ? 'text-green-600' : 'text-red-600' }}">
                                            <x-icon name="{{ $kpis['session_change'] >= 0 ? 'arrow-trending-up' : 'arrow-trending-down' }}" class="h-4 w-4 mr-1" />
                                            {{ abs($kpis['session_change']) }}%
                                        </div>
                                    @endif
                                </dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Main Analytics Charts -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
            <!-- User Growth & Engagement -->
            <div class="bg-white dark:bg-gray-800 shadow rounded-lg">
                <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-white">
                        {{ __('admin.user_growth_engagement') }}
                    </h3>
                </div>
                <div class="p-6">
                    <div class="h-64" id="user-engagement-chart" data-chart='@json($userEngagementData ?? [])'>
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

            <!-- Revenue Analytics -->
            <div class="bg-white dark:bg-gray-800 shadow rounded-lg">
                <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-white">
                        {{ __('admin.revenue_analytics') }}
                    </h3>
                </div>
                <div class="p-6">
                    <div class="h-64" id="revenue-chart" data-chart='@json($revenueData ?? [])'>
                        <!-- Chart will be rendered here -->
                        <div class="flex items-center justify-center h-full text-gray-500 dark:text-gray-400">
                            <div class="text-center">
                                <x-icon name="currency-dollar" class="mx-auto h-12 w-12 mb-4" />
                                <p>{{ __('admin.chart_loading') }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Detailed Analytics -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 mb-8">
            <!-- User Demographics -->
            <div class="bg-white dark:bg-gray-800 shadow rounded-lg">
                <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-white">
                        {{ __('admin.user_demographics') }}
                    </h3>
                </div>
                <div class="p-6">
                    @if($demographics && count($demographics) > 0)
                        <div class="space-y-4">
                            @foreach($demographics as $demo)
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center space-x-3">
                                        <div class="flex-shrink-0">
                                            <div class="h-3 w-3 rounded-full" style="background-color: {{ $demo['color'] }}"></div>
                                        </div>
                                        <span class="text-sm text-gray-900 dark:text-white">{{ $demo['label'] }}</span>
                                    </div>
                                    <div class="text-right">
                                        <div class="text-sm font-medium text-gray-900 dark:text-white">
                                            {{ $demo['percentage'] }}%
                                        </div>
                                        <div class="text-xs text-gray-500 dark:text-gray-400">
                                            {{ number_format($demo['count']) }} {{ __('admin.users') }}
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-8">
                            <x-icon name="user-group" class="mx-auto h-8 w-8 text-gray-400" />
                            <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">
                                {{ __('admin.no_demographic_data') }}
                            </p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Top Job Categories -->
            <div class="bg-white dark:bg-gray-800 shadow rounded-lg">
                <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-white">
                        {{ __('admin.top_job_categories') }}
                    </h3>
                </div>
                <div class="p-6">
                    @if($topCategories && count($topCategories) > 0)
                        <div class="space-y-3">
                            @foreach($topCategories as $category)
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center space-x-2">
                                        <x-icon name="briefcase" class="h-4 w-4 text-gray-400" />
                                        <span class="text-sm text-gray-900 dark:text-white">{{ $category['name'] }}</span>
                                    </div>
                                    <div class="text-right">
                                        <div class="text-sm font-medium text-gray-900 dark:text-white">
                                            {{ $category['job_count'] }}
                                        </div>
                                        <div class="text-xs text-gray-500 dark:text-gray-400">
                                            {{ $category['application_count'] }} {{ __('admin.applications') }}
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-8">
                            <x-icon name="briefcase" class="mx-auto h-8 w-8 text-gray-400" />
                            <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">
                                {{ __('admin.no_category_data') }}
                            </p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Geographic Distribution -->
            <div class="bg-white dark:bg-gray-800 shadow rounded-lg">
                <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-white">
                        {{ __('admin.geographic_distribution') }}
                    </h3>
                </div>
                <div class="p-6">
                    @if($geoDistribution && count($geoDistribution) > 0)
                        <div class="space-y-3">
                            @foreach($geoDistribution as $location)
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center space-x-2">
                                        <x-icon name="map-pin" class="h-4 w-4 text-gray-400" />
                                        <span class="text-sm text-gray-900 dark:text-white">{{ $location['name'] }}</span>
                                    </div>
                                    <div class="text-right">
                                        <div class="text-sm font-medium text-gray-900 dark:text-white">
                                            {{ $location['percentage'] }}%
                                        </div>
                                        <div class="text-xs text-gray-500 dark:text-gray-400">
                                            {{ $location['user_count'] }} {{ __('admin.users') }}
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-8">
                            <x-icon name="map" class="mx-auto h-8 w-8 text-gray-400" />
                            <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">
                                {{ __('admin.no_geographic_data') }}
                            </p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Performance Metrics -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
            <!-- System Performance -->
            <div class="bg-white dark:bg-gray-800 shadow rounded-lg">
                <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-white">
                        {{ __('admin.system_performance') }}
                    </h3>
                </div>
                <div class="p-6">
                    <div class="space-y-4">
                        <div class="flex items-center justify-between">
                            <span class="text-sm text-gray-900 dark:text-white">{{ __('admin.average_page_load_time') }}</span>
                            <div class="text-right">
                                <div class="text-sm font-medium text-gray-900 dark:text-white">
                                    {{ $performance['avg_page_load'] ?? '0.0' }}s
                                </div>
                                <div class="text-xs text-gray-500 dark:text-gray-400">
                                    {{ __('admin.target_under_2s') }}
                                </div>
                            </div>
                        </div>

                        <div class="flex items-center justify-between">
                            <span class="text-sm text-gray-900 dark:text-white">{{ __('admin.server_response_time') }}</span>
                            <div class="text-sm font-medium text-gray-900 dark:text-white">
                                {{ $performance['server_response_time'] ?? '0' }}ms
                            </div>
                        </div>

                        <div class="flex items-center justify-between">
                            <span class="text-sm text-gray-900 dark:text-white">{{ __('admin.database_query_time') }}</span>
                            <div class="text-sm font-medium text-gray-900 dark:text-white">
                                {{ $performance['db_query_time'] ?? '0' }}ms
                            </div>
                        </div>

                        <div class="flex items-center justify-between">
                            <span class="text-sm text-gray-900 dark:text-white">{{ __('admin.cache_hit_rate') }}</span>
                            <div class="text-sm font-medium text-blue-600 dark:text-blue-400">
                                {{ $performance['cache_hit_rate'] ?? '0' }}%
                            </div>
                        </div>

                        <div class="flex items-center justify-between">
                            <span class="text-sm text-gray-900 dark:text-white">{{ __('admin.error_rate') }}</span>
                            <div class="text-sm font-medium {{ ($performance['error_rate'] ?? 0) > 1 ? 'text-red-600 dark:text-red-400' : 'text-green-600 dark:text-green-400' }}">
                                {{ $performance['error_rate'] ?? '0' }}%
                            </div>
                        </div>

                        <div class="flex items-center justify-between">
                            <span class="text-sm text-gray-900 dark:text-white">{{ __('admin.uptime') }}</span>
                            <div class="text-sm font-medium text-green-600 dark:text-green-400">
                                {{ $performance['uptime'] ?? '99.9' }}%
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- User Behavior Analytics -->
            <div class="bg-white dark:bg-gray-800 shadow rounded-lg">
                <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-white">
                        {{ __('admin.user_behavior_analytics') }}
                    </h3>
                </div>
                <div class="p-6">
                    <div class="space-y-4">
                        <div class="flex items-center justify-between">
                            <span class="text-sm text-gray-900 dark:text-white">{{ __('admin.bounce_rate') }}</span>
                            <div class="text-sm font-medium text-gray-900 dark:text-white">
                                {{ $userBehavior['bounce_rate'] ?? '0' }}%
                            </div>
                        </div>

                        <div class="flex items-center justify-between">
                            <span class="text-sm text-gray-900 dark:text-white">{{ __('admin.pages_per_session') }}</span>
                            <div class="text-sm font-medium text-gray-900 dark:text-white">
                                {{ $userBehavior['pages_per_session'] ?? '0' }}
                            </div>
                        </div>

                        <div class="flex items-center justify-between">
                            <span class="text-sm text-gray-900 dark:text-white">{{ __('admin.new_vs_returning') }}</span>
                            <div class="text-right">
                                <div class="text-sm font-medium text-gray-900 dark:text-white">
                                    {{ $userBehavior['new_users_percentage'] ?? '0' }}% / {{ $userBehavior['returning_users_percentage'] ?? '0' }}%
                                </div>
                                <div class="text-xs text-gray-500 dark:text-gray-400">
                                    {{ __('admin.new_returning') }}
                                </div>
                            </div>
                        </div>

                        <div class="flex items-center justify-between">
                            <span class="text-sm text-gray-900 dark:text-white">{{ __('admin.mobile_vs_desktop') }}</span>
                            <div class="text-right">
                                <div class="text-sm font-medium text-gray-900 dark:text-white">
                                    {{ $userBehavior['mobile_percentage'] ?? '0' }}% / {{ $userBehavior['desktop_percentage'] ?? '0' }}%
                                </div>
                                <div class="text-xs text-gray-500 dark:text-gray-400">
                                    {{ __('admin.mobile_desktop') }}
                                </div>
                            </div>
                        </div>

                        <div class="flex items-center justify-between">
                            <span class="text-sm text-gray-900 dark:text-white">{{ __('admin.top_traffic_source') }}</span>
                            <div class="text-sm font-medium text-blue-600 dark:text-blue-400">
                                {{ $userBehavior['top_traffic_source'] ?? __('admin.direct') }}
                            </div>
                        </div>

                        <div class="flex items-center justify-between">
                            <span class="text-sm text-gray-900 dark:text-white">{{ __('admin.user_retention_rate') }}</span>
                            <div class="text-sm font-medium text-green-600 dark:text-green-400">
                                {{ $userBehavior['retention_rate'] ?? '0' }}%
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Detailed Data Tables -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
            <!-- Top Performing Pages -->
            <div class="bg-white dark:bg-gray-800 shadow rounded-lg">
                <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-white">
                        {{ __('admin.top_performing_pages') }}
                    </h3>
                </div>
                <div class="overflow-hidden">
                    @if($topPages && count($topPages) > 0)
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                                <thead class="bg-gray-50 dark:bg-gray-700">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                            {{ __('admin.page') }}
                                        </th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                            {{ __('admin.views') }}
                                        </th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                            {{ __('admin.avg_time') }}
                                        </th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                    @foreach($topPages as $page)
                                        <tr>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                                                {{ $page['path'] }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                                                {{ number_format($page['views']) }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                                                {{ $page['avg_time'] }}
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="px-6 py-8 text-center">
                            <x-icon name="document" class="mx-auto h-12 w-12 text-gray-400" />
                            <h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-white">
                                {{ __('admin.no_page_data') }}
                            </h3>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Recent Search Terms -->
            <div class="bg-white dark:bg-gray-800 shadow rounded-lg">
                <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-white">
                        {{ __('admin.popular_search_terms') }}
                    </h3>
                </div>
                <div class="overflow-hidden">
                    @if($searchTerms && count($searchTerms) > 0)
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                                <thead class="bg-gray-50 dark:bg-gray-700">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                            {{ __('admin.search_term') }}
                                        </th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                            {{ __('admin.searches') }}
                                        </th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                            {{ __('admin.results') }}
                                        </th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                    @foreach($searchTerms as $term)
                                        <tr>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                                                {{ $term['term'] }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                                                {{ number_format($term['count']) }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                                                {{ number_format($term['results']) }}
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="px-6 py-8 text-center">
                            <x-icon name="magnifying-glass" class="mx-auto h-12 w-12 text-gray-400" />
                            <h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-white">
                                {{ __('admin.no_search_data') }}
                            </h3>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
{{-- Analytics scripts bundled via resources/js/app.js --}}
@endpush