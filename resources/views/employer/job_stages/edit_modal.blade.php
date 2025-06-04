<div id="editJobStageModal" class="modal fade" role="dialog" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <h3>{{ __('messages.job_stage.edit_job_stage') }}</h3>
                <button type="button" aria-label="Close" class="px-4 py-2 rounded font-medium transition-colors -close"
                        data-bs-dismiss="modal">
                </button>
            </div>
            {{ Form::open(['id'=>'editJobStageForm']) }}
            <div class="modal-body">
                <div class="alert p-4 rounded-md mb-4 -danger hide hidden" id="editValidationErrorsBox"></div>
                {{ Form::hidden('jobStageId',null,['id'=>'jobStageId']) }}
                <div class="flex flex-wrap">
                    <div class="flex-1 -sm-12 mb-5">
                        {{ Form::label('name',__('messages.job_tag.name').(':'), ['class' => 'form-label']) }}
                        <span class="required"></span>
                        {{ Form::text('name', null, ['id'=>'editName','class' => 'form-control','required', 'placeholder'=>
                        __('messages.job_tag.name')]) }}
                    </div>
                    <div class="flex-1 -sm-12 mb-5">
                        {{ Form::label('description', __('messages.job_tag.description').(':'),['class' => 'form-label']) }}
                        <span class="required"></span>
                        <div id="editStageDescription"></div>
                        {{ Form::hidden('description', null, ['id' => 'edit_job_stage_desc']) }}
                    </div>
                </div>
            </div>
            <div class="modal-footer pt-0">
                {{ Form::button(__('messages.common.save'), ['type' => 'submit','class' => 'btn btn-primary m-0','id' => 'jobStageEditSaveBtn','data-loading-text' => "<span class='spinner-border spinner-border-sm'></span> ".__('messages.common.process')]) }}
                <button type="button" class="btn px-4 py-2 rounded font-medium transition-colors -secondary my-0 ms-5 me-0"
                        id="btnEditCancel"
                        data-bs-dismiss="modal">{{ __('messages.common.cancel') }}</button>
            </div>
            {{ Form::close() }}
        </div>
    </div>
</div>
