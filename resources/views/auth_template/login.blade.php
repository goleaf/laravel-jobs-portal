@extends('layouts.auth')
@section('title')
    Admin Login
@endsection
@section('content')
    <!--begin::Main-->
    <div class="flex flex-column flex-column-fluid items-center justify-center p-0">
        <div class="flex-1 -12 text-center">
            <a href="{{ route('front.home') }}" class="image mb-7 mb-sm-10" data-turbo="false">
                <img alt="Logo" src="{{ asset(getSettingValue('logo')) }}" class="img-fluid logo-fix-size">
            </a>
        </div>
        <div class="width-540">
            @if(\Illuminate\Support\Facades\Session::has('status'))
                <p class="alert p-4 rounded-md mb-4 -success">{{ \Illuminate\Support\Facades\Session::get('status') }}</p>
            @endif
            @include('flash::message')
            @include('layouts.errors')
        </div>
        <div class="bg-theme-white rounded-15 shadow-md width-540 px-5 px-sm-7 py-10 mx-auto">
            <h1 class="text-center mb-7">Admin Login</h1>
            <form method="POST" action="{{ route('login') }}">
                @csrf
                <div class="mb-sm-7 mb-4">
                    <label for="formInputEmail" class="block text-sm font-medium text-gray-700 mb-1">
                        Email:<span class="required"></span>
                    </label>
                    <input class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-primary-500 {{ $errors->has("email') ? ' is-invalid' : '' }}" id="formInputEmail" aria-describedby="emailHelp" "
                           type="email" placeholder="Enter Email" name="email" required autocomplete="off" autofocus>
                </div>
                <div class="mb-sm-7 mb-4 position-relative">
                    <div class="flex justify-between">
                        <label for="formInputPassword" class="block text-sm font-medium text-gray-700 mb-1">Password:<span class="required"></span></label>
                        <a href="{{ route('password.request') }}" class="link-info fs-6 text-decoration-none">
                            Forgot Password ?
                        </a>
                    </div>
                    <input type="password" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-primary-500 {{ $errors->has("password') ? ' is-invalid' : '' }}" id="formInputPassword"
                           placeholder="Enter Password" name="password" required autocomplete="off" ">
                </div>
                <div class="mb-sm-7 mb-4 flex items-center">
                    <input type="checkbox" class="flex items-center -input" id="formCheck" {{ (Cookie::get('remember') !== null) ? 'checked' : '' }}>
                    <label class="flex items-center -label" for="formCheck">{{ __('messages.remember_me') }}</label>
                </div>
                <div class="d-grid">
                    <button type="submit" class="btn px-4 py-2 rounded font-medium transition-colors -primary" data-turbo="false">Login</button>
                </div>

                {{-- <div class="d-grid mt-3">
                    <button type="button" class="btn px-4 py-2 rounded font-medium transition-colors -danger w-full mb-5 admin-login"  data-turbo="false">Admin Login</button>
                </div> --}}
            </form>
        </div>
    </div>
    <!--end::Main-->
@endsection
