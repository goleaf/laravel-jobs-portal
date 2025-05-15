                    <div class="col-xl-6 col-lg-8 mx-auto">
                        @include('flash::message')
                        @formOpen(['id' => 'loginForm', 'class' => 'py-60 px-md-40 px-sm-20 px-30 bg-gray', 'method' => 'POST'])
                            @csrf
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group mb-md-4 mb-3 ">
                                        {{ Form::label('email', __('web.common.email'), ['class' => 'fs-16 text-secondary mb-3']) }}
                                        <span class="text-danger">*</span>
                                        {{ Form::email('email', null, [
                                            'class' => 'form-control fs-14 text-gray bg-white br-10 p-3',
                                            'id' => 'email',
                                            'placeholder' => __('web.login_menu.enter_email_address'),
                                            'required'
                                        ]) }}
                                    </div>
                                </div>
                                <div class="col-md-12 mb-3 position-relative">
                                    <div class="d-flex justify-content-between">
                                        {{ Form::label('password', __('web.common.password'), ['class' => 'fs-16 text-secondary mb-3']) }}
                                        <a href="{{ route('password.request') }}"
                                            class="text-primary fs-16 mb-3">{{ __('web.login_menu.forget_password') }}</a>
                                    </div>
                                    <span class="text-danger">*</span>
                                    {{ Form::password('password', [
                                        'class' => 'form-control fs-14 text-gray bg-white br-10 p-3',
                                        'id' => 'password',
                                        'placeholder' => __('web.login_menu.enter_password'),
                                        'required'
                                    ]) }}
                                    <span class="position-absolute d-flex align-items-center top-0 mt-7 bottom-0 end-0 me-4 input-icon input-password-hide cursor-pointer text-gray-600 fs-14 change-type ">
                                        <i class="fas fa-eye-slash"></i>
                                    </span>
                                </div>
                                <div class="col-12 mb-4">
                                    <div class="form-check">
                                        {{ Form::checkbox('remember', '1', null, ['class' => 'form-check-input', 'id' => 'remember']) }}
                                        <label class="form-check-label" for="remember">
                                            {{ __('web.login_menu.remember_me') }}
                                        </label>
                                    </div>
                                </div>
                                @if ($isGoogleReCaptchaEnabled)
                                    <div class="col-12">
                                        <div class="form-group mt10">
                                            <div class="g-recaptcha d-flex justify-content-center" id="gRecaptchaContainerCompanyRegistration"
                                                data-sitekey="{{ config('app.google_recaptcha_site_key') }}"></div>
                                            <div id="g-recaptcha-error"></div>
                                        </div>
                                    </div>
                                @endif
                                <div class="col-12 d-grid my-4">
                                    {{ Form::button(__('web.login_menu.login'), [
                                        'type' => 'submit',
                                        'class' => 'btn btn-secondary btn-secondary-login',
                                        'id' => 'loginBtn',
                                        'data-loading-text' => "<span class='spinner-border spinner-border-sm'></span> ".__('messages.common.process')
                                    ]) }}
                                </div>
                                <div class="col-12 d-flex justify-content-center">
                                    <span class="fs-16 me-1">{{ __('web.login_menu.new_on_website') }}</span><a
                                        href="{{ route('candidate.register') }}"
                                        class="text-primary fs-16">{{ __('web.login_menu.create_an_account') }}</a>
                                </div>
                                @php
                                    $envSetting = getEnvSetting();
                                @endphp
                                <div class="col-12">
                                    <div class="d-grid">
                                        @if (
                                            !empty($envSetting['facebook_app_id'] || config('services.facebook.client_id')) &&
                                                !empty($envSetting['facebook_app_secret'] || config('services.facebook.client_secret')) &&
                                                !empty($envSetting['facebook_redirect'] || config('services.facebook.redirect')))
                                            <a href="{{ url('/login/facebook') }}"
                                                class="btn facebook-btn d-flex align-items-center justify-content-center mb-3"><i
                                                    class="fa-brands fa-facebook-f fs-5 me-3"></i>{{ __('web.login_menu.login_via_facebook') }}
                                            </a>
                                        @endif
                                        @if (
                                            !empty($envSetting['google_client_id'] || config('services.google.client_id')) &&
                                                !empty($envSetting['google_client_secret'] || config('services.google.client_secret')) &&
                                                !empty($envSetting['google_redirect'] || config('services.google.redirect')))
                                            <a href="{{ url('/login/google') }}"
                                                class="btn google-btn d-flex align-items-center justify-content-center mb-3"><i
                                                    class="fa-brands fa-google fs-5 me-3"></i>{{ __('web.login_menu.login_via_google') }}
                                            </a>
                                        @endif
                                        @if (
                                            !empty($envSetting['linkedin_client_id'] || config('services.linkedin.client_id')) &&
                                                !empty($envSetting['linkedin_client_secret'] || config('services.linkedin.client_secret')) &&
                                                !empty(config('services.linkedin.redirect')))
                                            <a href="{{ url('/login/linkedin') }}"
                                                class="btn linkedin-btn d-flex align-items-center justify-content-center"><i
                                                    class="fa-brands fa-linkedin-in fs-5 me-3"></i>{{ __('web.login_menu.login_via_linkedin') }}
                                            </a>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @formClose()
                    </div> 