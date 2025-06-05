<div id="showModal" class="fixed inset-0 z-50 overflow-y-auto fade" role="dialog" tabindex="-1" aria-hidden="true">
    <div class="flex items-center justify-center min-h-screen px-4">
        <!-- Modal content-->
        <div class="bg-white rounded-lg shadow-xl max-w-lg w-full">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="modal-title">{{ __('messages.job_category.show_job_category') }}</h3>
                <button type="button" aria-label="Close" class="px-4 py-2 rounded font-medium transition-colors close"
                        data-bs-dismiss="modal">
                </button>
            </div>
            <div class="px-6 py-4">
                <div class="px-4 py-3 rounded-md border border-gray-300 mb-4 p-4 rounded-md mb-4 danger  hide hidden" id="maritalStatusValidationErrorsBox"></div>
                <div class="mb-5">
                    {{ Form::label('name', __('messages.job_category.name').(':'), ['class' => 'block text-sm font-medium text-gray-700 mb-1']) }}
                        <p id="showJobCategoryName" class="text-gray-600"></p>
                    </div>
                    <div class="mb-5">
                        {{ Form::label('description', __('messages.job_category.description').(':'),['class' => 'block text-sm font-medium text-gray-700 mb-1']) }}
                        <p id="showJobCategoryDescription" class="text-gray-600"></p>
                    </div>
            </div>
        </div>
    </div>
</div>
