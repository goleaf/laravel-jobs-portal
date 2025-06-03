@extends('layouts.auth')
@section('title')
    {{ __('Login') }}
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
            <h1 class="text-center mb-7">{{ __('Login') }}</h1>
            <form method="POST" action="{{ route('login') }}">
                @csrf
                <div class="mb-sm-7 mb-4">
                    <label for="formInputEmail" class="form-label">
                        {{ __('Email') }}:<span class="required"></span>
                    </label>
                    <input class="form-control {{ $errors->has('email') ? ' is-invalid' : '' }}" 
                           id="formInputEmail" 
                           type="email" 
                           placeholder="{{ __('Enter Email') }}" 
                           name="email" 
                           value="{{ old('email') }}"
                           required 
                           autocomplete="email" 
                           autofocus>
                    @error('email')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="mb-sm-7 mb-4 position-relative">
                    <div class="d-flex justify-content-between">
                        <label for="formInputPassword" class="form-label">
                            {{ __('Password') }}:<span class="required"></span>
                        </label>
                        @if (Route::has('password.request'))
                            <a href="{{ route('password.request') }}" class="link-info fs-6 text-decoration-none">
                                {{ __('Forgot Password?') }}
                            </a>
                        @endif
                    </div>
                    <input type="password" 
                           class="form-control {{ $errors->has('password') ? ' is-invalid' : '' }}" 
                           id="formInputPassword"
                           placeholder="{{ __('Enter Password') }}" 
                           name="password" 
                           required 
                           autocomplete="current-password">
                    @error('password')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="mb-sm-7 mb-4 form-check">
                    <input type="checkbox" 
                           class="form-check-input" 
                           id="formCheck" 
                           name="remember"
                           {{ old('remember') ? 'checked' : '' }}>
                    <label class="form-check-label" for="formCheck">
                        {{ __('Remember Me') }}
                    </label>
                </div>
                <div class="d-grid">
                    <button type="submit" class="btn btn-primary" data-turbo="false">
                        {{ __('Login') }}
                    </button>
                </div>

                @if (Route::has('register'))
                    <div class="text-center mt-4">
                        <p class="mb-0">
                            {{ __("Don't have an account?") }}
                            <a href="{{ route('register') }}" class="link-primary text-decoration-none">
                                {{ __('Register here') }}
                            </a>
                        </p>
                    </div>
                @endif
            </form>
        </div>
    </div>
    <!--end::Main-->
@endsection 