<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ __('About Us') }} - {{ config('app.name') }}</title>
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
                        <a class="nav-link active" href="{{ route('about-us') }}">{{ __('About Us') }}</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('contact') }}">{{ __('Contact') }}</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="bg-light py-5">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-6">
                    <h1 class="display-4 fw-bold text-primary">{{ __('About Our Job Portal') }}</h1>
                    <p class="lead">{{ __('Connecting talented professionals with amazing opportunities worldwide.') }}</p>
                </div>
                <div class="col-lg-6">
                    <img src="https://via.placeholder.com/500x300" class="img-fluid rounded" alt="About Us">
                </div>
            </div>
        </div>
    </section>

    <!-- Mission Section -->
    <section class="py-5">
        <div class="container">
            <div class="row">
                <div class="col-lg-8 mx-auto text-center">
                    <h2 class="mb-4">{{ __('Our Mission') }}</h2>
                    <p class="lead">
                        {{ __('We are dedicated to bridging the gap between job seekers and employers, creating meaningful connections that drive careers forward and help businesses thrive.') }}
                    </p>
                </div>
            </div>
        </div>
    </section>

    <!-- Features Section -->
    <section class="bg-light py-5">
        <div class="container">
            <div class="row">
                <div class="col-lg-12 text-center mb-5">
                    <h2>{{ __('Why Choose Us?') }}</h2>
                    <p class="lead">{{ __('We offer the best platform for both job seekers and employers') }}</p>
                </div>
            </div>
            <div class="row">
                <div class="col-md-4 mb-4">
                    <div class="card h-100 text-center">
                        <div class="card-body">
                            <div class="mb-3">
                                <i class="fas fa-search fa-3x text-primary"></i>
                            </div>
                            <h5 class="card-title">{{ __('Smart Job Search') }}</h5>
                            <p class="card-text">{{ __('Advanced search filters and AI-powered recommendations to find the perfect job match.') }}</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4 mb-4">
                    <div class="card h-100 text-center">
                        <div class="card-body">
                            <div class="mb-3">
                                <i class="fas fa-users fa-3x text-primary"></i>
                            </div>
                            <h5 class="card-title">{{ __('Quality Candidates') }}</h5>
                            <p class="card-text">{{ __('Access to verified, skilled professionals from various industries and backgrounds.') }}</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4 mb-4">
                    <div class="card h-100 text-center">
                        <div class="card-body">
                            <div class="mb-3">
                                <i class="fas fa-shield-alt fa-3x text-primary"></i>
                            </div>
                            <h5 class="card-title">{{ __('Secure Platform') }}</h5>
                            <p class="card-text">{{ __('Your data is protected with enterprise-level security and privacy measures.') }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Stats Section -->
    <section class="py-5">
        <div class="container">
            <div class="row text-center">
                <div class="col-md-3 mb-4">
                    <div class="card border-0">
                        <div class="card-body">
                            <h3 class="text-primary fw-bold">10,000+</h3>
                            <p class="mb-0">{{ __('Active Jobs') }}</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 mb-4">
                    <div class="card border-0">
                        <div class="card-body">
                            <h3 class="text-primary fw-bold">5,000+</h3>
                            <p class="mb-0">{{ __('Companies') }}</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 mb-4">
                    <div class="card border-0">
                        <div class="card-body">
                            <h3 class="text-primary fw-bold">50,000+</h3>
                            <p class="mb-0">{{ __('Job Seekers') }}</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 mb-4">
                    <div class="card border-0">
                        <div class="card-body">
                            <h3 class="text-primary fw-bold">95%</h3>
                            <p class="mb-0">{{ __('Success Rate') }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Team Section -->
    <section class="bg-light py-5">
        <div class="container">
            <div class="row">
                <div class="col-lg-12 text-center mb-5">
                    <h2>{{ __('Our Team') }}</h2>
                    <p class="lead">{{ __('Meet the passionate people behind our success') }}</p>
                </div>
            </div>
            <div class="row">
                <div class="col-md-4 mb-4">
                    <div class="card text-center">
                        <img src="https://via.placeholder.com/200x200" class="card-img-top mx-auto mt-3" style="width: 150px; height: 150px; border-radius: 50%;" alt="Team Member">
                        <div class="card-body">
                            <h5 class="card-title">{{ __('John Doe') }}</h5>
                            <p class="card-text text-muted">{{ __('CEO & Founder') }}</p>
                            <div>
                                <a href="#" class="text-primary me-2"><i class="fab fa-linkedin"></i></a>
                                <a href="#" class="text-primary me-2"><i class="fab fa-twitter"></i></a>
                                <a href="#" class="text-primary"><i class="fab fa-github"></i></a>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4 mb-4">
                    <div class="card text-center">
                        <img src="https://via.placeholder.com/200x200" class="card-img-top mx-auto mt-3" style="width: 150px; height: 150px; border-radius: 50%;" alt="Team Member">
                        <div class="card-body">
                            <h5 class="card-title">{{ __('Jane Smith') }}</h5>
                            <p class="card-text text-muted">{{ __('CTO') }}</p>
                            <div>
                                <a href="#" class="text-primary me-2"><i class="fab fa-linkedin"></i></a>
                                <a href="#" class="text-primary me-2"><i class="fab fa-twitter"></i></a>
                                <a href="#" class="text-primary"><i class="fab fa-github"></i></a>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4 mb-4">
                    <div class="card text-center">
                        <img src="https://via.placeholder.com/200x200" class="card-img-top mx-auto mt-3" style="width: 150px; height: 150px; border-radius: 50%;" alt="Team Member">
                        <div class="card-body">
                            <h5 class="card-title">{{ __('Mike Johnson') }}</h5>
                            <p class="card-text text-muted">{{ __('Head of Marketing') }}</p>
                            <div>
                                <a href="#" class="text-primary me-2"><i class="fab fa-linkedin"></i></a>
                                <a href="#" class="text-primary me-2"><i class="fab fa-twitter"></i></a>
                                <a href="#" class="text-primary"><i class="fab fa-github"></i></a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Call to Action -->
    <section class="py-5 bg-primary text-white">
        <div class="container text-center">
            <h2 class="mb-4">{{ __('Ready to Get Started?') }}</h2>
            <p class="lead mb-4">{{ __('Join thousands of job seekers and employers who trust our platform') }}</p>
            <div class="row justify-content-center">
                <div class="col-md-6">
                    <a href="{{ route('register') }}" class="btn btn-light btn-lg me-2">
                        <i class="fas fa-user-plus me-1"></i>
                        {{ __('Join as Job Seeker') }}
                    </a>
                    <a href="{{ route('register') }}" class="btn btn-outline-light btn-lg">
                        <i class="fas fa-building me-1"></i>
                        {{ __('Post a Job') }}
                    </a>
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