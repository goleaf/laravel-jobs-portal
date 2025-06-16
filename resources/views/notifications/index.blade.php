@extends('layouts.app')

@section('title', __('notifications.notification_center'))

@section('content')
<div class="min-h-screen bg-gray-50 dark:bg-gray-900 py-8">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="mb-8">
            <div class="sm:flex sm:items-center sm:justify-between">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900 dark:text-white">
                        {{ __('notifications.notification_center') }}
                    </h1>
                    <p class="mt-2 text-gray-600 dark:text-gray-400">
                        {{ __('notifications.manage_all_notifications') }}
                    </p>
                </div>
                
                <div class="mt-4 sm:mt-0 flex space-x-3">
                    <x-ui.button 
                        onclick="markAllAsRead()" 
                        variant="secondary"
                        icon="check"
                    >
                        {{ __('notifications.mark_all_read') }}
                    </x-ui.button>
                    
                    <x-ui.button 
                        href="{{ route('notifications.settings') }}" 
                        variant="primary"
                        icon="cog-6-tooth"
                    >
                        {{ __('notifications.settings') }}
                    </x-ui.button>
                </div>
            </div>
        </div>

        <!-- Notification Stats -->
        <div class="grid grid-cols-1 gap-5 sm:grid-cols-3 mb-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow rounded-lg">
                <div class="p-5">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <x-icon name="bell" class="h-6 w-6 text-blue-600 dark:text-blue-400" />
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400 truncate">
                                    {{ __('notifications.unread_notifications') }}
                                </dt>
                                <dd class="text-2xl font-semibold text-gray-900 dark:text-white">
                                    {{ number_format($stats['unread'] ?? 0) }}
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
                                    {{ __('notifications.today') }}
                                </dt>
                                <dd class="text-2xl font-semibold text-gray-900 dark:text-white">
                                    {{ number_format($stats['today'] ?? 0) }}
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
                            <x-icon name="calendar" class="h-6 w-6 text-purple-600 dark:text-purple-400" />
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400 truncate">
                                    {{ __('notifications.this_week') }}
                                </dt>
                                <dd class="text-2xl font-semibold text-gray-900 dark:text-white">
                                    {{ number_format($stats['week'] ?? 0) }}
                                </dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filter Tabs -->
        <div class="bg-white dark:bg-gray-800 shadow rounded-lg mb-8">
            <div class="border-b border-gray-200 dark:border-gray-700">
                <nav class="-mb-px flex space-x-8 px-6" aria-label="Tabs">
                    <button 
                        onclick="filterNotifications('all')" 
                        class="notification-tab active border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm"
                        data-filter="all"
                    >
                        {{ __('notifications.all') }}
                        <span class="bg-gray-100 dark:bg-gray-700 text-gray-900 dark:text-gray-300 ml-2 py-0.5 px-2.5 rounded-full text-xs font-medium">
                            {{ $stats['total'] ?? 0 }}
                        </span>
                    </button>
                    
                    <button 
                        onclick="filterNotifications('unread')" 
                        class="notification-tab border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm"
                        data-filter="unread"
                    >
                        {{ __('notifications.unread') }}
                        <span class="bg-blue-100 dark:bg-blue-900 text-blue-900 dark:text-blue-300 ml-2 py-0.5 px-2.5 rounded-full text-xs font-medium">
                            {{ $stats['unread'] ?? 0 }}
                        </span>
                    </button>
                    
                    <button 
                        onclick="filterNotifications('job_alerts')" 
                        class="notification-tab border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm"
                        data-filter="job_alerts"
                    >
                        {{ __('notifications.job_alerts') }}
                        <span class="bg-green-100 dark:bg-green-900 text-green-900 dark:text-green-300 ml-2 py-0.5 px-2.5 rounded-full text-xs font-medium">
                            {{ $stats['job_alerts'] ?? 0 }}
                        </span>
                    </button>
                    
                    <button 
                        onclick="filterNotifications('applications')" 
                        class="notification-tab border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm"
                        data-filter="applications"
                    >
                        {{ __('notifications.applications') }}
                        <span class="bg-yellow-100 dark:bg-yellow-900 text-yellow-900 dark:text-yellow-300 ml-2 py-0.5 px-2.5 rounded-full text-xs font-medium">
                            {{ $stats['applications'] ?? 0 }}
                        </span>
                    </button>
                    
                    <button 
                        onclick="filterNotifications('messages')" 
                        class="notification-tab border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm"
                        data-filter="messages"
                    >
                        {{ __('notifications.messages') }}
                        <span class="bg-purple-100 dark:bg-purple-900 text-purple-900 dark:text-purple-300 ml-2 py-0.5 px-2.5 rounded-full text-xs font-medium">
                            {{ $stats['messages'] ?? 0 }}
                        </span>
                    </button>
                </nav>
            </div>
        </div>

        <!-- Notifications List -->
        <div class="bg-white dark:bg-gray-800 shadow rounded-lg">
            <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                <div class="flex items-center justify-between">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-white">
                        {{ __('notifications.recent_notifications') }}
                    </h3>
                    
                    <div class="flex items-center space-x-3">
                        <x-ui.select
                            name="sort_by"
                            id="sort_by"
                            :options="[
                                'newest' => __('notifications.newest_first'),
                                'oldest' => __('notifications.oldest_first'),
                                'unread' => __('notifications.unread_first'),
                                'type' => __('notifications.by_type')
                            ]"
                            :selected="request('sort_by', 'newest')"
                            onchange="sortNotifications(this.value)"
                            class="w-40"
                        />
                        
                        <button 
                            onclick="refreshNotifications()" 
                            class="p-2 text-gray-400 hover:text-gray-600 dark:hover:text-gray-300"
                            title="{{ __('notifications.refresh') }}"
                        >
                            <x-icon name="arrow-path" class="h-5 w-5" />
                        </button>
                    </div>
                </div>
            </div>
            
            <div id="notifications-container">
                @if($notifications && count($notifications) > 0)
                    <div class="divide-y divide-gray-200 dark:divide-gray-700">
                        @foreach($notifications as $notification)
                            <div class="notification-item p-6 hover:bg-gray-50 dark:hover:bg-gray-700 {{ $notification['read_at'] ? '' : 'bg-blue-50 dark:bg-blue-900/20' }}" 
                                 data-id="{{ $notification['id'] }}" 
                                 data-type="{{ $notification['type'] }}"
                                 data-read="{{ $notification['read_at'] ? 'true' : 'false' }}">
                                <div class="flex items-start space-x-4">
                                    <!-- Notification Icon -->
                                    <div class="flex-shrink-0">
                                        <div class="h-10 w-10 rounded-full flex items-center justify-center {{ $notification['icon_bg'] ?? 'bg-blue-100 dark:bg-blue-900' }}">
                                            <x-icon 
                                                :name="$notification['icon'] ?? 'bell'" 
                                                class="h-5 w-5 {{ $notification['icon_color'] ?? 'text-blue-600 dark:text-blue-400' }}" 
                                            />
                                        </div>
                                    </div>
                                    
                                    <!-- Notification Content -->
                                    <div class="flex-1 min-w-0">
                                        <div class="flex items-start justify-between">
                                            <div class="flex-1">
                                                <h4 class="text-sm font-medium text-gray-900 dark:text-white">
                                                    {{ $notification['title'] }}
                                                </h4>
                                                <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                                                    {{ $notification['message'] }}
                                                </p>
                                                
                                                @if(isset($notification['action_url']))
                                                    <div class="mt-2">
                                                        <a href="{{ $notification['action_url'] }}" 
                                                           class="text-sm font-medium text-blue-600 hover:text-blue-500 dark:text-blue-400 dark:hover:text-blue-300"
                                                           onclick="markAsRead('{{ $notification['id'] }}')">
                                                            {{ $notification['action_text'] ?? __('notifications.view_details') }} →
                                                        </a>
                                                    </div>
                                                @endif
                                            </div>
                                            
                                            <!-- Notification Actions -->
                                            <div class="flex items-center space-x-2 ml-4">
                                                @if(!$notification['read_at'])
                                                    <button 
                                                        onclick="markAsRead('{{ $notification['id'] }}')"
                                                        class="text-xs text-blue-600 hover:text-blue-500 dark:text-blue-400 dark:hover:text-blue-300"
                                                        title="{{ __('notifications.mark_as_read') }}"
                                                    >
                                                        <x-icon name="check" class="h-4 w-4" />
                                                    </button>
                                                @endif
                                                
                                                <button 
                                                    onclick="deleteNotification('{{ $notification['id'] }}')"
                                                    class="text-xs text-red-600 hover:text-red-500 dark:text-red-400 dark:hover:text-red-300"
                                                    title="{{ __('notifications.delete') }}"
                                                >
                                                    <x-icon name="trash" class="h-4 w-4" />
                                                </button>
                                            </div>
                                        </div>
                                        
                                        <!-- Notification Meta -->
                                        <div class="mt-2 flex items-center space-x-4 text-xs text-gray-500 dark:text-gray-400">
                                            <span class="flex items-center">
                                                <x-icon name="clock" class="h-3 w-3 mr-1" />
                                                {{ $notification['created_at'] }}
                                            </span>
                                            
                                            <span class="flex items-center">
                                                <x-icon name="tag" class="h-3 w-3 mr-1" />
                                                {{ __('notifications.type_' . $notification['type']) }}
                                            </span>
                                            
                                            @if($notification['read_at'])
                                                <span class="flex items-center text-green-600 dark:text-green-400">
                                                    <x-icon name="check-circle" class="h-3 w-3 mr-1" />
                                                    {{ __('notifications.read') }}
                                                </span>
                                            @else
                                                <span class="flex items-center text-blue-600 dark:text-blue-400">
                                                    <x-icon name="exclamation-circle" class="h-3 w-3 mr-1" />
                                                    {{ __('notifications.unread') }}
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                    
                    <!-- Pagination -->
                    @if($notifications->hasPages())
                        <div class="px-6 py-4 border-t border-gray-200 dark:border-gray-700">
                            {{ $notifications->links() }}
                        </div>
                    @endif
                @else
                    <div class="px-6 py-12 text-center">
                        <x-icon name="bell-slash" class="mx-auto h-12 w-12 text-gray-400" />
                        <h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-white">
                            {{ __('notifications.no_notifications') }}
                        </h3>
                        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                            {{ __('notifications.no_notifications_description') }}
                        </p>
                        <div class="mt-6">
                            <x-ui.button 
                                href="{{ route('notifications.settings') }}" 
                                variant="primary"
                            >
                                {{ __('notifications.configure_notifications') }}
                            </x-ui.button>
                        </div>
                    </div>
                @endif
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="mt-8 bg-white dark:bg-gray-800 shadow rounded-lg">
            <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                <h3 class="text-lg font-medium text-gray-900 dark:text-white">
                    {{ __('notifications.quick_actions') }}
                </h3>
            </div>
            <div class="p-6">
                <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">
                    <x-ui.button 
                        onclick="enablePushNotifications()" 
                        variant="ghost" 
                        class="justify-start"
                        icon="device-phone-mobile"
                    >
                        {{ __('notifications.enable_push') }}
                    </x-ui.button>

                    <x-ui.button 
                        href="{{ route('notifications.email-preferences') }}" 
                        variant="ghost" 
                        class="justify-start"
                        icon="envelope"
                    >
                        {{ __('notifications.email_preferences') }}
                    </x-ui.button>

                    <x-ui.button 
                        onclick="exportNotifications()" 
                        variant="ghost" 
                        class="justify-start"
                        icon="arrow-down-tray"
                    >
                        {{ __('notifications.export_history') }}
                    </x-ui.button>

                    <x-ui.button 
                        onclick="clearOldNotifications()" 
                        variant="ghost" 
                        class="justify-start"
                        icon="trash"
                    >
                        {{ __('notifications.clear_old') }}
                    </x-ui.button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Push Notification Permission Modal -->
