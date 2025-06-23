@extends('layouts.app')

@section('title', __('reports.advanced_reporting'))

@section('content')
<div class="min-h-screen bg-gray-50 dark:bg-gray-900 py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="mb-8">
            <div class="sm:flex sm:items-center sm:justify-between">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900 dark:text-white">
                        {{ __('reports.advanced_reporting') }}
                    </h1>
                    <p class="mt-2 text-gray-600 dark:text-gray-400">
                        {{ __('reports.generate_custom_reports') }}
                    </p>
                </div>
                
                <div class="mt-4 sm:mt-0 flex space-x-3">
                    <x-ui.button 
                        href="{{ route('reports.templates') }}" 
                        variant="secondary"
                        icon="document-duplicate"
                    >
                        {{ __('reports.report_templates') }}
                    </x-ui.button>
                    
                    <x-ui.button 
                        onclick="openReportBuilder()" 
                        variant="primary"
                        icon="plus"
                    >
                        {{ __('reports.create_report') }}
                    </x-ui.button>
                </div>
            </div>
        </div>

        <!-- Quick Stats -->
        <div class="grid grid-cols-1 gap-5 sm:grid-cols-4 mb-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow rounded-lg">
                <div class="p-5">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <x-icon name="document-chart-bar" class="h-6 w-6 text-blue-600 dark:text-blue-400" />
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400 truncate">
                                    {{ __('reports.total_reports') }}
                                </dt>
                                <dd class="text-2xl font-semibold text-gray-900 dark:text-white">
                                    {{ number_format($stats['total_reports'] ?? 0) }}
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
                            <x-icon name="clock" class="h-6 w-6 text-green-600 dark:text-green-400" />
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400 truncate">
                                    {{ __('reports.scheduled_reports') }}
                                </dt>
                                <dd class="text-2xl font-semibold text-gray-900 dark:text-white">
                                    {{ number_format($stats['scheduled'] ?? 0) }}
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
                            <x-icon name="arrow-down-tray" class="h-6 w-6 text-purple-600 dark:text-purple-400" />
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400 truncate">
                                    {{ __('reports.exports_this_month') }}
                                </dt>
                                <dd class="text-2xl font-semibold text-gray-900 dark:text-white">
                                    {{ number_format($stats['exports'] ?? 0) }}
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
                            <x-icon name="chart-pie" class="h-6 w-6 text-yellow-600 dark:text-yellow-400" />
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400 truncate">
                                    {{ __('reports.data_sources') }}
                                </dt>
                                <dd class="text-2xl font-semibold text-gray-900 dark:text-white">
                                    {{ number_format($stats['data_sources'] ?? 8) }}
                                </dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Report Categories -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 mb-8">
            <!-- User Reports -->
            <div class="bg-white dark:bg-gray-800 shadow rounded-lg">
                <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-white">
                        {{ __('reports.user_reports') }}
                    </h3>
                </div>
                <div class="p-6">
                    <div class="space-y-3">
                        <x-ui.button 
                            onclick="generateReport('user_registration')" 
                            variant="ghost" 
                            class="w-full justify-start"
                            icon="users"
                        >
                            {{ __('reports.user_registration_trends') }}
                        </x-ui.button>

                        <x-ui.button 
                            onclick="generateReport('user_activity')" 
                            variant="ghost" 
                            class="w-full justify-start"
                            icon="chart-bar"
                        >
                            {{ __('reports.user_activity_analysis') }}
                        </x-ui.button>

                        <x-ui.button 
                            onclick="generateReport('user_demographics')" 
                            variant="ghost" 
                            class="w-full justify-start"
                            icon="user-group"
                        >
                            {{ __('reports.user_demographics') }}
                        </x-ui.button>

                        <x-ui.button 
                            onclick="generateReport('user_retention')" 
                            variant="ghost" 
                            class="w-full justify-start"
                            icon="arrow-trending-up"
                        >
                            {{ __('reports.user_retention_analysis') }}
                        </x-ui.button>
                    </div>
                </div>
            </div>

            <!-- Job Reports -->
            <div class="bg-white dark:bg-gray-800 shadow rounded-lg">
                <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-white">
                        {{ __('reports.job_reports') }}
                    </h3>
                </div>
                <div class="p-6">
                    <div class="space-y-3">
                        <x-ui.button 
                            onclick="generateReport('job_performance')" 
                            variant="ghost" 
                            class="w-full justify-start"
                            icon="briefcase"
                        >
                            {{ __('reports.job_performance_metrics') }}
                        </x-ui.button>

                        <x-ui.button 
                            onclick="generateReport('application_trends')" 
                            variant="ghost" 
                            class="w-full justify-start"
                            icon="document-text"
                        >
                            {{ __('reports.application_trends') }}
                        </x-ui.button>

                        <x-ui.button 
                            onclick="generateReport('salary_analysis')" 
                            variant="ghost" 
                            class="w-full justify-start"
                            icon="currency-dollar"
                        >
                            {{ __('reports.salary_analysis') }}
                        </x-ui.button>

                        <x-ui.button 
                            onclick="generateReport('category_insights')" 
                            variant="ghost" 
                            class="w-full justify-start"
                            icon="tag"
                        >
                            {{ __('reports.category_insights') }}
                        </x-ui.button>
                    </div>
                </div>
            </div>

            <!-- Business Reports -->
            <div class="bg-white dark:bg-gray-800 shadow rounded-lg">
                <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-white">
                        {{ __('reports.business_reports') }}
                    </h3>
                </div>
                <div class="p-6">
                    <div class="space-y-3">
                        <x-ui.button 
                            onclick="generateReport('revenue_analysis')" 
                            variant="ghost" 
                            class="w-full justify-start"
                            icon="chart-pie"
                        >
                            {{ __('reports.revenue_analysis') }}
                        </x-ui.button>

                        <x-ui.button 
                            onclick="generateReport('company_performance')" 
                            variant="ghost" 
                            class="w-full justify-start"
                            icon="building-office"
                        >
                            {{ __('reports.company_performance') }}
                        </x-ui.button>

                        <x-ui.button 
                            onclick="generateReport('market_trends')" 
                            variant="ghost" 
                            class="w-full justify-start"
                            icon="globe-alt"
                        >
                            {{ __('reports.market_trends') }}
                        </x-ui.button>

                        <x-ui.button 
                            onclick="generateReport('roi_analysis')" 
                            variant="ghost" 
                            class="w-full justify-start"
                            icon="calculator"
                        >
                            {{ __('reports.roi_analysis') }}
                        </x-ui.button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Reports -->
        <div class="bg-white dark:bg-gray-800 shadow rounded-lg mb-8">
            <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                <div class="flex items-center justify-between">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-white">
                        {{ __('reports.recent_reports') }}
                    </h3>
                    
                    <x-ui.button 
                        href="{{ route('reports.history') }}" 
                        variant="ghost" 
                        size="sm"
                    >
                        {{ __('reports.view_all') }}
                    </x-ui.button>
                </div>
            </div>
            
            <div class="overflow-hidden">
                @if($recentReports && count($recentReports) > 0)
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                            <thead class="bg-gray-50 dark:bg-gray-700">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                        {{ __('reports.report_name') }}
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                        {{ __('reports.type') }}
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                        {{ __('reports.created') }}
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                        {{ __('reports.status') }}
                                    </th>
                                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                        {{ __('reports.actions') }}
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                @foreach($recentReports as $report)
                                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm font-medium text-gray-900 dark:text-white">
                                                {{ $report['name'] }}
                                            </div>
                                            <div class="text-sm text-gray-500 dark:text-gray-400">
                                                {{ $report['description'] }}
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $report['type_color'] }}">
                                                {{ __('reports.type_' . $report['type']) }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                                            {{ $report['created_at'] }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $report['status_color'] }}">
                                                {{ __('reports.status_' . $report['status']) }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                            <div class="flex items-center justify-end space-x-2">
                                                @if($report['status'] === 'completed')
                                                    <x-ui.button 
                                                        href="{{ $report['download_url'] }}" 
                                                        variant="ghost" 
                                                        size="sm"
                                                        icon="arrow-down-tray"
                                                    >
                                                        {{ __('reports.download') }}
                                                    </x-ui.button>
                                                @endif
                                                
                                                <x-ui.button 
                                                    href="{{ route('reports.show', $report['id']) }}" 
                                                    variant="ghost" 
                                                    size="sm"
                                                    icon="eye"
                                                >
                                                    {{ __('reports.view') }}
                                                </x-ui.button>
                                                
                                                <x-ui.button 
                                                    onclick="deleteReport('{{ $report['id'] }}')" 
                                                    variant="ghost" 
                                                    size="sm"
                                                    icon="trash"
                                                >
                                                    {{ __('reports.delete') }}
                                                </x-ui.button>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="px-6 py-12 text-center">
                        <x-icon name="document-chart-bar" class="mx-auto h-12 w-12 text-gray-400" />
                        <h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-white">
                            {{ __('reports.no_reports') }}
                        </h3>
                        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                            {{ __('reports.create_first_report') }}
                        </p>
                        <div class="mt-6">
                            <x-ui.button 
                                onclick="openReportBuilder()" 
                                variant="primary"
                            >
                                {{ __('reports.create_report') }}
                            </x-ui.button>
                        </div>
                    </div>
                @endif
            </div>
        </div>

        <!-- Scheduled Reports -->
        <div class="bg-white dark:bg-gray-800 shadow rounded-lg">
            <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                <div class="flex items-center justify-between">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-white">
                        {{ __('reports.scheduled_reports') }}
                    </h3>
                    
                    <x-ui.button 
                        onclick="openScheduleModal()" 
                        variant="primary" 
                        size="sm"
                        icon="clock"
                    >
                        {{ __('reports.schedule_report') }}
                    </x-ui.button>
                </div>
            </div>
            
            <div class="p-6">
                @if($scheduledReports && count($scheduledReports) > 0)
                    <div class="space-y-4">
                        @foreach($scheduledReports as $schedule)
                            <div class="flex items-center justify-between p-4 border border-gray-200 dark:border-gray-600 rounded-lg">
                                <div class="flex items-center space-x-3">
                                    <div class="flex-shrink-0">
                                        <x-icon name="clock" class="h-5 w-5 text-gray-400" />
                                    </div>
                                    <div>
                                        <h4 class="text-sm font-medium text-gray-900 dark:text-white">
                                            {{ $schedule['name'] }}
                                        </h4>
                                        <p class="text-sm text-gray-500 dark:text-gray-400">
                                            {{ __('reports.runs') }} {{ $schedule['frequency'] }} • {{ __('reports.next_run') }}: {{ $schedule['next_run'] }}
                                        </p>
                                    </div>
                                </div>
                                
                                <div class="flex items-center space-x-2">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $schedule['status'] === 'active' ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200' : 'bg-gray-100 text-gray-800 dark:bg-gray-900 dark:text-gray-200' }}">
                                        {{ __('reports.status_' . $schedule['status']) }}
                                    </span>
                                    
                                    <x-ui.button 
                                        onclick="editSchedule('{{ $schedule['id'] }}')" 
                                        variant="ghost" 
                                        size="sm"
                                        icon="pencil"
                                    >
                                        {{ __('reports.edit') }}
                                    </x-ui.button>
                                    
                                    <x-ui.button 
                                        onclick="deleteSchedule('{{ $schedule['id'] }}')" 
                                        variant="ghost" 
                                        size="sm"
                                        icon="trash"
                                    >
                                        {{ __('reports.delete') }}
                                    </x-ui.button>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-8">
                        <x-icon name="clock" class="mx-auto h-12 w-12 text-gray-400" />
                        <h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-white">
                            {{ __('reports.no_scheduled_reports') }}
                        </h3>
                        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                            {{ __('reports.schedule_automatic_reports') }}
                        </p>
                        <div class="mt-6">
                            <x-ui.button 
                                onclick="openScheduleModal()" 
                                variant="primary"
                            >
                                {{ __('reports.schedule_first_report') }}
                            </x-ui.button>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Report Builder Modal -->
<x-ui.modal id="report-builder-modal" size="xl">
    <div class="p-6">
        <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-6">
            {{ __('reports.custom_report_builder') }}
        </h3>
        
        <form onsubmit="buildCustomReport(event)">
            <!-- Report Details -->
            <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 mb-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        {{ __('reports.report_name') }}
                    </label>
                    <x-ui.input
                        type="text"
                        name="report_name"
                        id="report_name"
                        placeholder="{{ __('reports.enter_report_name') }}"
                        required
                    />
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        {{ __('reports.report_type') }}
                    </label>
                    <x-ui.select
                        name="report_type"
                        id="report_type"
                        :options="[
                            'table' => __('reports.table_report'),
                            'chart' => __('reports.chart_report'),
                            'dashboard' => __('reports.dashboard_report'),
                            'export' => __('reports.export_report')
                        ]"
                        required
                    />
                </div>
            </div>
            
            <!-- Data Source -->
            <div class="mb-6">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    {{ __('reports.data_source') }}
                </label>
                <x-ui.select
                    name="data_source"
                    id="data_source"
                    :options="[
                        'users' => __('reports.users_data'),
                        'jobs' => __('reports.jobs_data'),
                        'applications' => __('reports.applications_data'),
                        'companies' => __('reports.companies_data'),
                        'revenue' => __('reports.revenue_data'),
                        'analytics' => __('reports.analytics_data')
                    ]"
                    onchange="updateAvailableFields(this.value)"
                    required
                />
            </div>
            
            <!-- Fields Selection -->
            <div class="mb-6">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    {{ __('reports.select_fields') }}
                </label>
                <div id="fields-container" class="grid grid-cols-2 gap-2 p-4 border border-gray-300 dark:border-gray-600 rounded-lg max-h-48 overflow-y-auto">
                    <!-- Fields will be populated dynamically -->
                </div>
            </div>
            
            <!-- Filters -->
            <div class="mb-6">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    {{ __('reports.filters') }}
                </label>
                <div id="filters-container" class="space-y-3">
                    <div class="flex items-center space-x-3">
                        <x-ui.select
                            name="filters[0][field]"
                            :options="[]"
                            class="flex-1"
                        />
                        <x-ui.select
                            name="filters[0][operator]"
                            :options="[
                                'equals' => __('reports.equals'),
                                'not_equals' => __('reports.not_equals'),
                                'greater_than' => __('reports.greater_than'),
                                'less_than' => __('reports.less_than'),
                                'contains' => __('reports.contains'),
                                'between' => __('reports.between')
                            ]"
                            class="w-32"
                        />
                        <x-ui.input
                            type="text"
                            name="filters[0][value]"
                            placeholder="{{ __('reports.filter_value') }}"
                            class="flex-1"
                        />
                        <x-ui.button 
                            type="button"
                            onclick="addFilter()"
                            variant="ghost"
                            size="sm"
                            icon="plus"
                        >
                        </x-ui.button>
                    </div>
                </div>
            </div>
            
            <!-- Date Range -->
            <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 mb-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        {{ __('reports.start_date') }}
                    </label>
                    <x-ui.input
                        type="date"
                        name="start_date"
                        id="start_date"
                    />
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        {{ __('reports.end_date') }}
                    </label>
                    <x-ui.input
                        type="date"
                        name="end_date"
                        id="end_date"
                    />
                </div>
            </div>
            
            <div class="flex justify-end space-x-3">
                <x-ui.button 
                    type="button"
                    onclick="closeModal('report-builder-modal')" 
                    variant="secondary"
                >
                    {{ __('reports.cancel') }}
                </x-ui.button>
                
                <x-ui.button 
                    type="submit" 
                    variant="primary"
                >
                    {{ __('reports.generate_report') }}
                </x-ui.button>
            </div>
        </form>
    </div>
