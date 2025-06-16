@extends('layouts.app')

@section('title', __('auth.reset_password'))

@section('content')
<div class="min-h-screen flex items-center justify-center bg-gray-50 dark:bg-gray-900 py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-md w-full space-y-8">
        <div>
            <div class="mx-auto h-12 w-auto flex justify-center">
                <img class="h-12 w-auto" src="{{ asset('images/logo.svg') }}" alt="{{ config('app.name') }}">
            </div>
            <h2 class="mt-6 text-center text-3xl font-extrabold text-gray-900 dark:text-white">
                {{ __('auth.reset_password') }}
            </h2>
            <p class="mt-2 text-center text-sm text-gray-600 dark:text-gray-400">
                {{ __('auth.reset_password_description') }}
            </p>
        </div>

        @if (session('status'))
            <div class="rounded-md bg-green-50 dark:bg-green-900/20 p-4">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <x-icon name="check-circle" class="h-5 w-5 text-green-400" />
                    </div>
                    <div class="ml-3">
                        <p class="text-sm font-medium text-green-800 dark:text-green-200">
                            {{ session('status') }}
                        </p>
                    </div>
                </div>
            </div>
        @endif

        <form class="mt-8 space-y-6" action="{{ route('password.email') }}" method="POST">
            @csrf
            
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
                    :hint="__('auth.enter_email_for_reset')"
                />
            </div>

            <div>
                <x-ui.button 
                    type="submit" 
                    variant="primary" 
                    size="lg" 
                    class="group relative w-full flex justify-center"
                >
                    <span class="absolute left-0 inset-y-0 flex items-center pl-3">
                        <x-icon name="paper-airplane" class="h-5 w-5 text-blue-500 group-hover:text-blue-400" />
                    </span>
                    {{ __('auth.send_reset_link') }}
                </x-ui.button>
            </div>

            <!-- Back to Login -->
            <div class="text-center">
                <a href="{{ route('login') }}" class="font-medium text-blue-600 hover:text-blue-500 dark:text-blue-400 dark:hover:text-blue-300">
                    {{ __('auth.back_to_login') }}
                </a>
            </div>
        </form>

        <!-- Security Notice -->
        <div class="mt-6 p-4 bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-md">
            <div class="flex">
                <div class="flex-shrink-0">
                    <x-icon name="information-circle" class="h-5 w-5 text-blue-400" />
                </div>
                <div class="ml-3">
                    <h3 class="text-sm font-medium text-blue-800 dark:text-blue-200">
                        {{ __('auth.security_notice') }}
                    </h3>
                    <div class="mt-2 text-sm text-blue-700 dark:text-blue-300">
                        <ul class="list-disc pl-5 space-y-1">
                            <li>{{ __('auth.check_email_inbox') }}</li>
                            <li>{{ __('auth.check_spam_folder') }}</li>
                            <li>{{ __('auth.link_expires_in_60_minutes') }}</li>
                            <li>{{ __('auth.contact_support_if_needed') }}</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>

        <!-- Additional Help -->
        <div class="text-center text-sm text-gray-600 dark:text-gray-400 space-y-2">
            <p>
                {{ __('auth.still_need_help') }}
                <a href="{{ route('contact') }}" class="font-medium text-blue-600 hover:text-blue-500 dark:text-blue-400 dark:hover:text-blue-300">
                    {{ __('auth.contact_support') }}
                </a>
            </p>
            
            <p>
                {{ __('auth.remember_password') }}
                <a href="{{ route('login') }}" class="font-medium text-blue-600 hover:text-blue-500 dark:text-blue-400 dark:hover:text-blue-300">
                    {{ __('auth.sign_in_now') }}
                </a>
            </p>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
// Auto-focus email input on load
document.addEventListener('DOMContentLoaded', function() {
    const emailInput = document.getElementById('email');
    if (emailInput && !emailInput.value) {
        emailInput.focus();
    }
});

// Form submission feedback
document.querySelector('form').addEventListener('submit', function(e) {
    const submitButton = this.querySelector('button[type="submit"]');
    const originalText = submitButton.textContent;
    
    submitButton.disabled = true;
    submitButton.innerHTML = `
        <div class="flex items-center justify-center">
            <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
            {{ __('auth.sending') }}...
        </div>
    `;
    
    // Re-enable button after 10 seconds as fallback
    setTimeout(() => {
        submitButton.disabled = false;
        submitButton.textContent = originalText;
    }, 10000);
});
</script>
@endpush 