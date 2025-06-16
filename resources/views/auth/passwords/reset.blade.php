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
                {{ __('auth.set_new_password') }}
            </h2>
            <p class="mt-2 text-center text-sm text-gray-600 dark:text-gray-400">
                {{ __('auth.create_strong_password') }}
            </p>
        </div>

        <form class="mt-8 space-y-6" action="{{ route('password.update') }}" method="POST">
            @csrf
            <input type="hidden" name="token" value="{{ $token }}">
            
            <div class="space-y-4">
                <x-ui.input
                    type="email"
                    name="email"
                    id="email"
                    :value="$email ?? old('email')"
                    :placeholder="__('auth.email_address')"
                    :label="__('auth.email_address')"
                    icon="envelope"
                    autocomplete="email"
                    required
                    readonly
                    :error="$errors->first('email')"
                />

                <x-ui.input
                    type="password"
                    name="password"
                    id="password"
                    :placeholder="__('auth.new_password')"
                    :label="__('auth.new_password')"
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
                    :placeholder="__('auth.confirm_new_password')"
                    :label="__('auth.confirm_new_password')"
                    icon="lock-closed"
                    autocomplete="new-password"
                    required
                    :error="$errors->first('password_confirmation')"
                />
            </div>

            <!-- Password Strength Indicator -->
            <div id="password-strength" class="hidden">
                <div class="mb-2">
                    <div class="flex justify-between items-center">
                        <span class="text-sm text-gray-700 dark:text-gray-300">{{ __('auth.password_strength') }}</span>
                        <span id="strength-text" class="text-sm font-medium"></span>
                    </div>
                    <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-2 mt-1">
                        <div id="strength-bar" class="h-2 rounded-full transition-all duration-300" style="width: 0%"></div>
                    </div>
                </div>
                
                <div class="text-sm text-gray-600 dark:text-gray-400">
                    <div class="grid grid-cols-2 gap-2">
                        <div id="req-length" class="flex items-center">
                            <x-icon name="x-mark" class="h-4 w-4 text-red-500 mr-1" />
                            <span>{{ __('auth.min_8_characters') }}</span>
                        </div>
                        <div id="req-uppercase" class="flex items-center">
                            <x-icon name="x-mark" class="h-4 w-4 text-red-500 mr-1" />
                            <span>{{ __('auth.uppercase_letter') }}</span>
                        </div>
                        <div id="req-lowercase" class="flex items-center">
                            <x-icon name="x-mark" class="h-4 w-4 text-red-500 mr-1" />
                            <span>{{ __('auth.lowercase_letter') }}</span>
                        </div>
                        <div id="req-number" class="flex items-center">
                            <x-icon name="x-mark" class="h-4 w-4 text-red-500 mr-1" />
                            <span>{{ __('auth.number') }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <div>
                <x-ui.button 
                    type="submit" 
                    variant="primary" 
                    size="lg" 
                    class="group relative w-full flex justify-center"
                    id="reset-button"
                >
                    <span class="absolute left-0 inset-y-0 flex items-center pl-3">
                        <x-icon name="key" class="h-5 w-5 text-blue-500 group-hover:text-blue-400" />
                    </span>
                    {{ __('auth.reset_password') }}
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
        <div class="mt-6 p-4 bg-amber-50 dark:bg-amber-900/20 border border-amber-200 dark:border-amber-800 rounded-md">
            <div class="flex">
                <div class="flex-shrink-0">
                    <x-icon name="shield-check" class="h-5 w-5 text-amber-400" />
                </div>
                <div class="ml-3">
                    <h3 class="text-sm font-medium text-amber-800 dark:text-amber-200">
                        {{ __('auth.security_tips') }}
                    </h3>
                    <div class="mt-2 text-sm text-amber-700 dark:text-amber-300">
                        <ul class="list-disc pl-5 space-y-1">
                            <li>{{ __('auth.use_unique_password') }}</li>
                            <li>{{ __('auth.include_special_characters') }}</li>
                            <li>{{ __('auth.avoid_personal_info') }}</li>
                            <li>{{ __('auth.consider_password_manager') }}</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const passwordInput = document.getElementById('password');
    const confirmInput = document.getElementById('password_confirmation');
    const strengthIndicator = document.getElementById('password-strength');
    const strengthBar = document.getElementById('strength-bar');
    const strengthText = document.getElementById('strength-text');
    const resetButton = document.getElementById('reset-button');

    // Password strength requirements
    const requirements = {
        length: { element: document.getElementById('req-length'), test: (pwd) => pwd.length >= 8 },
        uppercase: { element: document.getElementById('req-uppercase'), test: (pwd) => /[A-Z]/.test(pwd) },
        lowercase: { element: document.getElementById('req-lowercase'), test: (pwd) => /[a-z]/.test(pwd) },
        number: { element: document.getElementById('req-number'), test: (pwd) => /[0-9]/.test(pwd) }
    };

    function updatePasswordStrength() {
        const password = passwordInput.value;
        
        if (password.length === 0) {
            strengthIndicator.classList.add('hidden');
            return;
        }
        
        strengthIndicator.classList.remove('hidden');
        
        let score = 0;
        let validRequirements = 0;
        
        // Check each requirement
        Object.values(requirements).forEach(req => {
            const isValid = req.test(password);
            const icon = req.element.querySelector('svg');
            
            if (isValid) {
                validRequirements++;
                icon.setAttribute('class', 'h-4 w-4 text-green-500 mr-1');
                icon.innerHTML = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>';
            } else {
                icon.setAttribute('class', 'h-4 w-4 text-red-500 mr-1');
                icon.innerHTML = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>';
            }
        });
        
        // Calculate strength score
        score = (validRequirements / Object.keys(requirements).length) * 100;
        
        // Additional points for special characters and length
        if (/[!@#$%^&*()_+\-=\[\]{};':"\\|,.<>\/?]/.test(password)) score += 10;
        if (password.length >= 12) score += 10;
        if (password.length >= 16) score += 10;
        
        score = Math.min(score, 100);
        
        // Update strength bar and text
        strengthBar.style.width = score + '%';
        
        if (score < 30) {
            strengthBar.className = 'h-2 rounded-full transition-all duration-300 bg-red-500';
            strengthText.textContent = '{{ __("auth.weak") }}';
            strengthText.className = 'text-sm font-medium text-red-600';
        } else if (score < 60) {
            strengthBar.className = 'h-2 rounded-full transition-all duration-300 bg-yellow-500';
            strengthText.textContent = '{{ __("auth.fair") }}';
            strengthText.className = 'text-sm font-medium text-yellow-600';
        } else if (score < 80) {
            strengthBar.className = 'h-2 rounded-full transition-all duration-300 bg-blue-500';
            strengthText.textContent = '{{ __("auth.good") }}';
            strengthText.className = 'text-sm font-medium text-blue-600';
        } else {
            strengthBar.className = 'h-2 rounded-full transition-all duration-300 bg-green-500';
            strengthText.textContent = '{{ __("auth.strong") }}';
            strengthText.className = 'text-sm font-medium text-green-600';
        }
    }

    function validatePasswordMatch() {
        if (confirmInput.value && passwordInput.value !== confirmInput.value) {
            confirmInput.setCustomValidity('{{ __("auth.passwords_do_not_match") }}');
        } else {
            confirmInput.setCustomValidity('');
        }
        
        // Enable/disable submit button based on validation
        const isValid = passwordInput.value.length >= 8 && 
                       passwordInput.value === confirmInput.value &&
                       Object.values(requirements).every(req => req.test(passwordInput.value));
        
        resetButton.disabled = !isValid;
        if (!isValid) {
            resetButton.classList.add('opacity-50', 'cursor-not-allowed');
        } else {
            resetButton.classList.remove('opacity-50', 'cursor-not-allowed');
        }
    }

    // Event listeners
    passwordInput.addEventListener('input', function() {
        updatePasswordStrength();
        validatePasswordMatch();
    });
    
    confirmInput.addEventListener('input', validatePasswordMatch);

    // Auto-focus password input on load
    if (!passwordInput.value) {
        passwordInput.focus();
    }

    // Form submission
    document.querySelector('form').addEventListener('submit', function(e) {
        const submitButton = this.querySelector('button[type="submit"]');
        const originalText = submitButton.innerHTML;
        
        submitButton.disabled = true;
        submitButton.innerHTML = `
            <div class="flex items-center justify-center">
                <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                {{ __('auth.resetting') }}...
            </div>
        `;
    });
});
</script>
@endpush 