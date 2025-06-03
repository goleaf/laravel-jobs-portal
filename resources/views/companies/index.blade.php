@extends('layouts.simple')

@section('title', 'Browse Companies')

@section('content')
<div class="container py-5">
    <!-- Header Section -->
    <div class="row mb-5">
        <div class="col-lg-12 text-center">
            <h1 class="display-4 mb-3">Browse Companies</h1>
            <p class="lead">Discover amazing companies and find your next career opportunity</p>
        </div>
    </div>

    <!-- Search Section -->
    <div class="row mb-5">
        <div class="col-lg-8 mx-auto">
            <div class="card shadow-sm">
                <div class="card-body p-4">
                    <form action="{{ route('companies.index') }}" method="GET">
                        <div class="row g-3">
                            <div class="col-md-4">
                                <input type="text" class="form-control" name="search" placeholder="Company name..." value="{{ request('search') }}">
                            </div>
                            <div class="col-md-4">
                                <select class="form-select" name="industry">
                                    <option value="">All Industries</option>
                                    <option value="technology" {{ request('industry') == 'technology' ? 'selected' : '' }}>Technology</option>
                                    <option value="healthcare" {{ request('industry') == 'healthcare' ? 'selected' : '' }}>Healthcare</option>
                                    <option value="finance" {{ request('industry') == 'finance' ? 'selected' : '' }}>Finance</option>
                                    <option value="education" {{ request('industry') == 'education' ? 'selected' : '' }}>Education</option>
                                    <option value="retail" {{ request('industry') == 'retail' ? 'selected' : '' }}>Retail</option>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <button type="submit" class="btn btn-primary w-100">
                                    <i class="fas fa-search me-2"></i>Search Companies
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Companies Grid -->
    <div class="row">
        @for($i = 1; $i <= 12; $i++)
        <div class="col-lg-4 col-md-6 mb-4">
            <div class="card h-100 shadow-sm company-card">
                <div class="card-body p-4">
                    <div class="text-center mb-4">
                        <div class="company-logo-large mb-3">
                            <i class="fas fa-building fa-4x text-primary"></i>
                        </div>
                        <h4 class="mb-2">
                            @php
                                $companies = [
                                    'TechCorp Solutions',
                                    'HealthCare Plus',
                                    'Financial Services Inc',
                                    'EduTech Systems',
                                    'RetailMax Corporation',
                                    'Green Energy Ltd',
                                    'Digital Marketing Pro',
                                    'Construction Masters',
                                    'Food & Beverage Co',
                                    'Travel Solutions',
                                    'Auto Services Group',
                                    'Media & Entertainment'
                                ];
                            @endphp
                            {{ $companies[($i-1) % count($companies)] }}
                        </h4>
                        <p class="text-muted mb-3">
                            @php
                                $industries = ['Technology', 'Healthcare', 'Finance', 'Education', 'Retail', 'Energy'];
                            @endphp
                            {{ $industries[($i-1) % count($industries)] }}
                        </p>
                    </div>

                    <div class="company-stats mb-4">
                        <div class="row text-center">
                            <div class="col-4">
                                <div class="stat-item">
                                    <h6 class="fw-bold text-primary">{{ rand(50, 500) }}</h6>
                                    <small class="text-muted">Employees</small>
                                </div>
                            </div>
                            <div class="col-4">
                                <div class="stat-item">
                                    <h6 class="fw-bold text-success">{{ rand(5, 25) }}</h6>
                                    <small class="text-muted">Open Jobs</small>
                                </div>
                            </div>
                            <div class="col-4">
                                <div class="stat-item">
                                    <h6 class="fw-bold text-info">{{ rand(2015, 2020) }}</h6>
                                    <small class="text-muted">Founded</small>
                                </div>
                            </div>
                        </div>
                    </div>

                    <p class="card-text text-muted mb-4">
                        We are a leading company in our industry, committed to innovation and excellence. Join our team and be part of something amazing.
                    </p>

                    <div class="company-tags mb-4">
                        <span class="badge bg-primary me-1">Remote Work</span>
                        <span class="badge bg-success me-1">Health Benefits</span>
                        <span class="badge bg-info">Growth Opportunities</span>
                    </div>

                    <div class="d-grid gap-2">
                        <a href="{{ route('company.show', $i) }}" class="btn btn-primary">
                            <i class="fas fa-eye me-2"></i>View Company
                        </a>
                        <button class="btn btn-outline-success" onclick="followCompany({{ $i }})">
                            <i class="fas fa-plus me-2"></i>Follow
                        </button>
                    </div>
                </div>
            </div>
        </div>
        @endfor
    </div>

    <!-- Pagination -->
    <div class="row mt-5">
        <div class="col-12">
            <nav aria-label="Companies pagination">
                <ul class="pagination justify-content-center">
                    <li class="page-item disabled">
                        <a class="page-link" href="#" tabindex="-1">Previous</a>
                    </li>
                    <li class="page-item active"><a class="page-link" href="#">1</a></li>
                    <li class="page-item"><a class="page-link" href="#">2</a></li>
                    <li class="page-item"><a class="page-link" href="#">3</a></li>
                    <li class="page-item">
                        <a class="page-link" href="#">Next</a>
                    </li>
                </ul>
            </nav>
        </div>
    </div>
</div>
@endsection

@section('page_css')
<style>
    .company-card:hover {
        transform: translateY(-5px);
        transition: transform 0.3s ease;
        box-shadow: 0 8px 25px rgba(0,0,0,0.15) !important;
    }
    
    .company-logo-large {
        border: 3px solid #f8f9fa;
        border-radius: 50%;
        width: 100px;
        height: 100px;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto;
        background: #fff;
    }
    
    .stat-item {
        padding: 10px 5px;
    }
    
    .company-card .btn:hover {
        transform: translateY(-1px);
        transition: transform 0.2s ease;
    }
</style>
@endsection

@section('page_scripts')
<script>
function followCompany(id) {
    const btn = event.target;
    const isFollowing = btn.classList.contains('btn-success');
    
    if (isFollowing) {
        btn.classList.remove('btn-success');
        btn.classList.add('btn-outline-success');
        btn.innerHTML = '<i class="fas fa-plus me-2"></i>Follow';
    } else {
        btn.classList.remove('btn-outline-success');
        btn.classList.add('btn-success');
        btn.innerHTML = '<i class="fas fa-check me-2"></i>Following';
    }
    
    // Here you would typically make an AJAX call to follow/unfollow
    console.log('Company ' + id + ' follow status changed');
}
</script>
@endsection

