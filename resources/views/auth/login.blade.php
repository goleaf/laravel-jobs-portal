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

                        @formOpen(['url' => route('login'), 'id' => 'login-form'])
                            @csrf
                            <div class="form-group mb-3">
                                {{ Form::label('email', __('web.common.email').':') }}
                                <span class="text-danger">*</span>
                                {{ Form::email('email', old('email'), ['class' => 'form-control '.($errors->has('email') ? 'is-invalid' : ''), 'placeholder' => __('web.common.email')]) }}
                                @if ($errors->has('email'))
                                    <span class="text-danger">{{ $errors->first('email') }}</span>
                                @endif
                            </div>

                            <div class="form-group mb-3">
                                {{ Form::label('password', __('web.common.password').':') }}
                                <span class="text-danger">*</span>
                                {{ Form::password('password', ['class' => 'form-control '.($errors->has('password') ? 'is-invalid' : ''), 'placeholder' => __('web.common.password')]) }}
                                @if ($errors->has('password'))
                                    <span class="text-danger">{{ $errors->first('password') }}</span>
                                @endif
                            </div>

                            <div class="checkbox mb-3">
                                <label>
                                    {{ Form::checkbox('remember', 1, old('remember') ? true : false) }}
                                    {{ __('web.remember_me') }}
                                </label>
                            </div>

                            {{ Form::submit(__('web.login'), ['class' => 'btn btn-primary btn-block']) }}

                            <div class="text-center pt-3">
                                <a class="small" href="{{ route('password.request') }}">
                                    {{ __('web.forgot_your_password') }}
                                </a>
                            </div>
                        @formClose()

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
