<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ __('{{ __('auth.admin_login') }}') }} - {{ config('app.name') }}</title><style>
        .admin-login-bg {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
        }
        .login-card {
            backdrop-filter: blur(10px);
            background: rgba(255, 255, 255, 0.95);
            border: none;
            box-shadow: 0 8px 32px rgba(31, 38, 135, 0.37);
        }
        .admin-badge {
            background: linear-gradient(45deg, #ff6b6b, #ee5a24);
            color: white;
            padding: 0.5rem 1rem;
            border-radius: 20px;
            font-size: 0.875rem;
            font-weight: 600;
            display: inline-block;
            margin-bottom: 1rem;
        }
    </style>
</head>
<body class="admin-login-bg">
    <div class="container mx-auto px-4 mx-auto">
        <div class="flex flex-wrap justify-center items-center" style="min-height: 100vh;">
            <div class="md:w-6/12 flex-1 -lg-4">
                <div class="text-center mb-4">
                    <a href="{{ route('front.home') }}" class="text-white text-decoration-none">
                        <i class="fas fa-briefcase fa-3x mb-3"></i>
                        <h2 class="text-white">{{ config('app.name') }}</h2>
                    </a>
                </div>

                <div class="bg-white rounded-lg shadow-md border border-gray-300 border-gray-200 login- bg-white shadow rounded-lg overflow-hidden">
                    <div class="bg-white shadow rounded-lg overflow-hidden -body p-4">
                        <div class="text-center mb-4">
                            <div class="admin-badge">
                                <i class="fas fa-shield-alt me-1"></i>
                                {{ __('ADMIN ACCESS') }}
                            </div>
                            <h4 class="text-gray-900 mb-0">{{ __('Admin Portal') }}</h4>
                            <p class="text-gray-500">{{ __('Secure administrator login') }}</p>
                        </div>

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
                            <div class="px-4 py-3 rounded-md border border-gray-300 mb-4 bg-blue-50 border border-gray-300 border-blue-200 text-blue-800 p-4 rounded-md mb-4 -dismissible fade show" role="alert">
                                {{ session('status') }}
                                <button type="button" class="px-4 py-2 rounded font-medium transition-colors -close" data-bs-dismiss="alert"></button>
                            </div>
                        @endif

                        <form method="POST" action="{{ route('login.submit') }}">
                            @csrf
                            <input type="hidden" name="user_type" value="admin">
                            
                            <div class="mb-3">
                                <label for="email" class="block text-sm font-medium text-gray-700 mb-1">
                                    <i class="fas fa-envelope me-1 text-primary-600"></i>
                                    {{ __('Administrator Email') }}
                                </label>
                                <input id="email" type="email" 
                                       class="w-full px-3 py-2 border border-gray-300 border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 w-full px-3 py-2 border border-gray-300 border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-primary-500 -lg @error("email') is-invalid @enderror" 
                                       name="email" 
                                       value="{{ old('email') }}" 
                                       required 
                                       autocomplete="email" 
                                       autofocus
                                       placeholder="{{ __('Enter your admin email') }}">
                                @error('email')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label for="password" class="block text-sm font-medium text-gray-700 mb-1">
                                    <i class="fas fa-lock me-1 text-primary-600"></i>
                                    {{ __('{{ __('auth.password') }}') }}
                                </label>
                                <div class="flex">
                                    <input id="password" type="password" 
                                           class="w-full px-3 py-2 border border-gray-300 border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 w-full px-3 py-2 border border-gray-300 border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-primary-500 -lg @error("password') is-invalid @enderror" 
                                           name="password" 
                                           required 
                                           autocomplete="current-password"
                                           placeholder="{{ __('Enter your password') }}">
                                    <button class="inline-flex items-center px-4 py-2 border border-gray-300 border-transparent text-sm font-medium rounded-md transition duration-150 ease-in-out px-4 py-2 rounded font-medium transition-colors -outline-secondary" type="button" onclick="togglePassword()">
                                        <i class="fas fa-eye" id="passwordToggle"></i>
                                    </button>
                                </div>
                                @error('password')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>

                            <div class="mb-4 flex items-center">
                                <input class="flex items-center -input" type="checkbox" name="remember" id="remember" 
                                       {{ old('remember') ? 'checked' : '' }}>
                                <label class="flex items-center -label" for="remember">
                                    {{ __('Keep me signed in') }}
                                </label>
                            </div>

                            <div class="d-grid mb-3">
                                <button type="submit" class="inline-flex items-center px-4 py-2 border border-gray-300 border-transparent text-sm font-medium rounded-md transition duration-150 ease-in-out bg-primary-600 text-white hover: bg-primary-600 -700 px-4 py-2 rounded font-medium transition-colors -lg">
                                    <i class="fas fa-sign-in-alt me-1"></i>
                                    {{ __('Access Admin Panel') }}
                                </button>
                            </div>
                        </form>

                        @if (Route::has('password.request'))
                            <div class="text-center">
                                <a class="inline-flex items-center px-4 py-2 border border-gray-300 border-transparent text-sm font-medium rounded-md transition duration-150 ease-in-out px-4 py-2 rounded font-medium transition-colors -link text-decoration-none" href="{{ route('password.request') }}">
                                    <i class="fas fa-key me-1"></i>
                                    {{ __('Forgot Password?') }}
                                </a>
                            </div>
                        @endif

                        <hr class="my-4">
                        
                        <div class="text-center">
                            <div class="flex justify-center items-center text-gray-500">
                                <i class="fas fa-shield-check text-green-600 me-2"></i>
                                <small>{{ __('Secured by SSL encryption') }}</small>
                            </div>
                            <div class="mt-2">
                                <a href="{{ route('front.home') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 border-transparent text-sm font-medium rounded-md transition duration-150 ease-in-out border-gray-600 text-gray-600 hover:bg-gray-600 hover:text-white px-4 py-2 rounded font-medium transition-colors -sm">
                                    <i class="fas fa-arrow-left me-1"></i>
                                    {{ __('Back to Website') }}
                                </a>
                            </div>
                        </div>
                    </div>
                    
                    <div class="bg-white shadow rounded-lg overflow-hidden -footer bg-gray-100 text-center">
                        <small class="text-gray-500">
                            {{ __('Admin access is restricted to authorized personnel only') }}
                        </small>
                    </div>
                </div>

                <div class="text-center mt-4">
                    <small class="text-white-50">
                        &copy; {{ date('Y') }} {{ config('app.name') }}. {{ __('All rights reserved.') }}
                    </small>
                </div>
            </div>
        </div>
    </div><script>
        function togglePassword() {
            const passwordInput = document.getElementById('password');
            const passwordToggle = document.getElementById('passwordToggle');
            
            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                passwordToggle.className = 'fas fa-eye-slash';
            } else {
                passwordInput.type = 'password';
                passwordToggle.className = 'fas fa-eye';
            }
        }

        // Auto-dismiss alerts after 5 seconds
        setTimeout(function() {
            const alerts = document.querySelectorAll('.alert');
            alerts.forEach(function(alert) {
                const bsAlert = new bootstrap.Alert(alert);
                bsAlert.close();
            });
        }, 5000);
    </script>
</body>
</html> 