</x-ui.modal>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Initialize report system
    initializeReports();
});

function initializeReports() {
    // Set default date range to last 30 days
    const endDate = new Date();
    const startDate = new Date();
    startDate.setDate(startDate.getDate() - 30);
    
    document.getElementById('start_date').value = startDate.toISOString().split('T')[0];
    document.getElementById('end_date').value = endDate.toISOString().split('T')[0];
}

function generateReport(reportType) {
    // Show loading state
    showLoadingState();
    
    fetch('/reports/generate', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({
            type: reportType,
            start_date: document.getElementById('start_date')?.value,
            end_date: document.getElementById('end_date')?.value
        })
    })
    .then(response => response.json())
    .then(data => {
        hideLoadingState();
        
        if (data.success) {
            showNotification('{{ __("reports.report_generated") }}', 'success');
            
            // Redirect to report view or download
            if (data.redirect_url) {
                window.location.href = data.redirect_url;
            } else if (data.download_url) {
                window.open(data.download_url, '_blank');
            }
        } else {
            showNotification(data.message || '{{ __("reports.generation_failed") }}', 'error');
        }
    })
    .catch(error => {
        hideLoadingState();
        console.error('Error generating report:', error);
        showNotification('{{ __("reports.generation_error") }}', 'error');
    });
}

