@extends('layouts.app')

@section('title', __('auth.register'))

@section('content')
<div class="min-h-screen flex items-center justify-center bg-gray-50 dark:bg-gray-900 py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-md w-full space-y-8">
        <div>
            <div class="mx-auto h-12 w-auto flex justify-center">
                <img class="h-12 w-auto" src="{{ asset('images/logo.svg') }}" alt="{{ config('app.name') }}">
            </div>
            <h2 class="mt-6 text-center text-3xl font-extrabold text-gray-900 dark:text-white">
                {{ __('auth.create_account') }}
            </h2>
            <p class="mt-2 text-center text-sm text-gray-600 dark:text-gray-400">
                {{ __('auth.or') }}
                <a href="{{ route('login') }}" class="font-medium text-blue-600 hover:text-blue-500 dark:text-blue-400 dark:hover:text-blue-300">
                    {{ __('auth.sign_in_existing') }}
                </a>
            </p>
        </div>

        <!-- Account Type Selection -->
        <div class="bg-white dark:bg-gray-800 rounded-lg p-6 shadow-sm border border-gray-200 dark:border-gray-700">
            <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">
                {{ __('auth.account_type') }}
            </h3>
            
            <div class="grid grid-cols-1 gap-4">
                <label class="relative flex cursor-pointer rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 p-4 shadow-sm focus:outline-none hover:border-blue-500 dark:hover:border-blue-400 transition-colors">
                    <input type="radio" name="user_type" value="candidate" class="sr-only" checked>
                    <span class="flex flex-1">
                        <span class="flex flex-col">
                            <span class="block text-sm font-medium text-gray-900 dark:text-white">
                                {{ __('auth.job_seeker') }}
                            </span>
                            <span class="mt-1 flex items-center text-sm text-gray-500 dark:text-gray-400">
                                {{ __('auth.job_seeker_description') }}
                            </span>
                        </span>
                    </span>
                    <x-icon name="user" class="h-5 w-5 text-blue-600 dark:text-blue-400" />
                </label>

                <label class="relative flex cursor-pointer rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 p-4 shadow-sm focus:outline-none hover:border-blue-500 dark:hover:border-blue-400 transition-colors">
                    <input type="radio" name="user_type" value="employer" class="sr-only">
                    <span class="flex flex-1">
                        <span class="flex flex-col">
                            <span class="block text-sm font-medium text-gray-900 dark:text-white">
                                {{ __('auth.employer') }}
                            </span>
                            <span class="mt-1 flex items-center text-sm text-gray-500 dark:text-gray-400">
                                {{ __('auth.employer_description') }}
                            </span>
                        </span>
                    </span>
                    <x-icon name="building-office" class="h-5 w-5 text-blue-600 dark:text-blue-400" />
                </label>
            </div>
        </div>

        <form class="mt-8 space-y-6" action="{{ route('register') }}" method="POST">
            @csrf
            <input type="hidden" name="user_type" id="selected_user_type" value="candidate">
            
            <div class="space-y-4">
                <div class="grid grid-cols-2 gap-4">
                    <x-ui.input
                        type="text"
                        name="first_name"
                        id="first_name"
                        :value="old('first_name')"
                        :placeholder="__('auth.first_name')"
                        :label="__('auth.first_name')"
                        icon="user"
                        autocomplete="given-name"
                        required
                        :error="$errors->first('first_name')"
                    />
                    
                    <x-ui.input
                        type="text"
                        name="last_name"
                        id="last_name"
                        :value="old('last_name')"
                        :placeholder="__('auth.last_name')"
                        :label="__('auth.last_name')"
                        icon="user"
                        autocomplete="family-name"
                        required
                        :error="$errors->first('last_name')"
                    />
                </div>

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
                />

                <x-ui.input
                    type="password"
                    name="password"
                    id="password"
                    :placeholder="__('auth.password')"
                    :label="__('auth.password')"
                    icon="lock-closed"
                    autocomplete="new-password"
                    required
                    :error="$errors->first('password')"
                    :hint="__('auth.password_requirements')"
                />

                <x-ui.input
                    type="password"
                    name="password_confirmation"
                    id="password_confirmation"
                    :placeholder="__('auth.confirm_password')"
                    :label="__('auth.confirm_password')"
                    icon="lock-closed"
                    autocomplete="new-password"
                    required
                    :error="$errors->first('password_confirmation')"
                />

                <!-- Company Name (for employers) -->
                <div id="company_field" class="hidden">
                    <x-ui.input
                        type="text"
                        name="company_name"
                        id="company_name"
                        :value="old('company_name')"
                        :placeholder="__('auth.company_name')"
                        :label="__('auth.company_name')"
                        icon="building-office"
                        autocomplete="organization"
                        :error="$errors->first('company_name')"
                    />
                </div>
            </div>

            <!-- Terms and Privacy -->
            <div class="flex items-start">
                <div class="flex items-center h-5">
                    <input 
                        id="agree" 
                        name="agree" 
                        type="checkbox" 
                        class="focus:ring-blue-500 h-4 w-4 text-blue-600 border-gray-300 rounded"
                        required
                    >
                </div>
                <div class="ml-3 text-sm">
                    <label for="agree" class="text-gray-700 dark:text-gray-300">
                        {{ __('auth.i_agree_to') }}
                        <a href="{{ route('terms') }}" class="text-blue-600 hover:text-blue-500 dark:text-blue-400 dark:hover:text-blue-300" target="_blank">
                            {{ __('auth.terms_of_service') }}
                        </a>
                        {{ __('auth.and') }}
                        <a href="{{ route('privacy') }}" class="text-blue-600 hover:text-blue-500 dark:text-blue-400 dark:hover:text-blue-300" target="_blank">
                            {{ __('auth.privacy_policy') }}
                        </a>
                    </label>
                </div>
            </div>

            <!-- Marketing Consent -->
            <div class="flex items-start">
                <div class="flex items-center h-5">
                    <input 
                        id="marketing" 
                        name="marketing" 
                        type="checkbox" 
                        class="focus:ring-blue-500 h-4 w-4 text-blue-600 border-gray-300 rounded"
                        {{ old('marketing') ? 'checked' : '' }}
                    >
                </div>
                <div class="ml-3 text-sm">
                    <label for="marketing" class="text-gray-700 dark:text-gray-300">
                        {{ __('auth.marketing_consent') }}
                    </label>
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
                        <x-icon name="user-plus" class="h-5 w-5 text-blue-500 group-hover:text-blue-400" />
                    </span>
                    {{ __('auth.create_account') }}
                </x-ui.button>
            </div>

            <!-- Social Registration Options -->
            @if(config('services.google.client_id') || config('services.facebook.client_id') || config('services.linkedin.client_id'))
                <div class="mt-6">
                    <div class="relative">
                        <div class="absolute inset-0 flex items-center">
                            <div class="w-full border-t border-gray-300 dark:border-gray-600" />
                        </div>
                        <div class="relative flex justify-center text-sm">
                            <span class="px-2 bg-gray-50 dark:bg-gray-900 text-gray-500 dark:text-gray-400">
                                {{ __('auth.or_register_with') }}
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
    </div>
