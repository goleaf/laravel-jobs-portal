<x-auth-layout>
    <x-slot:title>{{ __('auth.login') }}</x-slot>

    <div class="min-h-screen flex items-center justify-center bg-gray-50 dark:bg-gray-900 py-12 px-4 sm:px-6 lg:px-8">
        <div class="max-w-md w-full space-y-8">
            <!-- Header -->
            <div class="text-center">
                <x-ui.brand href="{{ route('home') }}" name="{{ config('app.name') }}" class="mx-auto" />
                <x-ui.heading size="lg" class="mt-6">
                    <x-heroicon-o-lock-closed class="w-5 h-5 inline mr-2" />
                    {{ __('auth.welcome_back') }}
                </x-ui.heading>
                <x-ui.text class="mt-2 text-gray-600 dark:text-gray-400">
                    {{ __('auth.sign_in_to_account') }}
                </x-ui.text>
            </div>

            <!-- Flash Messages -->
            @if ($errors->any())
                <div class="bg-red-50 border border-red-200 rounded-md p-4">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <x-heroicon-o-exclamation-circle class="h-5 w-5 text-red-400" />
                        </div>
                        <div class="ml-3">
                            <h3 class="text-sm font-medium text-red-800">
                                {{ __('auth.errors_occurred') }}
                            </h3>
                            <div class="mt-2 text-sm text-red-700">
                                <ul class="list-disc list-inside space-y-1">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            @if (session('status'))
                <div class="bg-green-50 border border-green-200 rounded-md p-4">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <x-heroicon-o-check-circle class="h-5 w-5 text-green-400" />
                        </div>
                        <div class="ml-3">
                            <p class="text-sm text-green-700">
                                {{ session('status') }}
                            </p>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Login Form -->
            <x-ui.card class="mt-8">
                <x-ui.card-body>
                    <form method="POST" action="{{ route('login.submit') }}" class="space-y-6">
                        @csrf
                        
                        <!-- Email Field -->
                        <x-ui.field>
                            <x-ui.label for="email">
                                <x-heroicon-o-envelope class="w-4 h-4 inline mr-1" />
                                {{ __('auth.email_address') }}
                            </x-ui.label>
                            <x-ui.input 
                                id="email"
                                type="email" 
                                name="email" 
                                value="{{ old('email') }}"
                                required 
                                autocomplete="email" 
                                autofocus
                                placeholder="{{ __('auth.email_placeholder') }}"
                                :invalid="$errors->has('email')"
                            />
                            @error('email')
                                <x-ui.error>{{ $message }}</x-ui.error>
                            @enderror
                        </x-ui.field>

                        <!-- Password Field -->
                        <x-ui.field>
                            <x-ui.label for="password">
                                <x-heroicon-o-lock-closed class="w-4 h-4 inline mr-1" />
                                {{ __('auth.password') }}
                            </x-ui.label>
                            <x-ui.input 
                                id="password"
                                type="password" 
                                name="password" 
                                required 
                                autocomplete="current-password"
                                placeholder="{{ __('auth.password_placeholder') }}"
                                :invalid="$errors->has('password')"
                            />
                            @error('password')
                                <x-ui.error>{{ $message }}</x-ui.error>
                            @enderror
                        </x-ui.field>

                        <!-- Remember Me -->
                        <x-ui.checkbox 
                            name="remember" 
                            id="remember"
                            :checked="old('remember')"
                            label="{{ __('auth.remember_me') }}"
                        />

                        <!-- Submit Button -->
                        <x-ui.button 
                            type="submit" 
                            variant="primary" 
                            class="w-full"
                        >
                            <x-heroicon-o-arrow-right-end-on-rectangle class="w-4 h-4 mr-2" />
                            {{ __('auth.sign_in') }}
                        </x-ui.button>
                    </form>

                    <!-- Forgot Password Link -->
                    @if (Route::has('password.request'))
                        <div class="mt-6 text-center">
                            <x-ui.link 
                                href="{{ route('password.request') }}" 
                                class="text-sm text-gray-600 hover:text-gray-900 dark:text-gray-400 dark:hover:text-gray-100"
                            >
                                {{ __('auth.forgot_password') }}
                            </x-ui.link>
                        </div>
                    @endif
                </x-ui.card-body>
            </x-ui.card>

            <!-- Register Link -->
            <div class="text-center">
                <x-ui.text class="text-sm text-gray-600 dark:text-gray-400">
                    {{ __('auth.dont_have_account') }}
                    <x-ui.link 
                        href="{{ route('register') }}" 
                        class="font-medium text-blue-600 hover:text-blue-500 dark:text-blue-400"
                    >
                        {{ __('auth.create_account') }}
                    </x-ui.link>
                </x-ui.text>
            </div>

            <!-- Role Selection (if needed) -->
            @if(Route::has('admin.login'))
                <div class="mt-6">
                    <x-ui.separator />
                    <div class="mt-6 grid grid-cols-1 gap-3">
                        <x-ui.button 
                            href="{{ route('admin.login') }}" 
                            variant="secondary" 
                            class="w-full"
                        >
                            <x-heroicon-o-shield-check class="w-4 h-4 mr-2" />
                            {{ __('auth.admin_login') }}
                        </x-ui.button>
                    </div>
                </div>
            @endif
        </div>
    </div>
</x-auth-layout> 