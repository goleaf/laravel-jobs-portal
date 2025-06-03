@extends('layouts.simple')

@section('title', 'Home')

@section('content')
<!-- Hero Section Start -->
<section class="hero-section bg-primary text-white py-5">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-6">
                <h1 class="display-4 fw-bold mb-4">Find Your Dream Job Today</h1>
                <p class="lead mb-4">Connect with top employers and discover opportunities that match your skills and aspirations. Start your career journey with us.</p>
                <div class="d-flex gap-3">
                    <a href="{{ route('jobs.index') }}" class="btn btn-light btn-lg">Browse Jobs</a>
                    <a href="{{ route('register') }}" class="btn btn-outline-light btn-lg">Get Started</a>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="hero-image text-center">
                    <i class="fas fa-briefcase fa-10x opacity-75"></i>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Search Section -->
<section class="search-section py-5 bg-light">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-10">
                <div class="card shadow-sm">
                    <div class="card-body p-4">
                        <h3 class="text-center mb-4">Find Your Perfect Job</h3>
                        <form action="{{ route('jobs.index') }}" method="GET">
                            <div class="row g-3">
                                <div class="col-md-4">
                                    <label for="keyword" class="form-label">Job Title or Keyword</label>
                                    <input type="text" class="form-control" id="keyword" name="keyword" placeholder="e.g. Software Developer">
                                </div>
                                <div class="col-md-4">
                                    <label for="location" class="form-label">Location</label>
                                    <input type="text" class="form-control" id="location" name="location" placeholder="e.g. New York">
                                </div>
                                <div class="col-md-4">
                                    <label for="category" class="form-label">Category</label>
                                    <select class="form-select" id="category" name="category">
                                        <option value="">All Categories</option>
                                        <option value="technology">Technology</option>
                                        <option value="healthcare">Healthcare</option>
                                        <option value="finance">Finance</option>
                                        <option value="education">Education</option>
                                        <option value="marketing">Marketing</option>
                                    </select>
                                </div>
                                <div class="col-12">
                                    <button type="submit" class="btn btn-primary btn-lg w-100">
                                        <i class="fas fa-search me-2"></i>Search Jobs
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Stats Section -->
<section class="stats-section py-5">
    <div class="container">
        <div class="row text-center">
            <div class="col-md-3 mb-4">
                <div class="stat-item">
                    <i class="fas fa-briefcase fa-3x text-primary mb-3"></i>
                    <h3 class="fw-bold">{{ number_format(1250) }}+</h3>
                    <p class="text-muted">Active Jobs</p>
                </div>
            </div>
            <div class="col-md-3 mb-4">
                <div class="stat-item">
                    <i class="fas fa-building fa-3x text-primary mb-3"></i>
                    <h3 class="fw-bold">{{ number_format(850) }}+</h3>
                    <p class="text-muted">Companies</p>
                </div>
            </div>
            <div class="col-md-3 mb-4">
                <div class="stat-item">
                    <i class="fas fa-users fa-3x text-primary mb-3"></i>
                    <h3 class="fw-bold">{{ number_format(25000) }}+</h3>
                    <p class="text-muted">Candidates</p>
                </div>
            </div>
            <div class="col-md-3 mb-4">
                <div class="stat-item">
                    <i class="fas fa-handshake fa-3x text-primary mb-3"></i>
                    <h3 class="fw-bold">{{ number_format(5500) }}+</h3>
                    <p class="text-muted">Successful Hires</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Featured Jobs Section -->
<section class="featured-jobs py-5 bg-light">
    <div class="container">
        <div class="row">
            <div class="col-12">
                <h2 class="text-center mb-5">Featured Jobs</h2>
            </div>
        </div>
        <div class="row">
            @for($i = 1; $i <= 6; $i++)
            <div class="col-lg-4 col-md-6 mb-4">
                <div class="card h-100 shadow-sm">
                    <div class="card-body">
                        <div class="d-flex align-items-center mb-3">
                            <div class="company-logo me-3">
                                <i class="fas fa-building fa-2x text-primary"></i>
                            </div>
                            <div>
                                <h5 class="card-title mb-1">Sample Job Title {{ $i }}</h5>
                                <p class="text-muted mb-0">Sample Company {{ $i }}</p>
                            </div>
                        </div>
                        <p class="card-text text-muted">We are looking for a talented professional to join our growing team...</p>
                        <div class="job-meta mb-3">
                            <span class="badge bg-primary me-2"><i class="fas fa-map-marker-alt me-1"></i>Remote</span>
                            <span class="badge bg-success me-2"><i class="fas fa-clock me-1"></i>Full-time</span>
                            <span class="badge bg-info"><i class="fas fa-dollar-sign me-1"></i>$50k-$70k</span>
                        </div>
                        <a href="{{ route('jobs.show', $i) }}" class="btn btn-primary w-100">View Details</a>
                    </div>
                </div>
            </div>
            @endfor
        </div>
        <div class="text-center mt-4">
            <a href="{{ route('jobs.index') }}" class="btn btn-outline-primary btn-lg">View All Jobs</a>
        </div>
    </div>