<x-ui.modal id="push-permission-modal" size="md">
    <div class="p-6">
        <div class="flex items-center">
            <div class="flex-shrink-0">
                <x-icon name="device-phone-mobile" class="h-6 w-6 text-blue-600 dark:text-blue-400" />
            </div>
            <div class="ml-3">
                <h3 class="text-lg font-medium text-gray-900 dark:text-white">
                    {{ __('notifications.enable_push_notifications') }}
                </h3>
                <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">
                    {{ __('notifications.push_notification_description') }}
                </p>
            </div>
        </div>
        
        <div class="mt-6 flex justify-end space-x-3">
            <x-ui.button 
                onclick="closeModal('push-permission-modal')" 
                variant="secondary"
            >
                {{ __('notifications.not_now') }}
            </x-ui.button>
            
            <x-ui.button 
                onclick="requestPushPermission()" 
                variant="primary"
            >
                {{ __('notifications.enable') }}
            </x-ui.button>
        </div>
    </div>
</x-ui.modal>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Initialize notification system
    initializeNotifications();
    
    // Auto-refresh notifications every 30 seconds
    setInterval(refreshNotifications, 30000);
});

function initializeNotifications() {
    // Check for push notification support
    if ('Notification' in window && 'serviceWorker' in navigator) {
        // Service worker is available
        console.log('Push notifications supported');
    }
    
    // Load notification preferences
    loadNotificationPreferences();
}

