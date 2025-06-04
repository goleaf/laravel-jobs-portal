<x-auth-layout>
    <x-slot:title>{{ __('auth.login') }}</x-slot>

    <div class="min-h-screen flex items-center justify-center bg-gray-50 dark:bg-gray-900 py-12 px-4 sm:px-6 lg:px-8">
        <div class="max-w-md w-full space-y-8">
            <!-- Header -->
            <div class="text-center">
                <flux:brand href="{{ route('home') }}" name="{{ config('app.name') }}" class="mx-auto" />
                <flux:heading size="lg" class="mt-6">
                    <x-heroicon-o-lock-closed class="w-5 h-5 inline mr-2" />
                    {{ __('auth.welcome_back') }}
                </flux:heading>
                <flux:text class="mt-2 text-gray-600 dark:text-gray-400">
                    {{ __('auth.sign_in_to_account') }}
                </flux:text>
            </div>

            <!-- Flash Messages -->
            @if ($errors->any())
                <flux:callout variant="danger">
                    <flux:callout.icon name="exclamation-circle" />
                    <flux:callout.heading>{{ __('auth.errors_occurred') }}</flux:callout.heading>
                    <flux:callout.description>
                        <ul class="list-disc list-inside space-y-1">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </flux:callout.description>
                </flux:callout>
            @endif

            @if (session('status'))
                <flux:callout variant="success">
                    <flux:callout.icon name="check-circle" />
                    <flux:callout.description>
                        {{ session('status') }}
                    </flux:callout.description>
                </flux:callout>
            @endif

            <!-- Login Form -->
            <flux:card class="mt-8">
                <flux:card.body>
                    <form method="POST" action="{{ route('login.submit') }}" class="space-y-6">
                        @csrf
                        
                        <!-- Email Field -->
                        <flux:field>
                            <flux:label for="email">
                                <x-heroicon-o-envelope class="w-4 h-4 inline mr-1" />
                                {{ __('auth.email_address') }}
                            </flux:label>
                            <flux:input 
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
                                <flux:error>{{ $message }}</flux:error>
                            @enderror
                        </flux:field>

                        <!-- Password Field -->
                        <flux:field>
                            <flux:label for="password">
                                <x-heroicon-o-lock-closed class="w-4 h-4 inline mr-1" />
                                {{ __('auth.password') }}
                            </flux:label>
                            <flux:input 
                                id="password"
                                type="password" 
                                name="password" 
                                required 
                                autocomplete="current-password"
                                placeholder="{{ __('auth.password_placeholder') }}"
                                :invalid="$errors->has('password')"
                            />
                            @error('password')
                                <flux:error>{{ $message }}</flux:error>
                            @enderror
                        </flux:field>

                        <!-- Remember Me -->
                        <flux:checkbox 
                            name="remember" 
                            id="remember"
                            :checked="old('remember')"
                            label="{{ __('auth.remember_me') }}"
                        />

                        <!-- Submit Button -->
                        <flux:button 
                            type="submit" 
                            variant="primary" 
                            class="w-full"
                        >
                            <x-heroicon-o-arrow-right-end-on-rectangle class="w-4 h-4 mr-2" />
                            {{ __('auth.sign_in') }}
                        </flux:button>
                    </form>

                    <!-- Forgot Password Link -->
                    @if (Route::has('password.request'))
                        <div class="mt-6 text-center">
                            <flux:link 
                                href="{{ route('password.request') }}" 
                                class="text-sm text-gray-600 hover:text-gray-900 dark:text-gray-400 dark:hover:text-gray-100"
                            >
                                {{ __('auth.forgot_password') }}
                            </flux:link>
                        </div>
                    @endif
                </flux:card.body>
            </flux:card>

            <!-- Register Link -->
            <div class="text-center">
                <flux:text class="text-sm text-gray-600 dark:text-gray-400">
                    {{ __('auth.dont_have_account') }}
                    <flux:link 
                        href="{{ route('register') }}" 
                        class="font-medium text-blue-600 hover:text-blue-500 dark:text-blue-400"
                    >
                        {{ __('auth.create_account') }}
                    </flux:link>
                </flux:text>
            </div>

            <!-- Role Selection (if needed) -->
            @if(Route::has('admin.login'))
                <div class="mt-6">
                    <flux:separator />
                    <div class="mt-6 grid grid-cols-1 gap-3">
                        <flux:button 
                            href="{{ route('admin.login') }}" 
                            variant="secondary" 
                            class="w-full"
                        >
                            <x-heroicon-o-shield-check class="w-4 h-4 mr-2" />
                            {{ __('auth.admin_login') }}
                        </flux:button>
                    </div>
                </div>
            @endif
        </div>
    </div>
</x-auth-layout> 