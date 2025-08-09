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
    formData.append('error_id', document.querySelector('meta[name="error-id"]').getAttribute('content'));
    
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
            showNotification('Error report submitted successfully.', 'success');
            
            // Reset form
            event.target.reset();
        } else {
            showNotification(data.message || 'Error submitting report.', 'error');
        }
    })
    .catch(error => {
        console.error('Error submitting report:', error);
        showNotification('An error occurred while submitting the report.', 'error');
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
            'error_id': document.querySelector('meta[name="error-id"]').getAttribute('content'),
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
            error_id: document.querySelector('meta[name="error-id"]').getAttribute('content'),
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
                <p class="text-sm font-medium">Ready to retry connection</p>
                <p class="text-xs opacity-90">The issue might be resolved. You can retry now.</p>
            </div>
            <button onclick="refreshPage()" class="text-xs bg-white bg-opacity-20 hover:bg-opacity-30 px-2 py-1 rounded">
                Retry
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
    showNotification('Live chat is starting...', 'info');
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
                showNotification('Service restored!', 'success');
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
    } else {
        showNotification('Max retries reached. Please try again later.', 'error');
    }
} 