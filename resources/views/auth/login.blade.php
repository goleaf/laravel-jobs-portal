@extends('layouts.auth')

@section('title')
    {{ __('web.login') }}
@endsection

@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="mx-auto" style="max-width: 400px;">
                <div class="card">
                    <div class="card-body p-4">
                        <div class="text-center">
                            <h1 class="h4 text-gray-900 mb-4">{{ __('web.login') }}</h1>
                        </div>

                        {{ Aire::open()->route('login')->id('login-form') }}
                            @csrf
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
                                {{ Aire::checkbox('remember', __('web.remember_me'))
                                    ->value(1)
                                    ->checked(old('remember') ? true : false)
                                    ->id('remember')
                                }}
                            </div>

                            {{ Aire::submit(__('web.login'))
                                ->class('w-full px-4 py-2 text-white bg-blue-500 rounded-md hover:bg-blue-600 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2')
                            }}

                            <div class="text-center pt-3">
                                <a class="text-sm text-blue-600 hover:text-blue-800" href="{{ route('password.request') }}">
                                    {{ __('web.forgot_your_password') }}
                                </a>
                            </div>
                        {{ Aire::close() }}

                        <div class="text-center pt-3">
                            <a class="inline-block w-full px-4 py-2 text-center text-blue-500 border border-blue-500 rounded-md hover:bg-blue-50" href="{{ route('register') }}">
                                {{ __('web.register') }}
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
