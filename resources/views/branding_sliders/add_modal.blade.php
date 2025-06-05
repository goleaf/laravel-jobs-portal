<div id="addBrandingsSlidersModal" tabindex="-1" class="fixed inset-0 z-50 overflow-y-auto fade" role="dialog" aria-hidden="true">
    <div class="flex items-center justify-center min-h-screen px-4">
        <!-- Modal content-->
        <div class="shadow rounded bg-white -lg -xl max-w-lg w-full">
            <div class="border border border border-gray-300 -gray-300 px-6 py-4 -b -gray-200">
                <h3 class="fixed inset-0 z-50 overflow-y-auto -title">{{ __('messages.branding_slider.new_branding_slider') }}</h3>
                <button type="button" aria-label="Close" class="rounded px-4 py-2 font-medium transition-colors close"
                        data-bs-dismiss="modal">
                </button>
            </div>
            {{ Form::open(['id' => 'addBrandingSliderNewForm', 'files' => true]) }}
            <div class="px-6 py-4">
                <div class="rounded border p-4 mb-4 rounded border mb-4 border border-gray-300 -gray-300 px-4 py-3 -md -gray-300 -md danger hidden" id="validationErrorsBox">
                    <i class="fa-solid fa-face-frown me-5"></i>
                </div>
                <div class="flex-wrap flex">
                    <div class="mb-5 flex-1 sm-12">
                        <div class="flex-wrap flex">
                            <div class="mb-5 flex-1 sm-12">
                                {{ Form::label('title', __('messages.candidate_profile.title').':', ['class' => 'block text-sm font-medium text-gray-700 mb-1']) }}
                                <span class="required"></span>
                                {{ Form::text('title', null, ['class' => 'block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm', 'id' => 'title', 'text-red-500', 'placeholder' =>  __('messages.candidate_profile.title')]) }}
                            </div>

                            <div class="mb-5 flex-1 sm-12" io-image-input="true">
                                <label for="branding_slider" class="mb-1 block text-sm font-medium text-gray-700">
                                    {{ __('messages.image_slider.image').':' }}
                                    <span class="required"></span>
                                    <span data-bs-toggle="tooltip"
                              data-placement="top"
                              data-bs-original-title="{{ __('messages.setting.image_validation') }}">
        <i class="ml-1 fas fa-question-circle general-question-mark"></i>
</span>
                                </label>
                                <div class="block">
                                    <div class="image-picker">
                                        <div class="image previewImage" id="previewImage"
                                             style="background-image: url({{ asset('assets/img/infyom-logo.png') }})">
                                        </div>
                                        <span class="rounded picker-edit -full text-gray-500 fs-small"
                                              data-bs-toggle="tooltip"
                                              data-placement="top" data-bs-original-title="{{ __('messages.tooltip.change_image') }}">
                    <label>
                        <i class="fa-solid fa-pen" id="profileImageIcon"></i>
                        {{ Form::file('branding_slider',['class' => 'image-upload hidden', 'accept' => '.png, .jpg, .jpeg']) }}
                    </label>
                </span>
                                    </div>
                                </div>
                            </div>

                            <div class="mb-5 flex-1 sm-6">
                                <label
                                        class="mb-1 block text-sm font-medium text-gray-700">{{ __('messages.common.status').':' }}</label>
                                <label class="flex items-center form-switch form-switch-sm">
                                    <input type="checkbox" name="is_active" class="flex items-center input"
                                           id="active" checked>
                                    <span class=""></span>
                                </label>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="border pt-0 border border border-gray-300 -gray-300 px-6 py-4 -t -gray-200 flex justify-end space-x-2">
                {{ Form::button(__('messages.common.save'), ['type' => 'submit','class' => 'rounded-md bg-indigo-600 px-4 py-2 text-sm font-semibold text-white hover:bg-indigo-700 focus:outline-none transition-colors m-0','id' => 'brandingSliderSaveBtn','data-loading-text' =>"<span class="rounded border border border border border border-gray-300 -gray-300 animate-spin -full -2 -gray-300 -t-blue-600 spinner- -sm"></span>".__('messages.common.process')]) }}
                <button type="button" class="border border-gray-300 bg-transparent" id="btnCancel"
                        data-bs-dismiss="modal">{{ __('messages.common.cancel') }}</button>
            </div>
            {{ Form::close() }}
        </div>
    </div>
</div>
