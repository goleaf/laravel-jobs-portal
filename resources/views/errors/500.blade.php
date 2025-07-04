@extends('layouts.app')

@section('title', __('errors.server_error'))

@section('content')
<div class="min-h-screen bg-gray-50 dark:bg-gray-900 flex flex-col justify-center py-12 sm:px-6 lg:px-8">
    <div class="sm:mx-auto sm:w-full sm:max-w-lg">
        <div class="text-center">
            <!-- 500 Illustration -->
            <div class="mx-auto w-64 h-64 mb-8">
                <svg viewBox="0 0 400 300" class="w-full h-full text-red-600 dark:text-red-400">
                    <!-- Background -->
                    <rect width="400" height="300" fill="currentColor" opacity="0.1" rx="20"/>
                    
                    <!-- Server Icon -->
                    <rect x="150" y="80" width="100" height="80" fill="none" stroke="currentColor" stroke-width="4" rx="8"/>
                    <rect x="160" y="90" width="80" height="8" fill="currentColor" opacity="0.6" rx="4"/>
                    <rect x="160" y="105" width="80" height="8" fill="currentColor" opacity="0.4" rx="4"/>
                    <rect x="160" y="120" width="80" height="8" fill="currentColor" opacity="0.6" rx="4"/>
                    <rect x="160" y="135" width="80" height="8" fill="currentColor" opacity="0.4" rx="4"/>
                    
                    <!-- Error Symbol -->
                    <circle cx="200" cy="200" r="20" fill="currentColor" opacity="0.8"/>
                    <text x="200" y="208" text-anchor="middle" class="fill-white text-xl font-bold">!</text>
                    
                    <!-- 500 Text -->
                    <text x="200" y="250" text-anchor="middle" class="fill-current text-4xl font-bold" opacity="0.7">500</text>
                    
                    <!-- Warning Lines -->
                    <line x1="120" y1="180" x2="140" y2="180" stroke="currentColor" stroke-width="3" opacity="0.5"/>
                    <line x1="260" y1="180" x2="280" y2="180" stroke="currentColor" stroke-width="3" opacity="0.5"/>
                    <line x1="120" y1="200" x2="140" y2="200" stroke="currentColor" stroke-width="3" opacity="0.3"/>
                    <line x1="260" y1="200" x2="280" y2="200" stroke="currentColor" stroke-width="3" opacity="0.3"/>
                    <line x1="120" y1="220" x2="140" y2="220" stroke="currentColor" stroke-width="3" opacity="0.5"/>
                    <line x1="260" y1="220" x2="280" y2="220" stroke="currentColor" stroke-width="3" opacity="0.5"/>
                </svg>
            </div>

            <!-- Error Message -->
            <h1 class="text-4xl font-bold text-gray-900 dark:text-white mb-4">
                {{ __('errors.server_error') }}
            </h1>
            
            <p class="text-lg text-gray-600 dark:text-gray-400 mb-8 max-w-md mx-auto">
                {{ __('errors.server_error_description') }}
            </p>

            <!-- Error ID -->
            <div class="mb-8">
                <div class="inline-flex items-center px-4 py-2 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-lg">
                    <x-icon name="identification" class="h-4 w-4 text-red-600 dark:text-red-400 mr-2" />
                    <span class="text-sm font-medium text-red-800 dark:text-red-200">
                        {{ __('errors.error_id') }}: 
                        <code class="font-mono">{{ $errorId ?? 'ERR-' . time() }}</code>
                    </span>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="space-y-4 sm:space-y-0 sm:space-x-4 sm:flex sm:justify-center mb-12">
                <x-ui.button 
                    onclick="refreshPage()" 
                    variant="primary"
                    icon="arrow-path"
                >
                    {{ __('errors.try_again') }}
                </x-ui.button>

                <x-ui.button 
                    href="{{ route('home') }}" 
                    variant="secondary"
                    icon="home"
                >
                    {{ __('errors.go_home') }}
                </x-ui.button>

                <x-ui.button 
                    onclick="reportError()" 
                    variant="ghost"
                    icon="exclamation-triangle"
                >
                    {{ __('errors.report_error') }}
                </x-ui.button>
            </div>

            <!-- System Status -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-6 mb-8">
                <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4 flex items-center">
                    <x-icon name="server" class="h-5 w-5 mr-2" />
                    {{ __('errors.system_status') }}
                </h2>
                
                <div class="space-y-3">
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-gray-600 dark:text-gray-400">{{ __('errors.web_server') }}</span>
                        <div class="flex items-center">
                            <div class="w-2 h-2 bg-yellow-400 rounded-full mr-2"></div>
                            <span class="text-sm font-medium text-yellow-600 dark:text-yellow-400">{{ __('errors.degraded') }}</span>
                        </div>
                    </div>
                    
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-gray-600 dark:text-gray-400">{{ __('errors.database') }}</span>
                        <div class="flex items-center">
                            <div class="w-2 h-2 bg-green-400 rounded-full mr-2"></div>
                            <span class="text-sm font-medium text-green-600 dark:text-green-400">{{ __('errors.operational') }}</span>
                        </div>
                    </div>
                    
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-gray-600 dark:text-gray-400">{{ __('errors.api_services') }}</span>
                        <div class="flex items-center">
                            <div class="w-2 h-2 bg-green-400 rounded-full mr-2"></div>
                            <span class="text-sm font-medium text-green-600 dark:text-green-400">{{ __('errors.operational') }}</span>
                        </div>
                    </div>
                    
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-gray-600 dark:text-gray-400">{{ __('errors.file_storage') }}</span>
                        <div class="flex items-center">
                            <div class="w-2 h-2 bg-green-400 rounded-full mr-2"></div>
                            <span class="text-sm font-medium text-green-600 dark:text-green-400">{{ __('errors.operational') }}</span>
                        </div>
                    </div>
                </div>
                
                <div class="mt-4 pt-4 border-t border-gray-200 dark:border-gray-700">
                    <p class="text-xs text-gray-500 dark:text-gray-400">
                        {{ __('errors.last_updated') }}: {{ now()->format('M j, Y \a\t g:i A') }}
                    </p>
                </div>
            </div>

            <!-- What You Can Do -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-6 mb-8">
                <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">
                    {{ __('errors.what_you_can_do') }}
                </h2>
                
                <div class="space-y-4 text-left">
                    <div class="flex items-start space-x-3">
                        <div class="flex-shrink-0">
                            <div class="w-6 h-6 bg-blue-100 dark:bg-blue-900 rounded-full flex items-center justify-center">
                                <span class="text-xs font-bold text-blue-600 dark:text-blue-400">1</span>
                            </div>
                        </div>
                        <div>
                            <h3 class="text-sm font-medium text-gray-900 dark:text-white">{{ __('errors.wait_and_retry') }}</h3>
                            <p class="text-sm text-gray-600 dark:text-gray-400">{{ __('errors.wait_retry_description') }}</p>
                        </div>
                    </div>
                    
                    <div class="flex items-start space-x-3">
                        <div class="flex-shrink-0">
                            <div class="w-6 h-6 bg-blue-100 dark:bg-blue-900 rounded-full flex items-center justify-center">
                                <span class="text-xs font-bold text-blue-600 dark:text-blue-400">2</span>
                            </div>
                        </div>
                        <div>
                            <h3 class="text-sm font-medium text-gray-900 dark:text-white">{{ __('errors.check_status_page') }}</h3>
                            <p class="text-sm text-gray-600 dark:text-gray-400">
                                {{ __('errors.status_page_description') }}
                                <a href="{{ route('help.status') }}" class="text-blue-600 hover:text-blue-500 dark:text-blue-400 dark:hover:text-blue-300">
                                    {{ __('errors.status_page') }}
                                </a>
                            </p>
                        </div>
                    </div>
                    
                    <div class="flex items-start space-x-3">
                        <div class="flex-shrink-0">
                            <div class="w-6 h-6 bg-blue-100 dark:bg-blue-900 rounded-full flex items-center justify-center">
                                <span class="text-xs font-bold text-blue-600 dark:text-blue-400">3</span>
                            </div>
                        </div>
                        <div>
                            <h3 class="text-sm font-medium text-gray-900 dark:text-white">{{ __('errors.contact_support') }}</h3>
                            <p class="text-sm text-gray-600 dark:text-gray-400">{{ __('errors.contact_support_description') }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Contact Support -->
            <div class="bg-gradient-to-r from-red-500 to-pink-600 rounded-lg shadow-lg p-6">
                <div class="text-center">
                    <h2 class="text-xl font-bold text-white mb-2">
                        {{ __('errors.need_immediate_help') }}
                    </h2>
                    <p class="text-red-100 mb-6">
                        {{ __('errors.immediate_help_description') }}
                    </p>
                    
                    <div class="space-y-3 sm:space-y-0 sm:space-x-4 sm:flex sm:justify-center">
                        <x-ui.button 
                            onclick="openSupportModal()" 
                            variant="secondary"
                            class="bg-white text-red-600 hover:bg-gray-50"
                        >
                            {{ __('errors.contact_support') }}
                        </x-ui.button>
                        
                        <x-ui.button 
                            href="tel:+1-555-123-4567" 
                            variant="ghost"
                            class="text-white border-white hover:bg-white hover:text-red-600"
                        >
                            {{ __('errors.call_support') }}
                        </x-ui.button>
                    </div>
                </div>
            </div>

            <!-- Debug Information (Development Only) -->
            @if(config('app.debug') && isset($exception))
                <div class="mt-8 bg-gray-100 dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-lg p-4 text-left">
                    <h3 class="text-sm font-medium text-gray-900 dark:text-white mb-3">
                        {{ __('errors.debug_information') }}
                    </h3>
                    <div class="text-xs text-gray-700 dark:text-gray-300 space-y-2 font-mono">
                        <div>
                            <strong>{{ __('errors.exception') }}:</strong> {{ get_class($exception) }}
                        </div>
                        <div>
                            <strong>{{ __('errors.message') }}:</strong> {{ $exception->getMessage() }}
                        </div>
                        <div>
                            <strong>{{ __('errors.file') }}:</strong> {{ $exception->getFile() }}:{{ $exception->getLine() }}
                        </div>
                        <div>
                            <strong>{{ __('errors.timestamp') }}:</strong> {{ now()->toISOString() }}
                        </div>
                        <div>
                            <strong>{{ __('errors.request_id') }}:</strong> {{ request()->header('X-Request-ID', 'N/A') }}
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>

<!-- Error Report Modal -->
<x-ui.modal id="error-report-modal" size="lg">
    <div class="p-6">
        <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-6">
            {{ __('errors.report_error') }}
        </h3>
        
        <form onsubmit="submitErrorReport(event)">
            <div class="space-y-6">
                <!-- What were you doing? -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        {{ __('errors.what_were_you_doing') }}
                    </label>
                    <x-ui.textarea
                        name="user_action"
                        id="user_action"
                        rows="3"
                        placeholder="{{ __('errors.describe_action') }}"
                        required
                    />
                </div>
                
                <!-- Expected vs Actual -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            {{ __('errors.expected_result') }}
                        </label>
                        <x-ui.textarea
                            name="expected_result"
                            id="expected_result"
                            rows="3"
                            placeholder="{{ __('errors.what_should_happen') }}"
                        />
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            {{ __('errors.actual_result') }}
                        </label>
                        <x-ui.textarea
                            name="actual_result"
                            id="actual_result"
                            rows="3"
                            placeholder="{{ __('errors.what_actually_happened') }}"
                        />
                    </div>
                </div>
                
                <!-- Additional Information -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        {{ __('errors.additional_info') }}
                    </label>
                    <x-ui.textarea
                        name="additional_info"
                        id="additional_info"
                        rows="3"
                        placeholder="{{ __('errors.any_other_details') }}"
                    />
                </div>
                
                <!-- System Information (Auto-filled) -->
                <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4">
                    <h4 class="text-sm font-medium text-gray-900 dark:text-white mb-3">
                        {{ __('errors.system_information') }}
                    </h4>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 text-xs text-gray-600 dark:text-gray-400">
                        <div>
                            <strong>{{ __('errors.browser') }}:</strong> <span id="browser-info"></span>
                        </div>
                        <div>
                            <strong>{{ __('errors.screen_size') }}:</strong> <span id="screen-info"></span>
                        </div>
                        <div>
                            <strong>{{ __('errors.url') }}:</strong> <span id="url-info"></span>
                        </div>
                        <div>
                            <strong>{{ __('errors.timestamp') }}:</strong> <span id="timestamp-info"></span>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="flex justify-end space-x-3 mt-8">
                <x-ui.button 
                    type="button"
                    onclick="closeModal('error-report-modal')" 
                    variant="secondary"
                >
                    {{ __('errors.cancel') }}
                </x-ui.button>
                
                <x-ui.button 
                    type="submit" 
                    variant="primary"
                >
                    {{ __('errors.submit_report') }}
                </x-ui.button>
            </div>
        </form>
    </div>
</x-ui.modal>

<!-- Support Contact Modal -->
<x-ui.modal id="support-modal" size="md">
    <div class="p-6">
        <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-6">
            {{ __('errors.contact_support') }}
        </h3>
        
        <div class="space-y-4">
            <div class="flex items-center space-x-3 p-4 border border-gray-200 dark:border-gray-600 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 cursor-pointer" onclick="window.open('mailto:support@jobportal.com?subject=Server Error - {{ $errorId ?? 'ERR-' . time() }}')">
                <x-icon name="envelope" class="h-6 w-6 text-blue-600 dark:text-blue-400" />
                <div>
                    <h4 class="font-medium text-gray-900 dark:text-white">{{ __('errors.email_support') }}</h4>
                    <p class="text-sm text-gray-500 dark:text-gray-400">{{ __('errors.support_email') }}</p>
                </div>
            </div>
            
            <div class="flex items-center space-x-3 p-4 border border-gray-200 dark:border-gray-600 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 cursor-pointer" onclick="window.open('tel:+1-555-123-4567')">
                <x-icon name="phone" class="h-6 w-6 text-green-600 dark:text-green-400" />
                <div>
                    <h4 class="font-medium text-gray-900 dark:text-white">{{ __('errors.phone_support') }}</h4>
                    <p class="text-sm text-gray-500 dark:text-gray-400">+1 (555) 123-4567</p>
                </div>
            </div>
            
            <div class="flex items-center space-x-3 p-4 border border-gray-200 dark:border-gray-600 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 cursor-pointer" onclick="startLiveChat()">
                <x-icon name="chat-bubble-left-right" class="h-6 w-6 text-purple-600 dark:text-purple-400" />
                <div>
                    <h4 class="font-medium text-gray-900 dark:text-white">{{ __('errors.live_chat') }}</h4>
                    <p class="text-sm text-gray-500 dark:text-gray-400">{{ __('errors.available_24_7') }}</p>
                </div>
            </div>
        </div>
        
        <div class="mt-6 p-4 bg-yellow-50 dark:bg-yellow-900/20 border border-yellow-200 dark:border-yellow-800 rounded-lg">
            <div class="flex">
                <x-icon name="exclamation-triangle" class="h-5 w-5 text-yellow-600 dark:text-yellow-400 mr-2 mt-0.5" />
                <div>
                    <h4 class="text-sm font-medium text-yellow-800 dark:text-yellow-200">
                        {{ __('errors.include_error_id') }}
                    </h4>
                    <p class="text-sm text-yellow-700 dark:text-yellow-300 mt-1">
                        {{ __('errors.error_id_help') }}: <code class="font-mono bg-yellow-100 dark:bg-yellow-800 px-1 rounded">{{ $errorId ?? 'ERR-' . time() }}</code>
                    </p>
                </div>
            </div>
        </div>
        
        <div class="flex justify-end mt-6">
            <x-ui.button 
                onclick="closeModal('support-modal')" 
                variant="primary"
            >
                {{ __('errors.close') }}
            </x-ui.button>
        </div>
    </div>
</x-ui.modal>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Track server error for analytics
    trackServerError();
    
    // Fill system information
    fillSystemInfo();
    
    // Auto-retry after 30 seconds
    setTimeout(() => {
        showRetryNotification();
    }, 30000);
});

