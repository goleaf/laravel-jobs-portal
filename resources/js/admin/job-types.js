/**
 * Admin Job Types Management - Context7 Enhanced Module
 */

class JobTypesManager {
    constructor() {
        this.initializeEventListeners();
    }

    initializeEventListeners() {
        document.addEventListener('DOMContentLoaded', () => {
            this.setupBulkSelection();
        });
    }

    setupBulkSelection() {
        const selectAll = document.getElementById('select-all');
        const checkboxes = document.querySelectorAll('.job-type-checkbox');

        selectAll?.addEventListener('change', function() {
            checkboxes.forEach(checkbox => {
                checkbox.checked = this.checked;
            });
            this.toggleBulkActions();
        }.bind(this));

        checkboxes.forEach(checkbox => {
            checkbox.addEventListener('change', () => this.toggleBulkActions());
        });
    }

    toggleBulkActions() {
        const checkedBoxes = document.querySelectorAll('.job-type-checkbox:checked');
        const bulkActions = document.getElementById('bulk-actions');
        
        if (checkedBoxes.length > 0) {
            bulkActions?.style.setProperty('display', 'flex');
        } else {
            bulkActions?.style.setProperty('display', 'none');
        }
    }

    bulkAction(action) {
        const checkedBoxes = document.querySelectorAll('.job-type-checkbox:checked');
        if (checkedBoxes.length === 0) {
            alert('Please select items first');
            return;
        }

        const jobTypeIds = Array.from(checkedBoxes).map(cb => cb.value);
        
        if (confirm('Are you sure you want to perform this action?')) {
            document.getElementById('bulk-action-type').value = action;
            document.getElementById('bulk-job-type-ids').value = JSON.stringify(jobTypeIds);
            document.getElementById('bulk-action-form').submit();
        }
    }

    async toggleStatus(jobTypeId) {
        try {
            const response = await fetch(`/api/v1/job-types/${jobTypeId}/toggle-status`, {
                method: 'PATCH',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            });

            const data = await response.json();
            
            if (data.success) {
                location.reload();
            } else {
                alert(data.message || 'An error occurred');
            }
        } catch (error) {
            console.error('Error:', error);
            alert('An error occurred');
        }
    }

    deleteJobType(id, name) {
        if (confirm(`Are you sure you want to delete this job type?\n\n${name}`)) {
            const form = document.getElementById('delete-form');
            form.action = `/admin/job-types/${id}`;
            form.submit();
        }
    }

    async duplicateJobType(id) {
        if (confirm('Are you sure you want to duplicate this job type?')) {
            try {
                const response = await fetch(`/api/v1/job-types/${id}/duplicate`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    }
                });

                const data = await response.json();
                
                if (data.success) {
                    location.reload();
                } else {
                    alert(data.message || 'An error occurred');
                }
            } catch (error) {
                console.error('Error:', error);
                alert('An error occurred');
            }
        }
    }
}

// Initialize and expose globally
const jobTypesManager = new JobTypesManager();
window.bulkAction = (action) => jobTypesManager.bulkAction(action);
window.toggleStatus = (id) => jobTypesManager.toggleStatus(id);
window.deleteJobType = (id, name) => jobTypesManager.deleteJobType(id, name);
window.duplicateJobType = (id) => jobTypesManager.duplicateJobType(id); 