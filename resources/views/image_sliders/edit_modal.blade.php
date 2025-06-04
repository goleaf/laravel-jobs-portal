<div id="editImageSlidersModal" class="modal fade" tabindex="-1" role="dialog">
    <div class="modal-dialog">
        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <h3 class="modal-title">{{ __('messages.image_slider.edit_image_slider') }}</h3>
                <button type="button" aria-label="Close" class="px-4 py-2 rounded font-medium transition-colors -close"
                        data-bs-dismiss="modal">
                </button>
            </div>
            {{ Form::open(['id'=>'editImageSliderForm','files'=>true]) }}
            <div class="modal-body">
                <div class="alert p-4 rounded-md mb-4 -danger hidden" id="editValidationErrorsBox">
                    <i class="fa-solid fa-face-frown me-5"></i>
                </div>
                {{ Form::hidden('imageSliderId',null,['id'=>'imageSliderId']) }}
                <div class="flex flex-wrap">
                    <div class="flex-1 -sm-12">
                        {{--                        <div class="flex flex-wrap">--}}
                        {{--                            <div class="px-3">--}}
                        {{--                                {{ Form::label('image_slider', __('messages.image_slider.image').':') }}<span--}}
                        {{--                                        class="text-red-600">*</span>--}}
                        {{--                                <span><i class="fas fa-question-circle ml-1"--}}
                        {{--                                         data-toggle="tooltip"--}}
                        {{--                                         data-placement="top"--}}
                        {{--                                         title="{{ __('messages.image_slider.image_title_text') }}"></i></span>--}}
                        {{--                                <label class="image__file-upload"> {{ __('messages.setting.choose') }}--}}
                        {{--                                    {{ Form::file('image_slider',['id'=>'editImageSlider','class' => 'd-none']) }}--}}
                        {{--                                </label>--}}
                        {{--                            </div>--}}
                        {{--                            <div class="flex-1 -6 w-auto pl-3 mt-1">--}}
                        {{--                                <img id='editPreviewImage' class="img-thumbnail thumbnail-preview"--}}
                        {{--                                     src="{{ asset('assets/img/infyom-logo.png') }}">--}}
                        {{--                            </div>--}}
                        {{--                        </div>--}}
                        {{--                        <a href="#" target="_blank" id="imageSliderUrl"></a>--}}

                        <div class="flex-1 -sm-12 mb-5" io-image-input="true">
                            <label for="image_slider" class="block text-sm font-medium text-gray-700 mb-1">
                                {{__('messages.image_slider.image').':'}}
                                <span class="text-red-600">*</span>
                                <span data-bs-toggle="tooltip"
                                      data-placement="top"
                                      data-bs-original-title="{{  __('messages.image_slider.image_title_text') }}">
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
                        {{ Form::file('image_slider',['class' => 'image-upload d-none', 'accept' => '.png, .jpg, .jpeg']) }}
                    </label>
                </span>
                                </div>
                            </div>
                        </div>

                    </div>
                    <div class="flex-1 -sm-12">
                        {{--                        {{ Form::label('description', __('messages.image_slider.description').':') }}--}}
                        {{--                        {{ Form::textarea('description', null, ['class' => 'form-control', 'id' => 'editDescription']) }}--}}

                        {{ Form::label('description', __('messages.image_slider.description').':',['class' => 'form-label']) }}
                        <div id="editImageSliderDescriptionQuillData"></div>
                        {{ Form::hidden('description', null, ['id' => 'editDescriptionData']) }}
                    </div>
                    <div class="flex-1 -sm-4 mb-0 pt-1 mt-5">
                        {{ Form::label('status', __('messages.common.status').(':'),['class' => 'form-label']) }}
                        <div class="flex items-center form-switch">
                            <input class="flex items-center -input" name="is_active" type="checkbox"
                                   value="1" id="editIsActive" checked>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer pt-0">
                {{ Form::button(__('messages.common.save'), ['type' => 'submit','class' => 'btn btn-primary m-0','id' => 'imageSliderEditSaveBtn','data-loading-text' => "<span class='spinner-border spinner-border-sm'></span> ".__('messages.common.process')]) }}
                <button type="button" class="btn px-4 py-2 rounded font-medium transition-colors -secondary my-0 ms-5 me-0" id="btnCancel"
                        data-bs-dismiss="modal">{{ __('messages.common.cancel') }}</button>
            </div>
            {{ Form::close() }}
        </div>
    </div>
</div>
