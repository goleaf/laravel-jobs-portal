
@push('styles')
    @vite('resources/css/pages/index.css')
@endpush
@extends('front_web.layouts.app')
@section('title')
    {{ __('web.contact_us') }}
@endsection
@section('page_css')
    
@endsection
@section('content')
    <div class="contactus-page">
        <section class="hero-section relative bg-color-light py-40">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mx-auto px-4 mx-auto px-4 mx-auto">
                <div class="flex flex-wrap items-center justify-center">
                    <div class="flex-1 lg-6 text-center mb-lg-0 mb-md-5 mb-sm-4">
                        <div class="hero-content">
                            <h1 class="text-gray-600 mb-3">
                                {{ __('web.contact_us') }}
                            </h1>
                            <nav aria-label="breadcrumb">
                                <ol class="breadcrumb justify-center mb-0">
                                    <li class="breadcrumb-item"><a href="{{ route('front.home') }}"
                                                                    class="fs-18 text-gray">{{ __('web.home') }} </a>
                                    </li>
                                    <li class="breadcrumb-item text-indigo-600-600 fs-18"
                                        aria-current="page">{{ __('web.contact_us') }}</li>
                                </ol>
                            </nav>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <section class="contact-us-section py-60 mb-5">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mx-auto px-4 mx-auto px-4 mx-auto">
                <div class="contact-us bg-color-light">
                    <div class="flex flex-wrap">
                        <div class="flex-1 lg-4 lg:block hidden">
                            <div class="contact-img ms-5 ps-xl-5 mt-5">
                                <img src="{{ asset('front_web/images/contact-page.png') }}">
                            </div>
                        </div>
                        <div class="flex-1 lg-8">
                            @formOpen(['id' => 'formContact', 'name' => 'frm-contact', 'class' => 'py-40 pe-lg-5 px-4', 'method' => 'POST', 'url' => route('front.')])
                                @csrf
                                @include('flash::message')
                                @include('front_web.layouts.errors')
                                <div class="flex flex-wrap">
                                    <div class="mb-4 flex-1 -12">
                                        <div class="response"></div>
                                    </div>
                                    <div class="flex-1 md-6">
                                        <div class="mb-4">
                                            {{ Form::label('name', __('web.web_contact.your_name').':', ['class' => 'fs-16 text-secondary mb-2']) }}
                                            <span class="text-indigo-600 -600">*</span>
                                            {{ Form::text('name', old('name'), [
                                                'class' => 'block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm fs-14 text-gray br-10',
                                                'placeholder' => __('web.web_contact.your_name'),
                                                'autocomplete' => 'off',
                                                'text-red-500' => true
                                            ]) }}
                                        </div>
                                    </div>
                                    <div class="flex-1 md-6">
                                        <div class="mb-4">
                                            {{ Form::label('email', __('web.web_contact.your_email').':', ['class' => 'fs-16 text-secondary mb-2']) }}
                                            <span class="text-indigo-600 -600">*</span>
                                            {{ Form::email('email', old('email'), [
                                                'class' => 'block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm fs-14 text-gray br-10',
                                                'placeholder' => __('web.web_contact.your_email'),
                                                'autocomplete' => 'off',
                                                'text-red-500' => true
                                            ]) }}
                                        </div>
                                    </div>
                                    <div class="flex-1 md-6">
                                        <div class="mb-4">
                                            {{ Form::label('subject', __('web.web_contact.subject').':', ['class' => 'fs-16 text-secondary mb-2']) }}
                                            <span class="text-indigo-600 -600">*</span>
                                            {{ Form::text('subject', old('subject'), [
                                                'class' => 'block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm fs-14 text-gray br-10',
                                                'placeholder' => __('web.web_contact.subject'),
                                                'autocomplete' => 'off',
                                                'text-red-500' => true
                                            ]) }}
                                        </div>
                                    </div>
                                    <div class="flex-1 md-6">
                                        <div class="mb-4">
                                            {{ Form::label('phone_no', __('web.web_contact.your_phone_no').':', ['class' => 'fs-16 text-secondary mb-2']) }}
                                            {{ Form::tel('phone_no', old('phone_no'), [
                                                'class' => 'block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm fs-14 text-gray br-10 block',
                                                'placeholder' => __('web.web_contact.phone_number'),
                                                'autocomplete' => 'off',
                                                'id' => 'phoneNumber'
                                            ]) }}
                                            <input type="hidden" name="region_code" id="prefix_code">
                                            <p id="valid-msg" class="text-green-600 hidden fw-400 fs-small mt-2">{{ __('messages.phone.valid_number') }}</p>
                                            <p id="error-msg" class="text-red-600 hidden fw-400 fs-small mt-2"></p>
                                        </div>
                                    </div>
                                    <div class="flex-1 md-12">
                                        <div class="mb-4">
                                            {{ Form::label('message', __('web.web_contact.your_message').':', ['class' => 'fs-16 text-secondary mb-2']) }}
                                            <span class="text-indigo-600 -600">*</span>
                                            {{ Form::textarea('message', old('message'), [
                                                'class' => 'block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm fs-14 text-gray br-10',
                                                'rows' => 5,
                                                'placeholder' => __('web.web_contact.type_your_message'),
                                                'text-red-500' => true
                                            ]) }}
                                        </div>
                                    </div>
                                    @if(getSettingValue('enable_google_recaptcha'))
                                    <div class="flex-1 md-12">
                                        <div class="g-recaptcha flex justify-center" id="gRecaptchaContainerCompanyRegistration"
                                             data-sitekey="{{ config('app.google_recaptcha_site_key') }}"
                                             name="g-recaptcha"></div>
                                        <div id="g-recaptcha-error"></div>
                                    </div>
                                    @endif
                                </div>
                                <div class="flex flex-wrap justify-center mt-4">
                                    <div class="flex-1 sm-6 text-center">
                                        {{ Form::button(__('web.contact_us_menu.send_message'), [
                                            'type' => 'submit',
                                            'class' => 'rounded-md bg-indigo-600 px-4 py-2 text-sm font-semibold text-white hover:bg-indigo-700 focus:outline-none transition-colors'
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

{{-- @section('page_scripts') --}}
{{--  --}}

{{-- {{-- CDN JS removed - now using local assets --}}--}}
{{-- @endsection --}}

@push('scripts')
    @vite('resources/js/pages/index.js')
@endpush
