@extends('settings.index')
@section('title')
    {{ __('messages.setting.general') }}
@endsection
@section('section')
    {{ Form::open(['route' => 'settings.update', 'files' => true, 'id'=>'editGeneralSettingForm']) }}
    {{ Form::hidden('sectionName', $sectionName) }}
    <div class="flex-wrap mt-3 flex">
        <div class="flex-1 sm-6">
            {{ Form::label('application_name', __('messages.setting.application_name').':', ['class' => 'block text-sm font-medium text-gray-700 mb-1']) }}
            <span class="required"></span>
            {{ Form::text('application_name', $setting['application_name'], ['class' => 'block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm', 'text-red-500','placeholder'=>__('messages.setting.application_name')]) }}
        </div>
        <div class="flex-1 sm-6">
            {{ Form::label('application_name', __('messages.setting.company_url').':', ['class' => 'block text-sm font-medium text-gray-700 mb-1']) }}
            <span class="required"></span>
            {{ Form::text('company_url', $setting['company_url'], ['class' => 'block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm', 'text-red-500', 'id' => 'companyUrl','placeholder' => __('messages.setting.company_url')]) }}
        </div>
        <div class="mt-5 flex-1 sm-12 my-0">
            {{ Form::label('company_description', __('messages.setting.company_description').':', ['class' => 'block text-sm font-medium text-gray-700 mb-1']) }}
            <span class="required"></span>
            {{ Form::textarea('company_description', $setting['company_description'], ['class' => 'block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm h-75', 'text-red-500','placeholder' => __('messages.setting.company_description')]) }}
        </div>
    </div>
    <div class="flex-wrap flex">
        <!-- Logo Field -->
        {{-- <div class="mb-4 flex-1 sm-4"> --}}
        {{-- <div class="flex-wrap flex"> --}}
        {{-- <div class="px-3"> --}}
        {{ --  Form::label('app_logo', __('messages.setting.logo').':') <span class="text-red-600">*</span> -- }}
        {{-- <i class="ml-1 mt-1 fas fa-question-circle general-question-mark" data-toggle="tooltip" --}}
        {{-- data-placement="top" title="Upload 90 x 60 logo to get best user experience."></i> --}}
        {{ -- <label class="image__file-upload">  __('messages.setting.choose')  -- }}
        {{ --  Form::file('logo',['id'=>'logo','class' => 'hidden'])  -- }}
        {{-- </label> --}}
        {{-- </div> --}}
        {{-- <div class="pl-3 mt-1 w-auto"> --}}
        {{-- <img id='logoPreview' class="img-thumbnail thumbnail-preview" --}}
        {{ -- src="($setting['logo']) ? asset($setting['logo']) : asset('assets/img/infyom-logo.png') "> -- }}
        {{-- </div> --}}
        {{-- </div> --}}
        {{-- </div> --}}
        <div class="mb-5 flex-1 sm-12">
            <div class="flex-wrap flex">
                <div class="mb-5 lg:w-4/12 px-2 flex-1 sm-6" io-image-input="true">
                    <label for="app_logo" class="mb-1 block text-sm font-medium text-gray-700">
                        {{ __('messages.setting.logo').':' }}
                        <span class="required"></span>
                        <span data-bs-toggle="tooltip"
                              data-placement="top"
                              data-bs-original-title="{{ __('messages.setting.image_validation') }}">
        <i class="ml-1 fas fa-question-circle general-question-mark"></i>
</span>
                    </label>
                    <div class="block">
                        <div class="image-picker">
                            <div class="image previewImage" id="logoPreview"
                                 style="background-image: url({{ !empty($setting['logo']) ? $setting['logo'] : asset('assets/img/infyom-logo.png') }})">
                            </div>
                            <span class="rounded picker-edit -full text-gray-500 fs-small" data-bs-toggle="tooltip"
                                  data-placement="top" data-bs-original-title="{{ __('messages.tooltip.change_app_logo') }}">
                    <label>
                        <i class="fa-solid fa-pen" id="profileImageIcon"></i>
                        {{ Form::file('logo',['class' => 'image-upload hidden', 'accept' => '.png, .jpg, .jpeg']) }}
                    </label>
                </span>
                        </div>
                    </div>
                </div>
                <div class="mb-5 lg:w-4/12 px-2 flex-1 sm-6" io-image-input="true">
                    <label for="app_footer_logo" class="mb-1 block text-sm font-medium text-gray-700">
                        {{ __('messages.app_footer_logo').':' }}
                        <span class="required"></span>
                        <span data-bs-toggle="tooltip"
                              data-placement="top"
                              data-bs-original-title="{{ __('messages.setting.image_validation') }}">
        <i class="ml-1 fas fa-question-circle general-question-mark"></i>
