<div id="addImageSlidersModal" class="fixed inset-0 z-50 overflow-y-auto fade" tabindex="-1" role="dialog">
    <div class="flex items-center justify-center min-h-screen px-4">
        <!-- Modal content-->
        <div class="shadow rounded bg-white -lg -xl max-w-lg w-full">
            <div class="border border border border-gray-300 -gray-300 px-6 py-4 -b -gray-200">
                <h3 class="fixed inset-0 z-50 overflow-y-auto -title">{{ __('messages.image_slider.new_image_slider') }}</h3>
                <button type="button" aria-label="Close" class="rounded px-4 py-2 font-medium transition-colors close"
                        data-bs-dismiss="modal">
                </button>
            </div>
            {{ Form::open(['id'=>'addImageSliderForm','files'=>true]) }}
            <div class="px-6 py-4">
                <div class="rounded border p-4 mb-4 rounded border mb-4 border border-gray-300 -gray-300 px-4 py-3 -md -gray-300 -md danger hidden" id="validationErrorsBox">
                    <i class="fa-solid fa-face-frown me-5"></i>
                </div>
                <div class="flex-wrap flex">


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
                                <div class="image previewImage" id="previewImage"
                                     style="background-image: url({{ asset('assets/img/infyom-logo.png') }})">
                                </div>
                                <span class="rounded picker-edit -full text-gray-500 fs-small" data-bs-toggle="tooltip"
                                      data-placement="top" data-bs-original-title="{{ __('messages.tooltip.change_image') }}">
                    <label>
                        <i class="fa-solid fa-pen" id="profileImageIcon"></i>
                        {{ Form::file('image_slider',['class' => 'image-upload hidden', 'accept' => '.png, .jpg, .jpeg']) }}
                    </label>
                </span>
                            </div>
                        </div>
                    </div>

                    <div class="flex-1 sm-12">
                        {{ --  Form::label('description', __('messages.image_slider.description').':')  -- }}
                        {{ --  Form::textarea('description', null, ['class' => 'block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm', 'id' => 'description'])  -- }}
                        {{ Form::label('description', __('messages.image_slider.description').':',['class' => 'block text-sm font-medium text-gray-700 mb-1']) }}
                        <div id="addImageSliderDescriptionQuillData"></div>
                        {{ Form::hidden('description', null, ['id' => 'descriptionData']) }}
                    </div>
                    <div class="pt-1 mb-0 mt-5 flex-1 sm-4">
                        {{ -- <label> __('messages.common.status').':' </label><br> -- }}
                        {{-- <label class="pl-0 custom-switch"> --}}
                        {{-- <input type="checkbox" name="is_active" class="custom-switch-input" --}}
                        {{-- value="1" id="active" checked> --}}
                        {{-- <span class="custom-switch-indicator"></span> --}}
                        {{-- </label> --}}
                        {{ Form::label('status', __('messages.common.status').(':'),['class' => 'block text-sm font-medium text-gray-700 mb-1']) }}
                        <div class="flex items-center form-switch">
                            <input class="flex items-center input" name="is_active" type="checkbox"
                                   value="1" checked>
                        </div>
                    </div>
                </div>
            </div>
            <div class="border pt-0 border border border-gray-300 -gray-300 px-6 py-4 -t -gray-200 flex justify-end space-x-2">
                {{ Form::button(__('messages.common.save'), ['type' => 'submit','class' => 'rounded-md bg-indigo-600 px-4 py-2 text-sm font-semibold text-white hover:bg-indigo-700 focus:outline-none transition-colors m-0','id' => 'imageSliderSaveBtn','data-loading-text' =>"<span class="rounded border border border border border border-gray-300 -gray-300 animate-spin -full -2 -gray-300 -t-blue-600 spinner- -sm"></span>".__('messages.common.process')]) }}
                <button type="button" class="border border-gray-300 bg-transparent" id="btnCancel"
                        data-bs-dismiss="modal">{{ __('messages.common.cancel') }}</button>
            </div>
            {{ Form::close() }}
        </div>
    </div>
</div>
