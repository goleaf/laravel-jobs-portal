@extends('layouts.auth')
@section('title')
    Forgot Password
@endsection
@section('content')
<div class="flex flex-col flex-column-fluid items-center justify-center p-0">
    <div class="flex-1 -12 text-center">
        <a href="{{ route('front.home') }}" class="image mb-7 mb-sm-10" data-turbo="false">
            <img alt="Logo" src="{{ asset(getSettingValue('logo')) }}" class="img-fluid logo-fix-size">
        </a>
    </div>
    <div class="width-540">
        @include('flash::message')
        @include('front_web.layouts.errors')
        @if (session('status'))
            <div class="px-4 py-3 rounded-md border border-gray-300 mb-4 p-4 rounded-md mb-4 -success">
                {{ session('status') }}
            </div>
        @endif
    </div>
    <div class="bg-theme-white rounded-15 shadow-md width-540 px-5 px-sm-7 py-10 mx-auto">
        <div class="text-center">
            <h1 class="text-center mb-7">Reset Password ?</h1>
            <div class="mb-4">
                Enter your email to reset your password.
            </div>
        </div>
        @formOpen(['route' => 'password.email', 'method' => 'POST'])
            @csrf
            <div class="mb-sm-7 mb-4">
                {{ Form::label('formInputEmail', 'Email:', ['class' => 'form-label']) }}
                <span class="required"></span>
                {{ Form::email('email', old('email'), [
                    'class' => 'form-control',
                    'placeholder' => 'Your Email',
                    'autocomplete' => 'off',
                    'required' => true
                ]) }}
            </div>

            <div class="flex justify-center">
                {{ Form::button(__('Email Password Reset Link'), [
                    'type' => 'submit',
                    'class' => 'btn btn-primary'
                ]) }}
                <a href="{{ route('admin.login') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 border-transparent text-sm font-medium rounded-md transition duration-150 ease-in-out px-4 py-2 rounded font-medium transition-colors -secondary ms-3">Cancel</a>
            </div>
        @formClose()
    </div>
</div>

@endsection
