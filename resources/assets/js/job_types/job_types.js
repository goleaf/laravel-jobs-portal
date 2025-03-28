document.addEventListener('turbo:load', loadJobTypeData);

function loadJobTypeData() {
    if (!$('#indexJobTypeData').length) {
        return;
    }

    // Add job type modal - open and close
    listenClick('#addJobTypeBtn', function () {
        $('#addJobTypeModal').removeClass('hidden');
    });

    listenClick('#jobTypeBtnCancel', function () {
        $('#addJobTypeModal').addClass('hidden');
        resetForm('#addJobTypeForm', '#jobTypeValidationErrorsBox');
    });

    // Edit job type modal - open and close
    listenClick('#editJobType', function (event) {
        let jobTypeId = $(event.currentTarget).data('id');
        $.ajax({
            url: route('job-types.edit', jobTypeId),
            type: 'GET',
            success: function (result) {
                if (result.success) {
                    $('#jobTypeId').val(result.data.id);
                    $('#editName').val(result.data.name);
                    $('#editDescription').val(result.data.description);
                    $('#editJobTypeModal').removeClass('hidden');
                }
            },
            error: function (result) {
                displayErrorMessage(result.responseJSON.message);
            },
        });
    });

    listenClick('#btnEditCancel', function () {
        $('#editJobTypeModal').addClass('hidden');
        resetForm('#editJobTypeForm', '#editValidationErrorsBox');
    });

    // Delete job type
    listenClick('#deleteJobType', function (event) {
        let deleteJobTypeId = $(event.currentTarget).data('id');
        deleteItem(route('job-types.destroy', deleteJobTypeId), Lang.get('messages.job_type.job_type'));
    });

    // ESC key to close modals
    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape') {
            $('#addJobTypeModal').addClass('hidden');
            $('#editJobTypeModal').addClass('hidden');
        }
    });
}

function resetForm(formId, validationBoxId) {
    $(formId)[0].reset();
    $(validationBoxId).addClass('hidden');
}

listenSubmit('#addJobTypeForm', function (e) {
    e.preventDefault();
    showLoadingButton('#jobTypeBtnSave');
    
    $.ajax({
        url: route('job-types.store'),
        type: 'POST',
        data: $(this).serialize(),
        success: function (result) {
            if (result.success) {
                displaySuccessMessage(result.message);
                $('#addJobTypeModal').addClass('hidden');
                // Refresh Livewire component
                Livewire.dispatch('refresh');
            }
        },
        error: function (result) {
            displayValidationErrors(result.responseJSON.errors, '#jobTypeValidationErrorsBox');
        },
        complete: function () {
            hideLoadingButton('#jobTypeBtnSave');
        },
    });
});

listenSubmit('#editJobTypeForm', function (event) {
    event.preventDefault();
    showLoadingButton('#btnEditSave');
    
    const updateJobTypeId = $('#jobTypeId').val();
    $.ajax({
        url: route('job-types.update', updateJobTypeId),
        type: 'put',
        data: $(this).serialize(),
        success: function (result) {
            if (result.success) {
                displaySuccessMessage(result.message);
                $('#editJobTypeModal').addClass('hidden');
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
