{{-- <div class="px-6 py-4"> --}}
{{-- <div class="px-4 py-3 rounded-md border border-gray-300 mb-4 p-4 rounded-md mb-4 danger  hide hidden" id="editValidationErrorsBox"></div> --}}
    <div class="flex flex-wrap">
        <div class="flex-1 sm-12 mb-5">
            {{ Form::label('home_title', __('messages.cms_service.home_title').(':'), ['class' => 'block text-sm font-medium text-gray-700 mb-1']) }}
            <span class="required"></span>
            {{ Form::text('home_title', $cmsServices['home_title'], ['class' => 'block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm','text-red-500', 'placeholder' => __('messages.cms_service.home_title')]) }}
        </div>
        <div class="flex-1 sm-12 mb-5">
            {{ Form::label('home_description', __('messages.cms_service.home_description').(':'),['class' => 'block text-sm font-medium text-gray-700 mb-1']) }}
            {{ Form::textarea('home_description',$cmsServices['home_description'], ['class' => 'block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm','text-red-500', 'placeholder' => __('messages.cms_service.home_description')]) }}
        </div>
    </div>
    <div class="flex-1 sm-12 mb-5" io-image-input="true">
        <label for="home_banner" class="block text-sm font-medium text-gray-700 mb-1">
            {{ __('messages.cms_service.home_banner').':' }}
            <span class="required"></span>
           <span data-bs-toggle="tooltip"
                              data-placement="top"
                              data-bs-original-title="{{ __('messages.setting.image_validation') }}">
        <i class="fas fa-question-circle ml-1  general-question-mark"></i>
</span>
        </label>
        <div class="block">
            <div class="image-picker">
                <div class="image previewImage" id="homeBannerPreview"
                     style="background-image: url({{ ($cmsServices['home_banner']) ? asset($cmsServices['home_banner']) :asset('assets/img/infyom-logo.png') }})">
                </div>
                <span class="picker-edit rounded-circle text-gray-500 fs-small"
                      data-bs-toggle="tooltip"
                      data-placement="top" data-bs-original-title="{{ __('messages.tooltip.change_home_banner') }}">
                    <label>
                        <i class="fa-solid fa-pen" id="profileImageIcon"></i>
                        {{ Form::file('home_banner',['id'=>'home_banner','class' => 'image-upload d-none', 'accept' => '.png, .jpg, .jpeg']) }}
                    </label>
                </span>
            </div>
        </div>
    </div>

    <div class="flex flex-wrap mt-5">
        <!-- Submit Field -->
        <div class="flex justify-end">
            {{ Form::submit(__('messages.common.save'), ['class' => 'rounded-md bg-indigo-600 px-4 py-2 text-sm font-semibold text-white hover:bg-indigo-700 focus:outline-none transition-colors me-3']) }}
            {{ -- <a class="rounded-md transition">__('messages.common.cancel') </a> -- }}
        </div>
    </div>
{{-- </div> --}}




