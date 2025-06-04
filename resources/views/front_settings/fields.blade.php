<div class="flex flex-wrap">
    <div class="md:w-3/12 flex-1 -sm-12">
        <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('messages.front_settings.featured_jobs_enable') }}</label>
        <label class="pl-0 flex-1 -12 flex items-center form-switch">
            <input type="checkbox" name="featured_jobs_enable"
                   class="flex items-center -input featured-job-active"
                   data-id="{{ ($frontSettings['featured_jobs_enable'] == 1) ? 1 : 0 }}"
                    {{ ($frontSettings['featured_jobs_enable'] == 1) ? 'checked' : '' }} >
            <span class=""></span>
        </label>
    </div>
    <div class="col-xl-3 md:w-3/12 flex-1 -sm-12">
        {{ Form::label('currency', __('messages.front_settings.featured_listing_currency').':', ['class' => 'form-label']) }}
        <span class="required"></span>
        {{ Form::select('currency', $currencies, (isset($frontSettings['currency']) && $frontSettings['currency'])?$frontSettings['currency']:null ,['id'=>'currency','class' => 'form-select frontSettingCurrency','placeholder' => __('messages.company.select_currency'),'required']) }}
    </div>
    <div class="md:w-3/12 flex-1 -sm-12">
        <label name="featured_jobs_price"
               class="block text-sm font-medium text-gray-700 mb-1">{{ __('messages.front_settings.featured_jobs_price').':' }}</label>
        <span class="required"></span>
        {{ Form::text('featured_jobs_price', !empty($frontSettings['featured_jobs_price']) ? $frontSettings['featured_jobs_price'] : 0, ['class' => 'form-control salary', 'required','min' => 0, 'max' => '50000','placeholder' => __('messages.front_settings.featured_jobs_price'),'onkeyup' => 'if (/\D/g.test(this.value)) this.value = this.value.replace(/\D/g,"")']) }}
    </div>
    <div class="md:w-3/12 flex-1 -sm-12">
        <label name="featured_jobs_days"
               class="block text-sm font-medium text-gray-700 mb-1">{{ __('messages.front_settings.featured_jobs_due_days').':' }}</label>
        <span class="required"></span>
        {{ Form::text('featured_jobs_days', $frontSettings['featured_jobs_days'], ['class' => 'form-control salary', 'required','min' => 0, 'max' => '20', 'placeholder' => __('messages.front_settings.featured_jobs_due_days'), 'onkeyup' => 'if (/\D/g.test(this.value)) this.value = this.value.replace(/\D/g,"")']) }}
    </div>

    <div class="col-xl-3 md:w-3/12 flex-1 -sm-12 mt-5 mb-5">
        <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('messages.front_settings.featured_companies_enable') }}</label>
        <label class="pl-0 flex-1 -12 flex items-center form-switch">
            <input type="checkbox" name="featured_companies_enable"
                   class="flex items-center -input featured-company-active"
                    {{ ($frontSettings['featured_companies_enable'] == 1) ? 'checked' : '' }}>
            <span class=""></span>
        </label>
    </div>
    <div class="md:w-3/12 flex-1 -sm-12 mt-5 mb-5">
        <label name="featured_jobs_quota"
               class="block text-sm font-medium text-gray-700 mb-1">{{ __('messages.front_settings.featured_jobs_quota').':' }}</label>
        <span class="required"></span>
        {{ Form::text('featured_jobs_quota', $frontSettings['featured_jobs_quota'], ['class' => 'form-control salary', 'required','min' => 0, 'max' => '20', 'placeholder' => __('messages.front_settings.featured_jobs_quota'), 'onkeyup' => 'if (/\D/g.test(this.value)) this.value = this.value.replace(/\D/g,"")']) }}
    </div>
    <div class="md:w-3/12 flex-1 -sm-12 mt-5 mb-5">
        <label name="featured_companies_price"
               class="block text-sm font-medium text-gray-700 mb-1">{{ __('messages.front_settings.featured_companies_price').':' }}</label>
        <span class="required"></span>
        {{ Form::text('featured_companies_price', $frontSettings['featured_companies_price'], ['class' => 'form-control salary', 'required','min' => 0, 'max' => '50000', 'placeholder' => __('messages.front_settings.featured_companies_price'), 'onkeyup' => 'if (/\D/g.test(this.value)) this.value = this.value.replace(/\D/g,"")']) }}
    </div>
    <div class="md:w-3/12 flex-1 -sm-12 mt-5 mb-5">
        <label name="featured_companies_days"
               class="block text-sm font-medium text-gray-700 mb-1">{{ __('messages.front_settings.featured_companies_due_days').':' }}</label>
        <span class="required"></span>
        {{ Form::text('featured_companies_days', $frontSettings['featured_companies_days'], ['class' => 'form-control salary', 'required','min' => 0, 'max' => '20', 'placeholder' =>__('messages.front_settings.featured_companies_due_days'), 'onkeyup' => 'if (/\D/g.test(this.value)) this.value = this.value.replace(/\D/g,"")' ]) }}
    </div>

    <div class="col-xl-3 md:w-3/12 flex-1 -sm-12">
        <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('messages.front_settings.latest_jobs_enable') }}
            <span data-bs-toggle="tooltip"
                  data-placement="top"
                  data-bs-original-title="{{ __('messages.front_settings.latest_jobs_enable_message') }}">
                    <i class="fas fa-question-circle ml-1  general-question-mark"></i>
                </span>
        <label class="pl-0 flex-1 -12 flex items-center form-switch">
            <input type="checkbox" name="latest_jobs_enable"
                   class="flex items-center -input job-country-active"
                    {{ (isset($frontSettings['latest_jobs_enable']) && $frontSettings['latest_jobs_enable'] == 1) ? 'checked' : '' }}>
            <span class="custom-switch-indicator"></span>
        </label>
    </div>
    <div class="md:w-3/12 flex-1 -sm-12">
        <label name="featured_companies_quota"
               class="block text-sm font-medium text-gray-700 mb-1">{{ __('messages.front_settings.featured_companies_quota').':' }}</label>
        <span class="required"></span>
        {{ Form::text('featured_companies_quota', $frontSettings['featured_companies_quota'], ['class' => 'form-control salary', 'required','min' => 0, 'max' => '20', 'placeholder' => __('messages.front_settings.featured_companies_quota'), 'onkeyup' => 'if (/\D/g.test(this.value)) this.value = this.value.replace(/\D/g,"")']) }}
    </div>
    <div class="md:w-6/12 flex-1 -sm-12">
        <div class="block" io-image-input="true">
            <label for="favicon" class="block text-sm font-medium text-gray-700 mb-1">
                {{__('web.job_menu.advertise_image').':'}}
                <span class="text-red-600">*</span>
                <span data-bs-toggle="tooltip"
                      data-placement="top"
                      data-bs-original-title="{{  __('messages.setting.image_validation') }}">
                    <i class="fas fa-question-circle ml-1  general-question-mark"></i>
                </span>
            </label>
            <div class="block">
                <div class="image-picker">
                    <div class="image previewImage" id="previewImage"
                         style="background-image: url({{ !empty($frontSettings['advertise_image']) ? $frontSettings['advertise_image'] : asset('assets/img/infyom-logo.png') }})">
                    </div>
                    <span class="picker-edit rounded-circle text-gray-500 fs-small" data-bs-toggle="tooltip"
                          data-placement="top" data-bs-original-title="{{__('messages.tooltip.change_image')}}">
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
        {{ Form::submit(__('messages.common.save'), ['class' => 'btn btn-primary me-3','name' => 'save', 'id' => 'saveJob']) }}
        <a href="{{ route('admin.dashboard') }}"
           class="inline-flex items-center px-4 py-2 border border-gray-300 border-transparent text-sm font-medium rounded-md transition duration-150 ease-in-out px-4 py-2 rounded font-medium transition-colors -secondary me-2">{{__('messages.common.cancel')}}</a>
    </div>
</div>
