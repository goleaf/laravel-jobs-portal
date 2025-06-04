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
    <nav class="navbar navbar-expand-lg bg-white shadow-sm -dark bg-primary-600">
        <div class="container mx-auto">
            <a class="bg-white shadow-sm -brand" href="{{ route('front.home') }}">
                <i class="fas fa-briefcase me-2"></i>
                {{ config('app.name') }}
            </a>
            <button class="bg-white shadow-sm -toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="bg-white shadow-sm -toggler-icon"></span>
            </button>
            <div class="collapse bg-white shadow-sm -collapse" id="navbarNav">
                <ul class="bg-white shadow-sm -nav ms-auto">
                    <li class="nav-item">
                        <a class="text-gray-600 hover:text-gray-900 px-3 py-2 rounded-md text-sm font-medium" href="{{ route('front.home') }}">{{ __('Home') }}</a>
                    </li>
                    <li class="nav-item">
                        <a class="text-gray-600 hover:text-gray-900 px-3 py-2 rounded-md text-sm font-medium" href="{{ route('jobs.index') }}">{{ __('Jobs') }}</a>
                    </li>
                    <li class="nav-item">
                        <a class="text-gray-600 hover:text-gray-900 px-3 py-2 rounded-md text-sm font-medium" href="{{ route('companies.index') }}">{{ __('Companies') }}</a>
                    </li>
                    <li class="nav-item">
                        <a class="text-gray-600 hover:text-gray-900 px-3 py-2 rounded-md text-sm font-medium active" href="{{ route('about-us') }}">{{ __('About Us') }}</a>
                    </li>
                    <li class="nav-item">
                        <a class="text-gray-600 hover:text-gray-900 px-3 py-2 rounded-md text-sm font-medium" href="{{ route('contact') }}">{{ __('Contact') }}</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="bg-gray-100 py-5">
        <div class="container mx-auto">
            <div class="flex flex-wrap items-center">
                <div class="flex-1 -lg-6">
                    <h1 class="display-4 fw-bold text-primary-600">{{ __('About Our Job Portal') }}</h1>
                    <p class="lead">{{ __('Connecting talented professionals with amazing opportunities worldwide.') }}</p>
                </div>
                <div class="flex-1 -lg-6">
                    <img src="https://via.placeholder.com/500x300" class="img-fluid rounded" alt="About Us">
                </div>
            </div>
        </div>
    </section>

    <!-- Mission Section -->
    <section class="py-5">
        <div class="container mx-auto">
            <div class="flex flex-wrap">
                <div class="flex-1 -lg-8 mx-auto text-center">
                    <h2 class="mb-4">{{ __('Our Mission') }}</h2>
                    <p class="lead">
                        {{ __('We are dedicated to bridging the gap between job seekers and employers, creating meaningful connections that drive careers forward and help businesses thrive.') }}
                    </p>
                </div>
            </div>
        </div>
    </section>

    <!-- Features Section -->
    <section class="bg-gray-100 py-5">
        <div class="container mx-auto">
            <div class="flex flex-wrap">
                <div class="flex-1 -lg-12 text-center mb-5">
                    <h2>{{ __('Why Choose Us?') }}</h2>
                    <p class="lead">{{ __('We offer the best platform for both job seekers and employers') }}</p>
                </div>
            </div>
            <div class="flex flex-wrap">
                <div class="flex-1 -md-4 mb-4">
                    <div class="bg-white shadow rounded-lg overflow-hidden h-full text-center">
                        <div class="bg-white shadow rounded-lg overflow-hidden -body">
                            <div class="mb-3">
                                <i class="fas fa-search fa-3x text-primary-600"></i>
                            </div>
                            <h5 class="bg-white shadow rounded-lg overflow-hidden -title">{{ __('Smart Job Search') }}</h5>
                            <p class="bg-white shadow rounded-lg overflow-hidden -text">{{ __('Advanced search filters and AI-powered recommendations to find the perfect job match.') }}</p>
                        </div>
                    </div>
                </div>
                <div class="flex-1 -md-4 mb-4">
                    <div class="bg-white shadow rounded-lg overflow-hidden h-full text-center">
                        <div class="bg-white shadow rounded-lg overflow-hidden -body">
                            <div class="mb-3">
                                <i class="fas fa-users fa-3x text-primary-600"></i>
                            </div>
                            <h5 class="bg-white shadow rounded-lg overflow-hidden -title">{{ __('Quality Candidates') }}</h5>
                            <p class="bg-white shadow rounded-lg overflow-hidden -text">{{ __('Access to verified, skilled professionals from various industries and backgrounds.') }}</p>
                        </div>
                    </div>
                </div>
                <div class="flex-1 -md-4 mb-4">
                    <div class="bg-white shadow rounded-lg overflow-hidden h-full text-center">
                        <div class="bg-white shadow rounded-lg overflow-hidden -body">
                            <div class="mb-3">
                                <i class="fas fa-shield-alt fa-3x text-primary-600"></i>
                            </div>
                            <h5 class="bg-white shadow rounded-lg overflow-hidden -title">{{ __('Secure Platform') }}</h5>
                            <p class="bg-white shadow rounded-lg overflow-hidden -text">{{ __('Your data is protected with enterprise-level security and privacy measures.') }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Stats Section -->
    <section class="py-5">
        <div class="container mx-auto">
            <div class="flex flex-wrap text-center">
                <div class="flex-1 -md-3 mb-4">
                    <div class="bg-white shadow rounded-lg overflow-hidden border-0">
                        <div class="bg-white shadow rounded-lg overflow-hidden -body">
                            <h3 class="text-primary-600 fw-bold">10,000+</h3>
                            <p class="mb-0">{{ __('Active Jobs') }}</p>
                        </div>
                    </div>
                </div>
                <div class="flex-1 -md-3 mb-4">
                    <div class="bg-white shadow rounded-lg overflow-hidden border-0">
                        <div class="bg-white shadow rounded-lg overflow-hidden -body">
                            <h3 class="text-primary-600 fw-bold">5,000+</h3>
                            <p class="mb-0">{{ __('Companies') }}</p>
                        </div>
                    </div>
                </div>
                <div class="flex-1 -md-3 mb-4">
                    <div class="bg-white shadow rounded-lg overflow-hidden border-0">
                        <div class="bg-white shadow rounded-lg overflow-hidden -body">
                            <h3 class="text-primary-600 fw-bold">50,000+</h3>
                            <p class="mb-0">{{ __('Job Seekers') }}</p>
                        </div>
                    </div>
                </div>
                <div class="flex-1 -md-3 mb-4">
                    <div class="bg-white shadow rounded-lg overflow-hidden border-0">
                        <div class="bg-white shadow rounded-lg overflow-hidden -body">
                            <h3 class="text-primary-600 fw-bold">95%</h3>
                            <p class="mb-0">{{ __('Success Rate') }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Team Section -->
    <section class="bg-gray-100 py-5">
        <div class="container mx-auto">
            <div class="flex flex-wrap">
                <div class="flex-1 -lg-12 text-center mb-5">
                    <h2>{{ __('Our Team') }}</h2>
                    <p class="lead">{{ __('Meet the passionate people behind our success') }}</p>
                </div>
            </div>
            <div class="flex flex-wrap">
                <div class="flex-1 -md-4 mb-4">
                    <div class="bg-white shadow rounded-lg overflow-hidden text-center">
                        <img src="https://via.placeholder.com/200x200" class="bg-white shadow rounded-lg overflow-hidden -img-top mx-auto mt-3" style="width: 150px; height: 150px; border-radius: 50%;" alt="Team Member">
                        <div class="bg-white shadow rounded-lg overflow-hidden -body">
                            <h5 class="bg-white shadow rounded-lg overflow-hidden -title">{{ __('John Doe') }}</h5>
                            <p class="bg-white shadow rounded-lg overflow-hidden -text text-gray-500">{{ __('CEO & Founder') }}</p>
                            <div>
                                <a href="#" class="text-primary-600 me-2"><i class="fab fa-linkedin"></i></a>
                                <a href="#" class="text-primary-600 me-2"><i class="fab fa-twitter"></i></a>
                                <a href="#" class="text-primary-600"><i class="fab fa-github"></i></a>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="flex-1 -md-4 mb-4">
                    <div class="bg-white shadow rounded-lg overflow-hidden text-center">
                        <img src="https://via.placeholder.com/200x200" class="bg-white shadow rounded-lg overflow-hidden -img-top mx-auto mt-3" style="width: 150px; height: 150px; border-radius: 50%;" alt="Team Member">
                        <div class="bg-white shadow rounded-lg overflow-hidden -body">
                            <h5 class="bg-white shadow rounded-lg overflow-hidden -title">{{ __('Jane Smith') }}</h5>
                            <p class="bg-white shadow rounded-lg overflow-hidden -text text-gray-500">{{ __('CTO') }}</p>
                            <div>
                                <a href="#" class="text-primary-600 me-2"><i class="fab fa-linkedin"></i></a>
                                <a href="#" class="text-primary-600 me-2"><i class="fab fa-twitter"></i></a>
                                <a href="#" class="text-primary-600"><i class="fab fa-github"></i></a>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="flex-1 -md-4 mb-4">
                    <div class="bg-white shadow rounded-lg overflow-hidden text-center">
                        <img src="https://via.placeholder.com/200x200" class="bg-white shadow rounded-lg overflow-hidden -img-top mx-auto mt-3" style="width: 150px; height: 150px; border-radius: 50%;" alt="Team Member">
                        <div class="bg-white shadow rounded-lg overflow-hidden -body">
                            <h5 class="bg-white shadow rounded-lg overflow-hidden -title">{{ __('Mike Johnson') }}</h5>
                            <p class="bg-white shadow rounded-lg overflow-hidden -text text-gray-500">{{ __('Head of Marketing') }}</p>
                            <div>
                                <a href="#" class="text-primary-600 me-2"><i class="fab fa-linkedin"></i></a>
                                <a href="#" class="text-primary-600 me-2"><i class="fab fa-twitter"></i></a>
                                <a href="#" class="text-primary-600"><i class="fab fa-github"></i></a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Call to Action -->
    <section class="py-5 bg-primary-600 text-white">
        <div class="container mx-auto text-center">
            <h2 class="mb-4">{{ __('Ready to Get Started?') }}</h2>
            <p class="lead mb-4">{{ __('Join thousands of job seekers and employers who trust our platform') }}</p>
            <div class="flex flex-wrap justify-center">
                <div class="flex-1 -md-6">
                    <a href="{{ route('register') }}" class="btn bg-gray-100 text-gray-800 hover:bg-gray-200 px-4 py-2 rounded font-medium transition-colors -lg me-2">
                        <i class="fas fa-user-plus me-1"></i>
                        {{ __('Join as Job Seeker') }}
                    </a>
                    <a href="{{ route('register') }}" class="btn btn-outline-light px-4 py-2 rounded font-medium transition-colors -lg">
                        <i class="fas fa-building me-1"></i>
                        {{ __('Post a Job') }}
                    </a>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="bg-gray-800 text-white py-4">
        <div class="container mx-auto">
            <div class="flex flex-wrap">
                <div class="flex-1 -md-6">
                    <h5>{{ config('app.name') }}</h5>
                    <p>{{ __('Your trusted partner in career growth and talent acquisition.') }}</p>
                </div>
                <div class="flex-1 -md-6 text-md-end">
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