function openReportBuilder() {
    openModal('report-builder-modal');
}

function buildCustomReport(event) {
    event.preventDefault();
    
    const formData = new FormData(event.target);
    const reportData = Object.fromEntries(formData.entries());
    
    // Collect selected fields
    const selectedFields = Array.from(document.querySelectorAll('#fields-container input:checked'))
        .map(checkbox => checkbox.value);
    reportData.fields = selectedFields;
    
    // Collect filters
    const filters = [];
    document.querySelectorAll('#filters-container > div').forEach((filterRow, index) => {
        const field = filterRow.querySelector(`[name="filters[${index}][field]"]`)?.value;
        const operator = filterRow.querySelector(`[name="filters[${index}][operator]"]`)?.value;
        const value = filterRow.querySelector(`[name="filters[${index}][value]"]`)?.value;
        
        if (field && operator && value) {
            filters.push({ field, operator, value });
        }
    });
    reportData.filters = filters;
    
    showLoadingState();
    
    fetch('/reports/custom', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Content-Type': 'application/json'
        },
        body: JSON.stringify(reportData)
    })
    .then(response => response.json())
    .then(data => {
        hideLoadingState();
        
        if (data.success) {
            closeModal('report-builder-modal');
            showNotification('{{ __("reports.custom_report_generated") }}', 'success');
            
            // Refresh page or redirect
            if (data.redirect_url) {
                window.location.href = data.redirect_url;
            } else {
                window.location.reload();
            }
        } else {
            showNotification(data.message || '{{ __("reports.generation_failed") }}', 'error');
        }
    })
    .catch(error => {
        hideLoadingState();
        console.error('Error building custom report:', error);
        showNotification('{{ __("reports.generation_error") }}', 'error');
    });
}