function refreshPage() {
    window.location.reload();
}

function reportError() {
    openModal('error-report-modal');
}

function openSupportModal() {
    openModal('support-modal');
}

function submitErrorReport(event) {
    event.preventDefault();
    
    const formData = new FormData(event.target);
    
    // Add system information
    formData.append('browser_info', getBrowserInfo());
    formData.append('screen_info', getScreenInfo());
    formData.append('url', window.location.href);
    formData.append('timestamp', new Date().toISOString());
    formData.append('error_id', '{{ $errorId ?? 'ERR-' . time() }}');
    
    fetch('/errors/report', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            closeModal('error-report-modal');
            showNotification('{{ __("errors.report_submitted") }}', 'success');
            
            // Reset form
            event.target.reset();
        } else {
            showNotification(data.message || '{{ __("errors.report_failed") }}', 'error');
        }
    })
    .catch(error => {
        console.error('Error submitting report:', error);
        showNotification('{{ __("errors.report_error") }}', 'error');
    });
}

function fillSystemInfo() {
    document.getElementById('browser-info').textContent = getBrowserInfo();
    document.getElementById('screen-info').textContent = getScreenInfo();
    document.getElementById('url-info').textContent = window.location.href;
    document.getElementById('timestamp-info').textContent = new Date().toLocaleString();
}

