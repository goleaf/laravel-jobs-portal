@extends('layouts.app')

@section('content')
    <div class="container mx-auto">
        <div class="flex flex-wrap justify-center">
            <div class="flex-1 -md-8">
                <div class="bg-white shadow rounded-lg overflow-hidden">
                    <div class="bg-white shadow rounded-lg overflow-hidden -header">{{ __('Verify Your Email Address') }}</div>

                    <div class="bg-white shadow rounded-lg overflow-hidden -body">
                        @if (session('resent'))
                            <div class="alert p-4 rounded-md mb-4 -success" role="alert">
                                {{ __('A fresh verification link has been sent to your email address.') }}
                            </div>
                        @endif

                        {{ __('Before proceeding, please check your email for a verification link.') }}
                        {{ __('If you did not receive the email') }},
                        <form class="d-inline" method="POST" action="{{ route('verification.resend') }}">
                            @csrf
                            <button type="submit"
                                    class="btn px-4 py-2 rounded font-medium transition-colors -link p-0 m-0 align-baseline">{{ __('click here to request another') }}</button>
                            .
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
