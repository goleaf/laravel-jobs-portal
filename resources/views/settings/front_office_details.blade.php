@extends('settings.index')
@section('title')
    {{ __('messages.footer_settings') }}
@endsection
@section('section')
    {{ Form::open(['route' => 'settings.update','id'=>'editFrontSettingForm']) }}
    {{ Form::hidden('sectionName', $sectionName) }}
    <div class="flex-wrap mt-3 flex">
        <div class="flex-1 sm-12 my-0">
            {{ Form::label('address', __('messages.setting.address').':', ['class' => 'block text-sm font-medium text-gray-700 mb-1']) }}
            <span class="required"></span>
            {{ Form::textarea('address', $setting['address'], ['class' => 'block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm h-75', 'text-red-500','placeholder' => __('messages.setting.address')]) }}
        </div>
        <div class="flex-1 sm-6">
            {{ Form::label('phone', __('messages.setting.phone').':', ['class' => 'block text-sm font-medium text-gray-700 mb-1']) }}
            <span class="required"></span>
            <br>
            {{ Form::tel('phone', $setting['phone'], ['class' => 'block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm','onkeyup' => 'if (/\D/g.test(this.value)) this.value = this.value.replace(/\D/g,"")' ,'text-red-500','id'=>'phoneNumber']) }}
            {{ Form::hidden('region_code',null,['id'=>'prefix_code']) }}
            <br>
            <p id="valid-msg" class="mt-2 text-green-600 hidden fw-400 fs-small">{{ __('messages.phone.valid_number') }}</p>
            <p id="error-msg" class="mt-2 text-red-600 hidden fw-400 fs-small"></p>
        </div>
        <div class="flex-1 sm-6">
            {{ Form::label('email', __('messages.setting.email').':', ['class' => 'block text-sm font-medium text-gray-700 mb-1']) }}
            <span class="required"></span>
            {{ Form::email('email', $setting['email'], ['class' => 'block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm', 'text-red-500', 'placeholder' => __('messages.setting.email')]) }}
        </div>
    </div>
    <div class="mb-5 mt-4">
        <!-- Submit Field -->
        <div class="flex justify-end">
            {{ Form::button(__('messages.common.save'), ['type' => 'submit','class' => 'rounded-md bg-indigo-600 px-4 py-2 text-sm font-semibold text-white hover:bg-indigo-700 focus:outline-none transition-flex-1 px-4ors me-3']) }}
            <a href="{{ route('admin.dashboard', ['section' => 'front_office_details']) }}"
               class="border border-gray-300 bg-transparent">{{ __('messages.common.cancel') }}</a>
        </div>
    </div>
    {{ Form::close() }}
@endsection
@push('scripts')
    {{-- <script src="{{ asset('assets/js/inttel/js/intlTelInput.min.js') }}"></script> --}}
    {{-- <script src="{{ asset('assets/js/inttel/js/utils.min.js') }}"></script> --}}
    {{--  --}}
    {{-- <script src="{{ mix('assets/js/custom/phone-number-country-code.js') }}"></script> --}}

    
    {{-- <script src="{{ asset('assets/js/inttel/js/intlTelInput.min.js') }}"></script> --}}
    {{-- <script src="{{ asset('assets/js/inttel/js/utils.min.js') }}"></script> --}}
    {{-- <script src="{{ asset('assets/js/custom/phone-number-country-code.js') }}"></script> --}}
@endpush

@push('scripts')
    @vite('resources/js/pages/front_office_details.js')
@endpush
