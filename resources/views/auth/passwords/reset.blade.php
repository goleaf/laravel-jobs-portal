@extends('layouts.auth')
@section('title')
    @lang('web.new_password.reset_password')
@endsection
@section('content')
    <div class="flex flex-col flex-column-fluid items-center justify-center p-4">
        <div class="flex-1 -12 text-center">
            <a href="{{ route('front.home') }}" class="image mb-7 mb-sm-10" data-turbo="false">
                <img alt="Logo" src="{{ asset(getSettingValue('logo')) }}" class="img-fluid logo-fix-size">
            </a>
        </div>
        <div class="width-540">
            @include('flash::message')
            @include('layouts.errors')
        </div>
        <div class="bg-white rounded-15 shadow-md width-540 px-5 px-sm-7 py-10 mx-auto">
            <h1 class="text-center mb-7">@lang('web.new_password.new_password')</h1>
            @formOpen(['url' => url('/password/reset'), 'method' => 'POST'])
                @csrf
                <input type="hidden" name="token" value="{{ $request->route('token') }}">
                
                <!-- Email -->
                <div class="mb-sm-7 mb-4">
                    {{ Form::label('email', __('web.common.email'), ['class' => 'form-label']) }}
                    <span class="required"></span>
                    {{ Form::email('email', old('email'), [
                        'class' => 'form-control form-control-solid '.($errors->has('email') ? 'is-invalid' : ''),
                        'required',
                        'autofocus',
                        'autocomplete' => 'off',
                        'placeholder' => __('web.common.email')
                    ]) }}
                    @if ($errors->has('email'))
                        <div class="invalid-feedback">
                            {{ $errors->first('email') }}
                        </div>
                    @endif
                </div>

                <!-- Password -->
                <div class="mb-sm-7 mb-4">
                    {{ Form::label('password', __('web.common.password'), ['class' => 'form-label']) }}
                    <div class="mb-3 relative">
                        {{ Form::password('password', [
                            'class' => 'form-control form-control-solid '.($errors->has('password') ? 'is-invalid' : ''),
                            'required',
                            'autocomplete' => 'off',
                            'placeholder' => __('web.common.password')
                        ]) }}
                    </div>
                    @if ($errors->has('password'))
                        <div class="invalid-feedback">
                            {{ $errors->first('password') }}
                        </div>
                    @endif
                </div>

                <!-- Confirm Password -->
                <div class="fv- flex flex-wrap mb-5">
                    {{ Form::label('password_confirmation', __('web.common.confirm_password'), ['class' => 'form-label']) }}
                    {{ Form::password('password_confirmation', [
                        'class' => 'form-control form-control-solid '.($errors->has('password_confirmation') ? 'is-invalid' : ''),
                        'autocomplete' => 'off',
                        'placeholder' => __('web.common.confirm_password')
                    ]) }}
                    @if ($errors->has('password_confirmation'))
                        <div class="invalid-feedback">
                            {{ $errors->first('password_confirmation') }}
                        </div>
                    @endif
                </div>

                <div class="text-center">
                    {{ Form::submit(__('web.new_password.set_new_password'), ['class' => 'btn btn-primary']) }}
                </div>
            @formClose()
        </div>
    </div>
@endsection
