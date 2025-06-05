<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title') | {{ config('app.name', 'Job Portal') }}</title>
    
    <!-- Favicon -->
    <link rel="shortcut icon" href="/favicon.ico" type="image/x-icon">
    
    <!-- Vite Assets -->
    @vite(['resources/css/app.css', 'resources/css/vendor.css'])
    @yield('page_css')
</head>
<body>
    <!-- Navigation -->
    <nav class="bg-white shadow-sm border-b border border border-gray-300 -gray-300 -gray-200 bg-white shadow -expand-lg bg-white shadow-sm dark bg-indigo-600 -600">
        <div class="container mx-auto px-4 mx-auto px-4 mx-auto px-4 mx-auto">
            <a class="bg-white shadow-sm brand" href="{{ url('/') }}">
                <i class="fas fa-briefcase me-2"></i>{{ config('app.name', 'Job Portal') }}
            </a>
            
            <button class="bg-white shadow-sm toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="bg-white shadow-sm toggler-icon"></span>
            </button>
            
            <div class="collapse bg-white shadow-sm collapse" id="navbarNav">
                <ul class="bg-white shadow-sm nav me-auto">
                    <li class="">
                        <a class="text-gray-600 hover:text-gray-900 px-3 py-2 rounded -md text-sm font-medium {{ Request::is("/') ? 'active' : '' }}" href="{{ url('/') }}">{{ __('nav.home') }}</a>
                    </li>
                    <li class="">
                        <a class="text-gray-600 hover:text-gray-900 px-3 py-2 rounded -md text-sm font-medium {{ Request::is("jobs*') ? 'active' : '' }}" href="{{ route('jobs.index') }}">{{ __('nav.jobs') }}</a>
                    </li>
                    <li class="">
                        <a class="text-gray-600 hover:text-gray-900 px-3 py-2 rounded -md text-sm font-medium {{ Request::is("companies*') ? 'active' : '' }}" href="{{ route('companies.index') }}">{{ __('nav.companies') }}</a>
                    </li>
                    <li class="">
                        <a class="text-gray-600 hover:text-gray-900 px-3 py-2 rounded -md text-sm font-medium {{ Request::is("about-us') ? 'active' : '' }}" href="{{ route('cms.about-us.service') }}">{{ __('nav.about') }}</a>
                    </li>
                    <li class="">
                        <a class="text-gray-600 hover:text-gray-900 px-3 py-2 rounded -md text-sm font-medium {{ Request::is("contact') ? 'active' : '' }}" href="{{ route('contact.submit') }}">{{ __('nav.contact') }}</a>
                    </li>
                </ul>
                
                <ul class="bg-white shadow-sm nav">
                    <li class="">
                        <a class="text-gray-600 hover:text-gray-900 px-3 py-2 rounded -md text-sm font-medium" href="{{ route('login') }}">{{ __('nav.login') }}</a>
                    </li>
                    <li class="">
                        <a class="border border-gray-300 bg-transparent" href="{{ route('register') }}">{{ __('nav.register') }}</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <main>
        @yield('content')
    </main>

    <!-- Footer -->
    <footer class="bg-gray-800 text-light py-5 mt-5">
        <div class="container mx-auto px-4 mx-auto px-4 mx-auto px-4 mx-auto">
            <div class="flex flex-wrap">
                <div class="flex-1 md-4">
                    <h5><i class="fas fa-briefcase me-2"></i>{{ config('app.name', 'Job Portal') }}</h5>
                    <p>Your gateway to exciting career opportunities. Connect with top employers and find your dream job today.</p>
                </div>
                <div class="flex-1 md-2">
                    <h6>Quick Links</h6>
                    <ul class="list-unstyled">
                        <li><a href="{{ url('/') }}" class="text-light text-decoration-none">{{ __('nav.home') }}</a></li>
                        <li><a href="{{ route('jobs.index') }}" class="text-light text-decoration-none">{{ __('nav.jobs') }}</a></li>
                        <li><a href="{{ route('companies.index') }}" class="text-light text-decoration-none">{{ __('nav.companies') }}</a></li>
                        <li><a href="{{ route('cms.about-us.service') }}" class="text-light text-decoration-none">{{ __('nav.about') }}</a></li>
                    </ul>
                </div>
                <div class="flex-1 md-2">
                    <h6>For Job Seekers</h6>
                    <ul class="list-unstyled">
                        <li><a href="{{ route('register') }}" class="text-light text-decoration-none">Create Account</a></li>
                        <li><a href="{{ route('login') }}" class="text-light text-decoration-none">{{ __('nav.login') }}</a></li>
                        <li><a href="{{ route('jobs.index') }}" class="text-light text-decoration-none">Browse Jobs</a></li>
                    </ul>
                </div>
                <div class="flex-1 md-2">
                    <h6>For Employers</h6>
                    <ul class="list-unstyled">
                        <li><a href="{{ route('register') }}" class="text-light text-decoration-none">Post Jobs</a></li>
                        <li><a href="{{ route('login') }}" class="text-light text-decoration-none">Employer Login</a></li>
                    </ul>
                </div>
                <div class="flex-1 md-2">
                    <h6>Support</h6>
                    <ul class="list-unstyled">
                        <li><a href="{{ route('contact.submit') }}" class="text-light text-decoration-none">Contact Us</a></li>
                        <li><a href="#" class="text-light text-decoration-none">Help Center</a></li>
                        <li><a href="#" class="text-light text-decoration-none">Privacy Policy</a></li>
                    </ul>
                </div>
            </div>
            <hr class="my-4">
            <div class="flex flex-wrap items-center">
                <div class="flex-1 md-6">
                    <p class="mb-0">&copy; {{ date('Y') }} {{ config('app.name', 'Job Portal') }}. All rights reserved.</p>
                </div>
                <div class="flex-1 md-6 text-end">
                    <a href="#" class="text-light me-3"><i class="fab fa-facebook"></i></a>
                    <a href="#" class="text-light me-3"><i class="fab fa-twitter"></i></a>
                    <a href="#" class="text-light me-3"><i class="fab fa-linkedin"></i></a>
                    <a href="#" class="text-light"><i class="fab fa-instagram"></i></a>
                </div>
            </div>
        </div>
    </footer>

    <!-- Vite Assets -->
    @vite(['resources/js/app.js', 'resources/js/vendor.js'])
    @yield('page_scripts')
</body>
</html> 