function updateAvailableFields(dataSource) {
    const fieldsContainer = document.getElementById('fields-container');
    
    // Field definitions for each data source
    const fieldDefinitions = {
        users: [
            { value: 'id', label: '{{ __("reports.user_id") }}' },
            { value: 'name', label: '{{ __("reports.name") }}' },
            { value: 'email', label: '{{ __("reports.email") }}' },
            { value: 'role', label: '{{ __("reports.role") }}' },
            { value: 'created_at', label: '{{ __("reports.registration_date") }}' },
            { value: 'last_login', label: '{{ __("reports.last_login") }}' }
        ],
        jobs: [
            { value: 'id', label: '{{ __("reports.job_id") }}' },
            { value: 'title', label: '{{ __("reports.job_title") }}' },
            { value: 'company', label: '{{ __("reports.company") }}' },
            { value: 'category', label: '{{ __("reports.category") }}' },
            { value: 'salary', label: '{{ __("reports.salary") }}' },
            { value: 'location', label: '{{ __("reports.location") }}' },
            { value: 'applications_count', label: '{{ __("reports.applications_count") }}' },
            { value: 'views_count', label: '{{ __("reports.views_count") }}' }
        ],
        applications: [
            { value: 'id', label: '{{ __("reports.application_id") }}' },
            { value: 'job_title', label: '{{ __("reports.job_title") }}' },
            { value: 'candidate_name', label: '{{ __("reports.candidate_name") }}' },
            { value: 'status', label: '{{ __("reports.status") }}' },
            { value: 'applied_at', label: '{{ __("reports.application_date") }}' },
            { value: 'expected_salary', label: '{{ __("reports.expected_salary") }}' }
        ]
    };
    
    const fields = fieldDefinitions[dataSource] || [];
    
    fieldsContainer.innerHTML = '';
    
    fields.forEach(field => {
        const div = document.createElement('div');
        div.className = 'flex items-center';
        div.innerHTML = `
            <input 
                type="checkbox" 
                id="field_${field.value}" 
                value="${field.value}" 
                class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded"
            >
            <label for="field_${field.value}" class="ml-2 block text-sm text-gray-900 dark:text-gray-300">
                ${field.label}
            </label>
        `;
        fieldsContainer.appendChild(div);
    });
}

