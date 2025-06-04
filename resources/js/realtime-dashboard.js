/**
 * Real-Time Dashboard JavaScript Module
 * Handles WebSocket connections and live updates
 */

class RealTimeDashboard {
    constructor(config = {}) {
        this.config = {
            wsUrl: config.wsUrl || 'ws://localhost:6001',
            apiUrl: config.apiUrl || '/api/realtime',
            updateInterval: config.updateInterval || 30000,
            retryAttempts: config.retryAttempts || 3,
            retryDelay: config.retryDelay || 5000,
            ...config
        };

        this.websocket = null;
        this.isConnected = false;
        this.retryCount = 0;
        this.updateTimer = null;
        this.subscriptions = new Map();
        
        this.init();
    }

    /**
     * Initialize the real-time dashboard
     */
    init() {
        this.setupWebSocket();
        this.setupEventListeners();
        this.startPeriodicUpdates();
        this.loadInitialData();
        
        console.log('✅ Real-Time Dashboard initialized');
    }

    /**
     * Setup WebSocket connection
     */
    setupWebSocket() {
        try {
            this.websocket = new WebSocket(this.config.wsUrl);
            
            this.websocket.onopen = () => {
                this.isConnected = true;
                this.retryCount = 0;
                this.updateConnectionStatus('connected');
                console.log('🔗 WebSocket connected');
                
                this.authenticateWebSocket();
            };

            this.websocket.onmessage = (event) => {
                this.handleWebSocketMessage(event);
            };

            this.websocket.onclose = () => {
                this.isConnected = false;
                this.updateConnectionStatus('disconnected');
                console.log('🔌 WebSocket disconnected');
                
                this.attemptReconnection();
            };

            this.websocket.onerror = (error) => {
                console.error('❌ WebSocket error:', error);
                this.updateConnectionStatus('error');
            };

        } catch (error) {
            console.error('❌ Failed to setup WebSocket:', error);
            this.fallbackToPolling();
        }
    }

    /**
     * Handle incoming WebSocket messages
     */
    handleWebSocketMessage(event) {
        try {
            const data = JSON.parse(event.data);
            
            switch (data.type) {
                case 'job_application_status':
                    this.handleApplicationStatusUpdate(data);
                    break;
                case 'notification':
                    this.handleNotification(data);
                    break;
                case 'stats_update':
                    this.handleStatsUpdate(data);
                    break;
                case 'activity_feed':
                    this.handleActivityFeedUpdate(data);
                    break;
                default:
                    console.log('📨 Unknown message type:', data.type);
            }
        } catch (error) {
            console.error('❌ Error parsing WebSocket message:', error);
        }
    }

    /**
     * Handle job application status updates
     */
    handleApplicationStatusUpdate(data) {
        console.log('📄 Application status update:', data);
        
        this.updateApplicationStatusUI(data);
        
        this.showNotification({
            title: 'Application Status Updated',
            message: data.message,
            type: data.notification_type,
            timestamp: data.timestamp
        });
        
        this.refreshUserStats();
        
        document.dispatchEvent(new CustomEvent('applicationStatusChanged', {
            detail: data
        }));
    }

    /**
     * Handle general notifications
     */
    handleNotification(data) {
        this.showNotification(data);
        this.updateNotificationBadge();
    }

    /**
     * Handle statistics updates
     */
    handleStatsUpdate(data) {
        console.log('📊 Stats update:', data);
        this.updateDashboardStats(data);
    }

    /**
     * Handle activity feed updates
     */
    handleActivityFeedUpdate(data) {
        this.updateActivityFeed(data);
    }

    /**
     * Setup event listeners for UI interactions
     */
    setupEventListeners() {
        document.addEventListener('click', (e) => {
            if (e.target.classList.contains('status-update-btn')) {
                this.handleStatusUpdateClick(e);
            }
        });

        document.addEventListener('click', (e) => {
            if (e.target.classList.contains('notification-item')) {
                this.handleNotificationClick(e);
            }
        });

        const refreshBtn = document.getElementById('dashboard-refresh');
        if (refreshBtn) {
            refreshBtn.addEventListener('click', () => {
                this.refreshDashboard();
            });
        }

        document.addEventListener('visibilitychange', () => {
            if (document.hidden) {
                this.pauseUpdates();
            } else {
                this.resumeUpdates();
            }
        });
    }

    /**
     * Handle status update button clicks
     */
    async handleStatusUpdateClick(event) {
        const button = event.target;
        const applicationId = button.dataset.applicationId;
        const newStatus = button.dataset.newStatus;
        
        if (!applicationId || !newStatus) return;

        try {
            button.disabled = true;
            button.innerHTML = '<span class="spinner"></span> Updating...';

            const response = await fetch(`${this.config.apiUrl}/applications/${applicationId}/status`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                },
                body: JSON.stringify({
                    status: newStatus,
                    notes: button.dataset.notes || ''
                })
            });

            const result = await response.json();

