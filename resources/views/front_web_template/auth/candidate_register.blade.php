@extends('front_web_template.layouts.app')
@section('title')
    {{ __('web.register') }}
@endsection
@section('content')
    <div class="register-page">
        <!-- start hero section -->

        <section class="hero-section relative bg-gradient pt-15 pb-40">
            <div class="container mx-auto px-4 mx-auto">
              <div class="flex flex-wrap items-center justify-center">
                <div class="flex-1 lg-6 text-center mb-lg-0 mb-md-5 mb-sm-4">
                  <div class="hero-content">
                    <h1 class="text-gray-600 mb-3">
                        {{ __('web.register_menu.candidate') . ' ' . __('web.register') }}
                    </h1>
                    <nav aria-label="breadcrumb">
                      <ol class="breadcrumb justify-center mb-0">
                        <li class="breadcrumb-item"><a href="{{ route('front.home') }}" class="fs-18 text-gray">@lang('web.home') </a>
                        </li>
                        <li class="breadcrumb-item text-primary-600 fs-18" aria-current="page">@lang('web.register')</li>
                      </ol>
                    </nav>
                  </div>
                </div>
              </div>
            </div>
          </section>
        <!-- end hero section -->

        <!-- start candidate login section -->
        <section class="py-100">
            <div class="container mx-auto px-4 mx-auto">
                <div class="flex flex-wrap">
                    <div class="col-xl-6 flex-1 lg-8 mx-auto">
                        @include('flash::message')
                        @formOpen(['id' => 'addCandidateNewForm', 'class' => 'py-40 px-40 bg-gray', 'method' => 'POST'])
                            <div class="flex flex-wrap">
                                <div class="flex-1 -12 mb-4">
                                    <div class="form-group flex flex-wrap">
                                        <div class="col-sm-6 flex-1 -12 mb-3 mb-sm-0">
                                            <a href="{{ route('candidate.register') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 border-transparent text-sm font-medium rounded-md transition duration-150 ease-in-out bg-primary-600 text-white hover: bg-primary-600 -700 px-4 py-2 rounded font-medium transition-colors primary-register block">
                                                {{ __('web.register_menu.candidate') }} </a>
                                        </div>
                                        <div class="col-sm-6 flex-1 -12">
                                            <a href="{{ route('employer.register') }}"
                                                class="inline-flex items-center px-4 py-2 border border-gray-300 border-transparent text-sm font-medium rounded-md transition duration-150 ease-in-out px-4 py-2 rounded font-medium transition-colors light-primary block">
                                                {{ __('web.register_menu.employer') }} </a>
                                        </div>
                                    </div>
                                </div>
                                @csrf
                                <div id="candidateValidationErrBox">
                                    @include('layouts.errors')
                                </div>
                                {{ Form::hidden('type', '1') }}
                                <div class="flex-1 md-6">
                                    <div class="form-group mb-md-4 mb-3">
                                        {{ Form::label('candidateFirstName', __('web.common.first_name'), ['class' => 'fs-16 text-secondary mb-3']) }}
                                        <span class="text-red-600">*</span>
                                        {{ Form::text('first_name', null, [
                                            'class' => 'block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm fs-14 text-gray bg-white br-10 p-3',
                                            'id' => 'candidateFirstName',
                                            'placeholder' => __('web.register_menu.enter_first_name'),
                                            'required'
                                        ]) }}
                                    </div>
                                </div>
                                <div class="flex-1 md-6">
                                    <div class="form-group mb-md-4 mb-3">
                                        {{ Form::label('candidateLastName', __('web.common.last_name'), ['class' => 'fs-16 text-secondary mb-3']) }}
                                        <span class="text-red-600">*</span>
                                        {{ Form::text('last_name', null, [
                                            'class' => 'block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm fs-14 text-gray bg-white br-10 p-3',
                                            'id' => 'candidateLastName',
                                            'placeholder' => __('web.register_menu.enter_last_name'),
                                            'required'
                                        ]) }}
                                    </div>
                                </div>
                                <div class="flex-1 md-12">
                                    <div class="form-group mb-md-4 mb-3">
                                        {{ Form::label('candidateEmail', __('web.common.email'), ['class' => 'fs-16 text-secondary mb-3']) }}
                                        <span class="text-red-600">*</span>
                                        {{ Form::email('email', null, [
                                            'class' => 'block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm fs-14 text-gray bg-white br-10 p-3',
                                            'id' => 'candidateEmail',
                                            'placeholder' => __('web.register_menu.enter_email_address'),
                                            'required'
                                        ]) }}
                                    </div>
                                </div>
                                <div class="flex-1 md-6 relative">
                                    <div class="form-group mb-md-4 mb-3">
                                        {{ Form::label('candidatePassword', __('web.common.password'), ['class' => 'fs-16 text-secondary mb-3']) }}
                                        <span class="text-red-600">*</span>
                                        {{ Form::password('password', [
                                            'class' => 'block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm fs-14 text-gray bg-white br-10 p-3',
                                            'id' => 'candidatePassword',
                                            'placeholder' => __('web.register_menu.enter_password'),
                                            'required',
                                            'onkeypress' => 'return avoidSpace(event)'
                                        ]) }}
                                        <span class="absolute flex items-center top-0 mt-7 bottom-0 end-0 me-6 input-icon input-password-hide cursor-pointer text-gray-600 change-type change-type-register">
                                            <i class="fas fa-eye-slash"></i>
                                        </span>
                                    </div>
                                </div>
                                <div class="flex-1 md-6 relative">
                                    <div class="form-group mb-md-4 mb-3">
                                        {{ Form::label('candidateConfirmPassword', __('web.common.confirm_password'), ['class' => 'fs-16 text-secondary mb-3']) }}
                                        <span class="text-red-600">*</span>
                                        {{ Form::password('password_confirmation', [
                                            'class' => 'block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm fs-14 text-gray bg-white br-10 p-3',
                                            'id' => 'candidateConfirmPassword',
                                            'placeholder' => __('web.register_menu.confirm_password'),
                                            'required',
                                            'onkeypress' => 'return avoidSpace(event)'
                                        ]) }}
                                        <span class="absolute flex items-center top-0 mt-7 bottom-0 end-0 me-6 input-icon input-password-hide cursor-pointer text-gray-600 change-type change-type-register">
                                            <i class="fas fa-eye-slash"></i>
                                        </span>
                                    </div>
                                </div>
                            </div>
                            <div class="flex-1 -12 mb-4">
                                <div class="flex items-center">
                                    {{ Form::checkbox('privacyPolicy', '1', null, ['class' => 'form-check-input', 'id' => 'remember']) }}
                                    <label class="flex items-center label" for="remember">
                                        @lang('messages.by_signing_up_you_agree_to_our')
                                        <a href="{{ route('terms.conditions.list') }}" target="_blank"
                                            class="text-primary-600">{{ __('messages.setting.terms_conditions') }}</a>
                                        &
                                        <a href="{{ route('privacy.policy.list') }}" target="_blank"
                                            class="text-primary-600">{{ __('messages.setting.privacy_policy') }}</a>.
                                    </label>
                                </div>
                            </div>
                            @if ($isGoogleReCaptchaEnabled)
                                <div class="flex-1 -12">
                                    <div class="form-group mt10">
                                        <div class="g-recaptcha flex justify-start"
                                            id="gRecaptchaContainerCompanyRegistration"
                                            data-sitekey="{{ config('app.google_recaptcha_site_key') }}"></div>
                                        <div id="g-recaptcha-error"></div>
                                    </div>
                                </div>
                            @endif
                            <div class="flex-1 -12 d-grid my-4">
                                {{ Form::button(__('web.register_menu.create_account'), [
                                    'type' => 'submit',
                                    'class' => 'rounded-md border border-gray-300 bg-white px-4 py-2 text-sm font-semibold text-gray-700 hover:bg-gray-50 focus:outline-none transition-colors btn-secondary-login',
                                    'id' => 'btnCandidateSave',
                                    'data-loading-text' =>"<span class="spinner-border spinner-border-sm"></span>".__('messages.common.process')
                                ]) }}
                            </div>
                            @php
                                $envSetting = getEnvSetting();
                            @endphp
                            <div class="flex-1 -12">
                                <div class="d-grid">
                                    @if (
                                        !empty($envSetting['facebook_app_id'] || config('services.facebook.client_id')) &&
                                            !empty($envSetting['facebook_app_secret'] || config('services.facebook.client_secret')) &&
                                            !empty($envSetting['facebook_redirect'] || config('services.facebook.redirect')))
                                        <a href="{{ url('/login/facebook?type=1') }}"
                                            class="inline-flex items-center px-4 py-2 border border-gray-300 border-transparent text-sm font-medium rounded-md transition duration-150 ease-in-out facebook- px-4 py-2 rounded font-medium transition-colors flex items-center justify-center mb-3"><i
                                                class="fa-brands fa-facebook-f fs-5 me-3"></i>{{ __('web.login_menu.login_via_facebook') }}
                                        </a>
                                    @endif
                                    @if (
                                        !empty($envSetting['google_client_id'] || config('services.google.client_id')) &&
                                            !empty($envSetting['google_client_secret'] || config('services.google.client_secret')) &&
                                            !empty($envSetting['google_redirect'] || config('services.google.redirect')))
                                        <a href="{{ url('/login/google?type=1') }}"
                                            class="inline-flex items-center px-4 py-2 border border-gray-300 border-transparent text-sm font-medium rounded-md transition duration-150 ease-in-out google- px-4 py-2 rounded font-medium transition-colors flex items-center justify-center mb-3"><i
                                                class="fa-brands fa-google fs-5 me-3"></i>{{ __('web.login_menu.login_via_google') }}
                                        </a>
                                    @endif
                                    @if (
                                        !empty($envSetting['linkedin_client_id'] || config('services.linkedin.client_id')) &&
                                            !empty($envSetting['linkedin_client_secret'] || config('services.linkedin.client_secret')) &&
                                            !empty(config('services.linkedin.redirect')))
                                        <a href="{{ url('/login/linkedin?type=1') }}"
                                            class="inline-flex items-center px-4 py-2 border border-gray-300 border-transparent text-sm font-medium rounded-md transition duration-150 ease-in-out linkedin- px-4 py-2 rounded font-medium transition-colors flex items-center justify-center"><i
                                                class="fa-brands fa-linkedin-in fs-5 me-3"></i>{{ __('web.login_menu.login_via_linkedin') }}
                                        </a>
                                    @endif
                                </div>
                            </div>
                        @formClose()
                    </div>
                </div>
            </div>
        </section>
        <!-- end candidate login section -->
    </div>
    {{ Form::hidden('isGoogleReCaptchaEnabled', (bool) $isGoogleReCaptchaEnabled, ['id' => 'isGoogleReCaptchaEnabled']) }}
@endsection

{{ -- @section('page_scripts') -- }}
{{ --    <script> -- }}
{{ --        let registerSaveUrl ="{{ route('front.save.register') }}"; --}}
{{ --        let candidateLogInUrl ="{{ route('front.candidate.login') }}"; --}}
{{ --        let isGoogleReCaptchaEnabled ="{{ (boolean)$isGoogleReCaptchaEnabled }}"; --}}

{{ --    </script> -- }}
{{ --    @if ($isGoogleReCaptchaEnabled) -- }}
{{ --        {{-- CDN JS removed - now using local assets -- }} --}}
{{ --        <script src="{{asset('assets/js/front_register/google-recaptcha.js') }}"></script> --}}
{{ --    @endif -- }}
{{ -- @endsection -- }}
