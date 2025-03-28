@extends('layouts.auth')

@section('title')
    {{ __('web.register') }}
@endsection

@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="mx-auto" style="max-width: 500px;">
                <div class="card">
                    <div class="card-body p-4">
                        <div class="text-center">
                            <h1 class="h4 text-gray-900 mb-4">{{ __('web.register') }}</h1>
                        </div>

                        {{ Aire::open()->route('register')->id('register-form') }}
                            @csrf
                            <div class="mb-3">
                                {{ Aire::input('first_name', __('web.common.first_name'))
                                    ->required()
                                    ->placeholder(__('web.common.first_name'))
                                    ->value(old('first_name'))
                                    ->id('first_name')
                                    ->helpText($errors->first('first_name'))
                                    ->errorClass('border-red-300 focus:border-red-500 focus:ring-red-500')
                                    ->groupErrorClass('text-red-600')
                                }}
                            </div>

                            <div class="mb-3">
                                {{ Aire::input('last_name', __('web.common.last_name'))
                                    ->required()
                                    ->placeholder(__('web.common.last_name'))
                                    ->value(old('last_name'))
                                    ->id('last_name')
                                    ->helpText($errors->first('last_name'))
                                    ->errorClass('border-red-300 focus:border-red-500 focus:ring-red-500')
                                    ->groupErrorClass('text-red-600')
                                }}
                            </div>

                            <div class="mb-3">
                                {{ Aire::email('email', __('web.common.email'))
                                    ->required()
                                    ->placeholder(__('web.common.email'))
                                    ->value(old('email'))
                                    ->id('email')
                                    ->helpText($errors->first('email'))
                                    ->errorClass('border-red-300 focus:border-red-500 focus:ring-red-500')
                                    ->groupErrorClass('text-red-600')
                                }}
                            </div>

                            <div class="mb-3">
                                {{ Aire::password('password', __('web.common.password'))
                                    ->required()
                                    ->placeholder(__('web.common.password'))
                                    ->id('password')
                                    ->helpText($errors->first('password'))
                                    ->errorClass('border-red-300 focus:border-red-500 focus:ring-red-500')
                                    ->groupErrorClass('text-red-600')
                                }}
                            </div>

                            <div class="mb-3">
                                {{ Aire::password('password_confirmation', __('web.common.confirm_password'))
                                    ->required()
                                    ->placeholder(__('web.common.confirm_password'))
                                    ->id('password_confirmation')
                                }}
                            </div>

                            <div class="mb-3">
                                <div class="mb-2">
                                    <label for="user_type" class="block text-sm font-medium text-gray-700">
                                        {{ __('web.common.user_type') }}<span class="text-red-500">*</span>
                                    </label>
                                </div>
                                <div class="flex gap-4">
                                    {{ Aire::radioGroup('user_type')
                                        ->options([
                                            '1' => __('web.common.candidate'),
                                            '2' => __('web.common.employer')
                                        ])
                                        ->value(old('user_type'))
                                        ->helpText($errors->first('user_type'))
                                        ->errorClass('border-red-300 focus:border-red-500 focus:ring-red-500')
                                        ->groupErrorClass('text-red-600')
                                    }}
                                </div>
                            </div>

                            <div class="mb-3">
                                {{ Aire::checkbox('privacy_policy', __('web.agree_to_terms'))
                                    ->value('1')
                                    ->checked(old('privacy_policy'))
                                    ->id('privacy_policy')
                                    ->helpText($errors->first('privacy_policy'))
                                    ->errorClass('border-red-300 focus:border-red-500 focus:ring-red-500')
                                    ->groupErrorClass('text-red-600')
                                }}
                                <span class="text-red-500 ml-1">*</span>
                            </div>

                            {{ Aire::submit(__('web.register'))
                                ->class('w-full px-4 py-2 text-white bg-blue-500 rounded-md hover:bg-blue-600 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2')
                            }}

                            <div class="text-center mt-3">
                                <span class="text-sm text-gray-600">{{ __('web.already_have_an_account') }}</span>
                                <a class="text-sm text-blue-600 hover:text-blue-800" href="{{ route('login') }}">
                                    {{ __('web.sign_in') }}
                                </a>
                            </div>
                        {{ Aire::close() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
