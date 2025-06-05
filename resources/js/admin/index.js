// index Component
document.addEventListener('DOMContentLoaded', function() {
    try {
        // index Component
document.addEventListener('DOMContentLoaded', function() {
    try {
        // index Component JavaScript
// Enhanced with Context7 patterns

function deleteCandidate(id) {
    if (confirm('{{ __("Are you sure you want to delete this candidate?") }}')) {
        // Implementation for delete functionality
        fetch(`/admin/candidates/${id}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        }).then(response => {
            if (response.ok) {
                location.reload();
            }
        });
    }
}


    } catch (error) {
        console.error('Error in index component:', error);
    }
});
// index Component JavaScript
// Enhanced with Context7 patterns

function deleteJob(id) {
    if (confirm('{{ __("Are you sure you want to delete this job?") }}')) {
        fetch(`/admin/jobs/${id}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        }).then(response => {
            if (response.ok) {
                location.reload();
            }
        });
    }
}


    } catch (error) {
        console.error('Error in index component:', error);
    }
});