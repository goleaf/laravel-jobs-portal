@extends('layouts.auth')
@section('title')
    {{ __('Register') }}
@endsection
@section('content')
    <!--begin::Main-->
    <div class="d-flex flex-column flex-column-fluid align-items-center justify-content-center p-0">
        <div class="col-12 text-center">
            <a href="{{ route('front.home') }}" class="image mb-7 mb-sm-10" data-turbo="false">
                <img alt="Logo" src="{{ asset('assets/img/logo.png') }}" class="img-fluid logo-fix-size">
            </a>
        </div>
        <div class="width-540">
            @if(\Illuminate\Support\Facades\Session::has('status'))
                <p class="alert alert-success">{{ \Illuminate\Support\Facades\Session::get('status') }}</p>
            @endif
            @if (session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif
            @if (session('error'))
                <div class="alert alert-danger">{{ session('error') }}</div>
            @endif
            @if ($errors->any())
                <div class="alert alert-danger">
                    <ul class="mb-0">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
        </div>
        <div class="bg-theme-white rounded-15 shadow-md width-540 px-5 px-sm-7 py-10 mx-auto">
            <h1 class="text-center mb-7">{{ __('Register') }}</h1>
            <form method="POST" action="{{ route('register') }}" enctype="multipart/form-data">
                @csrf
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-sm-7 mb-4">
                            <label for="first_name" class="form-label">
                                {{ __('First Name') }}:<span class="required"></span>
                            </label>
                            <input id="first_name" type="text"
                                   class="form-control{{ $errors->has('first_name') ? ' is-invalid' : '' }}"
                                   name="first_name"
                                   placeholder="{{ __('Enter First Name') }}" 
                                   value="{{ old('first_name') }}"
                                   required autofocus>
                            @error('first_name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-sm-7 mb-4">
                            <label for="last_name" class="form-label">{{ __('Last Name') }}:</label>
                            <input id="last_name" type="text"
                                   class="form-control{{ $errors->has('last_name') ? ' is-invalid' : '' }}"
                                   name="last_name"
                                   placeholder="{{ __('Enter Last Name') }}" 
                                   value="{{ old('last_name') }}">
                            @error('last_name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-sm-7 mb-4">
                            <label for="email" class="form-label">
                                {{ __('Email') }}:<span class="required"></span>
                            </label>
                            <input id="email" type="email"
                                   class="form-control{{ $errors->has('email') ? ' is-invalid' : '' }}"
                                   placeholder="{{ __('Enter Email Address') }}" 
                                   name="email"
                                   value="{{ old('email') }}"
                                   required autocomplete="email">
                            @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-sm-7 mb-4">
                            <label for="phone" class="form-label">{{ __('Phone') }}:</label>
                            <input id="phone" type="text"
                                   class="form-control{{ $errors->has('phone') ? ' is-invalid' : '' }}"
                                   placeholder="{{ __('Enter Phone Number') }}" 
                                   name="phone" 
                                   value="{{ old('phone') }}">
                            @error('phone')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-sm-7 mb-4">
                            <label for="password" class="form-label">
                                {{ __('Password') }}:<span class="required"></span>
                            </label>
                            <input id="password" type="password"
                                   class="form-control{{ $errors->has('password') ? ' is-invalid': '' }}"
                                   placeholder="{{ __('Set Account Password') }}" 
                                   name="password" 
                                   required autocomplete="new-password">
                            @error('password')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-sm-7 mb-4">
                            <label for="password_confirmation" class="form-label">
                                {{ __('Confirm Password') }}:<span class="required"></span>
                            </label>
                            <input id="password_confirmation" type="password" 
                                   placeholder="{{ __('Confirm Account Password') }}"
                                   class="form-control{{ $errors->has('password_confirmation') ? ' is-invalid': '' }}"
                                   name="password_confirmation" 
                                   required autocomplete="new-password">
                            @error('password_confirmation')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="d-grid">
                            <button type="submit" class="btn btn-primary" data-turbo="false">
                                {{ __('Register') }}
                            </button>
                        </div>
                    </div>
                </div>
            </form>
            
            <div class="text-center mt-4">
                <p class="mb-0">
                    {{ __('Already have an account?') }}
                    <a href="{{ route('login') }}" class="link-primary text-decoration-none">
                        {{ __('Sign In') }}
                    </a>
                </p>
            </div>
        </div>
    </div>
    <!--end::Main-->
@endsection 