</div>
@endsection

@push('scripts')
<script>
// Handle account type selection
document.addEventListener('DOMContentLoaded', function() {
    const userTypeRadios = document.querySelectorAll('input[name="user_type"]');
    const selectedUserType = document.getElementById('selected_user_type');
    const companyField = document.getElementById('company_field');
    const companyNameInput = document.getElementById('company_name');

    userTypeRadios.forEach(radio => {
        radio.addEventListener('change', function() {
            selectedUserType.value = this.value;
            
            if (this.value === 'employer') {
                companyField.classList.remove('hidden');
                companyNameInput.required = true;
            } else {
                companyField.classList.add('hidden');
                companyNameInput.required = false;
                companyNameInput.value = '';
            }
        });
    });

    // Password strength indicator
    const passwordInput = document.getElementById('password');
    const passwordConfirm = document.getElementById('password_confirmation');

    function validatePasswordMatch() {
        if (passwordConfirm.value && passwordInput.value !== passwordConfirm.value) {
            passwordConfirm.setCustomValidity('{{ __("auth.passwords_do_not_match") }}');
        } else {
            passwordConfirm.setCustomValidity('');
        }
    }

    passwordInput.addEventListener('input', validatePasswordMatch);
    passwordConfirm.addEventListener('input', validatePasswordMatch);
});
</script>
@endpush 