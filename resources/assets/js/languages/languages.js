document.addEventListener('turbo:load', loadSetLanguageData);

function loadSetLanguageData() {
    if (!$('#indexSetLanguageData').length) {
        return;
    }

    // Add language modal - open and close
    listenClick('#addLanguageBtn', function () {
        $('#addLanguageModal').removeClass('hidden');
    });

    listenClick('#languageBtnCancel', function () {
        $('#addLanguageModal').addClass('hidden');
        resetForm('#addLanguageForm', '#languageValidationErrorsBox');
    });

    // Edit language modal - open and close
    listenClick('#editLanguage', function (event) {
        let languageId = $(event.currentTarget).data('id');
        $.ajax({
            url: route('languages.edit', languageId),
            type: 'GET',
            success: function (result) {
                if (result.success) {
                    let element = document.createElement('textarea');
                    element.innerHTML = result.data.language;
                    $('#languageId').val(result.data.id);
                    $('#editLanguage').val(element.value);
                    $('#editIso').val(result.data.iso_code);
                    $('#editLanguageModal').removeClass('hidden');
                }
            },
            error: function (result) {
                displayErrorMessage(result.responseJSON.message);
            },
        });
    });

    listenClick('#btnEditCancel', function () {
        $('#editLanguageModal').addClass('hidden');
        resetForm('#editLanguageForm', '#editValidationErrorsBox');
    });

    // Delete language
    listenClick('#deleteLanguage', function (event) {
        let deleteLanguageId = $(event.currentTarget).data('id');
        deleteItem(route('languages.destroy', deleteLanguageId), Lang.get('messages.language.language'));
    });

    // ESC key to close modals
    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape') {
            $('#addLanguageModal').addClass('hidden');
            $('#editLanguageModal').addClass('hidden');
        }
    });
}

function resetForm(formId, validationBoxId) {
    $(formId)[0].reset();
    $(validationBoxId).addClass('hidden');
}

listenSubmit('#addLanguageForm', function (e) {
    e.preventDefault();
    showLoadingButton('#languageBtnSave');
    
    $.ajax({
        url: route('languages.store'),
        type: 'POST',
        data: $(this).serialize(),
        success: function (result) {
            if (result.success) {
                displaySuccessMessage(result.message);
                $('#addLanguageModal').addClass('hidden');
                // Refresh Livewire component
                Livewire.dispatch('refresh');
            }
        },
        error: function (result) {
            displayValidationErrors(result.responseJSON.errors, '#languageValidationErrorsBox');
        },
        complete: function () {
            hideLoadingButton('#languageBtnSave');
        },
    });
});

listenSubmit('#editLanguageForm', function (event) {
    event.preventDefault();
    showLoadingButton('#btnEditSave');
    
    const updateLanguageId = $('#languageId').val();
    $.ajax({
        url: route('languages.update', updateLanguageId),
        type: 'put',
        data: $(this).serialize(),
        success: function (result) {
            if (result.success) {
                displaySuccessMessage(result.message);
                $('#editLanguageModal').addClass('hidden');
                // Refresh Livewire component
                Livewire.dispatch('refresh');
            }
        },
        error: function (result) {
            displayValidationErrors(result.responseJSON.errors, '#editValidationErrorsBox');
        },
        complete: function () {
            hideLoadingButton('#btnEditSave');
        },
    });
});

// Helper functions
function showLoadingButton(buttonId) {
    $(buttonId).prop('disabled', true);
    $(buttonId).html('<svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg> Loading...');
}

function hideLoadingButton(buttonId) {
    $(buttonId).prop('disabled', false);
    $(buttonId).html(Lang.get('messages.common.save'));
}

function displayValidationErrors(errors, boxId) {
    $(boxId).removeClass('hidden');
    $(boxId).html('');
    
    let errorList = '<ul class="mt-1 list-disc list-inside">';
    $.each(errors, function (key, value) {
        errorList += '<li>' + value[0] + '</li>';
    });
    errorList += '</ul>';
    
    $(boxId).html(errorList);
}
