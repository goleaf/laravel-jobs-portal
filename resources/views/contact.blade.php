<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ __('Contact Us') }} - {{ config('app.name') }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
        <div class="container">
            <a class="navbar-brand" href="{{ route('front.home') }}">
                <i class="fas fa-briefcase me-2"></i>
                {{ config('app.name') }}
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('front.home') }}">{{ __('Home') }}</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('jobs.index') }}">{{ __('Jobs') }}</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('companies.index') }}">{{ __('Companies') }}</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('about-us') }}">{{ __('About Us') }}</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" href="{{ route('contact') }}">{{ __('Contact') }}</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="bg-light py-5">
        <div class="container">
            <div class="row">
                <div class="col-lg-12 text-center">
                    <h1 class="display-4 fw-bold text-primary">{{ __('Contact Us') }}</h1>
                    <p class="lead">{{ __('We\'d love to hear from you. Send us a message and we\'ll respond as soon as possible.') }}</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Contact Form and Info Section -->
    <section class="py-5">
        <div class="container">
            @if (session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="fas fa-check-circle me-2"></i>
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            <div class="row">
                <!-- Contact Form -->
                <div class="col-lg-8">
                    <div class="card shadow-sm">
                        <div class="card-header bg-primary text-white">
                            <h4 class="mb-0">
                                <i class="fas fa-envelope me-2"></i>
                                {{ __('Send us a Message') }}
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

                            <form method="POST" action="{{ route('contact.submit') }}">
                                @csrf
                                
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="first_name" class="form-label">
                                                <i class="fas fa-user me-1"></i>
                                                {{ __('First Name') }} <span class="text-danger">*</span>
                                            </label>
                                            <input id="first_name" type="text" 
                                                   class="form-control @error('first_name') is-invalid @enderror" 
                                                   name="first_name" 
                                                   value="{{ old('first_name') }}" 
                                                   required>
                                            @error('first_name')
                                                <div class="invalid-feedback">{{ $message }}</div>
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
                                                   value="{{ old('last_name') }}">
                                            @error('last_name')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="email" class="form-label">
                                                <i class="fas fa-envelope me-1"></i>
                                                {{ __('Email Address') }} <span class="text-danger">*</span>
                                            </label>
                                            <input id="email" type="email" 
                                                   class="form-control @error('email') is-invalid @enderror" 
                                                   name="email" 
                                                   value="{{ old('email') }}" 
                                                   required>
                                            @error('email')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="phone" class="form-label">
                                                <i class="fas fa-phone me-1"></i>
                                                {{ __('Phone Number') }}
                                            </label>
                                            <input id="phone" type="tel" 
                                                   class="form-control @error('phone') is-invalid @enderror" 
                                                   name="phone" 
                                                   value="{{ old('phone') }}">
                                            @error('phone')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label for="subject" class="form-label">
                                        <i class="fas fa-tag me-1"></i>
                                        {{ __('Subject') }} <span class="text-danger">*</span>
                                    </label>
                                    <input id="subject" type="text" 
                                           class="form-control @error('subject') is-invalid @enderror" 
                                           name="subject" 
                                           value="{{ old('subject') }}" 
                                           required>
                                    @error('subject')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label for="message" class="form-label">
                                        <i class="fas fa-comment me-1"></i>
                                        {{ __('Message') }} <span class="text-danger">*</span>
                                    </label>
                                    <textarea id="message" 
                                              class="form-control @error('message') is-invalid @enderror" 
                                              name="message" 
                                              rows="6" 
                                              required>{{ old('message') }}</textarea>
                                    @error('message')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <div class="form-text">{{ __('Please provide as much detail as possible.') }}</div>
                                </div>

                                <div class="d-grid">
                                    <button type="submit" class="btn btn-primary btn-lg">
                                        <i class="fas fa-paper-plane me-1"></i>
                                        {{ __('Send Message') }}
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- Contact Information -->
                <div class="col-lg-4">
                    <div class="row">
                        <!-- Contact Details -->
                        <div class="col-12 mb-4">
                            <div class="card h-100">
                                <div class="card-body">
                                    <h5 class="card-title">
                                        <i class="fas fa-info-circle text-primary me-2"></i>
                                        {{ __('Contact Information') }}
                                    </h5>
                                    
                                    <div class="mb-3">
                                        <div class="d-flex align-items-start">
                                            <i class="fas fa-map-marker-alt text-primary me-3 mt-1"></i>
                                            <div>
                                                <h6 class="mb-1">{{ __('Address') }}</h6>
                                                <p class="text-muted mb-0">
                                                    123 Business Street<br>
                                                    Suite 100<br>
                                                    City, State 12345
                                                </p>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="mb-3">
                                        <div class="d-flex align-items-center">
                                            <i class="fas fa-phone text-primary me-3"></i>
                                            <div>
                                                <h6 class="mb-1">{{ __('Phone') }}</h6>
                                                <p class="text-muted mb-0">+1 (555) 123-4567</p>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="mb-3">
                                        <div class="d-flex align-items-center">
                                            <i class="fas fa-envelope text-primary me-3"></i>
                                            <div>
                                                <h6 class="mb-1">{{ __('Email') }}</h6>
                                                <p class="text-muted mb-0">contact@jobportal.com</p>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="mb-0">
                                        <div class="d-flex align-items-center">
                                            <i class="fas fa-clock text-primary me-3"></i>
                                            <div>
                                                <h6 class="mb-1">{{ __('Business Hours') }}</h6>
                                                <p class="text-muted mb-0">
                                                    {{ __('Monday - Friday: 9:00 AM - 6:00 PM') }}<br>
                                                    {{ __('Saturday: 10:00 AM - 4:00 PM') }}<br>
                                                    {{ __('Sunday: Closed') }}
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Quick Help -->
                        <div class="col-12 mb-4">
                            <div class="card bg-light">
                                <div class="card-body">
                                    <h5 class="card-title">
                                        <i class="fas fa-question-circle text-primary me-2"></i>
                                        {{ __('Need Quick Help?') }}
                                    </h5>
                                    <p class="card-text">{{ __('Check out our frequently asked questions or browse our help center.') }}</p>
                                    <div class="d-grid gap-2">
                                        <a href="#" class="btn btn-outline-primary btn-sm">
                                            <i class="fas fa-question me-1"></i>
                                            {{ __('FAQ') }}
                                        </a>
                                        <a href="#" class="btn btn-outline-info btn-sm">
                                            <i class="fas fa-life-ring me-1"></i>
                                            {{ __('Help Center') }}
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Social Media -->
                        <div class="col-12">
                            <div class="card">
                                <div class="card-body text-center">
                                    <h5 class="card-title">{{ __('Follow Us') }}</h5>
                                    <div class="d-flex justify-content-center gap-3">
                                        <a href="#" class="btn btn-outline-primary btn-sm">
                                            <i class="fab fa-facebook"></i>
                                        </a>
                                        <a href="#" class="btn btn-outline-info btn-sm">
                                            <i class="fab fa-twitter"></i>
                                        </a>
                                        <a href="#" class="btn btn-outline-primary btn-sm">
                                            <i class="fab fa-linkedin"></i>
                                        </a>
                                        <a href="#" class="btn btn-outline-danger btn-sm">
                                            <i class="fab fa-instagram"></i>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Map Section (Optional) -->
    <section class="py-5 bg-light">
        <div class="container">
            <div class="row">
                <div class="col-lg-12 text-center">
                    <h2 class="mb-4">{{ __('Find Us') }}</h2>
                    <div class="embed-responsive embed-responsive-16by9">
                        <div class="bg-secondary d-flex align-items-center justify-content-center" style="height: 400px; border-radius: 10px;">
                            <div class="text-center text-white">
                                <i class="fas fa-map-marked-alt fa-3x mb-3"></i>
                                <h5>{{ __('Interactive Map') }}</h5>
                                <p>{{ __('Map integration can be added here') }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="bg-dark text-white py-4">
        <div class="container">
            <div class="row">
                <div class="col-md-6">
                    <h5>{{ config('app.name') }}</h5>
                    <p>{{ __('Your trusted partner in career growth and talent acquisition.') }}</p>
                </div>
                <div class="col-md-6 text-md-end">
                    <div class="social-links">
                        <a href="#" class="text-white me-3"><i class="fab fa-facebook"></i></a>
                        <a href="#" class="text-white me-3"><i class="fab fa-twitter"></i></a>
                        <a href="#" class="text-white me-3"><i class="fab fa-linkedin"></i></a>
                        <a href="#" class="text-white"><i class="fab fa-instagram"></i></a>
                    </div>
                    <div class="mt-2">
                        <a href="{{ route('privacy.policy.list') }}" class="text-white-50 me-3">{{ __('Privacy Policy') }}</a>
                        <a href="{{ route('terms.conditions.list') }}" class="text-white-50">{{ __('Terms of Service') }}</a>
                    </div>
                </div>
            </div>
            <hr class="my-3">
            <div class="text-center">
                <p class="mb-0">&copy; {{ date('Y') }} {{ config('app.name') }}. {{ __('All rights reserved.') }}</p>
            </div>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html> 