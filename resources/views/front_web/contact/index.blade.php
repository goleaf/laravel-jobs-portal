@extends('front_web.layouts.app')
@section('title')
    {{ __('web.contact_us') }}
@endsection
@section('page_css')
    <style>
        .iti {
            display: block !important;
        }
    </style>
@endsection
@section('content')
    <div class="contactus-page">
        <section class="hero-section position-relative bg-color-light py-40">
            <div class="container">
                <div class="row align-items-center justify-content-center">
                    <div class="col-lg-6  text-center mb-lg-0 mb-md-5 mb-sm-4 ">
                        <div class="hero-content">
                            <h1 class=" text-secondary mb-3">
                                {{ __('web.contact_us') }}
                            </h1>
                            <nav aria-label="breadcrumb">
                                <ol class="breadcrumb  justify-content-center mb-0">
                                    <li class="breadcrumb-item "><a href="{{ route('front.home') }}"
                                                                    class="fs-18 text-gray">{{ __('web.home') }} </a>
                                    </li>
                                    <li class="breadcrumb-item text-primary fs-18"
                                        aria-current="page">{{ __('web.contact_us') }}</li>
                                </ol>
                            </nav>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <section class="contact-us-section py-60 mb-5">
            <div class="container">
                <div class="contact-us bg-color-light">
                    <div class="row">
                        <div class="col-lg-4 d-lg-block d-none">
                            <div class="contact-img ms-5 ps-xl-5 mt-5">
                                <img src="{{asset('front_web/images/contact-page.png')}}">
                            </div>
                        </div>
                        <div class="col-lg-8">
                            @formOpen(['id' => 'formContact', 'name' => 'frm-contact', 'class' => 'py-40 pe-lg-5 px-4', 'method' => 'POST', 'url' => route('front.contact.send')])
                                @csrf
                                @include('flash::message')
                                @include('front_web.layouts.errors')
                                <div class="row">
                                    <div class="form-group col-12">
                                        <div class="response"></div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            {{ Form::label('name', __('web.web_contact.your_name').':', ['class' => 'fs-16 text-secondary mb-2']) }}
                                            <span class="text-primary">*</span>
                                            {{ Form::text('name', old('name'), [
                                                'class' => 'form-control fs-14 text-gray br-10',
                                                'placeholder' => __('web.web_contact.your_name'),
                                                'autocomplete' => 'off',
                                                'required' => true
                                            ]) }}
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            {{ Form::label('email', __('web.web_contact.your_email').':', ['class' => 'fs-16 text-secondary mb-2']) }}
                                            <span class="text-primary">*</span>
                                            {{ Form::email('email', old('email'), [
                                                'class' => 'form-control fs-14 text-gray br-10',
                                                'placeholder' => __('web.web_contact.your_email'),
                                                'autocomplete' => 'off',
                                                'required' => true
                                            ]) }}
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            {{ Form::label('subject', __('web.web_contact.subject').':', ['class' => 'fs-16 text-secondary mb-2']) }}
                                            <span class="text-primary">*</span>
                                            {{ Form::text('subject', old('subject'), [
                                                'class' => 'form-control fs-14 text-gray br-10',
                                                'placeholder' => __('web.web_contact.subject'),
                                                'autocomplete' => 'off',
                                                'required' => true
                                            ]) }}
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            {{ Form::label('phone_no', __('web.web_contact.your_phone_no').':', ['class' => 'fs-16 text-secondary mb-2']) }}
                                            {{ Form::tel('phone_no', old('phone_no'), [
                                                'class' => 'form-control fs-14 text-gray br-10 d-block',
                                                'placeholder' => __('web.web_contact.phone_number'),
                                                'autocomplete' => 'off',
                                                'id' => 'phoneNumber'
                                            ]) }}
                                            <input type="hidden" name="region_code" id="prefix_code">
                                            <p id="valid-msg" class="text-success d-none fw-400 fs-small mt-2">{{ __('messages.phone.valid_number') }}</p>
                                            <p id="error-msg" class="text-danger d-none fw-400 fs-small mt-2"></p>
                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            {{ Form::label('message', __('web.web_contact.your_message').':', ['class' => 'fs-16 text-secondary mb-2']) }}
                                            <span class="text-primary">*</span>
                                            {{ Form::textarea('message', old('message'), [
                                                'class' => 'form-control fs-14 text-gray br-10',
                                                'rows' => 5,
                                                'placeholder' => __('web.web_contact.type_your_message'),
                                                'required' => true
                                            ]) }}
                                        </div>
                                    </div>
                                    @if(getSettingValue('enable_google_recaptcha'))
                                    <div class="col-md-12">
                                        <div class="g-recaptcha d-flex justify-content-center" id="gRecaptchaContainerCompanyRegistration"
                                             data-sitekey="{{ config('app.google_recaptcha_site_key') }}"
                                             name="g-recaptcha"></div>
                                        <div id="g-recaptcha-error"></div>
                                    </div>
                                    @endif
                                </div>
                                <div class="row justify-content-center mt-4">
                                    <div class="col-sm-6 text-center">
                                        {{ Form::button(__('web.contact_us_menu.send_message'), [
                                            'type' => 'submit',
                                            'class' => 'btn btn-primary'
                                        ]) }}
                                    </div>
                                </div>
                            @formClose()
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
@endsection
<script>
    var phoneNo = "{{ old('region_code').old('phone_no') }}";
</script>
{{--@section('page_scripts')--}}
{{--    <script>--}}
{{--        let isEdit = false--}}
{{--        var phoneNo = "{{ old('region_code').old('phone') }}"--}}
{{--        let utilsScript = "{{asset('assets/js/inttel/js/utils.min.js')}}"--}}
{{--    </script>--}}

{{--    <script src='https://www.google.com/rec aptcha/api.js'></script>--}}
{{--@endsection--}}
