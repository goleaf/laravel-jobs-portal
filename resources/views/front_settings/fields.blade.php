<div class="flex flex-wrap">
    <div class="md:w-3/12 flex-1 sm-12">
        <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('messages.front_settings.featured_jobs_enable') }}</label>
        <label class="pl-0 flex-1 -12 flex items-center form-switch">
            <input type="checkbox" name="featured_jobs_enable"
                   class="flex items-center input featured-job-active"
                   data-id="{{ ($frontSettings['featured_jobs_enable'] == 1) ? 1 : 0 }}"
                    {{ ($frontSettings['featured_jobs_enable'] == 1) ? 'checked' : '' }} >
            <span class=""></span>
        </label>
    </div>
    <div class="flex-1 -xl-3 md:w-3/12 flex-1 sm-12">
        {{ Form::label('currency', __('messages.front_settings.featured_listing_currency').':', ['class' => 'block text-sm font-medium text-gray-700 mb-1']) }}
        <span class="required"></span>
        {{ Form::select('currency', $currencies, (isset($frontSettings['currency']) && $frontSettings['currency'])?$frontSettings['currency']:null ,['id'=>'currency','class' => 'form-select frontSettingCurrency','placeholder' => __('messages.company.select_currency'),'text-red-500']) }}
    </div>
    <div class="md:w-3/12 flex-1 sm-12">
        <label name="featured_jobs_price"
               class="block text-sm font-medium text-gray-700 mb-1">{{ __('messages.front_settings.featured_jobs_price').':' }}</label>
        <span class="required"></span>
        {{ Form::text('featured_jobs_price', !empty($frontSettings['featured_jobs_price']) ? $frontSettings['featured_jobs_price'] : 0, ['class' => 'block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm salary', 'text-red-500','min' => 0, 'max' => '50000','placeholder' => __('messages.front_settings.featured_jobs_price'),'onkeyup' => 'if (/\D/g.test(this.value)) this.value = this.value.replace(/\D/g,"")']) }}
    </div>
    <div class="md:w-3/12 flex-1 sm-12">
        <label name="featured_jobs_days"
               class="block text-sm font-medium text-gray-700 mb-1">{{ __('messages.front_settings.featured_jobs_due_days').':' }}</label>
        <span class="required"></span>
        {{ Form::text('featured_jobs_days', $frontSettings['featured_jobs_days'], ['class' => 'block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm salary', 'text-red-500','min' => 0, 'max' => '20', 'placeholder' => __('messages.front_settings.featured_jobs_due_days'), 'onkeyup' => 'if (/\D/g.test(this.value)) this.value = this.value.replace(/\D/g,"")']) }}
    </div>

    <div class="flex-1 -xl-3 md:w-3/12 flex-1 sm-12 mt-5 mb-5">
        <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('messages.front_settings.featured_companies_enable') }}</label>
        <label class="pl-0 flex-1 -12 flex items-center form-switch">
            <input type="checkbox" name="featured_companies_enable"
                   class="flex items-center input featured-company-active"
                    {{ ($frontSettings['featured_companies_enable'] == 1) ? 'checked' : '' }}>
            <span class=""></span>
        </label>
    </div>
    <div class="md:w-3/12 flex-1 sm-12 mt-5 mb-5">
        <label name="featured_jobs_quota"
               class="block text-sm font-medium text-gray-700 mb-1">{{ __('messages.front_settings.featured_jobs_quota').':' }}</label>
        <span class="required"></span>
        {{ Form::text('featured_jobs_quota', $frontSettings['featured_jobs_quota'], ['class' => 'block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm salary', 'text-red-500','min' => 0, 'max' => '20', 'placeholder' => __('messages.front_settings.featured_jobs_quota'), 'onkeyup' => 'if (/\D/g.test(this.value)) this.value = this.value.replace(/\D/g,"")']) }}
    </div>
    <div class="md:w-3/12 flex-1 sm-12 mt-5 mb-5">
        <label name="featured_companies_price"
               class="block text-sm font-medium text-gray-700 mb-1">{{ __('messages.front_settings.featured_companies_price').':' }}</label>
        <span class="required"></span>
        {{ Form::text('featured_companies_price', $frontSettings['featured_companies_price'], ['class' => 'block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm salary', 'text-red-500','min' => 0, 'max' => '50000', 'placeholder' => __('messages.front_settings.featured_companies_price'), 'onkeyup' => 'if (/\D/g.test(this.value)) this.value = this.value.replace(/\D/g,"")']) }}
    </div>
    <div class="md:w-3/12 flex-1 sm-12 mt-5 mb-5">
        <label name="featured_companies_days"
               class="block text-sm font-medium text-gray-700 mb-1">{{ __('messages.front_settings.featured_companies_due_days').':' }}</label>
        <span class="required"></span>
        {{ Form::text('featured_companies_days', $frontSettings['featured_companies_days'], ['class' => 'block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm salary', 'text-red-500','min' => 0, 'max' => '20', 'placeholder' =>__('messages.front_settings.featured_companies_due_days'), 'onkeyup' => 'if (/\D/g.test(this.value)) this.value = this.value.replace(/\D/g,"")' ]) }}
    </div>

    <div class="flex-1 -xl-3 md:w-3/12 flex-1 sm-12">
        <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('messages.front_settings.latest_jobs_enable') }}
            <span data-bs-toggle="tooltip"
                  data-placement="top"
                  data-bs-original-title="{{ __('messages.front_settings.latest_jobs_enable_message') }}">
                    <i class="fas fa-question-circle ml-1 general-question-mark"></i>
                </span>
        <label class="pl-0 flex-1 -12 flex items-center form-switch">
            <input type="checkbox" name="latest_jobs_enable"
                   class="flex items-center input job-country-active"
                    {{ (isset($frontSettings['latest_jobs_enable']) && $frontSettings['latest_jobs_enable'] == 1) ? 'checked' : '' }}>
            <span class="custom-switch-indicator"></span>
        </label>
    </div>
    <div class="md:w-3/12 flex-1 sm-12">
        <label name="featured_companies_quota"
               class="block text-sm font-medium text-gray-700 mb-1">{{ __('messages.front_settings.featured_companies_quota').':' }}</label>
        <span class="required"></span>
        {{ Form::text('featured_companies_quota', $frontSettings['featured_companies_quota'], ['class' => 'block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm salary', 'text-red-500','min' => 0, 'max' => '20', 'placeholder' => __('messages.front_settings.featured_companies_quota'), 'onkeyup' => 'if (/\D/g.test(this.value)) this.value = this.value.replace(/\D/g,"")']) }}
    </div>
    <div class="md:w-6/12 flex-1 sm-12">
        <div class="block" io-image-input="true">
            <label for="favicon" class="block text-sm font-medium text-gray-700 mb-1">
                {{ __('web.job_menu.advertise_image').':' }}
                <span class="text-red-600">*</span>
                <span data-bs-toggle="tooltip"
                      data-placement="top"
                      data-bs-original-title="{{ __('messages.setting.image_validation') }}">
                    <i class="fas fa-question-circle ml-1 general-question-mark"></i>
                </span>
            </label>
            <div class="block">
                <div class="image-picker">
                    <div class="image previewImage" id="previewImage"
                         style="background-image: url({{ !empty($frontSettings['advertise_image']) ? $frontSettings['advertise_image'] : asset('assets/img/infyom-logo.png') }})">
                    </div>
                    <span class="picker-edit rounded -circle text-gray-500 fs-small" data-bs-toggle="tooltip"
                          data-placement="top" data-bs-original-title="{{ __('messages.tooltip.change_image') }}">
                    <label>
                        <i class="fa-solid fa-pen" id="profileImageIcon"></i>
                        {{ Form::file('advertise_image',['id'=>'advertiseImage','class' => 'image-upload d-none', 'accept' => '.png, .jpg, .jpeg']) }}
                    </label>
                </span>
                </div>
            </div>
        </div>
    </div>
    <div class="flex justify-end">
        {{ Form::submit(__('messages.common.save'), ['class' => 'rounded-md bg-indigo-600 px-4 py-2 text-sm font-semibold text-white hover:bg-indigo-700 focus:outline-none transition-colors me-3','name' => 'save', 'id' => 'saveJob']) }}
        <a href="{{ route('admin.dashboard') }}"
           class="border border-gray-300 bg-transparent">{{ __('messages.common.cancel') }}</a>
    </div>
</div>
