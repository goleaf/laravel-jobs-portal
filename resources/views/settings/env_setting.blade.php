@extends('settings.index')
@section('title')
    {{ __('messages.env') }}
@endsection
@section('section')
    {{ Form::open(['route' => 'settings.update', 'id' => 'envUpdateForm']) }}
    {{ Form::hidden('sectionName', $sectionName) }}
    <div class="flex flex-wrap mt-3">
        {{ --        <div class="flex-1 md-12 flex justify-end">-- }}
        {{ --            <label class="custom-switch mt-2">-- }}
        {{ --                <input type="checkbox" name="custom-switch-checkbox" class="custom-switch-input flex items-center input" id="enableEdit">-- }}
        {{ --                <span class="custom-switch-indicator"></span>-- }}
        {{ --                <span class="custom-switch-description fs-6 fw-bolder text-gray-700 mb-3 mt-5"-- }}
        {{ --                      id="envUpdateText">{{ __('messages.setting.enable_edit') }}</span>--}}
        {{ --            </label>-- }}
        {{ --        </div>-- }}
        <div class="flex-1 sm-6 mb-5">
            {{ Form::label('status', __('messages.setting.enable_edit'), ['class' => 'block text-sm font-medium text-gray-700 mb-1 mt-5']) }}
            <div class="flex items-center form-switch">
                <input class="flex items-center input" name="custom-switch-checkbox" id="enableEdit"
                       type="checkbox">
                <span class="custom-switch-indicator"></span>
            </div>
        </div>
        <div class="flex-1 sm-12 my-0">
            <div class="bg-white shadow rounded-lg overflow-hidden">
                <h5 class="mt-5">{{ __('messages.setting.facebook') }} :</h5>
                <div class="flex flex-wrap">
                    <div class="flex-1 sm-6">
                        {{ Form::label('facebook_app_id', __('messages.setting.facebook_app_id').':', ['class' => 'block text-sm font-medium text-gray-700 mb-1 mt-5']) }}
                        {{ Form::text('facebook_app_id', (empty($envSetting['facebook_app_id'])) ? ($facebook['FACEBOOK_APP_ID'] ?? null) : $envSetting['facebook_app_id'], ['class' => 'block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm', 'disabled', 'placeholder' => __('messages.setting.facebook_app_id')]) }}
                    </div>
                    <div class="flex-1 sm-6">
                        {{ Form::label('facebook_app_secret', __('messages.setting.facebook_app_secret').':', ['class' => 'block text-sm font-medium text-gray-700 mb-1 mt-5']) }}
                        {{ Form::text('facebook_app_secret', (empty($envSetting['facebook_app_secret'])) ? ($facebook['FACEBOOK_APP_SECRET'] ?? null) : $envSetting['facebook_app_secret'], ['class' => 'block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm', 'disabled', 'placeholder' => __('messages.setting.facebook_app_secret')]) }}
                    </div>
                    <div class="flex-1 sm-6">
                        {{ Form::label('facebook_redirect', __('messages.setting.facebook_redirect').':', ['class' => 'block text-sm font-medium text-gray-700 mb-1 mt-5']) }}
                        {{ Form::text('facebook_redirect',(empty($envSetting['facebook_redirect'])) ? ($facebook['FACEBOOK_REDIRECT'] ?? null) : $envSetting['facebook_redirect'], ['class' => 'block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm', 'disabled', 'placeholder' => __('messages.setting.facebook_redirect')]) }}
                    </div>
                </div>
            </div>
            <div class="bg-white shadow rounded-lg overflow-hidden">
                <h5 class="mt-5">{{ __('messages.setting.pusher') }} :</h5>
                <div class="flex flex-wrap">
                    <div class="flex-1 sm-6">
                        {{ Form::label('pusher_app_id', __('messages.setting.pusher_app_id').':', ['class' => 'block text-sm font-medium text-gray-700 mb-1 mt-5']) }}
                        {{ Form::text('pusher_app_id',(empty($envSetting['pusher_app_id'])) ? ($pusher['PUSHER_APP_ID'] ?? null) : $envSetting['pusher_app_id'] , ['class' => 'block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm', 'disabled', 'placeholder' => __('messages.setting.pusher_app_id')]) }}
                    </div>
                    <div class="flex-1 sm-6">
                        {{ Form::label('pusher_app_key', __('messages.setting.pusher_app_key').':', ['class' => 'block text-sm font-medium text-gray-700 mb-1 mt-5']) }}
                        {{ Form::text('pusher_app_key', (empty($envSetting['pusher_app_key'])) ? ($pusher['PUSHER_APP_KEY'] ?? null) : $envSetting['pusher_app_key'] , ['class' => 'block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm', 'disabled', 'placeholder' => __('messages.setting.pusher_app_key')]) }}
                    </div>
                    <div class="flex-1 sm-6">
                        {{ Form::label('pusher_app_secret', __('messages.setting.pusher_app_secret').':', ['class' => 'block text-sm font-medium text-gray-700 mb-1 mt-5']) }}
                        {{ Form::text('pusher_app_secret',(empty($envSetting['pusher_app_secret'])) ? ($pusher['PUSHER_APP_SECRET'] ?? null) : $envSetting['pusher_app_secret'], ['class' => 'block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm', 'disabled', 'placeholder' => __('messages.setting.pusher_app_secret')]) }}
                    </div>
                    <div class="flex-1 sm-6">
                        {{ Form::label('pusher_app_cluster', __('messages.setting.pusher_app_cluster').':', ['class' => 'block text-sm font-medium text-gray-700 mb-1 mt-5']) }}
                        {{ Form::text('pusher_app_cluster',(empty($envSetting['pusher_app_cluster'])) ? ($pusher['PUSHER_APP_CLUSTER'] ?? null) : $envSetting['pusher_app_cluster'], ['class' => 'block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm', 'disabled', 'placeholder' => __('messages.setting.pusher_app_cluster')]) }}
                    </div>
                </div>
            </div>
            <div class="bg-white shadow rounded-lg overflow-hidden">
                <h5 class="mt-5">{{ __('messages.setting.stripe') }} :</h5>
                <div class="flex flex-wrap">
                    <div class="flex-1 sm-6">
                        {{ Form::label('stripe_key', __('messages.setting.stripe_key').':', ['class' => 'block text-sm font-medium text-gray-700 mb-1 mt-5']) }}
                        {{ Form::text('stripe_key',(empty($envSetting['stripe_key'])) ? ($stripe['STRIPE_KEY'] ?? null) : $envSetting['stripe_key'] , ['class' => 'block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm', 'disabled', 'placeholder' => __('messages.setting.stripe_key')]) }}
                    </div>
                    <div class="flex-1 sm-6">
                        {{ Form::label('stripe_secret', __('messages.setting.stripe_secret_key').':', ['class' => 'block text-sm font-medium text-gray-700 mb-1 mt-5']) }}
                        {{ Form::text('stripe_secret',(empty($envSetting['stripe_secret'])) ? ($stripe['STRIPE_SECRET'] ?? null) : $envSetting['stripe_secret'], ['class' => 'block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm', 'disabled', 'placeholder' => __('messages.setting.stripe_secret_key')]) }}
                    </div>
                    <div class="flex-1 sm-6">
                        {{ Form::label('stripe_webhook_key', __('messages.setting.stripe_webhook_key').':', ['class' => 'block text-sm font-medium text-gray-700 mb-1 mt-5']) }}
                        {{ Form::text('stripe_webhook_key',(empty($envSetting['stripe_webhook_key'])) ? ($stripe['STRIPE_WEBHOOK_SECRET_KEY'] ?? null) : $envSetting['stripe_webhook_key'], ['class' => 'block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm', 'disabled', 'placeholder' => __('messages.setting.stripe_webhook_key')]) }}
                    </div>
                </div>
            </div>
            <div class="bg-white shadow rounded-lg overflow-hidden">
                <h5 class="mt-5">{{ __('messages.setting.paypal') }} :</h5>
                <div class="flex flex-wrap">
                    <div class="flex-1 sm-6">
                        {{ Form::label('paypal_client_id', __('messages.setting.paypal_client_id').':', ['class' => 'block text-sm font-medium text-gray-700 mb-1 mt-5']) }}
                        {{ Form::text('paypal_client_id',(empty($envSetting['paypal_client_id'])) ? ($paypal['PAYPAL_CLIENT_ID'] ?? null) : $envSetting['paypal_client_id'] , ['class' => 'block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm', 'disabled', 'placeholder' => __('messages.setting.paypal_client_id')]) }}
                    </div>
                    <div class="flex-1 sm-6">
                        {{ Form::label('paypal_secret', __('messages.setting.paypal_secret').':', ['class' => 'block text-sm font-medium text-gray-700 mb-1 mt-5']) }}
                        {{ Form::text('paypal_secret',(empty($envSetting['paypal_secret'])) ? ($paypal['PAYPAL_SECRET'] ?? null) : $envSetting['paypal_secret'] , ['class' => 'block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm', 'disabled', 'placeholder' => __('messages.setting.paypal_secret')]) }}
                    </div>
                </div>
                <div class="flex flex-wrap">
                  <h5 class="mt-5"> {{ __('messages.setting.paystack') }}</h5>
                  <div class="flex-1 sm-6">
                      {{ Form::label('paystack_key', __('messages.setting.paystack_key').':', ['class' => 'block text-sm font-medium text-gray-700 mb-1 mt-5']) }}
                      {{ Form::text('paystack_key',(empty($envSetting['paystack_key'])) ? ($paystack['PAYSTACK_PUBLIC_KEY'] ?? null) : $envSetting['paystack_key'] , ['class' => 'block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm', 'disabled', 'placeholder' => __('messages.setting.paystack_key')]) }}
                  </div>
                  <div class="flex-1 sm-6">
                      {{ Form::label('paystack_secret', __('messages.setting.paystack_secret').':', ['class' => 'block text-sm font-medium text-gray-700 mb-1 mt-5']) }}
                      {{ Form::text('paystack_secret',(empty($envSetting['paystack_secret'])) ? ($paystack['PAYSTACK_SECRET_KEY'] ?? null) : $envSetting['paystack_secret'] , ['class' => 'block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm', 'disabled', 'placeholder' => __('messages.setting.paystack_secret')]) }}
                  </div>
                  <div class="flex-1 sm-6">
                           {{ Form::label('paystack_payment_url', __('messages.setting.paystack_payment_url').':', ['class' => 'block text-sm font-medium text-gray-700 mb-1 mt-5']) }}
                           {{ Form::text('paystack_payment_url',(empty($envSetting['paystack_payment_url'])) ? ($paystack['PAYSTACK_PAYMENT_URL'] ?? null) : $envSetting['paystack_payment_url'] , ['class' => 'block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm', 'disabled', 'placeholder' => __('messages.setting.paystack_payment_url')]) }}
                       </div>
              </div>
            </div>
            <div class="bg-white shadow rounded-lg overflow-hidden">
                <h5 class="mt-5">{{ __('messages.setting.linkedin') }} :</h5>
                <div class="flex flex-wrap">
                    <div class="flex-1 sm-6">
                        {{ Form::label('linkedin_client_id', __('messages.setting.linkedin_client_id').':', ['class' => 'block text-sm font-medium text-gray-700 mb-1 mt-5']) }}
                        {{ Form::text('linkedin_client_id',(empty($envSetting['linkedin_client_id'])) ? ($linkedIn['LINKEDIN_CLIENT_ID'] ?? null) : $envSetting['linkedin_client_id'], ['class' => 'block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm', 'disabled', 'placeholder' => __('messages.setting.linkedin_client_id')]) }}
                    </div>
                    <div class="flex-1 sm-6">
                        {{ Form::label('linkedin_client_secret', __('messages.setting.linkedin_client_secret').':', ['class' => 'block text-sm font-medium text-gray-700 mb-1 mt-5']) }}

                        {{ Form::text('linkedin_client_secret',(empty($envSetting['linkedin_client_secret'])) ? ($linkedIn['LINKEDIN_CLIENT_SECRET'] ?? null) : $envSetting['linkedin_client_secret'] , ['class' => 'block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm', 'disabled', 'placeholder' => __('messages.setting.linkedin_client_secret')]) }}
                    </div>
                </div>
            </div>
            <div class="bg-white shadow rounded-lg overflow-hidden">
                <h5 class="mt-5">{{ __('messages.setting.google') }} :</h5>
                <div class="flex flex-wrap">
                    <div class="flex-1 sm-6">
                        {{ Form::label('google_client_id', __('messages.setting.google_client_id').':', ['class' => 'block text-sm font-medium text-gray-700 mb-1 mt-5']) }}
                        {{ Form::text('google_client_id',(empty($envSetting['google_client_id'])) ? ($google['GOOGLE_CLIENT_ID'] ?? null) : $envSetting['google_client_id'], ['class' => 'block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm', 'disabled', 'placeholder' => __('messages.setting.google_client_id')]) }}
                    </div>
                    <div class="flex-1 sm-6">
                        {{ Form::label('google_client_secret', __('messages.setting.google_client_secret').':', ['class' => 'block text-sm font-medium text-gray-700 mb-1 mt-5']) }}
                        {{ Form::text('google_client_secret',(empty($envSetting['google_client_secret'])) ? ($google['GOOGLE_CLIENT_SECRET'] ?? null) : $envSetting['google_client_secret'], ['class' => 'block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm', 'disabled', 'placeholder' => __('messages.setting.google_client_secret')]) }}
                    </div>
                    <div class="flex-1 sm-6">
                        {{ Form::label('google_redirect', __('messages.setting.google_redirect').':', ['class' => 'block text-sm font-medium text-gray-700 mb-1 mt-5']) }}
                        {{ Form::text('google_redirect',(empty($envSetting['google_redirect'])) ? ($google['GOOGLE_REDIRECT'] ?? null) : $envSetting['google_redirect'] , ['class' => 'block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm', 'disabled', 'placeholder' => __('messages.setting.google_redirect')]) }}
                    </div>
                </div>
            </div>
            <div class="bg-white shadow rounded-lg overflow-hidden">
                <h5 class="mt-5">{{ __('messages.setting.cookie') }} :</h5>
                <div class="flex flex-wrap">
                    <div class="flex-1 sm-6">
                        <label class="mt-2 pl-0 flex items-center form-switch">
                            {{ --                            <input type="checkbox" name="cookie_consent_enabled" class="custom-switch-input flex items-center input"-- }}
                            {{ --                                   id="enableCookie"-- }}
                            {{ --                                   {{ (!empty($cookie['COOKIE_CONSENT_ENABLED']) && filter_var($cookie['COOKIE_CONSENT_ENABLED'], FILTER_VALIDATE_BOOLEAN)) ? 'checked' : '' }} disabled>--}}
                            {{ --                            -- }}
                            <input class="flex items-center input mr-5" name="cookie_consent_enabled"
                                   id="enableCookie" type="checkbox"
                                   {{ isset($envSetting['cookie_consent_enabled']) && $envSetting['cookie_consent_enabled'] == true ? 'checked' : '' }} disabled>
                            <span class=""></span>
                            <span class="fw-bolder text-gray-700 ms-3"
                                  id="enableCookieText">
                                @if(!empty($envSetting['cookie_consent_enabled']))
                                    {{ __('messages.setting.disable_cookie') }}
                                @else
                                    {{ __('messages.setting.enable_cookie') }}
                                @endif
                            </span>
                        </label>
                    </div>
                </div>
            </div>
            <div class="flex justify-end mb-5 mt-5">
                {{ Form::button(__('messages.common.save'), ['type' => 'submit','class' => 'rounded-md bg-indigo-600 px-4 py-2 text-sm font-semibold text-white hover:bg-indigo-700 focus:outline-none transition-colors me-3','id' => 'btnSaveEnvData', 'disabled']) }}
                <a href="{{ route('admin.dashboard', ['section' => 'env_setting']) }}"
                   class="inline-flex items-center px-4 py-2 border border-gray-300 border-transparent text-sm font-medium rounded-md transition duration-150 ease-in-out px-4 py-2 rounded font-medium transition-colors secondary me-2">{{ __('messages.common.cancel') }}</a>
            </div>
        </div>
    {{ Form::close() }}
@endsection
