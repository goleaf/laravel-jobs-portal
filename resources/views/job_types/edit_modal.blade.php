<div id="editJobTypeModal" class="fixed inset-0 z-50 overflow-y-auto fade" role="dialog" tabindex="-1" aria-hidden="true">
    <div class="flex items-center justify-center min-h-screen px-4">
        <!-- Modal content-->
        <div class="bg-white rounded-lg shadow-xl max-w-lg w-full">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="modal-title">{{ __('messages.job_type.edit_job_type')  }}</h3>
                <button type="button" aria-label="Close" class="px-4 py-2 rounded font-medium transition-colors -close" data-bs-dismiss="modal">
                    <x-icons.close class="h-4 w-4" />
                </button>
            </div>
            {!! Form::open(['id' => 'editJobTypeForm']) !!}
            <div class="px-6 py-4">
                <div class="px-4 py-3 rounded-md border border-gray-300 mb-4 p-4 rounded-md mb-4 -danger fs-4 text-white flex items-center hidden"
                     id="editValidationErrorsBox">
                    <i class="fa-solid fa-face-frown me-5"></i>
                </div>
                {!! Form::hidden('jobTypeId', null, ['id' => 'jobTypeId']) !!}
                <div class="mb-5">
                    {!! Form::label('name', __('messages.job_type.name').(':'), ['class' => 'form-label']) !!}
                    <span class="required"></span>
                    {!! Form::text('name', null, [
                        'id' => 'editJobTypeName',
                        'class' => 'form-control',
                        'required' => 'required',
                        'placeholder' => __('messages.job_type.name')
                    ]) !!}
                </div>
                <div class="mb-5">
                    {!! Form::label('description', __('messages.job_type.description').(':'), ['class' => 'form-label']) !!}
                    <span class="required"></span>
                    <div id="editJobTypeDescriptionQuillData"></div>
                    {!! Form::hidden('description', null, ['id' => 'edit_job_type_desc']) !!}
                </div>
            </div>
            <div class="px-6 py-4 border-t border-gray-200 flex justify-end space-x-2 pt-0">
                {!! Form::button(__('messages.common.save'), [
                    'type' => 'submit',
                    'class' => 'btn btn-primary m-0',
                    'id' => 'jobTypeEditSaveBtn',
                    'data-loading-text' => "<span class="spinner-border spinner-border-sm"></span> ".__('messages.common.process')
                ]) !!}
                {!! Form::button(__('messages.common.cancel'), [
                    'type' => 'button',
                    'class' => 'btn btn-secondary my-0 ms-5 me-0',
                    'id' => 'btnEditCancel',
                    'data-bs-dismiss' => 'modal'
                ]) !!}
            </div>
            {!! Form::close() !!}
        </div>
    </div>
</div>
