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
                            <div class="form-group mb-3">
                                {{ Aire::email('email', __('web.common.email'))
                                    ->required()
                                    ->placeholder(__('web.common.email'))
                                    ->value(old('email'))
                                    ->groupClass('form-group')
                                    ->class(['form-control', 'is-invalid' => $errors->has('email')])
                                }}
                                @if ($errors->has('email'))
                                    <span class="text-danger">{{ $errors->first('email') }}</span>
                                @endif
                            </div>

                            <div class="form-group mb-3">
                                {{ Aire::password('password', __('web.common.password'))
                                    ->required()
                                    ->placeholder(__('web.common.password'))
                                    ->groupClass('form-group')
                                    ->class(['form-control', 'is-invalid' => $errors->has('password')])
                                }}
                                @if ($errors->has('password'))
                                    <span class="text-danger">{{ $errors->first('password') }}</span>
                                @endif
                            </div>

                            <div class="checkbox mb-3">
                                {{ Aire::checkbox('remember', __('web.remember_me'))
                                    ->value(1)
                                    ->checked(old('remember') ? true : false)
                                }}
                            </div>

                            {{ Aire::submit(__('web.login'))->class('btn btn-primary btn-block') }}

                            <div class="text-center pt-3">
                                <a class="small" href="{{ route('password.request') }}">
                                    {{ __('web.forgot_your_password') }}
                                </a>
                            </div>
                        {{ Aire::close() }}

                        <div class="text-center pt-3">
                            <a class="btn btn-outline-primary btn-block" href="{{ route('register') }}">
                                {{ __('web.register') }}
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
