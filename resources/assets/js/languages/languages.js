'use strict';

document.addEventListener('DOMContentLoaded', loadSetLanguageData);

function loadSetLanguageData() {
    if (!document.getElementById('indexSetLanguageData')) {
        return;
    }

    // Add language modal - open and close
    document.querySelector('#addLanguageBtn')?.addEventListener('click', function () {
        document.querySelector('#addLanguageModal').classList.remove('hidden');
    });

    document.querySelector('#languageBtnCancel')?.addEventListener('click', function () {
        document.querySelector('#addLanguageModal').classList.add('hidden');
        resetForm('#addLanguageForm', '#languageValidationErrorsBox');
    });

    // Edit language modal - open and close
    const editButtons = document.querySelectorAll('[data-action="edit-language"]');
    editButtons.forEach(button => {
        button.addEventListener('click', function (event) {
            const languageId = this.dataset.id;
            
            fetch(route('languages.edit', languageId), {
                method: 'GET',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Content-Type': 'application/json',
                }
            })
            .then(response => response.json())
            .then(result => {
                if (result.success) {
                    let element = document.createElement('textarea');
                    element.innerHTML = result.data.language;
                    
                    document.querySelector('#languageId').value = result.data.id;
                    document.querySelector('#editLanguage').value = element.value;
                    document.querySelector('#editIso').value = result.data.iso_code;
                    document.querySelector('#editLanguageModal').classList.remove('hidden');
                }
            })
            .catch(error => {
                displayErrorMessage(error.message || 'An error occurred');
            });
        });
    });

    document.querySelector('#btnEditCancel')?.addEventListener('click', function () {
        document.querySelector('#editLanguageModal').classList.add('hidden');
        resetForm('#editLanguageForm', '#editValidationErrorsBox');
    });

    // Delete language
    const deleteButtons = document.querySelectorAll('[data-action="delete-language"]');
    deleteButtons.forEach(button => {
        button.addEventListener('click', function (event) {
            const deleteLanguageId = this.dataset.id;
            deleteItem(route('languages.destroy', deleteLanguageId), Lang.get('messages.language.language'));
        });
    });

    // ESC key to close modals
    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape') {
            document.querySelector('#addLanguageModal')?.classList.add('hidden');
            document.querySelector('#editLanguageModal')?.classList.add('hidden');
        }
    });
}

function resetForm(formId, validationBoxId) {
    const form = document.querySelector(formId);
    const validationBox = document.querySelector(validationBoxId);
    
    if (form) form.reset();
    if (validationBox) validationBox.classList.add('hidden');
}

document.addEventListener('submit', function(e) {
    if (e.target.matches('#addLanguageForm')) {
        e.preventDefault();
        showLoadingButton('#languageBtnSave');
        
        const formData = new FormData(e.target);
        
        fetch(route('languages.store'), {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'X-Requested-With': 'XMLHttpRequest',
            },
            body: formData
        })
        .then(response => response.json())
        .then(result => {
            if (result.success) {
                displaySuccessMessage(result.message);
                document.querySelector('#addLanguageModal').classList.add('hidden');
                // Refresh Livewire component
                Livewire.dispatch('refresh');
            }
        })
        .catch(error => {
            if (error.errors) {
                displayValidationErrors(error.errors, '#languageValidationErrorsBox');
            } else {
                displayErrorMessage(error.message || 'An error occurred');
            }
        })
        .finally(() => {
            hideLoadingButton('#languageBtnSave');
        });
    }
    
    if (e.target.matches('#editLanguageForm')) {
        e.preventDefault();
        showLoadingButton('#btnEditSave');
        
        const updateLanguageId = document.querySelector('#languageId').value;
        const formData = new FormData(e.target);
        
        fetch(route('languages.update', updateLanguageId), {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'X-Requested-With': 'XMLHttpRequest',
                'X-HTTP-Method-Override': 'PUT'
            },
            body: formData
        })
        .then(response => response.json())
        .then(result => {
            if (result.success) {
                displaySuccessMessage(result.message);
                document.querySelector('#editLanguageModal').classList.add('hidden');
                // Refresh Livewire component
                Livewire.dispatch('refresh');
            }
        })
        .catch(error => {
            if (error.errors) {
                displayValidationErrors(error.errors, '#editValidationErrorsBox');
            } else {
                displayErrorMessage(error.message || 'An error occurred');
            }
        })
        .finally(() => {
            hideLoadingButton('#btnEditSave');
        });
    }
});

// Helper functions
function showLoadingButton(buttonId) {
    const button = document.querySelector(buttonId);
    if (!button) return;
    
    button.disabled = true;
    button.innerHTML = '<svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg> Loading...';
}

function hideLoadingButton(buttonId) {
    const button = document.querySelector(buttonId);
    if (!button) return;
    
    button.disabled = false;
    button.innerHTML = Lang.get('messages.common.save');
}

function displayValidationErrors(errors, boxId) {
    const box = document.querySelector(boxId);
    if (!box) return;
    
    box.classList.remove('hidden');
    box.innerHTML = '';
    
    let errorList = '<ul class="mt-1 list-disc list-inside">';
    Object.keys(errors).forEach(key => {
        errorList += '<li>' + errors[key][0] + '</li>';
    });
    errorList += '</ul>';
    
    box.innerHTML = errorList;
}
