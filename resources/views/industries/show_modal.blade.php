<div id="showIndustriesModal" class="modal fade" role="dialog" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <h3 class="modal-title">{{ __('messages.industry.industry_detail') }}</h3>
                <button type="button" aria-label="Close" class="px-4 py-2 rounded font-medium transition-colors -close"
                        data-bs-dismiss="modal">
                </button>
            </div>
            <div class="modal-body">
                <div class="alert p-4 rounded-md mb-4 -danger fs-4 text-white flex items-center hidden" id="maritalStatusValidationErrorsBox">
                    <i class="fa-solid fa-face-frown me-5"></i>
                </div>
                <div class="flex flex-wrap">
                    <div class="mb-5">
                        {{ Form::label('name', __('messages.industry.name').(':'), ['class' => 'form-label']) }}
                        <p id="showIndustryName" class="text-gray-600"></p>
                    </div>
                    <div class="mb-5">
                        {{ Form::label('showDescription', __('messages.industry.description').(':'),['class' => 'form-label']) }}
                        <p id="showIndustryDescription" class=" text-gray-600"></p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