function filterNotifications(type) {
    // Update active tab
    document.querySelectorAll('.notification-tab').forEach(tab => {
        tab.classList.remove('active', 'border-blue-500', 'text-blue-600');
        tab.classList.add('border-transparent', 'text-gray-500');
    });
    
    const activeTab = document.querySelector(`[data-filter="${type}"]`);
    activeTab.classList.add('active', 'border-blue-500', 'text-blue-600');
    activeTab.classList.remove('border-transparent', 'text-gray-500');
    
    // Filter notifications
    const notifications = document.querySelectorAll('.notification-item');
    notifications.forEach(notification => {
        const notificationType = notification.dataset.type;
        const isRead = notification.dataset.read === 'true';
        
        let show = false;
        
        switch(type) {
            case 'all':
                show = true;
                break;
            case 'unread':
                show = !isRead;
                break;
            case 'job_alerts':
                show = notificationType === 'job_alert';
                break;
            case 'applications':
                show = notificationType === 'application';
                break;
            case 'messages':
                show = notificationType === 'message';
                break;
        }
        
        notification.style.display = show ? 'block' : 'none';
    });
    
    // Update URL
    const url = new URL(window.location);
    url.searchParams.set('filter', type);
    window.history.pushState({}, '', url);
}

function sortNotifications(sortBy) {
    const container = document.getElementById('notifications-container');
    const notifications = Array.from(container.querySelectorAll('.notification-item'));
    
    notifications.sort((a, b) => {
        switch(sortBy) {
            case 'newest':
                return new Date(b.dataset.createdAt) - new Date(a.dataset.createdAt);
            case 'oldest':
                return new Date(a.dataset.createdAt) - new Date(b.dataset.createdAt);
            case 'unread':
                if (a.dataset.read === 'false' && b.dataset.read === 'true') return -1;
                if (a.dataset.read === 'true' && b.dataset.read === 'false') return 1;
                return new Date(b.dataset.createdAt) - new Date(a.dataset.createdAt);
            case 'type':
                return a.dataset.type.localeCompare(b.dataset.type);
            default:
                return 0;
        }
    });
    
    // Re-append sorted notifications
    const parent = notifications[0].parentNode;
    notifications.forEach(notification => parent.appendChild(notification));
}

