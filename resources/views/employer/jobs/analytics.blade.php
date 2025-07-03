@extends('layouts.app')

@section('title', __('jobs.job_analytics'))

@section('content')
<div class="min-h-screen bg-gray-50 dark:bg-gray-900 py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="mb-8">
            <div class="sm:flex sm:items-center sm:justify-between">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900 dark:text-white">
                        {{ __('jobs.job_analytics') }}
                    </h1>
                    <p class="mt-2 text-gray-600 dark:text-gray-400">
                        {{ __('jobs.track_performance_optimize_postings') }}
                    </p>
                </div>
                
                <div class="mt-4 sm:mt-0 flex space-x-3">
                    <x-ui.button 
                        href="{{ route('employer.jobs.analytics.export') }}" 
                        variant="secondary"
                        icon="arrow-down-tray"
                    >
                        {{ __('jobs.export_report') }}
                    </x-ui.button>
                    
                    <x-ui.button 
                        href="{{ route('employer.jobs.create') }}" 
                        variant="primary"
                        icon="plus"
                    >
                        {{ __('jobs.post_new_job') }}
                    </x-ui.button>
                </div>
            </div>
        </div>

        <!-- Time Period Filter -->
        <div class="bg-white dark:bg-gray-800 shadow rounded-lg mb-8">
            <div class="px-6 py-4">
                <form method="GET" action="{{ route('employer.jobs.analytics') }}" class="flex flex-wrap items-end gap-4">
                    <!-- Date Range -->
                    <div class="flex-1 min-w-0">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                            {{ __('jobs.date_range') }}
                        </label>
                        <x-ui.select
                            name="period"
                            id="period"
                            :options="[
                                'today' => __('jobs.today'),
                                'week' => __('jobs.this_week'),
                                'month' => __('jobs.this_month'),
                                '3months' => __('jobs.last_3_months'),
                                '6months' => __('jobs.last_6_months'),
                                'year' => __('jobs.this_year'),
                                'custom' => __('jobs.custom_range')
                            ]"
                            :selected="request('period', 'month')"
                            onchange="this.form.submit()"
                        />
                    </div>

                    <!-- Job Filter -->
                    <div class="w-64">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                            {{ __('jobs.specific_job') }}
                        </label>
                        <x-ui.select
                            name="job_id"
                            id="job_id"
                            :options="$jobs ?? []"
                            :selected="request('job_id')"
                            searchable="true"
                            onchange="this.form.submit()"
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
                            onchange="this.form.submit()"
                        >
                        <label for="compare_previous" class="ml-2 block text-sm text-gray-900 dark:text-gray-300">
                            {{ __('jobs.compare_previous_period') }}
                        </label>
                    </div>
                </form>
            </div>
        </div>

        <!-- Key Metrics Overview -->
        <div class="grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-4 mb-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow rounded-lg">
                <div class="p-5">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <x-icon name="eye" class="h-6 w-6 text-blue-600 dark:text-blue-400" />
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400 truncate">
                                    {{ __('jobs.total_views') }}
                                </dt>
                                <dd class="flex items-baseline">
                                    <div class="text-2xl font-semibold text-gray-900 dark:text-white">
                                        {{ number_format($metrics['total_views'] ?? 0) }}
                                    </div>
                                    @if(isset($metrics['views_change']))
                                        <div class="ml-2 flex items-baseline text-sm font-semibold {{ $metrics['views_change'] >= 0 ? 'text-green-600' : 'text-red-600' }}">
                                            <x-icon name="{{ $metrics['views_change'] >= 0 ? 'arrow-trending-up' : 'arrow-trending-down' }}" class="h-4 w-4 mr-1" />
                                            {{ abs($metrics['views_change']) }}%
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
                            <x-icon name="document-text" class="h-6 w-6 text-green-600 dark:text-green-400" />
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400 truncate">
                                    {{ __('jobs.total_applications') }}
                                </dt>
                                <dd class="flex items-baseline">
                                    <div class="text-2xl font-semibold text-gray-900 dark:text-white">
                                        {{ number_format($metrics['total_applications'] ?? 0) }}
                                    </div>
                                    @if(isset($metrics['applications_change']))
                                        <div class="ml-2 flex items-baseline text-sm font-semibold {{ $metrics['applications_change'] >= 0 ? 'text-green-600' : 'text-red-600' }}">
                                            <x-icon name="{{ $metrics['applications_change'] >= 0 ? 'arrow-trending-up' : 'arrow-trending-down' }}" class="h-4 w-4 mr-1" />
                                            {{ abs($metrics['applications_change']) }}%
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
                            <x-icon name="calculator" class="h-6 w-6 text-purple-600 dark:text-purple-400" />
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400 truncate">
                                    {{ __('jobs.conversion_rate') }}
                                </dt>
                                <dd class="flex items-baseline">
                                    <div class="text-2xl font-semibold text-gray-900 dark:text-white">
                                        {{ number_format($metrics['conversion_rate'] ?? 0, 1) }}%
                                    </div>
                                    @if(isset($metrics['conversion_change']))
                                        <div class="ml-2 flex items-baseline text-sm font-semibold {{ $metrics['conversion_change'] >= 0 ? 'text-green-600' : 'text-red-600' }}">
                                            <x-icon name="{{ $metrics['conversion_change'] >= 0 ? 'arrow-trending-up' : 'arrow-trending-down' }}" class="h-4 w-4 mr-1" />
                                            {{ abs($metrics['conversion_change']) }}%
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
                            <x-icon name="star" class="h-6 w-6 text-yellow-600 dark:text-yellow-400" />
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400 truncate">
                                    {{ __('jobs.quality_score') }}
                                </dt>
                                <dd class="flex items-baseline">
                                    <div class="text-2xl font-semibold text-gray-900 dark:text-white">
                                        {{ number_format($metrics['quality_score'] ?? 0, 1) }}/10
                                    </div>
                                    @if(isset($metrics['quality_change']))
                                        <div class="ml-2 flex items-baseline text-sm font-semibold {{ $metrics['quality_change'] >= 0 ? 'text-green-600' : 'text-red-600' }}">
                                            <x-icon name="{{ $metrics['quality_change'] >= 0 ? 'arrow-trending-up' : 'arrow-trending-down' }}" class="h-4 w-4 mr-1" />
                                            {{ abs($metrics['quality_change']) }}
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
            <!-- Views & Applications Chart -->
            <div class="bg-white dark:bg-gray-800 shadow rounded-lg">
                <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-white">
                        {{ __('jobs.views_applications_trend') }}
                    </h3>
                </div>
                <div class="p-6">
                    <div class="h-64" id="views-applications-chart">
                        <!-- Chart will be rendered here -->
                        <div class="flex items-center justify-center h-full text-gray-500 dark:text-gray-400">
                            <div class="text-center">
                                <x-icon name="chart-bar" class="mx-auto h-12 w-12 mb-4" />
                                <p>{{ __('jobs.chart_loading') }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Top Performing Jobs -->
            <div class="bg-white dark:bg-gray-800 shadow rounded-lg">
                <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-white">
                        {{ __('jobs.top_performing_jobs') }}
                    </h3>
                </div>
                <div class="p-6">
                    @if($topJobs && count($topJobs) > 0)
                        <div class="space-y-4">
                            @foreach($topJobs as $index => $job)
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center space-x-3">
                                        <div class="flex-shrink-0">
                                            <span class="inline-flex items-center justify-center h-8 w-8 rounded-full {{ $index === 0 ? 'bg-yellow-100 text-yellow-800' : ($index === 1 ? 'bg-gray-100 text-gray-800' : 'bg-orange-100 text-orange-800') }} text-sm font-medium">
                                                {{ $index + 1 }}
                                            </span>
                                        </div>
                                        <div class="flex-1 min-w-0">
                                            <h4 class="text-sm font-medium text-gray-900 dark:text-white truncate">
                                                <a href="{{ route('jobs.show', $job) }}" class="hover:text-blue-600 dark:hover:text-blue-400">
                                                    {{ $job['title'] }}
                                                </a>
                                            </h4>
                                            <p class="text-xs text-gray-500 dark:text-gray-400">
                                                {{ $job['views'] }} {{ __('jobs.views') }} • {{ $job['applications'] }} {{ __('jobs.applications') }}
                                            </p>
                                        </div>
                                    </div>
                                    <div class="text-right">
                                        <div class="text-sm font-medium text-gray-900 dark:text-white">
                                            {{ number_format($job['conversion_rate'], 1) }}%
                                        </div>
                                        <div class="text-xs text-gray-500 dark:text-gray-400">
                                            {{ __('jobs.conversion') }}
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-8">
                            <x-icon name="chart-bar" class="mx-auto h-12 w-12 text-gray-400" />
                            <h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-white">
                                {{ __('jobs.no_performance_data') }}
                            </h3>
                            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                                {{ __('jobs.post_jobs_see_analytics') }}
                            </p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Detailed Analytics -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 mb-8">
            <!-- Traffic Sources -->
            <div class="bg-white dark:bg-gray-800 shadow rounded-lg">
                <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-white">
                        {{ __('jobs.traffic_sources') }}
                    </h3>
                </div>
                <div class="p-6">
                    @if($trafficSources && count($trafficSources) > 0)
                        <div class="space-y-4">
                            @foreach($trafficSources as $source)
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center space-x-3">
                                        <div class="flex-shrink-0">
                                            <div class="h-3 w-3 rounded-full" style="background-color: {{ $source['color'] }}"></div>
                                        </div>
                                        <span class="text-sm text-gray-900 dark:text-white">{{ $source['name'] }}</span>
                                    </div>
                                    <div class="text-right">
                                        <div class="text-sm font-medium text-gray-900 dark:text-white">
                                            {{ $source['percentage'] }}%
                                        </div>
                                        <div class="text-xs text-gray-500 dark:text-gray-400">
                                            {{ number_format($source['views']) }} {{ __('jobs.views') }}
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-8">
                            <x-icon name="globe-alt" class="mx-auto h-8 w-8 text-gray-400" />
                            <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">
                                {{ __('jobs.no_traffic_data') }}
                            </p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Application Quality -->
            <div class="bg-white dark:bg-gray-800 shadow rounded-lg">
                <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-white">
                        {{ __('jobs.application_quality') }}
                    </h3>
                </div>
                <div class="p-6">
                    <div class="space-y-4">
                        <div class="flex items-center justify-between">
                            <span class="text-sm text-gray-900 dark:text-white">{{ __('jobs.qualified_candidates') }}</span>
                            <div class="text-right">
                                <div class="text-sm font-medium text-green-600 dark:text-green-400">
                                    {{ $qualityMetrics['qualified_percentage'] ?? 0 }}%
                                </div>
                                <div class="text-xs text-gray-500 dark:text-gray-400">
                                    {{ $qualityMetrics['qualified_count'] ?? 0 }} {{ __('jobs.candidates') }}
                                </div>
                            </div>
                        </div>

                        <div class="flex items-center justify-between">
                            <span class="text-sm text-gray-900 dark:text-white">{{ __('jobs.average_experience') }}</span>
                            <div class="text-sm font-medium text-gray-900 dark:text-white">
                                {{ $qualityMetrics['avg_experience'] ?? 0 }} {{ __('jobs.years') }}
                            </div>
                        </div>

                        <div class="flex items-center justify-between">
                            <span class="text-sm text-gray-900 dark:text-white">{{ __('jobs.skill_match_rate') }}</span>
                            <div class="text-sm font-medium text-blue-600 dark:text-blue-400">
                                {{ $qualityMetrics['skill_match_rate'] ?? 0 }}%
                            </div>
                        </div>

                        <div class="flex items-center justify-between">
                            <span class="text-sm text-gray-900 dark:text-white">{{ __('jobs.response_time') }}</span>
                            <div class="text-sm font-medium text-gray-900 dark:text-white">
                                {{ $qualityMetrics['avg_response_time'] ?? 0 }} {{ __('jobs.hours') }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Geographic Distribution -->
            <div class="bg-white dark:bg-gray-800 shadow rounded-lg">
                <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-white">
                        {{ __('jobs.geographic_distribution') }}
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
                                            {{ $location['count'] }} {{ __('jobs.applications') }}
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-8">
                            <x-icon name="map" class="mx-auto h-8 w-8 text-gray-400" />
                            <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">
                                {{ __('jobs.no_geographic_data') }}
                            </p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Optimization Recommendations -->
        @if($recommendations && count($recommendations) > 0)
            <div class="bg-white dark:bg-gray-800 shadow rounded-lg mb-8">
                <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-white">
                        {{ __('jobs.optimization_recommendations') }}
                    </h3>
                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                        {{ __('jobs.ai_powered_suggestions') }}
                    </p>
                </div>
                <div class="p-6">
                    <div class="space-y-4">
                        @foreach($recommendations as $recommendation)
                            <div class="flex items-start space-x-3 p-4 rounded-lg {{ $recommendation['priority'] === 'high' ? 'bg-red-50 dark:bg-red-900/20' : ($recommendation['priority'] === 'medium' ? 'bg-yellow-50 dark:bg-yellow-900/20' : 'bg-blue-50 dark:bg-blue-900/20') }}">
                                <div class="flex-shrink-0">
                                    <x-icon 
                                        :name="$recommendation['priority'] === 'high' ? 'exclamation-triangle' : ($recommendation['priority'] === 'medium' ? 'light-bulb' : 'information-circle')" 
                                        class="h-5 w-5 {{ $recommendation['priority'] === 'high' ? 'text-red-600 dark:text-red-400' : ($recommendation['priority'] === 'medium' ? 'text-yellow-600 dark:text-yellow-400' : 'text-blue-600 dark:text-blue-400') }}" 
                                    />
                                </div>
                                <div class="flex-1 min-w-0">
                                    <h4 class="text-sm font-medium text-gray-900 dark:text-white">
                                        {{ $recommendation['title'] }}
                                    </h4>
                                    <p class="mt-1 text-sm text-gray-700 dark:text-gray-300">
                                        {{ $recommendation['description'] }}
                                    </p>
                                    @if(isset($recommendation['action_url']))
                                        <div class="mt-2">
                                            <a href="{{ $recommendation['action_url'] }}" class="text-sm font-medium {{ $recommendation['priority'] === 'high' ? 'text-red-600 hover:text-red-500 dark:text-red-400' : ($recommendation['priority'] === 'medium' ? 'text-yellow-600 hover:text-yellow-500 dark:text-yellow-400' : 'text-blue-600 hover:text-blue-500 dark:text-blue-400') }}">
                                                {{ $recommendation['action_text'] }} →
                                            </a>
                                        </div>
                                    @endif
                                </div>
                                <div class="flex-shrink-0">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $recommendation['priority'] === 'high' ? 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200' : ($recommendation['priority'] === 'medium' ? 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200' : 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200') }}">
                                        {{ __('jobs.priority_' . $recommendation['priority']) }}
                                    </span>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        @endif

        <!-- Individual Job Performance -->
        @if($jobPerformance && count($jobPerformance) > 0)
            <div class="bg-white dark:bg-gray-800 shadow rounded-lg">
                <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-white">
                        {{ __('jobs.individual_job_performance') }}
                    </h3>
                </div>
                <div class="overflow-hidden">
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                            <thead class="bg-gray-50 dark:bg-gray-700">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                        {{ __('jobs.job_title') }}
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                        {{ __('jobs.views') }}
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                        {{ __('jobs.applications') }}
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                        {{ __('jobs.conversion_rate') }}
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                        {{ __('jobs.quality_score') }}
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                        {{ __('jobs.status') }}
                                    </th>
                                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                        {{ __('jobs.actions') }}
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                @foreach($jobPerformance as $job)
                                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="flex items-center">
                                                <div>
                                                    <div class="text-sm font-medium text-gray-900 dark:text-white">
                                                        <a href="{{ route('jobs.show', $job['id']) }}" class="hover:text-blue-600 dark:hover:text-blue-400">
                                                            {{ $job['title'] }}
                                                        </a>
                                                    </div>
                                                    <div class="text-sm text-gray-500 dark:text-gray-400">
                                                        {{ __('jobs.posted') }} {{ $job['posted_date'] }}
                                                    </div>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                                            {{ number_format($job['views']) }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                                            {{ number_format($job['applications']) }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="flex items-center">
                                                <span class="text-sm text-gray-900 dark:text-white">
                                                    {{ number_format($job['conversion_rate'], 1) }}%
                                                </span>
                                                @if($job['conversion_trend'] !== 0)
                                                    <x-icon 
                                                        :name="$job['conversion_trend'] > 0 ? 'arrow-trending-up' : 'arrow-trending-down'" 
                                                        class="ml-1 h-4 w-4 {{ $job['conversion_trend'] > 0 ? 'text-green-500' : 'text-red-500' }}" 
                                                    />
                                                @endif
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="flex items-center">
                                                @for($i = 1; $i <= 5; $i++)
                                                    <x-icon 
                                                        name="star" 
                                                        class="h-4 w-4 {{ $i <= $job['quality_score'] ? 'text-yellow-400' : 'text-gray-300 dark:text-gray-600' }}" 
                                                    />
                                                @endfor
                                                <span class="ml-2 text-sm text-gray-500 dark:text-gray-400">
                                                    {{ number_format($job['quality_score'], 1) }}
                                                </span>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                                {{ $job['status'] === 'active' ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200' : '' }}
                                                {{ $job['status'] === 'paused' ? 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200' : '' }}
                                                {{ $job['status'] === 'expired' ? 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200' : '' }}
                                                {{ $job['status'] === 'draft' ? 'bg-gray-100 text-gray-800 dark:bg-gray-900 dark:text-gray-200' : '' }}
                                            ">
                                                {{ __('jobs.status_' . $job['status']) }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                            <div class="flex items-center justify-end space-x-2">
                                                <x-ui.button 
                                                    href="{{ route('employer.jobs.analytics.detail', $job['id']) }}" 
                                                    variant="ghost" 
                                                    size="sm"
                                                    icon="chart-bar"
                                                >
                                                    {{ __('jobs.details') }}
                                                </x-ui.button>
                                                
                                                <x-ui.button 
                                                    href="{{ route('employer.jobs.edit', $job['id']) }}" 
                                                    variant="ghost" 
                                                    size="sm"
                                                    icon="pencil"
                                                >
                                                    {{ __('jobs.edit') }}
                                                </x-ui.button>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
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
    initializeViewsApplicationsChart();
    
    // Auto-refresh data every 5 minutes
    setInterval(refreshAnalytics, 300000);
});

function initializeViewsApplicationsChart() {
    const ctx = document.getElementById('views-applications-chart');
    if (!ctx) return;
    
    // Sample data - replace with actual data from backend
    const chartData = @json($chartData ?? []);
    
    new Chart(ctx, {
        type: 'line',
        data: {
            labels: chartData.labels || [],
            datasets: [
                {
                    label: '{{ __("jobs.views") }}',
                    data: chartData.views || [],
                    borderColor: 'rgb(59, 130, 246)',
                    backgroundColor: 'rgba(59, 130, 246, 0.1)',
                    tension: 0.4
                },
                {
                    label: '{{ __("jobs.applications") }}',
                    data: chartData.applications || [],
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

function refreshAnalytics() {
    // Refresh analytics data without page reload
    fetch(window.location.href, {
        headers: {
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(response => response.json())
    .then(data => {
        // Update metrics
        updateMetrics(data.metrics);
        
        // Update charts
        updateCharts(data.chartData);
        
        console.log('Analytics data refreshed');
    })
    .catch(error => {
        console.error('Error refreshing analytics:', error);
    });
}

function updateMetrics(metrics) {
    // Update metric values in the UI
    Object.keys(metrics).forEach(key => {
        const element = document.querySelector(`[data-metric="${key}"]`);
        if (element) {
            element.textContent = metrics[key];
        }
    });
}

function updateCharts(chartData) {
    // Update chart data
    if (window.viewsApplicationsChart) {
        window.viewsApplicationsChart.data.labels = chartData.labels;
        window.viewsApplicationsChart.data.datasets[0].data = chartData.views;
        window.viewsApplicationsChart.data.datasets[1].data = chartData.applications;
        window.viewsApplicationsChart.update();
    }
}

// Export functionality
function exportAnalytics(format) {
    const form = document.createElement('form');
    form.method = 'POST';
    form.action = '{{ route("employer.jobs.analytics.export") }}';
    
    // Add CSRF token
    const csrfInput = document.createElement('input');
    csrfInput.type = 'hidden';
    csrfInput.name = '_token';
    csrfInput.value = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    form.appendChild(csrfInput);
    
    // Add format
    const formatInput = document.createElement('input');
    formatInput.type = 'hidden';
    formatInput.name = 'format';
    formatInput.value = format;
    form.appendChild(formatInput);
    
    // Add current filters
    const urlParams = new URLSearchParams(window.location.search);
    urlParams.forEach((value, key) => {
        const input = document.createElement('input');
        input.type = 'hidden';
        input.name = key;
        input.value = value;
        form.appendChild(input);
    });
    
    document.body.appendChild(form);
    form.submit();
    document.body.removeChild(form);
}
</script>
@endpush 