function addFilter() {
    const filtersContainer = document.getElementById('filters-container');
    const filterCount = filtersContainer.children.length;
    
    const filterDiv = document.createElement('div');
    filterDiv.className = 'flex items-center space-x-3';
    filterDiv.innerHTML = `
        <select name="filters[${filterCount}][field]" class="flex-1 rounded-md border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
            <option value="">{{ __('reports.select_field') }}</option>
        </select>
        <select name="filters[${filterCount}][operator]" class="w-32 rounded-md border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
            <option value="equals">{{ __('reports.equals') }}</option>
            <option value="not_equals">{{ __('reports.not_equals') }}</option>
            <option value="greater_than">{{ __('reports.greater_than') }}</option>
            <option value="less_than">{{ __('reports.less_than') }}</option>
            <option value="contains">{{ __('reports.contains') }}</option>
            <option value="between">{{ __('reports.between') }}</option>
        </select>
        <input 
            type="text" 
            name="filters[${filterCount}][value]" 
            placeholder="{{ __('reports.filter_value') }}"
            class="flex-1 rounded-md border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white"
        >
        <button type="button" onclick="removeFilter(this)" class="p-2 text-red-600 hover:text-red-500">
            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
            </svg>
        </button>
    `;
    
    filtersContainer.appendChild(filterDiv);
}