</span>
                    </label>
                    <div class="block">
                        <div class="image-picker">
                            <div class="image previewImage" id="footerLogoPreview"
                                 style="background-image: url({{ !empty($setting['footer_logo']) ? $setting['footer_logo'] : asset('assets/img/infyom-logo.png') }})">
                            </div>
                            <span class="rounded picker-edit -full text-gray-500 fs-small" data-bs-toggle="tooltip"
                                  data-placement="top" data-bs-original-title="{{ __('messages.tooltip.change_app_logo') }}">
                    <label>
                        <i class="fa-solid fa-pen" id="profileImageIcon"></i>
                        {{ Form::file('footer_logo',['class' => 'image-upload hidden', 'accept' => '.png, .jpg, .jpeg']) }}
                    </label>
                </span>
                        </div>
                    </div>
                </div>
                <div class="mb-5 lg:w-4/12 px-2 flex-1 sm-6" io-image-input="true">
                    <label for="favicon" class="mb-1 block text-sm font-medium text-gray-700">
                        {{ __('messages.setting.favicon').':' }}
                        <span class="required"></span>
                        <span data-bs-toggle="tooltip"
                              data-placement="top"
                              data-bs-original-title="{{ __('messages.setting.image_validation') }}">
        <i class="ml-1 fas fa-question-circle general-question-mark"></i>
