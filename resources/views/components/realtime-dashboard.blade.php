{{ -- Real-Time Dashboard Component -- }}
<div id="dashboard-container" class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    {{ -- Header with Connection Status -- }}
    <div class="mb-8 flex justify-between items-center">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">{{ __('dashboard.realtime_dashboard') }}</h1>
            <p class="text-gray-600 mt-1">{{ __('dashboard.live_updates_description') }}</p>
        </div>
        
        <div class="flex items-center space-x-4">
            {{ -- Connection Status Indicator -- }}
            <div class="flex items-center space-x-2">
                <div id="connection-status" class="connection-status status-connecting" title="Connecting...">
                    <div class="w-3 h-3 rounded-full bg-yellow-400 animate-pulse"></div>
                </div>
                <span class="text-sm text-gray-600">{{ __('dashboard.connection_status') }}</span>
            </div>
            
            {{ -- Refresh Button -- }}
            <button id="dashboard-refresh" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                </svg>
                {{ __('dashboard.refresh') }}
            </button>
        </div>
    </div>

    {{ -- Statistics Cards -- }}
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        @if(auth()->user()->user_type === 'candidate')
            {{ -- Candidate Stats -- }}
            <div class="bg-white overflow-hidden shadow rounded-lg">
                <div class="p-5">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <svg class="h-6 w-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 truncate">{{ __('dashboard.total_applications') }}</dt>
                                <dd class="text-lg font-medium text-gray-900" id="stat-total_applications">0</dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-white overflow-hidden shadow rounded-lg">
                <div class="p-5">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <svg class="h-6 w-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 truncate">{{ __('dashboard.pending_applications') }}</dt>
                                <dd class="text-lg font-medium text-gray-900" id="stat-pending_applications">0</dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-white overflow-hidden shadow rounded-lg">
                <div class="p-5">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <svg class="h-6 w-6 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                            </svg>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 truncate">{{ __('dashboard.interviews_scheduled') }}</dt>
                                <dd class="text-lg font-medium text-gray-900" id="stat-interviews_scheduled">0</dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-white overflow-hidden shadow rounded-lg">
                <div class="p-5">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <svg class="h-6 w-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 truncate">{{ __('dashboard.successful_applications') }}</dt>
                                <dd class="text-lg font-medium text-gray-900" id="stat-successful_applications">0</dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>
        @else
            {{ -- Employer Stats -- }}
            <div class="bg-white overflow-hidden shadow rounded-lg">
                <div class="p-5">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <svg class="h-6 w-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2-2v2m8 0V6a2 2 0 012 2v6a2 2 0 01-2 2H8a2 2 0 01-2-2V8a2 2 0 012-2V6"></path>
                            </svg>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 truncate">{{ __('dashboard.active_jobs') }}</dt>
                                <dd class="text-lg font-medium text-gray-900" id="stat-active_jobs">0</dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-white overflow-hidden shadow rounded-lg">
                <div class="p-5">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <svg class="h-6 w-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                            </svg>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 truncate">{{ __('dashboard.total_applications') }}</dt>
                                <dd class="text-lg font-medium text-gray-900" id="stat-total_applications">0</dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-white overflow-hidden shadow rounded-lg">
                <div class="p-5">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <svg class="h-6 w-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                            </svg>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 truncate">{{ __('dashboard.pending_reviews') }}</dt>
                                <dd class="text-lg font-medium text-gray-900" id="stat-pending_reviews">0</dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-white overflow-hidden shadow rounded-lg">
                <div class="p-5">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <svg class="h-6 w-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                            </svg>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 truncate">{{ __('dashboard.scheduled_interviews') }}</dt>
                                <dd class="text-lg font-medium text-gray-900" id="stat-scheduled_interviews">0</dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    </div>

    {{ -- Main Content Grid -- }}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        {{ -- Activity Feed -- }}
        <div class="lg:col-span-2">
            <div class="bg-white shadow rounded-lg">
                <div class="px-4 py-5 sm:p-6">
                    <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">
                        {{ __('dashboard.recent_activity') }}
                        <span class="ml-2 inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800" id="activity-live-indicator">
                            <span class="w-2 h-2 bg-green-400 rounded-full mr-1.5 animate-pulse"></span>
                            {{ __('dashboard.live') }}
                        </span>
                    </h3>
                    
                    <div id="activity-feed" class="space-y-4 max-h-96 overflow-y-auto">
                        {{ -- Activity items will be dynamically loaded here -- }}
                        <div class="flex items-center justify-center py-8 text-gray-500">
                            <svg class="animate-spin ml-1 mr-3 h-5 w-5 text-gray-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            {{ __('dashboard.loading_activities') }}
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{ -- Real-time Metrics Panel -- }}
        <div class="space-y-6">
            {{ -- System Health -- }}
            <div class="bg-white shadow rounded-lg">
                <div class="px-4 py-5 sm:p-6">
                    <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">{{ __('dashboard.system_health') }}</h3>
                    
                    <div class="space-y-3">
                        <div class="flex items-center justify-between">
                            <span class="text-sm text-gray-600">{{ __('dashboard.database') }}</span>
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                {{ __('dashboard.healthy') }}
                            </span>
                        </div>
                        
                        <div class="flex items-center justify-between">
                            <span class="text-sm text-gray-600">{{ __('dashboard.cache') }}</span>
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                {{ __('dashboard.healthy') }}
                            </span>
                        </div>
                        
                        <div class="flex items-center justify-between">
                            <span class="text-sm text-gray-600">{{ __('dashboard.websockets') }}</span>
                            <span id="websocket-status" class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                {{ __('dashboard.connecting') }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            {{ -- Live Metrics -- }}
            <div class="bg-white shadow rounded-lg">
                <div class="px-4 py-5 sm:p-6">
                    <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">{{ __('dashboard.live_metrics') }}</h3>
                    
                    <div class="space-y-4">
                        <div>
                            <div class="flex items-center justify-between mb-1">
                                <span class="text-sm text-gray-600">{{ __('dashboard.active_users') }}</span>
                                <span class="text-sm font-medium text-gray-900" id="metric-active-users">--</span>
                            </div>
                            <div class="w-full bg-gray-200 rounded-full h-2">
                                <div class="bg-blue-600 h-2 rounded-full" style="width: 65%"></div>
                            </div>
                        </div>
                        
                        <div>
                            <div class="flex items-center justify-between mb-1">
                                <span class="text-sm text-gray-600">{{ __('dashboard.system_load') }}</span>
                                <span class="text-sm font-medium text-gray-900" id="metric-system-load">--</span>
                            </div>
                            <div class="w-full bg-gray-200 rounded-full h-2">
                                <div class="bg-green-600 h-2 rounded-full" style="width: 45%"></div>
                            </div>
                        </div>
                        
                        <div>
                            <div class="flex items-center justify-between mb-1">
                                <span class="text-sm text-gray-600">{{ __('dashboard.response_time') }}</span>
                                <span class="text-sm font-medium text-gray-900" id="metric-response-time">--</span>
                            </div>
                            <div class="w-full bg-gray-200 rounded-full h-2">
                                <div class="bg-yellow-600 h-2 rounded-full" style="width: 30%"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{ -- Quick Actions -- }}
            <div class="bg-white shadow rounded-lg">
                <div class="px-4 py-5 sm:p-6">
                    <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">{{ __('dashboard.quick_actions') }}</h3>
                    
                    <div class="space-y-3">
                        @if(auth()->user()->user_type === 'candidate')
                            <a href="{{ route('jobs.index') }}" class="w-full inline-flex justify-center items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                {{ __('dashboard.browse_jobs') }}
                            </a>
                            
                            <a href="{{ route('candidate.applications.index') }}" class="w-full inline-flex justify-center items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                {{ __('dashboard.my_applications') }}
                            </a>
                        @else
                            <a href="{{ route('employer.jobs.create') }}" class="w-full inline-flex justify-center items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                {{ __('dashboard.post_job') }}
                            </a>
                            
                            <a href="{{ route('employer.applications.index') }}" class="w-full inline-flex justify-center items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                {{ __('dashboard.manage_applications') }}
                            </a>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{ -- Notification Container (will be created by JavaScript) -- }}
{{ -- JavaScript will create: <div id="notification-container" class="fixed top-4 right-4 z-50 space-y-2"></div> -- }}

