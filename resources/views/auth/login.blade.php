@extends('layouts.app')

@section('title', __('auth.login'))

@section('content')
<div class="min-h-screen flex items-center justify-center bg-gray-50 dark:bg-gray-900 py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-md w-full space-y-8">
        <div>
            <div class="mx-auto h-12 w-auto flex justify-center">
                <img class="h-12 w-auto" src="{{ asset('images/logo.svg') }}" alt="{{ config('app.name') }}">
            </div>
            <h2 class="mt-6 text-center text-3xl font-extrabold text-gray-900 dark:text-white">
                {{ __('auth.sign_in_to_account') }}
            </h2>
            <p class="mt-2 text-center text-sm text-gray-600 dark:text-gray-400">
                {{ __('auth.or') }}
                <a href="{{ route('register') }}" class="font-medium text-blue-600 hover:text-blue-500 dark:text-blue-400 dark:hover:text-blue-300">
                    {{ __('auth.create_new_account') }}
                </a>
            </p>
        </div>

        <form class="mt-8 space-y-6" action="{{ route('login') }}" method="POST">
            @csrf
            
            <div class="rounded-md shadow-sm -space-y-px">
                <div>
                    <x-ui.input
                        type="email"
                        name="email"
                        id="email"
                        :value="old('email')"
                        :placeholder="__('auth.email_address')"
                        :label="__('auth.email_address')"
                        icon="envelope"
                        autocomplete="email"
                        required
                        :error="$errors->first('email')"
                        class="rounded-t-md"
                    />
                </div>
                
                <div class="mt-4">
                    <x-ui.input
                        type="password"
                        name="password"
                        id="password"
                        :placeholder="__('auth.password')"
                        :label="__('auth.password')"
                        icon="lock-closed"
                        autocomplete="current-password"
                        required
                        :error="$errors->first('password')"
                        class="rounded-b-md"
                    />
                </div>
            </div>

            <div class="flex items-center justify-between">
                <div class="flex items-center">
                    <input 
                        id="remember-me" 
                        name="remember" 
                        type="checkbox" 
                        class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded"
                        {{ old('remember') ? 'checked' : '' }}
                    >
                    <label for="remember-me" class="ml-2 block text-sm text-gray-900 dark:text-gray-300">
                        {{ __('auth.remember_me') }}
                    </label>
                </div>

                <div class="text-sm">
                    <a href="{{ route('password.request') }}" class="font-medium text-blue-600 hover:text-blue-500 dark:text-blue-400 dark:hover:text-blue-300">
                        {{ __('auth.forgot_password') }}
                    </a>
                </div>
            </div>

            <div>
                <x-ui.button 
                    type="submit" 
                    variant="primary" 
                    size="lg" 
                    class="group relative w-full flex justify-center"
                >
                    <span class="absolute left-0 inset-y-0 flex items-center pl-3">
                        <x-icon name="lock-closed" class="h-5 w-5 text-blue-500 group-hover:text-blue-400" />
                    </span>
                    {{ __('auth.sign_in') }}
                </x-ui.button>
            </div>

            <!-- Social Login Options -->
            @if(config('services.google.client_id') || config('services.facebook.client_id') || config('services.linkedin.client_id'))
                <div class="mt-6">
                    <div class="relative">
                        <div class="absolute inset-0 flex items-center">
                            <div class="w-full border-t border-gray-300 dark:border-gray-600" />
                        </div>
                        <div class="relative flex justify-center text-sm">
                            <span class="px-2 bg-gray-50 dark:bg-gray-900 text-gray-500 dark:text-gray-400">
                                {{ __('auth.or_continue_with') }}
                            </span>
                        </div>
                    </div>

                    <div class="mt-6 grid grid-cols-{{ collect([config('services.google.client_id'), config('services.facebook.client_id'), config('services.linkedin.client_id')])->filter()->count() }} gap-3">
                        @if(config('services.google.client_id'))
                            <a href="{{ route('auth.social', 'google') }}" class="w-full inline-flex justify-center py-2 px-4 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm bg-white dark:bg-gray-800 text-sm font-medium text-gray-500 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                                <x-icon name="google" class="h-5 w-5" />
                                <span class="ml-2">Google</span>
                            </a>
                        @endif

                        @if(config('services.facebook.client_id'))
                            <a href="{{ route('auth.social', 'facebook') }}" class="w-full inline-flex justify-center py-2 px-4 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm bg-white dark:bg-gray-800 text-sm font-medium text-gray-500 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                                <x-icon name="facebook" class="h-5 w-5" />
                                <span class="ml-2">Facebook</span>
                            </a>
                        @endif

                        @if(config('services.linkedin.client_id'))
                            <a href="{{ route('auth.social', 'linkedin') }}" class="w-full inline-flex justify-center py-2 px-4 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm bg-white dark:bg-gray-800 text-sm font-medium text-gray-500 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                                <x-icon name="linkedin" class="h-5 w-5" />
                                <span class="ml-2">LinkedIn</span>
                            </a>
                        @endif
                    </div>
                </div>
            @endif
        </form>

        <!-- Additional Links -->
        <div class="text-center text-sm text-gray-600 dark:text-gray-400 space-y-2">
            <p>
                {{ __('auth.need_help') }}
                <a href="{{ route('help') }}" class="font-medium text-blue-600 hover:text-blue-500 dark:text-blue-400 dark:hover:text-blue-300">
                    {{ __('auth.contact_support') }}
                </a>
            </p>
            
            <p>
                {{ __('auth.by_signing_in') }}
                <a href="{{ route('terms') }}" class="font-medium text-blue-600 hover:text-blue-500 dark:text-blue-400 dark:hover:text-blue-300">
                    {{ __('auth.terms_of_service') }}
                </a>
                {{ __('auth.and') }}
                <a href="{{ route('privacy') }}" class="font-medium text-blue-600 hover:text-blue-500 dark:text-blue-400 dark:hover:text-blue-300">
                    {{ __('auth.privacy_policy') }}
                </a>
            </p>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
// Auto-focus first input on load
document.addEventListener('DOMContentLoaded', function() {
    const emailInput = document.getElementById('email');
    if (emailInput && !emailInput.value) {
        emailInput.focus();
    }
});

// Enhanced form validation
document.querySelector('form').addEventListener('submit', function(e) {
    const email = document.getElementById('email').value;
    const password = document.getElementById('password').value;
    
    if (!email || !password) {
        e.preventDefault();
        // Show validation message
        if (!email) {
            document.getElementById('email').focus();
        } else if (!password) {
            document.getElementById('password').focus();
        }
    }
});
</script>
@endpush
