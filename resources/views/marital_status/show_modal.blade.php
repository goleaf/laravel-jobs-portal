<div id="showMaritalStatusModal" class="fixed inset-0 z-50 overflow-y-auto fade" role="dialog" tabindex="-1" aria-hidden="true">
    <div class="flex items-center justify-center min-h-screen px-4">
        <!-- Modal content-->
        <div class="shadow rounded bg-white -lg -xl max-w-lg w-full">
            <div class="border border px-6 py-4 -b -gray-200">
                <h3 class="fixed inset-0 z-50 overflow-y-auto -title">{{ __('messages.marital_status.marital_status_detail') }}</h3>
                <button type="button" aria-label="Close" class="rounded px-4 py-2 font-medium transition-colors close"
                        data-bs-dismiss="modal">
                </button>
            </div>
            {{ Form::open(['id' => 'showForm']) }}
            <div class="px-6 py-4">
                <div class="rounded border p-4 mb-4 rounded border mb-4 px-4 py-3 -md -gray-300 -md danger fs-4 text-white flex items-center hidden" id="maritalStatusValidationErrorsBox">
                    <i class="fa-solid fa-face-frown me-5"></i>
                </div>
                    <div class="mb-5">
                        {{ Form::label('marital_status', __('messages.marital_status.marital_status').(':'), ['class' => 'block text-sm font-medium text-gray-700 mb-1']) }}
                        <p id="showMaritalStatus" class="text-gray-600"></p>
                    </div>
                    <div class="mb-5">
                        {{ Form::label('description', __('messages.marital_status.description').(':'),['class' => 'block text-sm font-medium text-gray-700 mb-1']) }}
                        <p id="showMaritalStatusDescription" class="text-gray-600"></p>
                    </div>

                </div>
            {{ Form::close() }}
        </div>
    </div>
</div>
