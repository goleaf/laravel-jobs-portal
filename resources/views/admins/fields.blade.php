<div class="flex flex-wrap">
    <div class="col-xl-6 md:w-6/12 flex-1 sm-12 mb-5">
        {{ Form::label('first_name',__('messages.candidate.first_name').':', ['class' => 'block text-sm font-medium text-gray-700 mb-1 ']) }}
        <span class="required"></span>
        {{ Form::text('first_name', null, ['class' => 'block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm','required', 'placeholder' => __('messages.candidate.first_name')]) }}
    </div>
    <div class="col-xl-6 md:w-6/12 flex-1 sm-12 mb-5">
        {{ Form::label('last_name',__('messages.candidate.last_name').':', ['class' => 'block text-sm font-medium text-gray-700 mb-1']) }}
        <span class="required"></span>
        {{ Form::text('last_name', null, ['class' => 'block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm','required', 'placeholder' => __('messages.candidate.last_name')]) }}
    </div>
    <div class="col-xl-6 md:w-6/12 flex-1 sm-12 mb-5">
        {{ Form::label('email',__('messages.candidate.email').':', ['class' => 'block text-sm font-medium text-gray-700 mb-1']) }}
        <span class="required"></span>
        {{ Form::text('email', null, ['class' => 'block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm','required', 'placeholder' => __('messages.candidate.email')]) }}
    </div>
    <div class="col-xl-6 md:w-6/12 flex-1 sm-12 mb-55 mobile-itel-width">
        {{ Form::label('phone', __('messages.candidate.phone').':', ['class' => 'block text-sm font-medium text-gray-700 mb-1 ']) }}
        <br>
        {{ Form::tel('phone', null, ['class' => 'block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm d', 'onkeyup' => 'if (/\D/g.test(this.value)) this.value = this.value.replace(/\D/g,"")','id'=>'phoneNumber','placeholder' => __('messages.inquiry.phone_no')]) }}
        {{ Form::hidden('region_code',null,['id'=>'prefix_code']) }}
        <p id="valid-msg" class="text-green-600 hidden fw-400 fs-small mt-2">{{ __('messages.phone.valid_number') }}</p>
        <p id="error-msg" class="text-red-600 hidden fw-400 fs-small mt-2">{{ __('messages.phone.invalid_number') }}</p>
    </div>
    <div class="col-xl-6 md:w-6/12 flex-1 sm-12 mb-5">
        {{ Form::label('password',__('messages.candidate.password').':', ['class' => 'block text-sm font-medium text-gray-700 mb-1']) }}
        <span class="required"></span>
        {{ Form::password('password', [
            'class' => 'block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm-solid',
            'id' => 'password',
            'required' => true,
            'placeholder' => __('messages.candidate.password')
        ]) }}
    </div>
    <div class="col-xl-6 md:w-6/12 flex-1 sm-12 mb-5">
        {{ Form::label('cpassword',__('messages.candidate.conform_password').':', ['class' => 'block text-sm font-medium text-gray-700 mb-1']) }}
        <span class="required"></span>
        {{ Form::password('cpassword', [
            'class' => 'block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm-solid',
            'id' => 'cpassword',
            'required' => true,
            'placeholder' => __('messages.candidate.conform_password')
        ]) }}
    </div>
    {{ Form::label('Profile',__('messages.profile').':', ['class' => 'block text-sm font-medium text-gray-700 mb-1']) }}
    <div class="block">
        <div class="image-picker">
            <div class="image previewImage" id="logoPreview"
                 style="background-image: url({{ asset('assets/img/infyom-logo.png') }})">
            </div>
            <span class="picker-edit rounded-circle text-gray-500 fs-small" data-bs-toggle="tooltip"
                  data-placement="top" data-bs-original-title="{{ __("messages.tooltip.change_profile") }}">
                    <label>
                        <i class="fa-solid fa-pen" id="profileImageIcon"></i>
                        {{ Form::file('profile',['class' => 'image-upload d-none', 'accept' => '.png, .jpg, .jpeg']) }}
                    </label>
                </span>
        </div>
    </div>
    <div class="flex justify-end mt-5">
        {{ Form::submit(__('messages.common.save'), ['class' => 'rounded-md bg-indigo-600 px-4 py-2 text-sm font-semibold text-white hover:bg-indigo-700 focus:outline-none transition-colors me-3']) }}
        <a href="{{ route('admin.admin.index') }}"
           class="inline-flex items-center px-4 py-2 border border-gray-300 border-transparent text-sm font-medium rounded-md transition duration-150 ease-in-out px-4 py-2 rounded font-medium transition-colors secondary me-2">{{ __('messages.common.cancel') }}</a>
    </div>
</div>

