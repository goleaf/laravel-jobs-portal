// show Component
document.addEventListener('DOMContentLoaded', function() {
    try {
        // show Component JavaScript
// Enhanced with Universal patterns

function showApplyModal() {
    const modal = new bootstrap.Modal(document.getElementById('applyModal'));
    modal.show();
}

function toggleSaveJob() {
    const btn = event.target.closest('button');
    const icon = btn.querySelector('i');
    
    if (icon.classList.contains('far')) {
        icon.classList.remove('far');
        icon.classList.add('fas');
        btn.classList.remove('btn-outline-danger');
        btn.classList.add('btn-danger');
        btn.innerHTML = '<i class="fas fa-heart me-2"></i>Job Saved';
    } else {
        icon.classList.remove('fas');
        icon.classList.add('far');
        btn.classList.remove('btn-danger');
        btn.classList.add('btn-outline-danger');
        btn.innerHTML = '<i class="far fa-heart me-2"></i>Save Job';
    }
}

function shareJob(platform) {
    const url = window.location.href;
    const title = 'Senior Software Developer at TechCorp Solutions';
    
    let shareUrl = '';
    switch(platform) {
        case 'facebook':
            shareUrl = `https://www.facebook.com/sharer/sharer.php?u=${encodeURIComponent(url)}`;
            break;
        case 'twitter':
            shareUrl = `https://twitter.com/intent/tweet?url=${encodeURIComponent(url)}&text=${encodeURIComponent(title)}`;
            break;
        case 'linkedin':
            shareUrl = `https://www.linkedin.com/sharing/share-offsite/?url=${encodeURIComponent(url)}`;
            break;
    }
    
    if (shareUrl) {
        window.open(shareUrl, '_blank', 'width=600,height=400');
    }
}

function copyJobLink() {
    navigator.clipboard.writeText(window.location.href).then(() => {
        alert('Job link copied to clipboard!');
    });
}

function submitApplication() {
    const form = document.getElementById('applyForm');
    if (form.checkValidity()) {
        // Simulate form submission
        const btn = event.target;
        btn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Submitting...';
        btn.disabled = true;
        
        setTimeout(() => {
            alert('Application submitted successfully! We\'ll be in touch soon.');
            bootstrap.Modal.getInstance(document.getElementById('applyModal')).hide();
            form.reset();
            btn.innerHTML = '<i class="fas fa-paper-plane me-2"></i>Submit Application';
            btn.disabled = false;
        }, 2000);
    } else {
        form.reportValidity();
    }
}


    } catch (error) {
        console.error('Error in show component:', error);
    }
});