            if (response.ok) {
                this.showNotification({
                    title: 'Success',
                    message: 'Application status updated successfully',
                    type: 'success'
                });
            } else {
                throw new Error(result.error || 'Failed to update status');
            }

        } catch (error) {
            console.error('❌ Error updating status:', error);
            this.showNotification({
                title: 'Error',
                message: error.message,
                type: 'error'
            });
        } finally {
            button.disabled = false;
            button.innerHTML = button.dataset.originalText || 'Update';
        }
    }

    /**
     * Update application status in UI
     */
    updateApplicationStatusUI(data) {
        const statusElements = document.querySelectorAll(`[data-application-id="${data.application_id}"]`);
        
        statusElements.forEach(element => {
            if (element.classList.contains('status-badge')) {
                element.className = `status-badge status-${data.new_status}`;
                element.textContent = data.new_status.replace('_', ' ').toUpperCase();
            }
            
            if (element.classList.contains('status-timeline')) {
                this.updateStatusTimeline(element, data);
            }
        });
    }

    /**
     * Update status timeline
     */
    updateStatusTimeline(element, data) {
        const timelineItem = document.createElement('div');
        timelineItem.className = 'timeline-item';
        timelineItem.innerHTML = `
            <div class="timeline-marker bg-${this.getStatusColor(data.new_status)}"></div>
            <div class="timeline-content">
                <h4>${data.message}</h4>
                <p class="text-sm text-gray-600">${new Date(data.timestamp).toLocaleString()}</p>
            </div>
        `;
        
        element.prepend(timelineItem);
    }

    /**
     * Show notification
     */
    showNotification(notification) {
        const container = document.getElementById('notification-container') || this.createNotificationContainer();
        
        const notificationEl = document.createElement('div');
        notificationEl.className = `notification notification-${notification.type} animate-slide-in`;
        notificationEl.innerHTML = `
            <div class="notification-content">
                <div class="notification-icon">
                    ${this.getNotificationIcon(notification.type)}
                </div>
                <div class="notification-text">
                    <h4>${notification.title}</h4>
                    <p>${notification.message}</p>
                </div>
                <button class="notification-close">&times;</button>
            </div>
        `;
        
        notificationEl.querySelector('.notification-close').addEventListener('click', () => {
            notificationEl.remove();
        });
        
        container.appendChild(notificationEl);
        
        setTimeout(() => {
            if (notificationEl.parentNode) {
                notificationEl.classList.add('animate-slide-out');
                setTimeout(() => notificationEl.remove(), 300);
            }
        }, 5000);
    }

    /**
     * Create notification container
     */
    createNotificationContainer() {
        const container = document.createElement('div');
        container.id = 'notification-container';
        container.className = 'fixed top-4 right-4 z-50 space-y-2';
        document.body.appendChild(container);
        return container;
    }

    /**
     * Update dashboard statistics
     */
    updateDashboardStats(stats) {
        Object.entries(stats).forEach(([key, value]) => {
            const element = document.getElementById(`stat-${key}`);
            if (element) {
                this.animateCounterUpdate(element, value);
            }
        });
    }

    /**
     * Animate counter updates
     */
    animateCounterUpdate(element, newValue) {
        const currentValue = parseInt(element.textContent) || 0;
        const increment = (newValue - currentValue) / 20;
        let current = currentValue;
        
        const timer = setInterval(() => {
            current += increment;
            if ((increment > 0 && current >= newValue) || (increment < 0 && current <= newValue)) {
                current = newValue;
                clearInterval(timer);
            }
            element.textContent = Math.round(current);
        }, 50);
    }

    /**
     * Update activity feed
     */
    updateActivityFeed(activities) {
        const feedContainer = document.getElementById('activity-feed');
        if (!feedContainer) return;

        feedContainer.innerHTML = '';
        
        activities.forEach(activity => {
            const activityEl = document.createElement('div');
            activityEl.className = 'activity-item p-3 border-b border-gray-200 hover:bg-gray-50';
            activityEl.innerHTML = `
                <div class="flex items-center space-x-3">
                    <div class="activity-icon text-${activity.color}-500">
                        ${this.getActivityIcon(activity.icon)}
                    </div>
                    <div class="flex-1">
                        <h4 class="font-medium text-gray-900">${activity.title}</h4>
                        <p class="text-sm text-gray-600">${activity.description}</p>
                        <p class="text-xs text-gray-400">${this.formatRelativeTime(activity.timestamp)}</p>
                    </div>
                </div>
            `;
            
            feedContainer.appendChild(activityEl);
        });
    }

    /**
     * Load initial dashboard data
     */
    async loadInitialData() {
        try {
            const response = await fetch(`${this.config.apiUrl}/dashboard`);
            const data = await response.json();
            
            if (response.ok) {
                this.updateDashboardStats(data.user_stats);
                this.updateActivityFeed(data.recent_activities);
            }
        } catch (error) {
            console.error('❌ Error loading initial data:', error);
        }
    }

    /**
     * Refresh entire dashboard
     */
    async refreshDashboard() {
        console.log('🔄 Refreshing dashboard...');
        await this.loadInitialData();
        await this.refreshUserStats();
        await this.refreshActivityFeed();
    }

    /**
     * Refresh user statistics
     */
    async refreshUserStats() {
        try {
            const response = await fetch(`${this.config.apiUrl}/stats`);
            const stats = await response.json();
            
            if (response.ok) {
                this.updateDashboardStats(stats);
            }
        } catch (error) {
            console.error('❌ Error refreshing stats:', error);
        }
    }

    /**
     * Refresh activity feed
     */
    async refreshActivityFeed() {
        try {
            const response = await fetch(`${this.config.apiUrl}/activity`);
            const data = await response.json();
            
            if (response.ok) {
                this.updateActivityFeed(data.activities);
            }
        } catch (error) {
            console.error('❌ Error refreshing activity feed:', error);
        }
    }

    /**
     * Start periodic updates
     */
    startPeriodicUpdates() {
        this.updateTimer = setInterval(() => {
            if (this.isConnected) {
                this.refreshUserStats();
            }
        }, this.config.updateInterval);
    }

    /**
     * Pause updates
     */
    pauseUpdates() {
        if (this.updateTimer) {
            clearInterval(this.updateTimer);
            this.updateTimer = null;
        }
    }

    /**
     * Resume updates
     */
    resumeUpdates() {
        if (!this.updateTimer) {
            this.startPeriodicUpdates();
            this.refreshDashboard();
        }
    }

    /**
     * Attempt WebSocket reconnection
     */
    attemptReconnection() {
        if (this.retryCount < this.config.retryAttempts) {
            this.retryCount++;
            console.log(`🔄 Attempting reconnection (${this.retryCount}/${this.config.retryAttempts})...`);
            
            setTimeout(() => {
                this.setupWebSocket();
            }, this.config.retryDelay);
        } else {
            console.log('❌ Max retry attempts reached, falling back to polling');
            this.fallbackToPolling();
        }
    }

    /**
     * Fallback to polling
     */
    fallbackToPolling() {
        console.log('📡 Using polling fallback');
        this.startPeriodicUpdates();
    }

    /**
     * Authenticate WebSocket
     */
    async authenticateWebSocket() {
        try {
            const response = await fetch(`${this.config.apiUrl}/websocket-auth`);
            const authData = await response.json();
            
            if (response.ok && this.websocket.readyState === WebSocket.OPEN) {
                this.websocket.send(JSON.stringify({
                    type: 'authenticate',
                    data: authData
                }));
                
                authData.channels.forEach(channel => {
                    this.subscribeToChannel(channel);
                });
            }
        } catch (error) {
            console.error('❌ WebSocket authentication failed:', error);
        }
    }

    /**
     * Subscribe to channel
     */
    subscribeToChannel(channel) {
        if (this.websocket.readyState === WebSocket.OPEN) {
            this.websocket.send(JSON.stringify({
                type: 'subscribe',
                channel: channel
            }));
            
            this.subscriptions.set(channel, true);
            console.log(`📡 Subscribed to channel: ${channel}`);
        }
    }

    /**
     * Update connection status
     */
    updateConnectionStatus(status) {
        const indicator = document.getElementById('connection-status');
        if (indicator) {
            indicator.className = `connection-status status-${status}`;
            indicator.title = `Connection: ${status}`;
        }
    }

    /**
     * Utility functions
     */
    getStatusColor(status) {
        const colors = {
            pending: 'yellow',
            reviewed: 'blue', 
            shortlisted: 'green',
            interview_scheduled: 'orange',
            interview_completed: 'blue',
            rejected: 'red',
            hired: 'green',
            withdrawn: 'gray'
        };
        return colors[status] || 'gray';
    }

    getNotificationIcon(type) {
        const icons = {
            success: '✅',
            error: '❌',
            warning: '⚠️',
            info: 'ℹ️'
        };
        return icons[type] || 'ℹ️';
    }

    getActivityIcon(icon) {
        const icons = {
            'user-plus': '👤➕',
            'clock': '🕐',
            'star': '⭐',
            'calendar': '📅',
            'check': '✅',
            'x': '❌'
        };
        return icons[icon] || '📄';
    }

    formatRelativeTime(timestamp) {
        const date = new Date(timestamp);
        const now = new Date();
        const diff = Math.floor((now - date) / 1000);

        if (diff < 60) return 'Just now';
        if (diff < 3600) return `${Math.floor(diff / 60)} minutes ago`;
        if (diff < 86400) return `${Math.floor(diff / 3600)} hours ago`;
        return `${Math.floor(diff / 86400)} days ago`;
    }

    /**
     * Cleanup
     */
    destroy() {
        if (this.websocket) {
            this.websocket.close();
        }
        
        if (this.updateTimer) {
            clearInterval(this.updateTimer);
        }
        
        this.subscriptions.clear();
        console.log('🧹 Real-Time Dashboard destroyed');
    }
}

// Auto-initialize when DOM is ready
document.addEventListener('DOMContentLoaded', () => {
    if (document.getElementById('dashboard-container')) {
        window.realtimeDashboard = new RealTimeDashboard({
            wsUrl: window.APP_CONFIG?.websocket_url || 'ws://localhost:6001',
            apiUrl: '/api/realtime'
        });
    }
});

// Export for module use
if (typeof module !== 'undefined' && module.exports) {
    module.exports = RealTimeDashboard;
} 