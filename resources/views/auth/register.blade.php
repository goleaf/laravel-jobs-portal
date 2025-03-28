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

                        @formOpen(['url' => route('register'), 'id' => 'register-form'])
                            @csrf
                            <div class="form-group mb-3">
                                {{ Form::label('first_name', __('web.common.first_name').':') }}
                                <span class="text-danger">*</span>
                                {{ Form::text('first_name', old('first_name'), ['class' => 'form-control '.($errors->has('first_name') ? 'is-invalid' : ''), 'placeholder' => __('web.common.first_name'), 'required']) }}
                                @if ($errors->has('first_name'))
                                    <span class="text-danger">{{ $errors->first('first_name') }}</span>
                                @endif
                            </div>

                            <div class="form-group mb-3">
                                {{ Form::label('last_name', __('web.common.last_name').':') }}
                                <span class="text-danger">*</span>
                                {{ Form::text('last_name', old('last_name'), ['class' => 'form-control '.($errors->has('last_name') ? 'is-invalid' : ''), 'placeholder' => __('web.common.last_name'), 'required']) }}
                                @if ($errors->has('last_name'))
                                    <span class="text-danger">{{ $errors->first('last_name') }}</span>
                                @endif
                            </div>

                            <div class="form-group mb-3">
                                {{ Form::label('email', __('web.common.email').':') }}
                                <span class="text-danger">*</span>
                                {{ Form::email('email', old('email'), ['class' => 'form-control '.($errors->has('email') ? 'is-invalid' : ''), 'placeholder' => __('web.common.email'), 'required']) }}
                                @if ($errors->has('email'))
                                    <span class="text-danger">{{ $errors->first('email') }}</span>
                                @endif
                            </div>

                            <div class="form-group mb-3">
                                {{ Form::label('password', __('web.common.password').':') }}
                                <span class="text-danger">*</span>
                                {{ Form::password('password', ['class' => 'form-control '.($errors->has('password') ? 'is-invalid' : ''), 'placeholder' => __('web.common.password'), 'required']) }}
                                @if ($errors->has('password'))
                                    <span class="text-danger">{{ $errors->first('password') }}</span>
                                @endif
                            </div>

                            <div class="form-group mb-3">
                                {{ Form::label('password_confirmation', __('web.common.confirm_password').':') }}
                                <span class="text-danger">*</span>
                                {{ Form::password('password_confirmation', ['class' => 'form-control', 'placeholder' => __('web.common.confirm_password'), 'required']) }}
                            </div>

                            <div class="form-group mb-3">
                                <div class="d-flex justify-content-between align-items-center">
                                    {{ Form::label('user_type', __('web.common.user_type').':') }}
                                    <span class="text-danger">*</span>
                                </div>
                                <div class="d-flex gap-4">
                                    <div class="form-check">
                                        {{ Form::radio('user_type', '1', old('user_type') == '1', ['class' => 'form-check-input', 'id' => 'candidate']) }}
                                        {{ Form::label('candidate', __('web.common.candidate'), ['class' => 'form-check-label']) }}
                                    </div>
                                    <div class="form-check">
                                        {{ Form::radio('user_type', '2', old('user_type') == '2', ['class' => 'form-check-input', 'id' => 'employer']) }}
                                        {{ Form::label('employer', __('web.common.employer'), ['class' => 'form-check-label']) }}
                                    </div>
                                </div>
                                @if ($errors->has('user_type'))
                                    <span class="text-danger">{{ $errors->first('user_type') }}</span>
                                @endif
                            </div>

                            <div class="form-group mb-3">
                                <div class="form-check">
                                    {{ Form::checkbox('privacy_policy', '1', old('privacy_policy'), ['class' => 'form-check-input', 'id' => 'privacy_policy']) }}
                                    {{ Form::label('privacy_policy', __('web.agree_to_terms'), ['class' => 'form-check-label']) }}
                                    <span class="text-danger">*</span>
                                </div>
                                @if ($errors->has('privacy_policy'))
                                    <span class="text-danger">{{ $errors->first('privacy_policy') }}</span>
                                @endif
                            </div>

                            {{ Form::submit(__('web.register'), ['class' => 'btn btn-primary btn-block']) }}

                            <div class="text-center mt-3">
                                <span>{{ __('web.already_have_an_account') }}</span>
                                <a class="small" href="{{ route('login') }}">
                                    {{ __('web.sign_in') }}
                                </a>
                            </div>
                        @formClose()
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
