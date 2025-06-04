/**
 * Action Buttons Component
 * Handles edit and delete actions for table components
 */

export class ActionButtons {
    constructor() {
        this.initializeEventListeners();
    }

    initializeEventListeners() {
        // Edit button click handler
        document.addEventListener('click', (e) => {
            if (e.target.closest('.company-size-edit-btn')) {
                this.handleEdit(e.target.closest('.company-size-edit-btn'));
            }
        });

        // Delete button click handler
        document.addEventListener('click', (e) => {
            if (e.target.closest('.company-size-delete-btn')) {
                this.handleDelete(e.target.closest('.company-size-delete-btn'));
            }
        });
    }

    handleEdit(button) {
        const id = button.getAttribute('data-id');
        if (!id) {
            console.error('No data-id found on edit button');
            return;
        }

        // Trigger Livewire event or AJAX call
        if (window.Livewire) {
            window.Livewire.dispatch('editCompanySize', { id: id });
        } else {
            // Fallback to traditional AJAX
            this.ajaxEdit(id);
        }
    }

    handleDelete(button) {
        const id = button.getAttribute('data-id');
        if (!id) {
            console.error('No data-id found on delete button');
            return;
        }

        // Show confirmation dialog
        if (confirm(this.getTranslation('common.confirm_delete'))) {
            if (window.Livewire) {
                window.Livewire.dispatch('deleteCompanySize', { id: id });
            } else {
                // Fallback to traditional AJAX
                this.ajaxDelete(id);
            }
        }
    }

    ajaxEdit(id) {
        // Implementation for traditional AJAX edit
        console.log('Edit company size with ID:', id);
    }

    ajaxDelete(id) {
        // Implementation for traditional AJAX delete
        console.log('Delete company size with ID:', id);
    }

    getTranslation(key) {
        // Simple translation helper
        const translations = {
            'common.confirm_delete': 'Are you sure you want to delete this item?'
        };
        return translations[key] || key;
    }
}

// Initialize when DOM is loaded
document.addEventListener('DOMContentLoaded', () => {
    new ActionButtons();
}); 