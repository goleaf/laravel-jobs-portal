@extends('layouts.app')

@section('title', __('admin.dashboard'))

@section('content')
<div class="min-h-screen bg-gray-50 dark:bg-gray-900">
    <!-- Admin Header -->
    <div class="bg-white dark:bg-gray-800 shadow-sm border-b border-gray-200 dark:border-gray-700">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
            <div class="lg:flex lg:items-center lg:justify-between">
                <div class="flex-1 min-w-0">
                    <h1 class="text-2xl font-bold leading-7 text-gray-900 dark:text-white sm:text-3xl sm:truncate">
                        {{ __('admin.admin_dashboard') }}
                    </h1>
                    <div class="mt-1 flex flex-col sm:flex-row sm:flex-wrap sm:mt-0 sm:space-x-6">
                        <div class="mt-2 flex items-center text-sm text-gray-500 dark:text-gray-400">
                            <x-icon name="shield-check" class="flex-shrink-0 mr-1.5 h-5 w-5" />
                            {{ __('admin.administrator') }}
                        </div>
                        <div class="mt-2 flex items-center text-sm text-gray-500 dark:text-gray-400">
                            <x-icon name="clock" class="flex-shrink-0 mr-1.5 h-5 w-5" />
                            {{ __('admin.last_login') }}: {{ auth()->user()->last_login_at?->diffForHumans() ?? __('admin.never') }}
                        </div>
                    </div>
                </div>
                <div class="mt-5 flex lg:mt-0 lg:ml-4">
                    <span class="hidden sm:block">
                        <x-ui.button 
                            href="{{ route('admin.settings') }}" 
                            variant="secondary"
                            icon="cog-6-tooth"
                        >
                            {{ __('admin.system_settings') }}
                        </x-ui.button>
                    </span>
                    
                    <span class="ml-3">
                        <x-ui.button 
                            href="{{ route('admin.users.create') }}" 
                            variant="primary"
                            icon="user-plus"
                        >
                            {{ __('admin.add_user') }}
                        </x-ui.button>
                    </span>
                </div>
            </div>
        </div>
    </div>

    <!-- System Stats Overview -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
        <div class="grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-4">
            <!-- Total Users -->
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
                                <dd class="text-lg font-medium text-gray-900 dark:text-white">
                                    {{ number_format($stats['total_users'] ?? 0) }}
                                </dd>
                            </dl>
                        </div>
                    </div>
                </div>
                <div class="bg-gray-50 dark:bg-gray-700 px-5 py-3">
                    <div class="text-sm">
                        <span class="text-green-600 dark:text-green-400 font-medium">
                            +{{ $stats['new_users_today'] ?? 0 }}
                        </span>
                        <span class="text-gray-500 dark:text-gray-400 ml-1">{{ __('admin.today') }}</span>
                    </div>
                </div>
            </div>

            <!-- Active Jobs -->
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
                                <dd class="text-lg font-medium text-gray-900 dark:text-white">
                                    {{ number_format($stats['active_jobs'] ?? 0) }}
                                </dd>
                            </dl>
                        </div>
                    </div>
                </div>
                <div class="bg-gray-50 dark:bg-gray-700 px-5 py-3">
                    <div class="text-sm">
                        <a href="{{ route('admin.jobs') }}" class="font-medium text-blue-600 hover:text-blue-500 dark:text-blue-400 dark:hover:text-blue-300">
                            {{ __('admin.manage_jobs') }}
                        </a>
                    </div>
                </div>
            </div>

            <!-- Total Applications -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow rounded-lg">
                <div class="p-5">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <x-icon name="document-text" class="h-6 w-6 text-purple-600 dark:text-purple-400" />
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400 truncate">
                                    {{ __('admin.total_applications') }}
                                </dt>
                                <dd class="text-lg font-medium text-gray-900 dark:text-white">
                                    {{ number_format($stats['total_applications'] ?? 0) }}
                                </dd>
                            </dl>
                        </div>
                    </div>
                </div>
                <div class="bg-gray-50 dark:bg-gray-700 px-5 py-3">
                    <div class="text-sm">
                        <span class="text-blue-600 dark:text-blue-400 font-medium">
                            {{ $stats['applications_today'] ?? 0 }}
                        </span>
                        <span class="text-gray-500 dark:text-gray-400 ml-1">{{ __('admin.today') }}</span>
                    </div>
                </div>
            </div>

            <!-- System Revenue -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow rounded-lg">
                <div class="p-5">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <x-icon name="currency-dollar" class="h-6 w-6 text-emerald-600 dark:text-emerald-400" />
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400 truncate">
                                    {{ __('admin.monthly_revenue') }}
                                </dt>
                                <dd class="text-lg font-medium text-gray-900 dark:text-white">
                                    ${{ number_format($stats['monthly_revenue'] ?? 0, 2) }}
                                </dd>
                            </dl>
                        </div>
                    </div>
                </div>
                <div class="bg-gray-50 dark:bg-gray-700 px-5 py-3">
                    <div class="text-sm">
                        <span class="text-green-600 dark:text-green-400 font-medium">
                            +{{ number_format(($stats['revenue_growth'] ?? 0) * 100, 1) }}%
                        </span>
                        <span class="text-gray-500 dark:text-gray-400 ml-1">{{ __('admin.vs_last_month') }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content Grid -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pb-12">
        <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">
            <!-- Recent Activity -->
            <div class="bg-white dark:bg-gray-800 shadow rounded-lg">
                <div class="p-6">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-lg leading-6 font-medium text-gray-900 dark:text-white">
                            {{ __('admin.recent_activity') }}
                        </h3>
                        <x-ui.button 
                            href="{{ route('admin.activity') }}" 
                            variant="ghost" 
                            size="sm"
                        >
                            {{ __('admin.view_all') }}
                        </x-ui.button>
                    </div>
                    
                    @if($recentActivity && $recentActivity->count() > 0)
                        <div class="space-y-4">
                            @foreach($recentActivity as $activity)
                                <div class="flex items-start space-x-3">
                                    <div class="flex-shrink-0">
                                        <div class="h-8 w-8 rounded-full bg-blue-100 dark:bg-blue-900 flex items-center justify-center">
                                            <x-icon name="{{ $activity->icon ?? 'bell' }}" class="h-4 w-4 text-blue-600 dark:text-blue-400" />
                                        </div>
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <p class="text-sm font-medium text-gray-900 dark:text-white">
                                            {{ $activity->description }}
                                        </p>
                                        <p class="text-xs text-gray-500 dark:text-gray-400">
                                            {{ $activity->created_at->diffForHumans() }}
                                        </p>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-6">
                            <x-icon name="bell" class="mx-auto h-12 w-12 text-gray-400" />
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

            <!-- System Status -->
            <div class="bg-white dark:bg-gray-800 shadow rounded-lg">
                <div class="p-6">
                    <h3 class="text-lg leading-6 font-medium text-gray-900 dark:text-white mb-4">
                        {{ __('admin.system_status') }}
                    </h3>
                    
                    <div class="space-y-4">
                        <!-- Database Status -->
                        <div class="flex items-center justify-between">
                            <div class="flex items-center">
                                <x-icon name="circle-stack" class="h-5 w-5 text-gray-400 mr-2" />
                                <span class="text-sm text-gray-700 dark:text-gray-300">{{ __('admin.database') }}</span>
                            </div>
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200">
                                {{ __('admin.healthy') }}
                            </span>
                        </div>

                        <!-- Cache Status -->
                        <div class="flex items-center justify-between">
                            <div class="flex items-center">
                                <x-icon name="bolt" class="h-5 w-5 text-gray-400 mr-2" />
                                <span class="text-sm text-gray-700 dark:text-gray-300">{{ __('admin.cache') }}</span>
                            </div>
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200">
                                {{ __('admin.active') }}
                            </span>
                        </div>

                        <!-- Queue Status -->
                        <div class="flex items-center justify-between">
                            <div class="flex items-center">
                                <x-icon name="queue-list" class="h-5 w-5 text-gray-400 mr-2" />
                                <span class="text-sm text-gray-700 dark:text-gray-300">{{ __('admin.job_queue') }}</span>
                            </div>
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200">
                                {{ $stats['pending_jobs'] ?? 0 }} {{ __('admin.pending') }}
                            </span>
                        </div>

                        <!-- Storage Status -->
                        <div class="flex items-center justify-between">
                            <div class="flex items-center">
                                <x-icon name="server" class="h-5 w-5 text-gray-400 mr-2" />
                                <span class="text-sm text-gray-700 dark:text-gray-300">{{ __('admin.storage') }}</span>
                            </div>
                            <span class="text-sm text-gray-500 dark:text-gray-400">
                                {{ number_format(($stats['storage_used'] ?? 0) / 1024 / 1024, 1) }}GB {{ __('admin.used') }}
                            </span>
                        </div>

                        <!-- Mail Status -->
                        <div class="flex items-center justify-between">
                            <div class="flex items-center">
                                <x-icon name="envelope" class="h-5 w-5 text-gray-400 mr-2" />
                                <span class="text-sm text-gray-700 dark:text-gray-300">{{ __('admin.email_service') }}</span>
                            </div>
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200">
                                {{ __('admin.operational') }}
                            </span>
                        </div>
                    </div>

                    <div class="mt-6 pt-4 border-t border-gray-200 dark:border-gray-700">
                        <div class="flex space-x-3">
                            <x-ui.button 
                                href="{{ route('admin.system.maintenance') }}" 
                                variant="secondary" 
                                size="sm"
                                icon="wrench-screwdriver"
                            >
                                {{ __('admin.maintenance') }}
                            </x-ui.button>
                            
                            <x-ui.button 
                                href="{{ route('admin.system.logs') }}" 
                                variant="secondary" 
                                size="sm"
                                icon="document-text"
                            >
                                {{ __('admin.view_logs') }}
                            </x-ui.button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Admin Quick Actions -->
        <div class="mt-8 bg-white dark:bg-gray-800 shadow rounded-lg">
            <div class="p-6">
                <h3 class="text-lg leading-6 font-medium text-gray-900 dark:text-white mb-4">
                    {{ __('admin.quick_actions') }}
                </h3>
                
                <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">
                    <x-ui.button 
                        href="{{ route('admin.users') }}" 
                        variant="secondary" 
                        class="justify-center"
                        icon="users"
                    >
                        {{ __('admin.manage_users') }}
                    </x-ui.button>
                    
                    <x-ui.button 
                        href="{{ route('admin.jobs') }}" 
                        variant="secondary" 
                        class="justify-center"
                        icon="briefcase"
                    >
                        {{ __('admin.manage_jobs') }}
                    </x-ui.button>
                    
                    <x-ui.button 
                        href="{{ route('admin.companies') }}" 
                        variant="secondary" 
                        class="justify-center"
                        icon="building-office"
                    >
                        {{ __('admin.manage_companies') }}
                    </x-ui.button>
                    
                    <x-ui.button 
                        href="{{ route('admin.reports') }}" 
                        variant="secondary" 
                        class="justify-center"
                        icon="chart-bar"
                    >
                        {{ __('admin.view_reports') }}
                    </x-ui.button>
                    
                    <x-ui.button 
                        href="{{ route('admin.content') }}" 
                        variant="secondary" 
                        class="justify-center"
                        icon="document"
                    >
                        {{ __('admin.content_management') }}
                    </x-ui.button>
                    
                    <x-ui.button 
                        href="{{ route('admin.settings') }}" 
                        variant="secondary" 
                        class="justify-center"
                        icon="cog-6-tooth"
                    >
                        {{ __('admin.system_settings') }}
                    </x-ui.button>
                    
                    <x-ui.button 
                        href="{{ route('admin.backups') }}" 
                        variant="secondary" 
                        class="justify-center"
                        icon="server"
                    >
                        {{ __('admin.backups') }}
                    </x-ui.button>
                    
                    <x-ui.button 
                        href="{{ route('admin.security') }}" 
                        variant="secondary" 
                        class="justify-center"
                        icon="shield-check"
                    >
                        {{ __('admin.security') }}
                    </x-ui.button>
                </div>
            </div>
        </div>

        <!-- System Alerts -->
        @if(isset($systemAlerts) && $systemAlerts->count() > 0)
            <div class="mt-8 bg-white dark:bg-gray-800 shadow rounded-lg">
                <div class="p-6">
                    <h3 class="text-lg leading-6 font-medium text-gray-900 dark:text-white mb-4">
                        {{ __('admin.system_alerts') }}
                    </h3>
                    
                    <div class="space-y-4">
                        @foreach($systemAlerts as $alert)
                            <div class="rounded-md p-4 {{ $alert->type === 'error' ? 'bg-red-50 dark:bg-red-900/20' : ($alert->type === 'warning' ? 'bg-yellow-50 dark:bg-yellow-900/20' : 'bg-blue-50 dark:bg-blue-900/20') }}">
                                <div class="flex">
                                    <div class="flex-shrink-0">
                                        <x-icon 
                                            :name="$alert->type === 'error' ? 'exclamation-triangle' : ($alert->type === 'warning' ? 'exclamation-circle' : 'information-circle')" 
                                            class="h-5 w-5 {{ $alert->type === 'error' ? 'text-red-400' : ($alert->type === 'warning' ? 'text-yellow-400' : 'text-blue-400') }}" 
                                        />
                                    </div>
                                    <div class="ml-3">
                                        <h3 class="text-sm font-medium {{ $alert->type === 'error' ? 'text-red-800 dark:text-red-200' : ($alert->type === 'warning' ? 'text-yellow-800 dark:text-yellow-200' : 'text-blue-800 dark:text-blue-200') }}">
                                            {{ $alert->title }}
                                        </h3>
                                        <div class="mt-2 text-sm {{ $alert->type === 'error' ? 'text-red-700 dark:text-red-300' : ($alert->type === 'warning' ? 'text-yellow-700 dark:text-yellow-300' : 'text-blue-700 dark:text-blue-300') }}">
                                            <p>{{ $alert->message }}</p>
                                        </div>
                                        @if($alert->action_url)
                                            <div class="mt-4">
                                                <div class="-mx-2 -my-1.5 flex">
                                                    <x-ui.button 
                                                        href="{{ $alert->action_url }}" 
                                                        variant="ghost" 
                                                        size="sm"
                                                    >
                                                        {{ $alert->action_text ?? __('admin.view_details') }}
                                                    </x-ui.button>
                                                </div>
                                            </div>
                                        @endif
                                    </div>
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
// Auto-refresh dashboard data every 30 seconds
setInterval(function() {
    // Only refresh if tab is active
    if (!document.hidden) {
        // You can implement AJAX refresh for specific sections here
        console.log('Dashboard auto-refresh triggered');
    }
}, 30000);

// Real-time notifications
document.addEventListener('DOMContentLoaded', function() {
    // WebSocket or SSE connection for real-time updates
    if (typeof window.Echo !== 'undefined') {
        window.Echo.private('admin-dashboard')
            .listen('SystemAlert', (e) => {
                // Handle real-time system alerts
                console.log('New system alert:', e);
            });
    }
});
</script>
@endpush 