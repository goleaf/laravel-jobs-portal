// index Component
document.addEventListener('DOMContentLoaded', function() {
    try {
        // index Component
document.addEventListener('DOMContentLoaded', function() {
    try {
        // index Component
document.addEventListener('DOMContentLoaded', function() {
    try {
        // index Component
document.addEventListener('DOMContentLoaded', function() {
    try {
        // index Component
document.addEventListener('DOMContentLoaded', function() {
    try {
        // index Component
document.addEventListener('DOMContentLoaded', function() {
    try {
        // index Component
document.addEventListener('DOMContentLoaded', function() {
    try {
        // index Component JavaScript
// Enhanced with Context7 patterns

--}}
{{-- let enableEditText ="{{ __('messages.setting.enable_edit') }}"; --}}
{{-- let disableEditText ="{{ __('messages.setting.disable_edit') }}"; --}}
{{-- let enableCookie ="{{ __('messages.setting.enable_cookie') }}"; --}}
{{-- let disableCookie ="{{ __('messages.setting.disable_cookie') }}"; --}}
{{--


    } catch (error) {
        console.error('Error in index component:', error);
    }
});
// index Component JavaScript
// Enhanced with Context7 patterns

let getEmployerJobs ="{{ url('admin/employer-jobs') }}";
        let jobDetails ="{{ url('admin/jobs') }}";
        let jobNotification ="{{ url('admin/job-notifications') }}";


    } catch (error) {
        console.error('Error in index component:', error);
    }
});
// index Component JavaScript
// Enhanced with Context7 patterns

--}}
{{-- var statusArray = JSON.parse('@json($statusArray)'); --}}
{{--


    } catch (error) {
        console.error('Error in index component:', error);
    }
});
// index Component JavaScript
// Enhanced with Context7 patterns

document.addEventListener('livewire:initialized', () => {
        // Listening for the event to fill form when editing
        Livewire.on('fillCareerLevelForm', data => {
            document.getElementById('careerLevelId').value = data.id;
            document.getElementById('editCareerLevel').value = data.levelName;
        });
        
        // Success toast message
        Livewire.on('showSuccessToast', ({message}) => {
            displaySuccessMessage(message);
        });
        
        // Error toast message
        Livewire.on('showErrorToast', ({message}) => {
            displayErrorMessage(message);
        });
    });


    } catch (error) {
        console.error('Error in index component:', error);
    }
});
// index Component JavaScript
// Enhanced with Context7 patterns

document.addEventListener('livewire:initialized', () => {
            Livewire.on('refresh', () => {
                Livewire.dispatch('refresh');
            });
            
            Livewire.on('success', (event) => {
                window.dispatchEvent(new CustomEvent('success', { detail: event }));
            });
            
            Livewire.on('error', (event) => {
                window.dispatchEvent(new CustomEvent('error', { detail: event }));
            });
        });


    } catch (error) {
        console.error('Error in index component:', error);
    }
});
// index Component JavaScript
// Enhanced with Context7 patterns

let testimonialImageSaveUrl ="{{ route('download.image') }}";


    } catch (error) {
        console.error('Error in index component:', error);
    }
});
// index Component JavaScript
// Enhanced with Context7 patterns

document.addEventListener('livewire:initialized', () => {
            Livewire.on('refresh', () => {
                window.dispatchEvent(new CustomEvent('refresh-table'));
            });
            
            Livewire.on('success', (message) => {
                window.dispatchEvent(new CustomEvent('success', {
                    detail: {message: message}
                }));
            });
            
            Livewire.on('error', (message) => {
                window.dispatchEvent(new CustomEvent('error', {
                    detail: {message: message}
                }));
            });
        });


    } catch (error) {
        console.error('Error in index component:', error);
    }
});