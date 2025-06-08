<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{  __(' __('auth.register') ')  }} - {{ config('app.name') }}</title></head>
<body class="bg-gray-100">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mx-auto px-4 mx-auto px-4 mx-auto">
        <div class="flex flex-wrap justify-center">
            <div class="md:w-8/12 flex-1 lg-6">
                <div class="bg-white shadow rounded -lg overflow-hidden shadow-sm mt-5">
                    <div class="bg-white shadow rounded -lg overflow-hidden header text-center bg-green-600 text-white">
                        <h4 class="mb-0">
                            <i class="fas fa-user-plus me-2"></i>
                            {{  __(' __('auth.register') ')  }}
                        </h4>
                    </div>
                    <div class="bg-white shadow rounded -lg overflow-hidden body">
                        @if ($errors->any())
                            <div class="px-4 py-3 rounded-md border border-gray-300 mb-4 bg-red-50 border border-gray-300 border border-gray-300 -red-200 text-red-800 p-4 rounded -md mb-4 dismissible fade show" role="rounded-md p-4">
                                <ul class="mb-0">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                                <button type="button" class="px-4 py-2 rounded font-medium transition-colors close" data-bs-dismiss="rounded-md p-4"></button>
                            </div>
                        @endif

                        <form method="POST" action="{{ route('register') }}">
                            @csrf
                            
                            <div class="flex flex-wrap">
                                <div class="flex-1 md-6">
                                    <div class="mb-3">
                                        <label for="first_name" class="block text-sm font-medium text-gray-700 mb-1">
                                            <i class="fas fa-user me-1"></i>
                                            {{  __(' __('auth.first_name') ')  }}
                                        </label>
                                        <input id="first_name" type="text" 
                                               class="w-full px-3 py-2 border border-gray-300 border border-gray-300 -gray-300 rounded -md focus:outline-none focus:ring-2 focus:ring-primary-500 @error("first_name') is-invalid @enderror" 
                                               name="first_name" 
                                               value="{{ old('first_name') }}" 
                                               required 
                                               autocomplete="given-name" 
                                               autofocus>
                                        @error('first_name')
                                            <div class="invalid-feedback">
                                                {{ $message }}
                                            </div>
                                        @enderror
                                    </div>
                                </div>
                                
                                <div class="flex-1 md-6">
                                    <div class="mb-3">
                                        <label for="last_name" class="block text-sm font-medium text-gray-700 mb-1">
                                            <i class="fas fa-user me-1"></i>
                                            {{  __(' __('auth.last_name') ')  }}
                                        </label>
                                        <input id="last_name" type="text" 
                                               class="w-full px-3 py-2 border border-gray-300 border border-gray-300 -gray-300 rounded -md focus:outline-none focus:ring-2 focus:ring-primary-500 @error("last_name') is-invalid @enderror" 
                                               name="last_name" 
                                               value="{{ old('last_name') }}" 
                                               autocomplete="family-name">
                                        @error('last_name')
                                            <div class="invalid-feedback">
                                                {{ $message }}
                                            </div>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="email" class="block text-sm font-medium text-gray-700 mb-1">
                                    <i class="fas fa-envelope me-1"></i>
                                    {{  __(' __('auth.email_address') ')  }}
                                </label>
                                <input id="email" type="email" 
                                       class="w-full px-3 py-2 border border-gray-300 border border-gray-300 -gray-300 rounded -md focus:outline-none focus:ring-2 focus:ring-primary-500 @error("email') is-invalid @enderror" 
                                       name="email" 
                                       value="{{ old('email') }}" 
                                       required 
                                       autocomplete="email">
                                @error('email')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label for="phone" class="block text-sm font-medium text-gray-700 mb-1">
                                    <i class="fas fa-phone me-1"></i>
                                    {{  __(' __('auth.phone_number') ')  }} <small class="text-gray-500">({{ __('Optional') }})</small>
                                </label>
                                <input id="phone" type="tel" 
                                       class="w-full px-3 py-2 border border-gray-300 border border-gray-300 -gray-300 rounded -md focus:outline-none focus:ring-2 focus:ring-primary-500 @error("phone') is-invalid @enderror" 
                                       name="phone" 
                                       value="{{ old('phone') }}" 
                                       autocomplete="tel">
                                @error('phone')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>

                            <div class="flex flex-wrap">
                                <div class="flex-1 md-6">
                                    <div class="mb-3">
                                        <label for="password" class="block text-sm font-medium text-gray-700 mb-1">
                                            <i class="fas fa-lock me-1"></i>
                                            {{  __(' __('auth.password') ')  }}
                                        </label>
                                        <input id="password" type="password" 
                                               class="w-full px-3 py-2 border border-gray-300 border border-gray-300 -gray-300 rounded -md focus:outline-none focus:ring-2 focus:ring-primary-500 @error("password') is-invalid @enderror" 
                                               name="password" 
                                               required 
                                               autocomplete="new-password">
                                        @error('password')
                                            <div class="invalid-feedback">
                                                {{ $message }}
                                            </div>
                                        @enderror
                                        <div class="text-xs text-gray-500 mt-1">
                                            {{ __('Password must be at least 8 characters') }}
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="flex-1 md-6">
                                    <div class="mb-3">
                                        <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-1">
                                            <i class="fas fa-lock me-1"></i>
                                            {{  __(' __('auth.confirm_password') ')  }}
                                        </label>
                                        <input id="password_confirmation" type="password" 
                                               class="w-full px-3 py-2 border border-gray-300 border border-gray-300 -gray-300 rounded -md focus:outline-none focus:ring-2 focus:ring-primary-500" 
                                               name="password_confirmation" 
                                               required 
                                               autocomplete="new-password">
                                    </div>
                                </div>
                            </div>

                            <div class="mb-3 flex items-center">
                                <input class="flex items-center input" type="checkbox" name="terms" id="terms" required>
                                <label class="flex items-center label" for="terms">
                                    {{ __('I agree to the') }} 
                                    <a href="{{ route('terms.conditions.list.index') }}" target="_blank">{{ __('Terms and Conditions') }}</a>
                                    {{ __('and') }}
                                    <a href="{{ route('privacy.policy.list.index') }}" target="_blank">{{ __('Privacy Policy') }}</a>
                                </label>
                            </div>

                            <div class="d-grid">
                                <button type="submit" class="border border-gray-300 bg-transparent">
                                    <i class="fas fa-user-plus me-1"></i>
                                    {{  __(' __('auth.create_account') ')  }}
                                </button>
                            </div>
                        </form>

                        <hr>
                        
                        <div class="text-center">
                            <p class="mb-2">{{ __('Already have an account?') }}</p>
                            <a href="{{ route('login') }}" class="border border-gray-300 bg-transparent">
                                <i class="fas fa-sign-in-alt me-1"></i>
                                {{ __('Login Here') }}
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div></body>
</html> 