function getBrowserInfo() {
    const ua = navigator.userAgent;
    let browser = 'Unknown';
    
    if (ua.includes('Chrome')) browser = 'Chrome';
    else if (ua.includes('Firefox')) browser = 'Firefox';
    else if (ua.includes('Safari')) browser = 'Safari';
    else if (ua.includes('Edge')) browser = 'Edge';
    
    return `${browser} (${navigator.platform})`;
}

function getScreenInfo() {
    return `${screen.width}x${screen.height} (${window.innerWidth}x${window.innerHeight})`;
}

function trackServerError() {
    // Track server error for analytics
    if (typeof gtag !== 'undefined') {
        gtag('event', 'server_error', {
            'error_id': '{{ $errorId ?? 'ERR-' . time() }}',
            'page_location': window.location.href
        });
    }
    
    // Send to internal analytics
    fetch('/analytics/500', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({
            error_id: '{{ $errorId ?? 'ERR-' . time() }}',
            url: window.location.href,
            user_agent: navigator.userAgent,
            timestamp: new Date().toISOString()
        })
    })
    .catch(error => {
        console.error('Error tracking 500:', error);
    });
}

function showRetryNotification() {
    const notification = document.createElement('div');
    notification.className = 'fixed bottom-4 left-4 bg-blue-600 text-white p-4 rounded-lg shadow-lg z-50 max-w-sm';
    notification.innerHTML = `
        <div class="flex items-center space-x-3">
            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
            <div class="flex-1">
                <p class="text-sm font-medium">{{ __('errors.ready_to_retry') }}</p>
                <p class="text-xs opacity-90">{{ __('errors.issue_might_be_resolved') }}</p>
            </div>
            <button onclick="refreshPage()" class="text-xs bg-white bg-opacity-20 hover:bg-opacity-30 px-2 py-1 rounded">
                {{ __('errors.retry') }}
            </button>
            <button onclick="this.parentElement.parentElement.remove()" class="text-white hover:text-gray-200">
                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        </div>
    `;
    
    document.body.appendChild(notification);
    
    // Auto-remove after 10 seconds
    setTimeout(() => {
        if (notification.parentElement) {
            notification.remove();
        }
    }, 10000);
}

function startLiveChat() {
    // Implementation for live chat
    showNotification('{{ __("errors.live_chat_starting") }}', 'info');
    closeModal('support-modal');
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

// Automatic retry mechanism
let retryCount = 0;
const maxRetries = 3;

function autoRetry() {
    if (retryCount < maxRetries) {
        retryCount++;
        
        fetch(window.location.href, { method: 'HEAD' })
        .then(response => {
            if (response.ok) {
                showNotification('{{ __("errors.service_restored") }}', 'success');
                setTimeout(() => {
                    window.location.reload();
                }, 2000);
            } else {
                setTimeout(autoRetry, 60000); // Retry in 1 minute
            }
        })
        .catch(() => {
            setTimeout(autoRetry, 60000); // Retry in 1 minute
        });
    }
}

// Start auto-retry after 2 minutes
setTimeout(autoRetry, 120000);
</script>
@endpush 