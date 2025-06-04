<div id="editBrandingsSlidersModal" tabindex="-1" class="fixed inset-0 z-50 overflow-y-auto fade" role="dialog" aria-hidden="true">
    <div class="flex items-center justify-center min-h-screen px-4">
        <!-- Modal content-->
        <div class="bg-white rounded-lg shadow-xl max-w-lg w-full">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="modal-title">{{ __('messages.branding_slider.edit_branding_slider') }}</h3>
                <button type="button" aria-label="Close" class="px-4 py-2 rounded font-medium transition-colors -close"
                        data-bs-dismiss="modal">
                </button>
            </div>
            {{ Form::open(['id' => 'editBrandingSlidersForm', 'files' => true]) }}
            <div class="px-6 py-4">
                <div class="px-4 py-3 rounded-md border border-gray-300 mb-4 p-4 rounded-md mb-4 -danger hidden" id="editValidationErrorsBox">
                    <i class="fa-solid fa-face-frown me-5"></i>
                </div>
                {{ Form::hidden('brandingSliderId', null, ['id' => 'brandingSliderId']) }}
                <div class="flex flex-wrap">
                    <div class="flex-1 -sm-12 mb-5">
                        <div class="flex flex-wrap">
                            <div class="flex-1 -sm-12 mb-5">
                                {{ Form::label('title', __('messages.candidate_profile.title').':', ['class' => 'form-label']) }}
                                <span class="required"></span>
                                {{ Form::text('title', null, ['class' => 'form-control', 'id' => 'editTitle', 'required', 'placeholder' =>  __('messages.candidate_profile.title')]) }}
                            </div>


                            <div class="flex-1 -sm-12 mb-5" io-image-input="true">
                                <label for="branding_slider" class="block text-sm font-medium text-gray-700 mb-1">
                                    {{__('messages.image_slider.image').':'}}
                                    <span class="required"></span>
                                    <span data-bs-toggle="tooltip"
                              data-placement="top"
                              data-bs-original-title="{{  __('messages.setting.image_validation') }}">
        <i class="fas fa-question-circle ml-1  general-question-mark"></i>
</span>
                                </label>
                                <div class="block">
                                    <div class="image-picker">
                                        <div class="image previewImage" id="editPreviewImage"
                                             style="background-image: url({{ asset('assets/img/infyom-logo.png') }})">
                                        </div>
                                        <span class="picker-edit rounded-circle text-gray-500 fs-small"
                                              data-bs-toggle="tooltip"
                                              data-placement="top" data-bs-original-title="{{__('messages.tooltip.change_image')}}">
                    <label>
                        <i class="fa-solid fa-pen" id="profileImageIcon"></i>
                        {{ Form::file('branding_slider',['class' => 'image-upload d-none', 'accept' => '.png, .jpg, .jpeg']) }}
                    </label>
                </span>
                                    </div>
                                </div>
                            </div>
                            {{--                            <div class="flex-1 -sm-12 mb-0 pt-1">--}}
                            {{--                                <a href="#" target="_blank" id="brandingSliderUrl"></a>--}}
                            {{--                            </div>--}}
                            <div class="flex-1 -sm-6 mb-5">
                                <label
                                        class="block text-sm font-medium text-gray-700 mb-1">{{ __('messages.common.status').':' }}</label>
                                <label class="flex items-center form-switch form-switch-sm">
                                    <input type="checkbox" name="is_active" id="editIsActive" class="flex items-center -input"
                                           id="active">
                                    <span class=""></span>
                                </label>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="px-6 py-4 border-t border-gray-200 flex justify-end space-x-2 pt-0">
                {{ Form::button(__('messages.common.save'), ['type' => 'submit','class' => 'btn btn-primary m-0','id' => 'editBrandingSliderSaveBtn','data-loading-text' => "<span class="spinner-border spinner-border-sm"></span> ".__('messages.common.process')]) }}
                <button type="button" class="inline-flex items-center px-4 py-2 border border-gray-300 border-transparent text-sm font-medium rounded-md transition duration-150 ease-in-out px-4 py-2 rounded font-medium transition-colors -secondary my-0 ms-5 me-0"
                        data-bs-dismiss="modal">{{ __('messages.common.cancel') }}</button>
            </div>
            {{ Form::close() }}
        </div>
    </div>
</div>
