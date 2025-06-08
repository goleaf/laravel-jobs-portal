<div id="editImageSlidersModal" class="fixed inset-0 z-50 overflow-y-auto fade" tabindex="-1" role="dialog">
    <div class="flex items-center justify-center min-h-screen px-4">
        <!-- Modal content-->
        <div class="shadow rounded bg-white -lg -xl max-w-lg w-full">
            <div class="border border border border-gray-300 -gray-300 px-6 py-4 -b -gray-200">
                <h3 class="fixed inset-0 z-50 overflow-y-auto -title">{{ __('messages.image_slider.edit_image_slider') }}</h3>
                <button type="button" aria-label="Close" class="transition duration-150 ease-in-out flex-1"
                        data-bs-dismiss="modal">
                </button>
            </div>
            {{ Form::open(['id'=>'editImageSliderForm','files'=>true]) }}
            <div class="px-6 py-4">
                <div class="rounded border p-4 mb-4 rounded border mb-4 border border-gray-300 -gray-300 px-4 py-3 -md -gray-300 -md danger hidden" id="editValidationErrorsBox">
                    <i class="flex-wrap fa-solid fa-face-fflex -mx-4n me-5"></i>
                </div>
                {{ Form::hidden('imageSliderId',null,['id'=>'imageSliderId']) }}
                <div class="flex-wrap flex">
                    <div class="flex-1 sm-12">
                        {{-- <div class="flex-wrap flex"> --}}
                        {{-- <div class="px-3"> --}}
                        {{ --  Form::label('image_slider', __('messages.image_slider.image').':') <span -- }}
                        {{-- class="text-red-600">*</span> --}}
                        {{-- <span><i class="ml-1 fas fa-question-circle" --}}
                        {{-- data-toggle="tooltip" --}}
                        {{-- data-placement="top" --}}
                        {{ -- title=" __('messages.image_slider.image_title_text') "></i></span> -- }}
                        {{ -- <label class="image__file-upload">  __('messages.setting.choose')  -- }}
                        {{ --  Form::file('image_slider',['id'=>'editImageSlider','class' => 'hidden'])  -- }}
                        {{-- </label> --}}
                        {{-- </div> --}}
                        {{-- <div class="pl-3 mt-1 flex-1 -6 w-auto"> --}}
                        {{-- <img id='editPreviewImage' class="img-thumbnail thumbnail-preview" --}}
                        {{ -- src=" asset('assets/img/infyom-logo.png') "> -- }}
                        {{-- </div> --}}
                        {{-- </div> --}}
                        {{-- <a href="#" target="_blank" id="imageSliderUrl"></a> --}}

                        <div class="mb-5 flex-1 sm-12" io-image-input="true">
                            <label for="image_slider" class="mb-1 block text-sm font-medium text-gray-700">
                                {{ __('messages.image_slider.image').':' }}
                                <span class="text-red-600">*</span>
                                <span data-bs-toggle="tooltip"
                                      data-placement="top"
                                      data-bs-original-title="{{ __('messages.image_slider.image_title_text') }}">
        <i class="ml-1 fas fa-question-circle general-question-mark"></i>
</span>
                            </label>
                            <div class="block">
                                <div class="image-picker">
                                    <div class="image previewImage" id="editPreviewImage"
                                         style="background-image: url({{ asset('assets/img/infyom-logo.png') }})">
                                    </div>
                                    <span class="rounded picker-edit -full text-gray-500 fs-small"
                                          data-bs-toggle="tooltip"
                                          data-placement="top" data-bs-original-title="{{ __('messages.tooltip.change_image') }}">
                    <label>
                        <i class="fa-solid fa-pen" id="profileImageIcon"></i>
                        {{ Form::file('image_slider',['class' => 'image-upload hidden', 'accept' => '.png, .jpg, .jpeg']) }}
                    </label>
                </span>
                                </div>
                            </div>
                        </div>

                    </div>
                    <div class="flex-1 sm-12">
                        {{ --  Form::label('description', __('messages.image_slider.description').':')  -- }}
                        {{ --  Form::textarea('description', null, ['class' => 'block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm', 'id' => 'editDescription'])  -- }}

                        {{ Form::label('description', __('messages.image_slider.description').':',['class' => 'block text-sm font-medium text-gray-700 mb-1']) }}
                        <div id="editImageSliderDescriptionQuillData"></div>
                        {{ Form::hidden('description', null, ['id' => 'editDescriptionData']) }}
                    </div>
                    <div class="pt-1 mb-0 mt-5 flex-1 sm-4">
                        {{ Form::label('status', __('messages.common.status').(':'),['class' => 'block text-sm font-medium text-gray-700 mb-1']) }}
                        <div class="flex items-center form-switch">
                            <input class="flex items-center input" name="is_active" type="checkbox"
                                   value="1" id="editIsActive" checked>
                        </div>
                    </div>
                </div>
            </div>
            <div class="border pt-0 border border border-gray-300 -gray-300 px-6 py-4 -t -gray-200 flex justify-end space-x-2">
                {{ Form::button(__('messages.common.save'), ['type' => 'submit','class' => 'rounded-md bg-indigo-600 px-4 py-2 text-sm font-semibold text-white hover:bg-indigo-700 focus:outline-none transition-flex-1 px-4ors m-0','id' => 'imageSliderEditSaveBtn','data-loading-text' =>"<span class="rounded border border border border border border-gray-300 -gray-300 animate-spin -full -2 -gray-300 -t-blue-600 spinner- -sm"></span>".__('messages.common.process')]) }}
                <button type="button" class="border border-gray-300 bg-transparent" id="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 transition-flex-1 px-4ors duration-200Cancel"
                        data-bs-dismiss="modal">{{ __('messages.common.cancel') }}</button>
            </div>
            {{ Form::close() }}
        </div>
    </div>
</div>
