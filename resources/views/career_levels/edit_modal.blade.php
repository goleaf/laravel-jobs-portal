<div class="modal fade" role="dialog" tabindex="-1" aria-hidden="true" id="editCareerLevelModal">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h3 class="modal-title">{{ __('career_level.edit_career_level') }}</h3>
                <button type="button" aria-label="Close" class="px-4 py-2 rounded font-medium transition-colors -close"
                        data-bs-dismiss="modal">
                </button>
            </div>
            {{ Form::open(['id' => 'editCareerLevelForm']) }}
            <div class="modal-body">
                <div class="alert p-4 rounded-md mb-4 -danger fs-4 text-white flex items-center hidden"
                     id="editValidationErrorsBox">
                    <i class="fa-solid fa-face-frown me-5"></i>
                </div>
                {{ Form::hidden('careerLevelId',null,['id'=>'careerLevelId']) }}
                <div class="mb-5">
                    {{ Form::label('level_name',__('career_level.level_name').':', ['class' => 'form-label']) }}
                    <span
                            class="required"></span>
                    {{ Form::text('level_name', null, ['class' => 'form-control','required','id' => 'editCareerLevel', 'placeholder' => __('career_level.level_name')]) }}
                </div>
            </div>
            <div class="modal-footer pt-0">
                {{ Form::button(__('common.save'), ['type'=>'submit','class' => 'btn btn-primary m-0','id'=>'editCareerLevelBtnSave','data-loading-text'=>"<span class='spinner-border spinner-border-sm'></span> ".__('common.process')]) }}
                <button type="button" id="btnEditCancel" class="btn px-4 py-2 rounded font-medium transition-colors -secondary my-0 ms-5 me-0"
                        data-bs-dismiss="modal">{{ __('common.cancel') }}
                </button>
            </div>
            {{ Form::close() }}
        </div>
    </div>
</div>