</span>
                    </label>
                    <div class="block">
                        <div class="image-picker">
                            <div class="image previewImage" id="faviconPreview"
                                 style="background-image: url({{ !empty($setting['favicon']) ? $setting['favicon'] : asset('assets/img/infyom-logo.png') }})">
                            </div>
                            <span class="rounded picker-edit -full text-gray-500 fs-small" data-bs-toggle="tooltip"
                                  data-placement="top" data-bs-original-title="{{ __('messages.tooltip.change_favicon') }}">
                    <label>
                        <i class="fa-solid fa-pen" id="profileImageIcon"></i>
                        {{ Form::file('favicon',['class' => 'image-upload hidden', 'accept' => '.png, .jpg, .jpeg']) }}
                    </label>
                </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- <div class="mb-4 flex-1 sm-4"> --}}
        {{-- <div class="flex-wrap flex"> --}}
        {{-- <div class="px-3"> --}}
        {{ --  Form::label('app_footer_logo','Footer Logo:') <span class="text-red-600">*</span> -- }}
        {{-- <i class="ml-1 mt-1 fas fa-question-circle general-question-mark" data-toggle="tooltip" --}}
        {{-- data-placement="top" title="Upload 90 x 60 logo to get best user experience."></i> --}}
        {{ -- <label class="image__file-upload">  __('messages.setting.choose')  -- }}
        {{ --  Form::file('footer_logo',['id'=>'footerLogo','class' => 'hidden'])  -- }}
        {{-- </label> --}}
        {{-- </div> --}}
        {{-- <div class="pl-3 mt-1 w-auto"> --}}
        {{-- <img id='footerLogoPreview' class="img-thumbnail thumbnail-preview" --}}
        {{ -- src="($setting['footer_logo']) ? asset($setting['footer_logo']) : asset('assets/img/infyom-logo.png') "> -- }}
        {{-- </div> --}}
        {{-- </div> --}}
        {{-- </div> --}}
        {{-- <div class="mb-4 flex-1 sm-4"> --}}
        {{-- <div class="flex-wrap flex"> --}}
        {{-- <div class="px-3"> --}}
        {{ --  Form::label('favicon', __('messages.setting.favicon').':')  -- }}
        {{-- <span class="text-red-600">*</span><i class="ml-1 mt-1 fas fa-question-circle general-question-mark" --}}
        {{-- data-toggle="tooltip" data-placement="top" --}}
        {{-- title="The image must be of pixel 16 x 16 and 32 x 32."></i> --}}
        {{ -- <label class="image__file-upload">  __('messages.setting.choose')  -- }}
        {{ --  Form::file('favicon',['id'=>'favicon','class' => 'hidden'])  -- }}
        {{-- </label> --}}
        {{-- </div> --}}
        {{-- <div class="pl-3 mt-1 w-auto"> --}}
        {{-- <img id='faviconPreview' class="mt-4 img-thumbnail thumbnail-preview width-40px" --}}
        {{ -- src="($setting['favicon']) ? asset($setting['favicon']) : asset('assets/img/infyom-logo.png') "> -- }}
        {{-- </div> --}}
        {{-- </div> --}}
        {{-- </div> --}}
        {{-- <div class="mb-4 lg:w-full px-2 flex-1 md-12 flex justify-start"> --}}
        {{-- <label class="flex-wrap pl-0 mt-0 custom-switch switch-label flex"> --}}
        {{-- <input type="checkbox" name="enable_google_recaptcha" class="custom-switch-input flex items-center input" --}}
        {{ --  ($setting['enable_google_recaptcha']) ? 'checked' : ''  value="1"> -- }}
        {{-- <span class="custom-switch-indicator switch-span"></span> --}}
        {{-- </label> --}}
        {{ -- <span class="mb-3 font-bold custom-switch-description fs-6 fw-bolder text-gray-700"> __('messages.setting.enable_google_recaptcha') </span> -- }}
        {{-- </div> --}}
        <div class="mb-5 md:w-5/12 flex-1 sm-6">
            {{ Form::label('status', __('messages.setting.enable_google_recaptcha'), ['class' => 'block text-sm font-medium text-gray-700 mb-1 inline']) }}
            <span class="required"></span>
            <div class="flex items-center form-switch">
                <input class="flex items-center input" name="enable_google_recaptcha" type="checkbox"
                       value="1"
                       {{ ($setting['enable_google_recaptcha']) ? 'checked' : '' }} placeholder="{{ __('messages.setting.enable_google_recaptcha') }}">
            </div>
        </div>
        <div class="flex-1 px-4 md:w-4/12 lg:w-3/12 px-2 -sm-3 flex-1 -12">
            <div class="mb-4 mb-3">
                {{ Form::label('default_country_code', __('messages.common.default_country_code').':', ['class' => 'block text-sm font-medium text-gray-700 mb-1']) }}
                <span class="required"></span>
                {{ Form::text('default_country_data', null, ['class' => 'block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm'  ,'placeholder'=>__('messages.common.default_country_code'), 'id'=>'defaultCountryData']) }}
                {{ Form::hidden('default_country_code',$setting['default_country_code'] ,['id'=>'defaultCountryCode',]) }}
            </div>
        </div>
        <div class="flex-1 px-4 md:w-3/12 lg:w-3/12 px-2 -sm-3 flex-1 -12">
            <div class="mb-4 mb-3">
                {{ Form::label('default_language', __('messages.common.default_language').':', ['class' => 'block text-sm font-medium text-gray-700 mb-1']) }}
                {{ Form::select('default_language', $languages, $setting['default_language'] ?? null, ['class' => 'block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm', 'aria-label'=>"Select a language",'data-control'=>'select2','placeholder' => __('messages.common.select_language')]) }}
            </div>
        </div>
    </div>
    <div class="flex-wrap mb-5 mt-4 flex">
        <!-- Submit Field -->
        <div class="flex justify-end">
            {{ Form::button(__('messages.common.save'), ['type' => 'submit','class' => 'rounded-md bg-indigo-600 px-4 py-2 text-sm font-semibold text-white hover:bg-indigo-700 focus:outline-none transition-flex-1 px-4ors me-3']) }}
            <a href="{{ route('admin.dashboard', ['section' => 'general']) }}"
               class="border border-gray-300 bg-transparent">{{ __('messages.common.cancel') }}</a>
        </div>
    </div>
    {{ Form::close() }}
@endsection