function removeFilter(button) {
    button.closest('div').remove();
}

function deleteReport(reportId) {
    if (!confirm('{{ __("reports.confirm_delete") }}')) return;
    
    fetch(`/reports/${reportId}`, {
        method: 'DELETE',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Content-Type': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showNotification('{{ __("reports.report_deleted") }}', 'success');
            window.location.reload();
        } else {
            showNotification(data.message || '{{ __("reports.delete_failed") }}', 'error');
        }
    })
    .catch(error => {
        console.error('Error deleting report:', error);
        showNotification('{{ __("reports.delete_error") }}', 'error');
    });
}

function openScheduleModal() {
    // Implementation for scheduling reports
    showNotification('{{ __("reports.schedule_feature_coming_soon") }}', 'info');
}

function showLoadingState() {
    // Show loading overlay or spinner
    const loadingDiv = document.createElement('div');
    loadingDiv.id = 'loading-overlay';
    loadingDiv.className = 'fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50';
    loadingDiv.innerHTML = `
        <div class="bg-white dark:bg-gray-800 rounded-lg p-6 flex items-center space-x-3">
            <svg class="animate-spin h-5 w-5 text-blue-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
            <span class="text-gray-900 dark:text-white">{{ __('reports.generating_report') }}...</span>
        </div>
    `;
    document.body.appendChild(loadingDiv);
}

function hideLoadingState() {
    const loadingOverlay = document.getElementById('loading-overlay');
    if (loadingOverlay) {
        loadingOverlay.remove();
    }
}

function showNotification(message, type = 'info') {
    const toast = document.createElement('div');
    toast.className = `fixed top-4 right-4 z-50 p-4 rounded-lg shadow-lg ${
        type === 'success' ? 'bg-green-500 text-white' :
        type === 'error' ? 'bg-red-500 text-white' :
        'bg-blue-500 text-white'
    }`;
    toast.textContent = message;
    
    document.body.appendChild(toast);
    
    setTimeout(() => {
        toast.remove();
    }, 3000);
}
</script>
@endpush
