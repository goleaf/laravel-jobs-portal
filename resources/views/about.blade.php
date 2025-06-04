@extends('layouts.simple')

@section('title', __('About Us'))

@section('content')
<div class="container-fluid">
    <!-- Hero Section -->
    <div class="row bg-primary text-white py-5 mb-0">
        <div class="col-12 text-center">
            <h1 class="display-4 fw-bold mb-4">{{ __('About JobPortal') }}</h1>
            <p class="lead">{{ __('Connecting talent with opportunity since 2020') }}</p>
        </div>
    </div>

    <!-- Mission Section -->
    <div class="container py-5">
        <div class="row align-items-center mb-5">
            <div class="col-lg-6">
                <h2 class="fw-bold mb-4">{{ __('Our Mission') }}</h2>
                <p class="lead text-muted mb-4">
                    {{ __('We believe that everyone deserves a job they love. Our mission is to connect talented individuals with amazing companies and help build careers that matter.') }}
                </p>
                <p>
                    {{ __('Founded with the vision of making job searching and hiring more efficient, transparent, and successful for everyone involved, we have grown to become a trusted platform for thousands of job seekers and employers worldwide.') }}
                </p>
                <div class="row mt-4">
                    <div class="col-md-6">
                        <div class="d-flex align-items-center mb-3">
                            <i class="fas fa-check-circle text-success me-3 fs-4"></i>
                            <span>{{ __('Quality job matches') }}</span>
                        </div>
                        <div class="d-flex align-items-center mb-3">
                            <i class="fas fa-check-circle text-success me-3 fs-4"></i>
                            <span>{{ __('Trusted by top employers') }}</span>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="d-flex align-items-center mb-3">
                            <i class="fas fa-check-circle text-success me-3 fs-4"></i>
                            <span>{{ __('Advanced matching technology') }}</span>
                        </div>
                        <div class="d-flex align-items-center mb-3">
                            <i class="fas fa-check-circle text-success me-3 fs-4"></i>
                            <span>{{ __('24/7 support') }}</span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="bg-light rounded p-4 text-center">
                    <i class="fas fa-handshake fa-5x text-primary mb-3"></i>
                    <h4>{{ __('Building Connections') }}</h4>
                    <p class="text-muted">{{ __('Every day, we help people find their dream jobs and companies find their perfect candidates.') }}</p>
                </div>
            </div>
        </div>

        <!-- Stats Section -->
        <div class="row py-5 bg-light rounded mb-5">
            <div class="col-12">
                <h2 class="text-center fw-bold mb-5">{{ __('Our Impact') }}</h2>
                <div class="row text-center">
                    <div class="col-md-3 mb-4">
                        <div class="h-100">
                            <i class="fas fa-briefcase fa-3x text-primary mb-3"></i>
                            <h3 class="fw-bold text-primary">50,000+</h3>
                            <p class="text-muted">{{ __('Jobs Posted') }}</p>
                        </div>
                    </div>
                    <div class="col-md-3 mb-4">
                        <div class="h-100">
                            <i class="fas fa-building fa-3x text-success mb-3"></i>
                            <h3 class="fw-bold text-success">5,000+</h3>
                            <p class="text-muted">{{ __('Companies') }}</p>
                        </div>
                    </div>
                    <div class="col-md-3 mb-4">
                        <div class="h-100">
                            <i class="fas fa-users fa-3x text-info mb-3"></i>
                            <h3 class="fw-bold text-info">100,000+</h3>
                            <p class="text-muted">{{ __('Job Seekers') }}</p>
                        </div>
                    </div>
                    <div class="col-md-3 mb-4">
                        <div class="h-100">
                            <i class="fas fa-trophy fa-3x text-warning mb-3"></i>
                            <h3 class="fw-bold text-warning">25,000+</h3>
                            <p class="text-muted">{{ __('Successful Hires') }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Values Section -->
        <div class="row mb-5">
            <div class="col-12">
                <h2 class="text-center fw-bold mb-5">{{ __('Our Values') }}</h2>
                <div class="row">
                    <div class="col-md-4 mb-4">
                        <div class="card h-100 border-0 shadow-sm">
                            <div class="card-body text-center p-4">
                                <i class="fas fa-heart fa-3x text-danger mb-3"></i>
                                <h5 class="card-title">{{ __('Passion') }}</h5>
                                <p class="card-text text-muted">{{ __('We are passionate about helping people find meaningful work and build successful careers.') }}</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4 mb-4">
                        <div class="card h-100 border-0 shadow-sm">
                            <div class="card-body text-center p-4">
                                <i class="fas fa-shield-alt fa-3x text-primary mb-3"></i>
                                <h5 class="card-title">{{ __('Trust') }}</h5>
                                <p class="card-text text-muted">{{ __('We build trust through transparency, reliability, and consistent delivery of quality service.') }}</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4 mb-4">
                        <div class="card h-100 border-0 shadow-sm">
                            <div class="card-body text-center p-4">
                                <i class="fas fa-lightbulb fa-3x text-warning mb-3"></i>
                                <h5 class="card-title">{{ __('Innovation') }}</h5>
                                <p class="card-text text-muted">{{ __('We continuously innovate to provide better tools and experiences for job seekers and employers.') }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Team Section -->
        <div class="row mb-5">
            <div class="col-12">
                <h2 class="text-center fw-bold mb-5">{{ __('Meet Our Team') }}</h2>
                <div class="row">
                    @php
                    $team = [
                        ['name' => 'John Smith', 'role' => 'CEO & Founder', 'image' => 'https://via.placeholder.com/300x300', 'bio' => 'Passionate about connecting people with opportunities.'],
                        ['name' => 'Sarah Johnson', 'role' => 'CTO', 'image' => 'https://via.placeholder.com/300x300', 'bio' => 'Leading our technology innovation and platform development.'],
                        ['name' => 'Michael Brown', 'role' => 'Head of Operations', 'image' => 'https://via.placeholder.com/300x300', 'bio' => 'Ensuring smooth operations and excellent user experience.'],
                        ['name' => 'Emily Davis', 'role' => 'Head of Marketing', 'image' => 'https://via.placeholder.com/300x300', 'bio' => 'Building our brand and connecting with our community.']
                    ];
                    @endphp

                    @foreach($team as $member)
                    <div class="col-lg-3 col-md-6 mb-4">
                        <div class="card h-100 border-0 shadow-sm">
                            <div class="card-body text-center p-4">
                                <div class="rounded-circle mx-auto mb-3" style="width: 120px; height: 120px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); display: flex; align-items: center; justify-content: center;">
                                    <i class="fas fa-user fa-3x text-white"></i>
                                </div>
                                <h5 class="card-title">{{ $member['name'] }}</h5>
                                <p class="text-primary fw-bold">{{ $member['role'] }}</p>
                                <p class="card-text text-muted small">{{ $member['bio'] }}</p>
                                <div class="d-flex justify-content-center gap-2">
                                    <a href="#" class="btn btn-outline-primary btn-sm">
                                        <i class="fab fa-linkedin-in"></i>
                                    </a>
                                    <a href="#" class="btn btn-outline-info btn-sm">
                                        <i class="fab fa-twitter"></i>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>

        <!-- Contact CTA -->
        <div class="row">
            <div class="col-12">
                <div class="bg-primary text-white rounded p-5 text-center">
                    <h2 class="fw-bold mb-4">{{ __('Ready to Get Started?') }}</h2>
                    <p class="lead mb-4">{{ __('Join thousands of professionals who have found success through our platform.') }}</p>
                    <div class="d-flex justify-content-center gap-3 flex-wrap">
                        <a href="{{ route('register') }}" class="btn btn-warning btn-lg">
                            <i class="fas fa-user-plus me-2"></i>{{ __('Join as Job Seeker') }}
                        </a>
                        <a href="{{ route('employer.register') }}" class="btn btn-outline-light btn-lg">
                            <i class="fas fa-building me-2"></i>{{ __('Post Jobs') }}
                        </a>
                        <a href="{{ route('contact') }}" class="btn btn-outline-light btn-lg">
                            <i class="fas fa-envelope me-2"></i>{{ __('Contact Us') }}
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
.card {
    transition: transform 0.2s ease-in-out;
}

.card:hover {
    transform: translateY(-5px);
}

.bg-primary {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%) !important;
}

.btn-warning {
    background-color: #ffc107;
    border-color: #ffc107;
}

.btn-warning:hover {
    background-color: #e0a800;
    border-color: #d39e00;
    transform: translateY(-1px);
}
</style>
@endpush 