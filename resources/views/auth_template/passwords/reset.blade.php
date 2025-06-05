@extends('layouts.auth')
@section('title')
    Reset Password
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
            <h1 class="text-center mb-7">Setup New Password</h1>
            @formOpen(['url' => '/password/reset', 'method' => 'POST'])
                @csrf
                {{ Form::hidden('token', $token) }}
                <!--Email-->
                <div class="mb-sm-7 mb-4">
                    {{ Form::label('email', 'Email', ['class' => 'block text-sm font-medium text-gray-700 mb-1']) }}
                    <span class="required"></span>
                    {{ Form::email('email', old('email'), [
                        'class' => 'block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm-solid' . ($errors->has('email') ? ' is-invalid' : ''),
                        'required' => true,
                        'autofocus' => true,
                        'autocomplete' => 'off',
                        'placeholder' => 'Email'
                    ]) }}
                    <div class="invalid-feedback">
                        {{ $errors->first('email') }}
                    </div>
                </div>

                {{ --Password-- }}
                <div class="mb-sm-7 mb-4">
                    {{ Form::label('password', 'Password', ['class' => 'block text-sm font-medium text-gray-700 mb-1']) }}
                    <div class="mb-3 relative">
                        {{ Form::password('password', [
                            'class' => 'block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm-solid' . ($errors->has('password') ? ' is-invalid' : ''),
                            'required' => true,
                            'autocomplete' => 'off'
                        ]) }}
                    </div>
                    <div class="invalid-feedback">
                        {{ $errors->first('password') }}
                    </div>
                </div>

                <!-- Confirm Password -->
                <div class="fv- flex flex-wrap mb-5">
                    {{ Form::label('password_confirmation', 'Confirm Password', ['class' => 'block text-sm font-medium text-gray-700 mb-1']) }}
                    {{ Form::password('password_confirmation', [
                        'class' => 'block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm-solid' . ($errors->has('password_confirmation') ? ' is-invalid' : ''),
                        'autocomplete' => 'off'
                    ]) }}
                    <div class="invalid-feedback">
                        {{ $errors->first('password_confirmation') }}
                    </div>
                </div>

                <div class="text-center">
                    {{ Form::button('<span class="indicator-label">Set a New Password</span>', [
                        'type' => 'submit',
                        'class' => 'rounded-md bg-indigo-600 px-4 py-2 text-sm font-semibold text-white hover:bg-indigo-700 focus:outline-none transition-colors'
                    ]) }}
                </div>
            @formClose()
        </div>
@endsection
