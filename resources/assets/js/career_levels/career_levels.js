document.addEventListener('turbo:load', loadCareerLevelData);

function loadCareerLevelData() {
    if (!$('#indexCareerLevelData').length) {
        return;
    }

    listenClick('.addCareerLevelModal', function () {
        $('#addCareerModal').appendTo('body').modal('show');
    });

    listenClick('.career-level-edit-btn', function (event) {
        let careerLevelId = $(event.currentTarget).attr('data-id');
        Livewire.dispatch('editCareerLevel', { id: careerLevelId });
        $('#editCareerLevelModal').appendTo('body').modal('show');
    });

    listenClick('.career-level-delete-btn', function (event) {
        let careerLevelId = $(event.currentTarget).attr('data-id');
        Swal.fire({
            title: Lang.get('js.delete_confirm_title'),
            text: Lang.get('js.delete_confirm_text', {'name': Lang.get('js.career_level')}),
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: Lang.get('js.yes'),
            cancelButtonText: Lang.get('js.no')
        }).then((result) => {
            if (result.isConfirmed) {
                Livewire.dispatch('deleteCareerLevel', { id: careerLevelId });
            }
        });
    });

    listenHiddenBsModal('#addCareerModal', function () {
        resetModalForm('#addCareerForm', '#careerValidationErrorsBox');
    });

    listenHiddenBsModal('#editCareerLevelModal', function () {
        resetModalForm('#editCareerLevelForm', '#editValidationErrorsBox');
    });
}

listenSubmit('#addCareerForm', function (e) {
    e.preventDefault();
    
    const saveBtn = document.querySelector('#careerBtnSave');
    const loadingText = "<span class='spinner-border spinner-border-sm'></span> " + Lang.get('js.processing');
    saveBtn.innerHTML = loadingText;
    saveBtn.disabled = true;
    
    const formData = new FormData(this);
    
    Livewire.dispatch('createCareerLevel', {
        levelName: formData.get('level_name')
    }).then(() => {
        $('#addCareerModal').modal('hide');
        this.reset();
    }).catch(error => {
        displayErrorMessage(error);
    }).finally(() => {
        saveBtn.innerHTML = Lang.get('js.save');
        saveBtn.disabled = false;
    });
});

listenSubmit('#editCareerLevelForm', function (event) {
    event.preventDefault();
    
    const saveBtn = document.querySelector('#editCareerLevelBtnSave');
    const loadingText = "<span class='spinner-border spinner-border-sm'></span> " + Lang.get('js.processing');
    saveBtn.innerHTML = loadingText;
    saveBtn.disabled = true;
    
    const formData = new FormData(this);
    const editCareerLevelId = formData.get('careerLevelId');
    
    Livewire.dispatch('updateCareerLevel', {
        id: editCareerLevelId,
        levelName: formData.get('level_name')
    }).then(() => {
        $('#editCareerLevelModal').modal('hide');
    }).catch(error => {
        displayErrorMessage(error);
    }).finally(() => {
        saveBtn.innerHTML = Lang.get('js.save');
        saveBtn.disabled = false;
    });
});
