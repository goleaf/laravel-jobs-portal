@extends('layouts.auth')
@section('title')
    Reset Password
@endsection
@section('content')
    <div class="d-flex flex-column flex-column-fluid align-items-center justify-content-center p-4">
        <div class="col-12 text-center">
            <a href="{{ route('front.home') }}" class="image mb-7 mb-sm-10" data-turbo="false">
                <img alt="Logo" src="{{ asset(getSettingValue('logo')) }}" class="img-fluid logo-fix-size">
            </a>
        </div>
        <div class="width-540">
            @include('flash::message')
            @include('layouts.errors')
        </div>
        <div class="bg-white rounded-15 shadow-md width-540 px-5 px-sm-7 py-10 mx-auto">
            <h1 class="text-center mb-7">Setup New Password</h1>
            @formOpen(['url' => '/password/reset', 'method' => 'POST'])
                @csrf
                {{ Form::hidden('token', $token) }}
                <!--Email-->
                <div class="mb-sm-7 mb-4">
                    {{ Form::label('email', 'Email', ['class' => 'form-label']) }}
                    <span class="required"></span>
                    {{ Form::email('email', old('email'), [
                        'class' => 'form-control form-control-solid' . ($errors->has('email') ? ' is-invalid' : ''),
                        'required' => true,
                        'autofocus' => true,
                        'autocomplete' => 'off',
                        'placeholder' => 'Email'
                    ]) }}
                    <div class="invalid-feedback">
                        {{ $errors->first('email') }}
                    </div>
                </div>

                {{--Password--}}
                <div class="mb-sm-7 mb-4">
                    {{ Form::label('password', 'Password', ['class' => 'form-label']) }}
                    <div class="mb-3 position-relative">
                        {{ Form::password('password', [
                            'class' => 'form-control form-control-solid' . ($errors->has('password') ? ' is-invalid' : ''),
                            'required' => true,
                            'autocomplete' => 'off'
                        ]) }}
                    </div>
                    <div class="invalid-feedback">
                        {{ $errors->first('password') }}
                    </div>
                </div>

                <!-- Confirm Password -->
                <div class="fv-row mb-5">
                    {{ Form::label('password_confirmation', 'Confirm Password', ['class' => 'form-label']) }}
                    {{ Form::password('password_confirmation', [
                        'class' => 'form-control form-control-solid' . ($errors->has('password_confirmation') ? ' is-invalid' : ''),
                        'autocomplete' => 'off'
                    ]) }}
                    <div class="invalid-feedback">
                        {{ $errors->first('password_confirmation') }}
                    </div>
                </div>

                <div class="text-center">
                    {{ Form::button('<span class="indicator-label">Set a New Password</span>', [
                        'type' => 'submit',
                        'class' => 'btn btn-primary'
                    ]) }}
                </div>
            @formClose()
        </div>
@endsection
