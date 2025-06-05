@extends('layouts.auth')
@section('title')
    Register
@endsection
@section('content')
    <div class="bg-white rounded-lg shadow-md border border-gray-300 border-gray-200 bg-white shadow rounded-lg overflow-hidden primary">
        <div class="bg-white shadow rounded-lg overflow-hidden header"><h4>Register</h4></div>

        <div class="bg-white shadow rounded-lg overflow-hidden body pt-1">
            <form method="POST" action="{{ route('register') }}" enctype="multipart/form-data">
                @csrf
                @if ($errors->any())
                    <div class="px-4 py-3 rounded-md border border-gray-300 mb-4 p-4 rounded-md mb-4 danger p-0">
                        <ul>
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif
                <div class="flex flex-wrap">
                    <div class="flex-1 md-6">
                        <div class="form-group">
                            <label for="first_name">First Name</label><span class="text-red-600">*</span>
                            <input id="firstName" type="text"
                                   class="w-full px-3 py-2 border border-gray-300 border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-primary-500 {{ $errors->has("first_name') ? ' is-invalid' : '' }}"
                                   name="first_name"
                                   tabindex="1" placeholder="Enter First Name" value="{{ old('first_name') }}"
                                   autofocus>
                            <div class="invalid-feedback">
                                {{ $errors->first('first_name') }}
                            </div>
                        </div>
                    </div>
                    <div class="flex-1 md-6">
                        <div class="form-group">
                            <label for="last_name">Last Name</label>
                            <input id="lastName" type="text"
                                   class="w-full px-3 py-2 border border-gray-300 border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-primary-500 {{ $errors->has("last_name') ? ' is-invalid' : '' }}"
                                   name="last_name"
                                   tabindex="1" placeholder="Enter Last name" value="{{ old('last_name') }}"
                                   autofocus>
                            <div class="invalid-feedback">
                                {{ $errors->first('last_name') }}
                            </div>
                        </div>
                    </div>
                    <div class="flex-1 md-6">
                        <div class="form-group">
                            <label for="email">Email</label><span class="text-red-600">*</span>
                            <input id="email" type="email"
                                   class="w-full px-3 py-2 border border-gray-300 border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-primary-500 {{ $errors->has("email') ? ' is-invalid' : '' }}"
                                   placeholder="Enter Email address" name="email" tabindex="1"
                                   value="{{ old('email') }}"
                                   required autofocus>
                            <div class="invalid-feedback">
                                {{ $errors->first('email') }}
                            </div>
                        </div>
                    </div>
                    <div class="flex-1 md-6">
                        <div class="form-group">
                            <label for="phone">Phone</label>
                            <input id="phone" type="text"
                                   class="w-full px-3 py-2 border border-gray-300 border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-primary-500 {{ $errors->has("phone') ? ' is-invalid' : '' }}"
                                   placeholder="Enter Phone Number" name="phone" tabindex="1" value="{{ old('phone') }}"
                                   autofocus>
                            <div class="invalid-feedback">
                                {{ $errors->first('phone') }}
                            </div>
                        </div>
                    </div>
                    <div class="flex-1 md-6">
                        <div class="form-group">
                            <label for="password" class="control-label">Password</label><span
                                    class="text-red-600">*</span>
                            <input id="password" type="password"
                                   class="w-full px-3 py-2 border border-gray-300 border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-primary-500 {{ $errors->has("password') ? ' is-invalid': '' }}"
                                   placeholder="Set account password" name="password" tabindex="2" required>
                            <div class="invalid-feedback">
                                {{ $errors->first('password') }}
                            </div>
                        </div>
                    </div>
                    <div class="flex-1 md-6">
                        <div class="form-group">
                            <label for="password_confirmation" class="control-label">Confirm Password</label>
                            <input id="password_confirmation" type="password" placeholder="Confirm account password"
                                   class="w-full px-3 py-2 border border-gray-300 border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-primary-500 {{ $errors->has("password_confirmation') ? ' is-invalid': '' }}"
                                   name="password_confirmation" tabindex="2">
                            <div class="invalid-feedback">
                                {{ $errors->first('password_confirmation') }}
                            </div>
                        </div>
                    </div>
                    <div class="flex-1 md-12 mt-4">
                        <div class="form-group">
                            <button type="submit" class="inline-flex items-center px-4 py-2 border border-gray-300 border-transparent text-sm font-medium rounded-md transition duration-150 ease-in-out bg-primary-600 text-white hover: bg-primary-600 -700 px-6 py-3 text-lg px-4 py-2 rounded font-medium transition-colors block" tabindex="4">
                                Register
                            </button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
    <div class="mt-5 text-gray-500 text-center">
        Already have an account? <a href="{{ route('login') }}">Sign In</a>
    </div>
@endsection
