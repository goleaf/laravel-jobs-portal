@extends('layouts.auth')

@section('content')
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mx-auto px-4 mx-auto px-4 mx-auto">
        <div class="flex flex-wrap justify-center">
            <div class="flex-1 md-8">
                <div class="bg-white shadow rounded -lg overflow-hidden">
                    <div class="bg-white shadow rounded -lg overflow-hidden header">{{ __('Confirm Password') }}</div>

                    <div class="bg-white shadow rounded -lg overflow-hidden body">
                        {{ __('Please confirm your password before continuing.') }}

                        <form method="POST" action="{{ route('password.confirm') }}">
                            @csrf

                            <div class="mb-4 flex flex-wrap">
                                <label for="password"
                                       class="md:w-4/12 flex-1 - block text-sm font-medium text-gray-700 mb-1 text-md-right">{{ __('messages.password') }}</label>

                                <div class="flex-1 md-6">
                                    <input id="password" type="password"
                                           class="w-full px-3 py-2 border border-gray-300 border border-gray-300 -gray-300 rounded -md focus:outline-none focus:ring-2 focus:ring-primary-500 @error("password') is-invalid @enderror" name="password"
                                           required autocomplete="current-password">

                                    @error('password')
                                    <span class="invalid-feedback" role="rounded-md p-4">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                    @enderror
                                </div>
                            </div>

                            <div class="mb-4 flex flex-wrap mb-0">
                                <div class="flex-1 md-8 offset-md-4">
                                    <button type="submit" class="border border-gray-300 bg-transparent">
                                        {{ __('messages.confirm_password') }}
                                    </button>

                                    @if (Route::has('password.request'))
                                        <a class="border border-gray-300 bg-transparent" href="{{ route('password.request') }}">
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