{{ -- CSS Styles for Real-time Components -- }}
<style>
.connection-status {
    display: inline-flex;
    align-items: center;
}

.status-connected .w-3 {
    background-color: #10b981 !important;
    animation: none;
}

.status-disconnected .w-3 {
    background-color: #ef4444 !important;
    animation: pulse 2s infinite;
}

.status-error .w-3 {
    background-color: #f59e0b !important;
    animation: pulse 1s infinite;
}

.notification {
    @apply bg-white border border-gray-200 rounded-lg shadow-lg p-4 max-w-sm w-full;
}

.notification-success {
    @apply border-green-200 bg-green-50;
}

.notification-error {
    @apply border-red-200 bg-red-50;
}

.notification-warning {
    @apply border-yellow-200 bg-yellow-50;
}

.notification-info {
    @apply border-blue-200 bg-blue-50;
}

.animate-slide-in {
    animation: slideIn 0.3s ease-out forwards;
}

.animate-slide-out {
    animation: slideOut 0.3s ease-in forwards;
}

@keyframes slideIn {
    from {
        transform: translateX(100%);
        opacity: 0;
    }
    to {
        transform: translateX(0);
        opacity: 1;
    }
}

@keyframes slideOut {
    from {
        transform: translateX(0);
        opacity: 1;
    }
    to {
        transform: translateX(100%);
        opacity: 0;
    }
}

.activity-item {
    transition: background-color 0.2s ease;
}

.timeline-item {
    @apply flex items-start space-x-3 pb-4;
}

.timeline-marker {
    @apply w-3 h-3 rounded-full mt-1.5 flex-shrink-0;
}

.status-badge {
    @apply inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium;
}

.status-pending {
    @apply bg-yellow-100 text-yellow-800;
}

.status-reviewed {
    @apply bg-blue-100 text-blue-800;
}

.status-shortlisted {
    @apply bg-green-100 text-green-800;
}

.status-interview_scheduled {
    @apply bg-orange-100 text-orange-800;
}

.status-interview_completed {
    @apply bg-blue-100 text-blue-800;
}

.status-rejected {
    @apply bg-red-100 text-red-800;
}

.status-hired {
    @apply bg-green-100 text-green-800;
}

.status-withdrawn {
    @apply bg-gray-100 text-gray-800;
}
</style>

{{ -- Load Real-time Dashboard JavaScript -- }}
@push('scripts')
<script src="{{ asset('js/realtime-dashboard.js') }}"></script>
<script>
    // Global configuration for the dashboard
    window.APP_CONFIG = {
        websocket_url: '{{ config("broadcasting.connections.pusher.options.host","localhost:6001") }}',
        user_type: '{{ auth()->user()->user_type }}',
        user_id: {{ auth()->user()->id }},
        csrf_token: '{{ csrf_token() }}'
    };
</script>
@endpush 