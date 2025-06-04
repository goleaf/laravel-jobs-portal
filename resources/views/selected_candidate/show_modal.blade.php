<div id="showModal" class="modal fade" role="dialog" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <h3>{{ __('messages.marital_status.marital_status_detail') }}</h3>
                <button type="button" aria-label="Close" class="px-4 py-2 rounded font-medium transition-colors -close"
                        data-bs-dismiss="modal">
                </button>
            </div>
            {{ Form::open(['id' => 'showForm']) }}
            <div class="modal-body">
                <div class="alert p-4 rounded-md mb-4 -danger  hide hidden" id="maritalStatusValidationErrorsBox"></div>
                <div class="flex flex-wrap">
                    <div class="flex-1 -sm-12 mb-5">
                        {{ Form::label('marital_status', __('messages.marital_status.marital_status').(':'), ['class' => 'pb-2 fs-5 text-gray-600']) }}
                        <p id="showMaritalStatus" class="fs-5 text-gray-800"></p>
                    </div>
                    <div class="flex-1 -sm-12 mb-5">
                        {{ Form::label('description', __('messages.marital_status.description').(':'),['class' => 'pb-2 fs-5 text-gray-600']) }}
                        <p id="showDescription" class="fs-5 text-gray-800"></p>
                    </div>

                </div>
            </div>
            {{ Form::close() }}
        </div>
    </div>
</div>
