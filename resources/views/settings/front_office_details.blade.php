@extends('settings.index')
@section('title')
    {{ __('messages.footer_settings') }}
@endsection
@section('section')
    {{ Form::open(['route' => 'settings.update','id'=>'editFrontSettingForm']) }}
    {{ Form::hidden('sectionName', $sectionName) }}
    <div class="flex flex-wrap mt-3">
        <div class="flex-1 -sm-12 my-0">
            {{ Form::label('address', __('messages.setting.address').':', ['class' => 'form-label']) }}
            <span class="required"></span>
            {{ Form::textarea('address', $setting['address'], ['class' => 'form-control h-75', 'required','placeholder' => __('messages.setting.address')]) }}
        </div>
        <div class="flex-1 -sm-6">
            {{ Form::label('phone', __('messages.setting.phone').':', ['class' => 'form-label']) }}
            <span class="required"></span>
            <br>
            {{ Form::tel('phone', $setting['phone'], ['class' => 'form-control','onkeyup' => 'if (/\D/g.test(this.value)) this.value = this.value.replace(/\D/g,"")' ,'required','id'=>'phoneNumber']) }}
            {{ Form::hidden('region_code',null,['id'=>'prefix_code']) }}
            <br>
            <p id="valid-msg" class="text-green-600 hidden fw-400 fs-small mt-2">{{__('messages.phone.valid_number')}}</p>
            <p id="error-msg" class="text-red-600 hidden fw-400 fs-small mt-2"></p>
        </div>
        <div class="flex-1 -sm-6">
            {{ Form::label('email', __('messages.setting.email').':', ['class' => 'form-label']) }}
            <span class="required"></span>
            {{ Form::email('email', $setting['email'], ['class' => 'form-control', 'required', 'placeholder' => __('messages.setting.email')]) }}
        </div>
    </div>
    <div class="mt-4 mb-5">
        <!-- Submit Field -->
        <div class="flex justify-content-end">
            {{ Form::button(__('messages.common.save'), ['type' => 'submit','class' => 'btn btn-primary me-3']) }}
            <a href="{{ route('admin.dashboard', ['section' => 'front_office_details']) }}"
               class="btn px-4 py-2 rounded font-medium transition-colors -secondary me-2">{{__('messages.common.cancel')}}</a>
        </div>
    </div>
    {{ Form::close() }}
@endsection
@push('scripts')
    {{--    <script src="{{ asset('assets/js/inttel/js/intlTelInput.min.js') }}"></script>--}}
    {{--    <script src="{{ asset('assets/js/inttel/js/utils.min.js') }}"></script>--}}
    {{--    <script>--}}
    {{--        let utilsScript = "{{asset('assets/js/inttel/js/utils.min.js')}}";--}}
    {{--        let phoneNo = "{{ old('region_code').old('phone') }}";--}}
    {{--        let isEdit = true;--}}
    {{--    </script>--}}
    {{--    <script src="{{ mix('assets/js/custom/phone-number-country-code.js') }}"></script>--}}

    <script>
        let isEdit = true;
        var phoneNo = "{{ old('region_code').old('phone') }}";
    </script>
    {{--    <script src="{{ asset('assets/js/inttel/js/intlTelInput.min.js') }}"></script>--}}
    {{--    <script src="{{ asset('assets/js/inttel/js/utils.min.js') }}"></script>--}}
    {{--    <script src="{{ asset('assets/js/custom/phone-number-country-code.js') }}"></script>--}}
@endpush
