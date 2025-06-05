@extends('front_web_template.layouts.app')
@section('title')
    {{ __('web.login') }}
@endsection
@section('content')
    <div class="login-page">
        <!-- start hero section -->
        <section class="hero-section relative bg-gradient pt-15 pb-40">
            <div class="container mx-auto px-4 mx-auto px-4 mx-auto px-4 mx-auto">
              <div class="flex flex-wrap items-center justify-center">
                <div class="flex-1 lg-6 text-center mb-lg-0 mb-md-5 mb-sm-4">
                  <div class="hero-content">
                    <h1 class="text-gray-600 mb-3">
                        {{ __('web.register_menu.candidate') . ' ' . __('web.login') }}
                    </h1>
                    <nav aria-label="breadcrumb">
                      <ol class="breadcrumb justify-center mb-0">
                        <li class="breadcrumb-item"><a href="{{ route('front.home') }}" class="fs-18 text-gray">@lang('web.home') </a>
                        </li>
                        <li class="breadcrumb-item text-indigo-600-600 fs-18" aria-current="page">@lang('web.login')</li>
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
            <div class="container mx-auto px-4 mx-auto px-4 mx-auto px-4 mx-auto">
                <div class="flex flex-wrap">
                    <div class="flex-1 -xl-6 flex-1 lg-8 mx-auto">
                        @include('flash::message')
                        <form method="POST" action="{{ route('front.') }}" id="candidateForm"
                            class="py-40 px-40 bg-gray">
                            <div class="flex flex-wrap">
                                <div class="flex-1 -12 mb-4">
                                    <div class="mb-4 flex flex-wrap">
                                        <div class="flex-1 -sm-6 flex-1 -12 mb-3 mb-sm-0">
                                            <a href="{{ route('candidate.') }}" class="border border-gray-300 bg-transparent">
                                                {{ __('web.register_menu.candidate') }} </a>
                                        </div>
                                        <div class="flex-1 -sm-6 flex-1 -12">
                                            <a href="{{ route('front.') }}"
                                                class="border border-gray-300 bg-transparent">
                                                {{ __('web.register_menu.employer') }} </a>
                                        </div>
                                    </div>
                                </div>
                                @csrf
                                <div id="candidateValidationErrBox">
                                    @include('layouts.errors')
                                </div>
                                <input type="hidden" name="type" value="1" />
                                <div class="flex-1 md-12 mb-4">
                                    <div class="mb-4">
                                        <label for="" class="fs-16 text-gray-600 mb-3">{{ __('web.common.email') }}
                                            <span class="text-red-600">*</span>
                                        </label>
                                        <input type="email" class="w-full px-3 py-2 border border-gray-300 border border border-gray-300 -gray-300 -gray-300 rounded -md focus:outline-none focus:ring-2 focus:ring-primary-500 fs-14 text-gray bg-white br-10 p-3" name="email"
                                               id="email"
                                               value="{{ (Cookie::get('email') !== null) ? Cookie::get('email') : '' }}"
                                               autofocus placeholder="@lang('web.login_menu.enter_your_email')" required>
                                    </div>
                                </div>

                                <div class="flex-1 md-12 relative">
                                    <div class="mb-4 mb-md-4 mb-3">
                                        <label for=""
                                            class="fs-16 text-gray-600 mb-3">{{ __('web.common.password') }}
                                            <span class="text-red-600">*</span></label>
                                        <input type="password" class="w-full px-3 py-2 border border-gray-300 border border border-gray-300 -gray-300 -gray-300 rounded -md focus:outline-none focus:ring-2 focus:ring-primary-500 fs-14 text-gray bg-white br-10 p-3"
                                            name="password" id="password" placeholder="@lang('web.login_menu.your_passowrd')"
                                            value="{{ Cookie::get('password') !== null ? Cookie::get('password') : '' }}"
                                            required>
                                            <span class="absolute flex items-center top-0 mt-7 bottom-0 end-0 me-6 input-icon input-password-hide cursor-pointer text-gray-600 change-type">
                                                <i class="fas fa-eye-slash"></i>
                                            </span>
                                    </div>
                                </div>
                            </div>
                            <div class="flex-1 -12">
                                <div class="flex items-center justify-between flex-wrap">
                                    <div class="flex items-center">
                                        <input type="checkbox" name="remember" class="flex items-center input" id="remember"
                                            {{ Cookie::get('remember') !== null ? 'checked' : '' }}>
                                        <label class="flex items-center label" for="remember">
                                            {{ __('web.login_menu.remember_me') }}
                                        </label>
                                    </div>
                                    <a href="{{ route('password.request') }}"
                                        class="text-indigo-600-600" data-turbo="false">{{ __('web.login_menu.forget_password') }}</a>
                                </div>
                            </div>
                            <div class="flex-1 -12 d-grid my-4">
                                <button type="submit" class="border border-gray-300 bg-transparent"
                                    data-turbo="false">{{ __('web.login') }}</button>
                            </div>
                            @php
                                $envSetting = getEnvSetting();
                            @endphp
                            <div class="flex-1 -12">
                                <div class="mb-3">{{ __('web.login_menu.don\'t_have_an_account') }} <a
                                        href="{{ route('employer.register') }}"
                                        class="text-indigo-600 -600">{{ __('web.sign_up') }}</a></div>
                                <div class="d-grid">
                                    @if (
                                        !empty($envSetting['facebook_app_id'] || config('services.facebook.client_id')) &&
                                            !empty($envSetting['facebook_app_secret'] || config('services.facebook.client_secret')) &&
                                            !empty($envSetting['facebook_redirect'] || config('services.facebook.redirect')))
                                        <a href="{{ url('/login/facebook?type=1') }}"
                                            class="border border-gray-300 bg-transparent"><i
                                                class="fa-brands fa-facebook-f fs-5 me-3"></i>{{ __('web.login_menu.login_via_facebook') }}
                                        </a>
                                    @endif
                                    @if (
                                        !empty($envSetting['google_client_id'] || config('services.google.client_id')) &&
                                            !empty($envSetting['google_client_secret'] || config('services.google.client_secret')) &&
                                            !empty($envSetting['google_redirect'] || config('services.google.redirect')))
                                        <a href="{{ url('/login/google?type=1') }}"
                                            class="border border-gray-300 bg-transparent"><i
                                                class="fa-brands fa-google fs-5 me-3"></i>{{ __('web.login_menu.login_via_google') }}
                                        </a>
                                    @endif
                                    @if (
                                        !empty($envSetting['linkedin_client_id'] || config('services.linkedin.client_id')) &&
                                            !empty($envSetting['linkedin_client_secret'] || config('services.linkedin.client_secret')) &&
                                            !empty(config('services.linkedin.redirect')))
                                        <a href="{{ url('/login/linkedin?type=1') }}"
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
@endsection


{{-- @section('page_scripts') --}}
{{--  --}}
{{-- <script src="{{asset('assets/js/auto_fill/auto_fill.js') }}"></script> --}}
{{-- @endsection --}}

@push('scripts')
    @vite('resources/js/components/candidate_login.js')
@endpush
