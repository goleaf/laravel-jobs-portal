<div id="jobShiftEditModal" class="fixed inset-0 z-50 overflow-y-auto fade" role="dialog" tabindex="-1" aria-hidden="true">
<div class="flex items-center justify-center min-h-screen px-4">
    <!-- Modal content-->
    <div class="bg-white rounded-lg shadow-xl max-w-lg w-full">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="modal-title">{{ __('messages.job_shift.edit_job_shift')  }}</h3>
            <button type="button" aria-label="Close" class="px-4 py-2 rounded font-medium transition-colors -close"
                    data-bs-dismiss="modal">
            </button>
        </div>
        {{ Form::open(['id'=>'editJobShiftForm'])  }}
        <div class="px-6 py-4">
            <div class="px-4 py-3 rounded-md border border-gray-300 mb-4 p-4 rounded-md mb-4 -danger  hide hidden" id="editValidationErrorsBox"></div>
            {{ Form::hidden('jobShiftId',null,['id'=>'jobShiftId'])  }}
            <div class="mb-5">
                {{ Form::label('shift',__('messages.job_shift.shift').(':'), ['class' => 'form-label'])  }}
                <span class="required"></span>
                {{ Form::text('shift', null, ['id'=>'editShift','class' => 'form-control','required', 'placeholder' => __('messages.job_shift.shift')])  }}
            </div>
            <div class="mb-5">
                {{ Form::label('description',__('messages.job_shift.description').(':'),['class' => 'form-label'])  }}
                <span class="required"></span>
                <div id="editJobShiftDescriptionQuillData"></div>
                {{ Form::hidden('description', null, ['id' => 'edit_job_shift_desc'])  }}
            </div>

        </div>
        <div class="px-6 py-4 border-t border-gray-200 flex justify-end space-x-2 pt-0">
            {{ Form::button(__('messages.common.save'), ['type' => 'submit','class' => 'btn btn-primary m-0','id' => 'jobShiftEditSaveBtn','data-loading-text' => "<span class="spinner-border spinner-border-sm"></span> ".__('messages.common.process')])  }}
            <button type="button" class="inline-flex items-center px-4 py-2 border border-gray-300 border-transparent text-sm font-medium rounded-md transition duration-150 ease-in-out px-4 py-2 rounded font-medium transition-colors -secondary my-0 ms-5 me-0"
                    id="btnEditCancel"
                    data-bs-dismiss="modal">{{ __('messages.common.cancel')  }}</button>
            </div>
        {{ Form::close()  }}
    </div>
</div>
</div>