function markAsRead(notificationId) {
    fetch(`/notifications/${notificationId}/read`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Content-Type': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            const notification = document.querySelector(`[data-id="${notificationId}"]`);
            notification.classList.remove('bg-blue-50', 'dark:bg-blue-900/20');
            notification.dataset.read = 'true';
            
            // Update unread count
            updateUnreadCount();
            
            // Remove mark as read button
            const markReadBtn = notification.querySelector('[onclick*="markAsRead"]');
            if (markReadBtn) markReadBtn.remove();
        }
    })
    .catch(error => {
        console.error('Error marking notification as read:', error);
    });
}

function markAllAsRead() {
    fetch('/notifications/mark-all-read', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Content-Type': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Update all notifications
            document.querySelectorAll('.notification-item').forEach(notification => {
                notification.classList.remove('bg-blue-50', 'dark:bg-blue-900/20');
                notification.dataset.read = 'true';
            });
            
            // Remove all mark as read buttons
            document.querySelectorAll('[onclick*="markAsRead"]').forEach(btn => btn.remove());
            
            // Update unread count
            updateUnreadCount();
            
            // Show success message
            showNotification('{{ __("notifications.all_marked_read") }}', 'success');
        }
    })
    .catch(error => {
        console.error('Error marking all notifications as read:', error);
    });
}

