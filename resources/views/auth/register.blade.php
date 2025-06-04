<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ __('Register') }} - {{ config('app.name') }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8 col-lg-6">
                <div class="card shadow-sm mt-5">
                    <div class="card-header text-center bg-success text-white">
                        <h4 class="mb-0">
                            <i class="fas fa-user-plus me-2"></i>
                            {{ __('Register') }}
                        </h4>
                    </div>
                    <div class="card-body">
                        @if ($errors->any())
                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                <ul class="mb-0">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        @endif

                        <form method="POST" action="{{ route('register') }}">
                            @csrf
                            
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="first_name" class="form-label">
                                            <i class="fas fa-user me-1"></i>
                                            {{ __('First Name') }}
                                        </label>
                                        <input id="first_name" type="text" 
                                               class="form-control @error('first_name') is-invalid @enderror" 
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
                                
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="last_name" class="form-label">
                                            <i class="fas fa-user me-1"></i>
                                            {{ __('Last Name') }}
                                        </label>
                                        <input id="last_name" type="text" 
                                               class="form-control @error('last_name') is-invalid @enderror" 
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
                                <label for="email" class="form-label">
                                    <i class="fas fa-envelope me-1"></i>
                                    {{ __('Email Address') }}
                                </label>
                                <input id="email" type="email" 
                                       class="form-control @error('email') is-invalid @enderror" 
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
                                <label for="phone" class="form-label">
                                    <i class="fas fa-phone me-1"></i>
                                    {{ __('Phone Number') }} <small class="text-muted">({{ __('Optional') }})</small>
                                </label>
                                <input id="phone" type="tel" 
                                       class="form-control @error('phone') is-invalid @enderror" 
                                       name="phone" 
                                       value="{{ old('phone') }}" 
                                       autocomplete="tel">
                                @error('phone')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="password" class="form-label">
                                            <i class="fas fa-lock me-1"></i>
                                            {{ __('Password') }}
                                        </label>
                                        <input id="password" type="password" 
                                               class="form-control @error('password') is-invalid @enderror" 
                                               name="password" 
                                               required 
                                               autocomplete="new-password">
                                        @error('password')
                                            <div class="invalid-feedback">
                                                {{ $message }}
                                            </div>
                                        @enderror
                                        <div class="form-text">
                                            {{ __('Password must be at least 8 characters') }}
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="password_confirmation" class="form-label">
                                            <i class="fas fa-lock me-1"></i>
                                            {{ __('Confirm Password') }}
                                        </label>
                                        <input id="password_confirmation" type="password" 
                                               class="form-control" 
                                               name="password_confirmation" 
                                               required 
                                               autocomplete="new-password">
                                    </div>
                                </div>
                            </div>

                            <div class="mb-3 form-check">
                                <input class="form-check-input" type="checkbox" name="terms" id="terms" required>
                                <label class="form-check-label" for="terms">
                                    {{ __('I agree to the') }} 
                                    <a href="{{ route('terms.conditions.list') }}" target="_blank">{{ __('Terms and Conditions') }}</a>
                                    {{ __('and') }}
                                    <a href="{{ route('privacy.policy.list') }}" target="_blank">{{ __('Privacy Policy') }}</a>
                                </label>
                            </div>

                            <div class="d-grid">
                                <button type="submit" class="btn btn-success btn-lg">
                                    <i class="fas fa-user-plus me-1"></i>
                                    {{ __('Create Account') }}
                                </button>
                            </div>
                        </form>

                        <hr>
                        
                        <div class="text-center">
                            <p class="mb-2">{{ __('Already have an account?') }}</p>
                            <a href="{{ route('login') }}" class="btn btn-outline-primary">
                                <i class="fas fa-sign-in-alt me-1"></i>
                                {{ __('Login Here') }}
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html> 