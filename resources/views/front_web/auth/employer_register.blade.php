@extends('front_web.layouts.app')
@section('title')
    {{ __('web.register') }}
@endsection
@section('content')
    <div class="register-page">
        <!-- start hero section -->
        <section class="hero-section relative bg-color-light py-40">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mx-auto px-4 mx-auto px-4 mx-auto">
                <div class="flex flex-wrap items-center justify-center">
                    <div class="flex-1 lg-6 text-center mb-lg-0 mb-md-5 mb-sm-4">
                        <div class="hero-content">
                            <h1 class="text-gray-600 mb-3">
                                {{ __('web.register_menu.employer').' '.__('web.register') }}
                            </h1>
                            <nav aria-label="breadcrumb">
                                <ol class="breadcrumb justify-center mb-0">
                                    <li class="breadcrumb-item">
                                        <a href="{{ route('front.home') }}" class="fs-18 text-gray">
                                            @lang('web.home')
                                        </a>
                                    </li>
                                    <li class="breadcrumb-item text-indigo-600-600 fs-18" aria-current="page">@lang('web.register')</li>
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
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mx-auto px-4 mx-auto px-4 mx-auto">
                <div class="flex flex-wrap">
                    <div class="flex-1 -xl-6 flex-1 lg-8 mx-auto">
                        @include('flash::message')
                        <form method="POST" action="{{ route('front.home') }}" id="addEmployerNewForm"
                              class="py-40 px-40 bg-gray">
                            <div class="flex flex-wrap">
                                <div class="flex-1 -12 mb-4">
                                    <div class="mb-4 flex flex-wrap">
                                        <div class="flex-1 -sm-6 flex-1 -12 mb-3 mb-sm-0">
                                            <a href="{{ route('candidate.register') }}"
                                               class="border border-gray-300 bg-transparent">
                                                {{ __('web.register_menu.candidate') }} </a>
                                        </div>
                                        <div class="flex-1 -sm-6 flex-1 -12">
                                            <a href="{{ route('employer.register') }}"
                                               class="border border-gray-300 bg-transparent">
                                                {{ __('web.register_menu.employer') }} </a>
                                        </div>
                                    </div>
                                </div>
                                @csrf
                                <div id="candidateValidationErrBox">
                                    @include('layouts.errors')
                                </div>
                                <input type="hidden" name="type" value="2"/>
                                <div class="flex-1 md-6 mb-4">
                                    <div class="mb-4">
                                        <label for="" class="fs-16 text-gray-600 mb-2">{{ __('web.common.name') }}
                                            <span class="text-indigo-600 -600">*</span>
                                        </label>
                                        <input type="text" class="w-full px-3 py-2 border border-gray-300 border border border-gray-300 -gray-300 -gray-300 rounded -md focus:outline-none focus:ring-2 focus:ring-primary-500 fs-14 text-gray br-10" name="first_name"
                                               id="employerFirstName" placeholder="{{ __('messages.enter_first_name') }}"
                                               required>
                                    </div>
                                </div>
                                <div class="flex-1 md-6 mb-4">
                                    <div class="mb-4">
                                        <label for="" class="fs-16 text-gray-600 mb-2">{{ __('web.common.email') }}
                                            <span class="text-indigo-600 -600">*</span>
                                        </label>
                                        <input type="email" class="w-full px-3 py-2 border border-gray-300 border border border-gray-300 -gray-300 -gray-300 rounded -md focus:outline-none focus:ring-2 focus:ring-primary-500 fs-14 text-gray br-10" name="email"
                                               id="employerEmail" placeholder="{{ __('messages.email_address') }}"
                                               required>
                                    </div>
                                </div>
                                <div class="flex-1 md-6 mb-4">
                                    <div class="mb-4">
                                        <label for="" class="fs-16 text-gray-600 mb-2">{{ __('web.common.password') }}
                                            <span class="text-red-600">*</span></label>
                                        <input type="password" name="password" placeholder="{{ __('messages.password') }}"
                                               class="w-full px-3 py-2 border border-gray-300 border border border-gray-300 -gray-300 -gray-300 rounded -md focus:outline-none focus:ring-2 focus:ring-primary-500 fs-14 text-gray br-10" id="employerPassword"
                                               required onkeypress="return avoidSpace(event)">
                                    </div>
                                </div>
                                <div class="flex-1 md-6 mb-4">
                                    <div class="mb-4">
                                        <label for="" class="fs-16 text-gray-600 mb-2">{{ __('web.common.confirm_password') }}
                                            <span class="text-red-600">*</span></label>
                                        <input type="password" name="password_confirmation"
                                               placeholder="{{ __('messages.company.confirm_password') }}"
                                               class="w-full px-3 py-2 border border-gray-300 border border border-gray-300 -gray-300 -gray-300 rounded -md focus:outline-none focus:ring-2 focus:ring-primary-500 fs-14 text-gray br-10" id="employerConfirmPassword"
                                               required onkeypress="return avoidSpace(event)">
                                    </div>
                                </div>
                            </div>
                            <div class="flex-1 -12 mb-4">
                                <div class="flex items-center">
                                    <input type="checkbox" name="privacyPolicy" class="flex items-center input" id="remember">
                                    <label class="flex items-center label" for="remember">
                                        @lang('messages.by_signing_up_you_agree_to_our')
                                        <a href="{{ route('terms.conditions.list.index') }}"
                                           target="_blank">{{ __('messages.setting.terms_conditions') }}</a>
                                        &
                                        <a href="{{ route('privacy.policy.list.index') }}"
                                           target="_blank">{{ __('messages.setting.privacy_policy') }}</a>.
                                    </label>
                                </div>
                            </div>
                            @if($isGoogleReCaptchaEnabled)
                                <div class="flex-1 -12">
                                    <div class="mb-4 mt10">
                                        <div class="g-recaptcha flex justify-center" id="gRecaptchaContainerCompanyRegistration"
                                             data-sitekey="{{ config('app.google_recaptcha_site_key') }}"></div>
                                        <div id="g-recaptcha-error"></div>
                                    </div>
                                </div>
                            @endif
                            <div class="flex-1 -12 d-grid my-4">
                                <button type="submit" class="border border-gray-300 bg-transparent" id="btnEmployerSave" data-loading-text="<span class="animate-spin h-5 w-5 border-2 border-current border-t-transparent rounded -full spinner- border border border-gray-300 -gray-300 -sm"></span> {{ __('messages.common.process') }}">
                                    {{ __('web.register_menu.create_account') }}</button>
                            </div>
                            @php
                            $envSetting = getEnvSetting();
                            @endphp
                            <div class="flex-1 -12">
                                <div class="d-grid">
                                    @if(!empty(($envSetting['facebook_app_id']) || config('services.facebook.client_id')) && !empty(($envSetting['facebook_app_secret']) || config('services.facebook.client_secret')) && !empty(($envSetting['facebook_redirect']) || config('services.facebook.redirect')) )
                                    <a href="{{ url('/login/facebook?type=2') }}"
                                       class="border border-gray-300 bg-transparent"><i
                                                class="fa-brands fa-facebook-f fs-5 me-3"></i>{{ __('web.login_menu.login_via_facebook') }}
                                    </a>
                                    @endif
                                    @if(!empty(($envSetting['google_client_id']) || config('services.google.client_id')) && !empty(($envSetting['google_client_secret']) || config('services.google.client_secret')) && !empty(($envSetting['google_redirect']) || config('services.google.redirect')) )
                                    <a href="{{ url('/login/google?type=2') }}"
                                       class="border border-gray-300 bg-transparent"><i
                                                class="fa-brands fa-google fs-5 me-3"></i>{{ __('web.login_menu.login_via_google') }}
                                    </a>
                                    @endif
                                    @if(!empty(($envSetting['linkedin_client_id']) || config('services.linkedin.client_id')) && !empty(($envSetting['linkedin_client_secret']) || config('services.linkedin.client_secret')) && !empty(config('services.linkedin.redirect')) )
                                    <a href="{{ url('/login/linkedin?type=2') }}"
                                       class="border border-gray-300 bg-transparent"><i
                                                class="fa-brands fa-linkedin-in fs-5 me-3"></i>{{ __('web.login_menu.login_via_linkedin') }}
                                    </a>
                                    @endif
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </section>
        <!-- end candidate login section -->
    </div>
    {{ Form::hidden('isGoogleReCaptchaEnabled', (boolean)$isGoogleReCaptchaEnabled,['id' => 'isGoogleReCaptchaEnabled']) }}
@endsection

{{-- @section('page_scripts') --}}
{{--  --}}
{{-- @if($isGoogleReCaptchaEnabled) --}}
{{ -- -- CDN JS removed - now using local assets ---- }}
{{ -- <script src="asset('assets/js/front_register/google-recaptcha.js') "></script> -- }}
{{-- @endif --}}
{{-- @endsection --}}

@push('scripts')
    @vite('resources/js/components/employer_register.js')
@endpush