function deleteNotification(notificationId) {
    if (!confirm('{{ __("notifications.confirm_delete") }}')) return;
    
    fetch(`/notifications/${notificationId}`, {
        method: 'DELETE',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Content-Type': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            const notification = document.querySelector(`[data-id="${notificationId}"]`);
            notification.remove();
            
            // Update counts
            updateUnreadCount();
            
            showNotification('{{ __("notifications.notification_deleted") }}', 'success');
        }
    })
    .catch(error => {
        console.error('Error deleting notification:', error);
    });
}

function refreshNotifications() {
    fetch(window.location.href, {
        headers: {
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(response => response.text())
    .then(html => {
        const parser = new DOMParser();
        const doc = parser.parseFromString(html, 'text/html');
        const newContainer = doc.getElementById('notifications-container');
        
        if (newContainer) {
            document.getElementById('notifications-container').innerHTML = newContainer.innerHTML;
        }
        
        showNotification('{{ __("notifications.refreshed") }}', 'success');
    })
    .catch(error => {
        console.error('Error refreshing notifications:', error);
    });
}

function enablePushNotifications() {
    if (!('Notification' in window)) {
        alert('{{ __("notifications.push_not_supported") }}');
        return;
    }
    
    if (Notification.permission === 'granted') {
        showNotification('{{ __("notifications.push_already_enabled") }}', 'info');
        return;
    }
    
    openModal('push-permission-modal');
}

function requestPushPermission() {
    Notification.requestPermission().then(permission => {
        if (permission === 'granted') {
            // Register service worker and subscribe to push notifications
            registerPushNotifications();
            closeModal('push-permission-modal');
            showNotification('{{ __("notifications.push_enabled") }}', 'success');
        } else {
            showNotification('{{ __("notifications.push_denied") }}', 'error');
        }
    });
}

function registerPushNotifications() {
    if ('serviceWorker' in navigator) {
        navigator.serviceWorker.register('/sw.js')
        .then(registration => {
            console.log('Service Worker registered:', registration);
            
            // Subscribe to push notifications
            return registration.pushManager.subscribe({
                userVisibleOnly: true,
                applicationServerKey: '{{ config("app.vapid_public_key") }}'
            });
        })
        .then(subscription => {
            // Send subscription to server
            fetch('/notifications/push/subscribe', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify(subscription)
            });
        })
        .catch(error => {
            console.error('Error registering push notifications:', error);
        });
    }
}

function exportNotifications() {
    window.location.href = '/notifications/export';
}

function clearOldNotifications() {
    if (!confirm('{{ __("notifications.confirm_clear_old") }}')) return;
    
    fetch('/notifications/clear-old', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Content-Type': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            refreshNotifications();
            showNotification('{{ __("notifications.old_notifications_cleared") }}', 'success');
        }
    })
    .catch(error => {
        console.error('Error clearing old notifications:', error);
    });
}

function updateUnreadCount() {
    const unreadNotifications = document.querySelectorAll('[data-read="false"]').length;
    
    // Update badge counts
    document.querySelectorAll('[data-filter="unread"] span').forEach(span => {
        span.textContent = unreadNotifications;
    });
    
    // Update header notification badge
    const headerBadge = document.querySelector('.notification-badge');
    if (headerBadge) {
        if (unreadNotifications > 0) {
            headerBadge.textContent = unreadNotifications;
            headerBadge.style.display = 'block';
        } else {
            headerBadge.style.display = 'none';
        }
    }
}

function loadNotificationPreferences() {
    // Load user's notification preferences from localStorage or API
    const preferences = JSON.parse(localStorage.getItem('notificationPreferences') || '{}');
    
    // Apply preferences
    if (preferences.autoRefresh !== false) {
        // Auto-refresh is enabled by default
    }
}

function showNotification(message, type = 'info') {
    // Create and show a toast notification
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

// Initialize on page load
document.addEventListener('DOMContentLoaded', function() {
    // Apply current filter from URL
    const urlParams = new URLSearchParams(window.location.search);
    const filter = urlParams.get('filter') || 'all';
    filterNotifications(filter);
});
</script>
@endpush