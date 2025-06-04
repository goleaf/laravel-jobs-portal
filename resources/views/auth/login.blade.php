<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ __('{{ __('auth.login') }}') }} - {{ config('app.name') }}</title></head>
<body class="bg-gray-100">
    <div class="container mx-auto px-4 mx-auto">
        <div class="flex flex-wrap justify-center">
            <div class="md:w-6/12 flex-1 -lg-4">
                <div class="bg-white shadow rounded-lg overflow-hidden shadow-sm mt-5">
                    <div class="bg-white shadow rounded-lg overflow-hidden -header text-center bg-primary-600 text-white">
                        <h4 class="mb-0">
                            <i class="fas fa-sign-in-alt me-2"></i>
                            {{ __('{{ __('auth.login') }}') }}
                        </h4>
                    </div>
                    <div class="bg-white shadow rounded-lg overflow-hidden -body">
                        @if ($errors->any())
                            <div class="px-4 py-3 rounded-md border border-gray-300 mb-4 bg-red-50 border border-gray-300 border-red-200 text-red-800 p-4 rounded-md mb-4 -dismissible fade show" role="alert">
                                <ul class="mb-0">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                                <button type="button" class="px-4 py-2 rounded font-medium transition-colors -close" data-bs-dismiss="alert"></button>
                            </div>
                        @endif

                        @if (session('status'))
                            <div class="px-4 py-3 rounded-md border border-gray-300 mb-4 bg-green-50 border border-gray-300 border-green-200 text-green-800 p-4 rounded-md mb-4 -dismissible fade show" role="alert">
                                {{ session('status') }}
                                <button type="button" class="px-4 py-2 rounded font-medium transition-colors -close" data-bs-dismiss="alert"></button>
                            </div>
                        @endif

                        <form method="POST" action="{{ route('login.submit') }}">
                            @csrf
                            
                            <div class="mb-3">
                                <label for="email" class="block text-sm font-medium text-gray-700 mb-1">
                                    <i class="fas fa-envelope me-1"></i>
                                    {{ __('{{ __('auth.email_address') }}') }}
                                </label>
                                <input id="email" type="email" 
                                       class="w-full px-3 py-2 border border-gray-300 border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-primary-500 @error("email') is-invalid @enderror" 
                                       name="email" 
                                       value="{{ old('email') }}" 
                                       required 
                                       autocomplete="email" 
                                       autofocus>
                                @error('email')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label for="password" class="block text-sm font-medium text-gray-700 mb-1">
                                    <i class="fas fa-lock me-1"></i>
                                    {{ __('{{ __('auth.password') }}') }}
                                </label>
                                <input id="password" type="password" 
                                       class="w-full px-3 py-2 border border-gray-300 border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-primary-500 @error("password') is-invalid @enderror" 
                                       name="password" 
                                       required 
                                       autocomplete="current-password">
                                @error('password')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>

                            <div class="mb-3 flex items-center">
                                <input class="flex items-center -input" type="checkbox" name="remember" id="remember" 
                                       {{ old('remember') ? 'checked' : '' }}>
                                <label class="flex items-center -label" for="remember">
                                    {{ __('{{ __('auth.remember_me') }}') }}
                                </label>
                            </div>

                            <div class="d-grid">
                                <button type="submit" class="inline-flex items-center px-4 py-2 border border-gray-300 border-transparent text-sm font-medium rounded-md transition duration-150 ease-in-out px-4 py-2 rounded font-medium transition-colors -primary">
                                    <i class="fas fa-sign-in-alt me-1"></i>
                                    {{ __('{{ __('auth.login') }}') }}
                                </button>
                            </div>
                        </form>

                        <div class="mt-3 text-center">
                            @if (Route::has('password.request'))
                                <a class="inline-flex items-center px-4 py-2 border border-gray-300 border-transparent text-sm font-medium rounded-md transition duration-150 ease-in-out px-4 py-2 rounded font-medium transition-colors -link" href="{{ route('password.request') }}">
                                    {{ __('{{ __('auth.forgot_password') }}') }}
                                </a>
                            @endif
                        </div>

                        <hr>
                        
                        <div class="text-center">
                            <p class="mb-2">{{ __("Don't have an account?") }}</p>
                            <a href="{{ route('register') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 border-transparent text-sm font-medium rounded-md transition duration-150 ease-in-out px-4 py-2 rounded font-medium transition-colors -outline-success">
                                <i class="fas fa-user-plus me-1"></i>
                                {{ __('{{ __('auth.register') }}') }}
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div></body>
</html> 