<div id="showJobShiftModal" class="modal fade" role="dialog" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <h3 class="modal-title">{{ __('messages.job_shift.job_shift_detail') }}</h3>
                <button type="button" aria-label="Close" class="px-4 py-2 rounded font-medium transition-colors -close"
                        data-bs-dismiss="modal">
                </button>
            </div>
            {{ Form::open(['id' => 'showForm']) }}
            <div class="modal-body">
                <div class="alert p-4 rounded-md mb-4 -danger  hide hidden" id="maritalStatusValidationErrorsBox"></div>
                <div class="mb-5">
                    {{ Form::label('name',__('messages.job_shift.shift').(':'), ['class' => 'form-label']) }}
                        <p id="showShift" class="text-gray-600"></p>
                    </div>
                    <div class="mb-5">
                        {{ Form::label('description',__('messages.job_shift.description').(':'),['class' => 'form-label']) }}
                        <p id="showJobShiftDescription" class=" text-gray-600"></p>
                    </div>

                </div>
            {{ Form::close() }}
        </div>
    </div>
</div>