</section>

<!-- How It Works Section -->
<section class="how-it-works py-5">
    <div class="container">
        <div class="row">
            <div class="col-12">
                <h2 class="text-center mb-5">How It Works</h2>
            </div>
        </div>
        <div class="row">
            <div class="col-md-4 text-center mb-4">
                <div class="step-item">
                    <div class="step-icon mb-3">
                        <i class="fas fa-user-plus fa-3x text-primary"></i>
                    </div>
                    <h4>1. Create Profile</h4>
                    <p class="text-muted">Sign up and create your professional profile with your skills and experience.</p>
                </div>
            </div>
            <div class="col-md-4 text-center mb-4">
                <div class="step-item">
                    <div class="step-icon mb-3">
                        <i class="fas fa-search fa-3x text-primary"></i>
                    </div>
                    <h4>2. Search Jobs</h4>
                    <p class="text-muted">Browse thousands of job opportunities or let employers find you.</p>
                </div>
            </div>
            <div class="col-md-4 text-center mb-4">
                <div class="step-item">
                    <div class="step-icon mb-3">
                        <i class="fas fa-rocket fa-3x text-primary"></i>
                    </div>
                    <h4>3. Get Hired</h4>
                    <p class="text-muted">Apply to jobs, connect with employers, and land your dream job.</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Call to Action Section -->
<section class="cta-section py-5 bg-primary text-white">
    <div class="container">
        <div class="row justify-content-center text-center">
            <div class="col-lg-8">
                <h2 class="mb-4">Ready to Start Your Career Journey?</h2>
                <p class="lead mb-4">Join thousands of professionals who have found their dream jobs through our platform.</p>
                <div class="d-flex gap-3 justify-content-center">
                    <a href="{{ route('register') }}" class="btn btn-light btn-lg">
                        <i class="fas fa-user-plus me-2"></i>Join as Candidate
                    </a>
                    <a href="{{ route('register') }}" class="btn btn-outline-light btn-lg">
                        <i class="fas fa-building me-2"></i>Post Jobs as Employer
                    </a>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection

@section('page_css')
<style>
    .hero-section {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        min-height: 600px;
        display: flex;
        align-items: center;
    }
    
    .hero-image {
        animation: float 3s ease-in-out infinite;
    }
    
    @keyframes float {
        0%, 100% { transform: translateY(0px); }
        50% { transform: translateY(-20px); }
    }
    
    .stat-item:hover {
        transform: translateY(-5px);
        transition: transform 0.3s ease;
    }
    
    .card:hover {
        transform: translateY(-5px);
        transition: transform 0.3s ease;
        box-shadow: 0 8px 25px rgba(0,0,0,0.15) !important;
    }
    
    .step-item:hover .step-icon {
        transform: scale(1.1);
        transition: transform 0.3s ease;
    }
    
    .cta-section {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    }
</style>
@endsection

@section('page_scripts')
<script>
    // Add some interactivity
    document.addEventListener('DOMContentLoaded', function() {
        // Add smooth scrolling to anchor links
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                e.preventDefault();
                document.querySelector(this.getAttribute('href')).scrollIntoView({
                    behavior: 'smooth'
                });
            });
        });
        
        // Add loading state to search form
        const searchForm = document.querySelector('form');
        if (searchForm) {
            searchForm.addEventListener('submit', function() {
                const submitBtn = this.querySelector('button[type="submit"]');
                submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Searching...';
                submitBtn.disabled = true;
            });
        }
    });
</script>
@endsection
