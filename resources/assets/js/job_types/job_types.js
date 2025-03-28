'use strict';

document.addEventListener('DOMContentLoaded', function() {
    // Initialize event listeners
    initEventListeners();
});

function initEventListeners() {
    // Listen for Livewire events
    window.addEventListener('livewire:load', function() {
        // Modal events from Livewire
        Livewire.on('showJobTypeModal', () => {
            openModal('addJobTypeModal');
        });

        Livewire.on('hideJobTypeModal', () => {
            closeModal('addJobTypeModal');
        });

        Livewire.on('showEditJobTypeModal', () => {
            openModal('editJobTypeModal');
        });

        Livewire.on('hideEditJobTypeModal', () => {
            closeModal('editJobTypeModal');
        });

        // Notification events
        Livewire.on('showNotification', (params) => {
            displayNotification(params.message, params.type);
        });

        // Confirmation dialog for delete
        Livewire.on('showDeleteConfirmation', (id) => {
            if (confirm(window.translations.common.delete_confirmation)) {
                Livewire.emit('deleteJobType', id);
            }
        });
    });

    // Add click event listeners for modal triggers
    document.querySelectorAll('[data-modal-toggle]').forEach(element => {
        element.addEventListener('click', function() {
            const target = this.getAttribute('data-modal-toggle');
            toggleModal(target);
        });
    });

    // Add click event listeners for modal close buttons
    document.querySelectorAll('[data-modal-hide]').forEach(element => {
        element.addEventListener('click', function() {
            const target = this.getAttribute('data-modal-hide');
            closeModal(target);
        });
    });
}

/**
 * Open a modal by ID
 * @param {string} modalId - The ID of the modal to open
 */
function openModal(modalId) {
    const modal = document.getElementById(modalId);
    if (modal) {
        modal.classList.remove('hidden');
        document.body.classList.add('overflow-hidden');
        
        // Add backdrop
        const backdrop = document.createElement('div');
        backdrop.id = `${modalId}-backdrop`;
        backdrop.classList.add('modal-backdrop', 'bg-gray-900', 'bg-opacity-50', 'fixed', 'inset-0', 'z-40');
        document.body.appendChild(backdrop);
        
        // Add click event to backdrop for closing
        backdrop.addEventListener('click', function() {
            closeModal(modalId);
        });
        
        // Focus on first input if exists
        setTimeout(() => {
            const firstInput = modal.querySelector('input, select, textarea');
            if (firstInput) {
                firstInput.focus();
            }
        }, 100);
    }
}

/**
 * Close a modal by ID
 * @param {string} modalId - The ID of the modal to close
 */
function closeModal(modalId) {
    const modal = document.getElementById(modalId);
    if (modal) {
        modal.classList.add('hidden');
        document.body.classList.remove('overflow-hidden');
        
        // Remove backdrop
        const backdrop = document.getElementById(`${modalId}-backdrop`);
        if (backdrop) {
            backdrop.remove();
        }
    }
}

/**
 * Toggle modal visibility
 * @param {string} modalId - The ID of the modal to toggle
 */
function toggleModal(modalId) {
    const modal = document.getElementById(modalId);
    if (modal) {
        if (modal.classList.contains('hidden')) {
            openModal(modalId);
        } else {
            closeModal(modalId);
        }
    }
}

/**
 * Display notification
 * @param {string} message - The notification message
 * @param {string} type - The type of notification (success, error, info, warning)
 */
function displayNotification(message, type = 'success') {
    // Get or create notification container
    let container = document.getElementById('notification-container');
    
    if (!container) {
        container = document.createElement('div');
        container.id = 'notification-container';
        container.className = 'fixed top-4 right-4 z-50 flex flex-col space-y-2';
        document.body.appendChild(container);
    }
    
    // Create notification element
    const notification = document.createElement('div');
    notification.className = 'notification px-4 py-3 rounded shadow-md transform transition-all duration-300 ease-in-out';
    
    // Add type-specific classes
    switch (type) {
        case 'success':
            notification.classList.add('bg-green-500', 'text-white');
            break;
        case 'error':
            notification.classList.add('bg-red-500', 'text-white');
            break;
        case 'warning':
            notification.classList.add('bg-yellow-500', 'text-white');
            break;
        case 'info':
            notification.classList.add('bg-blue-500', 'text-white');
            break;
    }
    
    // Add message
    notification.textContent = message;
    
    // Add to container
    container.appendChild(notification);
    
    // Apply animation
    setTimeout(() => {
        notification.classList.add('opacity-100', 'translate-y-0');
    }, 10);
    
    // Auto dismiss after 3 seconds
    setTimeout(() => {
        notification.classList.add('opacity-0', 'translate-y-2');
        setTimeout(() => {
            notification.remove();
        }, 300);
    }, 3000);
}
