@extends('layouts.auth')

@section('content')
    <div class="container mx-auto px-4 mx-auto">
        <div class="flex flex-wrap justify-center">
            <div class="flex-1 -md-8">
                <div class="bg-white shadow rounded-lg overflow-hidden">
                    <div class="bg-white shadow rounded-lg overflow-hidden -header">{{ __('Confirm Password') }}</div>

                    <div class="bg-white shadow rounded-lg overflow-hidden -body">
                        {{ __('Please confirm your password before continuing.') }}

                        <form method="POST" action="{{ route('password.confirm') }}">
                            @csrf

                            <div class="form-group flex flex-wrap">
                                <label for="password"
                                       class="md:w-4/12 flex-1 - block text-sm font-medium text-gray-700 mb-1 text-md-right">{{ __('messages.password') }}</label>

                                <div class="flex-1 -md-6">
                                    <input id="password" type="password"
                                           class="w-full px-3 py-2 border border-gray-300 border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-primary-500 @error("password') is-invalid @enderror" name="password"
                                           required autocomplete="current-password">

                                    @error('password')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                    @enderror
                                </div>
                            </div>

                            <div class="form-group flex flex-wrap mb-0">
                                <div class="flex-1 -md-8 offset-md-4">
                                    <button type="submit" class="inline-flex items-center px-4 py-2 border border-gray-300 border-transparent text-sm font-medium rounded-md transition duration-150 ease-in-out px-4 py-2 rounded font-medium transition-colors -primary">
                                        {{ __('messages.confirm_password') }}
                                    </button>

                                    @if (Route::has('password.request'))
                                        <a class="inline-flex items-center px-4 py-2 border border-gray-300 border-transparent text-sm font-medium rounded-md transition duration-150 ease-in-out px-4 py-2 rounded font-medium transition-colors -link" href="{{ route('password.request') }}">
                                            {{ __('messages.forgot_password') }}
                                        </a>
                                    @endif
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
