<div id="addJobCategoryModal" class="fixed inset-0 z-50 overflow-y-auto fade" role="dialog" tabindex="-1" aria-hidden="true">
    <div class="flex items-center justify-center min-h-screen px-4 fixed inset-0 z-50 overflow-y-auto -lg">
        <!-- Modal content-->
        <div class="bg-white rounded -lg shadow-xl max-w-lg w-full">
            <div class="px-6 py-4 border-b border border border-gray-300 -gray-300 -gray-200">
                <h3 class="fixed inset-0 z-50 overflow-y-auto -title">{{ __('messages.job_category.new_job_category') }}</h3>
                <button type="button" aria-label="Close" class="px-4 py-2 rounded font-medium transition-colors close" data-bs-dismiss="modal">
                    <x-icons.close class="h-4 w-4" />
                </button>
            </div>
            {{ Form::open(['id' => 'addJobCategoryForm']) !!}
            <div class="px-6 py-4">
                <div class="px-4 py-3 rounded-md border border border border-gray-300 -gray-300 -gray-300 mb-4 p-4 rounded -md mb-4 danger hidden" id="jobCategoryValidationErrorsBox"></div>
                <div class="mb-5">
                    {{ Form::label('name', __('messages.job_category.name').':', ['class' => 'block text-sm font-medium text-gray-700 mb-1']) !!}
                    <span class="required"></span>
                    {{ Form::text('name', null, [
                        'class' => 'block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm',
                        'text-red-500' => 'text-red-500',
                        'placeholder' => __('messages.job_category.name')
                    ]) !!}
                </div>
                <div class="mb-5 h-full">
                    {{ Form::label('description', __('messages.job_category.description').':', ['class' => 'block text-sm font-medium text-gray-700 mb-1']) }}
                    <span class="required"></span>
                    <div id="addJobCategoryDescriptionQuillData" class="job-category-description"></div>
                    {{ Form::hidden('description', null, ['id' => 'jobCategoryDescriptionValue']) }}
                </div>
                <div class="flex-1 -xl-6 md:w-6/12 flex-1 sm-12 mb-5" io-image-input="true">
                    {{ Form::label('category_image', __('messages.common.category_image').':', ['class' => 'block text-sm font-medium text-gray-700 mb-1']) !!}
                    <span data-bs-toggle="tooltip"
                          data-placement="top"
                          data-bs-original-title="{{ __('messages.setting.image_validation') }}">
                        <i class="fas fa-question-circle ml-1 general-question-mark"></i>
                    </span>
                    <div class="block">
                        <div class="image-picker">
                            <div class="image previewImage" id="logoPreview"
                                 style="background-image: url({{ asset('front_web/images/job-categories.png') }})">
                            </div>
                            <span class="picker-edit rounded -circle text-gray-500 fs-small" data-bs-toggle="tooltip"
                                  data-placement="top" data-bs-original-title="{{ __('messages.tooltip.change_image') }}">
                                <label>
                                    <i class="fa-solid fa-pen" id="profileImageIcon"></i>
                                    {{ Form::file('customer_image', [
                                        'class' => 'image-upload d-none',
                                        'accept' => '.png, .jpg, .jpeg'
                                    ]) !!}
                                </label>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="px-6 py-4 border-t border border border-gray-300 -gray-300 -gray-200 flex justify-end space-x-2 pt-0">
                {{ Form::button(__('messages.common.save'), [
                    'type' => 'submit',
                    'class' => 'rounded-md bg-indigo-600 px-4 py-2 text-sm font-semibold text-white hover:bg-indigo-700 focus:outline-none transition-colors m-0',
                    'id' => 'jobCategoryBtnSave',
                    'data-loading-text' =>"<span class="animate-spin h-5 w-5 border-2 border-current border-t-transparent rounded -full spinner- border border border-gray-300 -gray-300 -sm"></span>".__('messages.common.process')
                ]) !!}
                {{ Form::button(__('messages.common.cancel'), [
                    'type' => 'button',
                    'class' => 'rounded-md border border-gray-300 bg-white px-4 py-2 text-sm font-semibold text-gray-700 hover:bg-gray-50 focus:outline-none transition-colors my-0 ms-5 me-0',
                    'id' => 'jobCategoryBtnCancel',
                    'data-bs-dismiss' => 'modal'
                ]) !!}
            </div>
            {{ Form::close() !!}
        </div>
    </div>
</div>
