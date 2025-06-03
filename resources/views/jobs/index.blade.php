@extends('layouts.simple')

@section('title', 'Browse Jobs')

@section('content')
<div class="container py-5">
    <!-- Search Header -->
    <div class="row mb-5">
        <div class="col-lg-12">
            <div class="card shadow-sm">
                <div class="card-body p-4">
                    <h2 class="text-center mb-4">Find Your Dream Job</h2>
                    <form action="{{ route('jobs.index') }}" method="GET">
                        <div class="row g-3">
                            <div class="col-md-3">
                                <input type="text" class="form-control" name="keyword" placeholder="Job title, keywords..." value="{{ request('keyword') }}">
                            </div>
                            <div class="col-md-3">
                                <input type="text" class="form-control" name="location" placeholder="Location" value="{{ request('location') }}">
                            </div>
                            <div class="col-md-3">
                                <select class="form-select" name="category">
                                    <option value="">All Categories</option>
                                    <option value="technology" {{ request('category') == 'technology' ? 'selected' : '' }}>Technology</option>
                                    <option value="healthcare" {{ request('category') == 'healthcare' ? 'selected' : '' }}>Healthcare</option>
                                    <option value="finance" {{ request('category') == 'finance' ? 'selected' : '' }}>Finance</option>
                                    <option value="education" {{ request('category') == 'education' ? 'selected' : '' }}>Education</option>
                                    <option value="marketing" {{ request('category') == 'marketing' ? 'selected' : '' }}>Marketing</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <button type="submit" class="btn btn-primary w-100">
                                    <i class="fas fa-search me-2"></i>Search Jobs
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Filters Sidebar -->
        <div class="col-lg-3 mb-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0"><i class="fas fa-filter me-2"></i>Filters</h5>
                </div>
                <div class="card-body">
                    <!-- Job Type Filter -->
                    <div class="mb-4">
                        <h6 class="fw-bold">Job Type</h6>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" value="full-time" id="fullTime">
                            <label class="form-check-label" for="fullTime">
                                Full-time <span class="badge bg-light text-dark">234</span>
                            </label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" value="part-time" id="partTime">
                            <label class="form-check-label" for="partTime">
                                Part-time <span class="badge bg-light text-dark">89</span>
                            </label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" value="contract" id="contract">
                            <label class="form-check-label" for="contract">
                                Contract <span class="badge bg-light text-dark">156</span>
                            </label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" value="remote" id="remote">
                            <label class="form-check-label" for="remote">
                                Remote <span class="badge bg-light text-dark">78</span>
                            </label>
                        </div>
                    </div>

                    <!-- Salary Range -->
                    <div class="mb-4">
                        <h6 class="fw-bold">Salary Range</h6>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" value="0-30k" id="salary1">
                            <label class="form-check-label" for="salary1">$0 - $30k</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" value="30k-50k" id="salary2">
                            <label class="form-check-label" for="salary2">$30k - $50k</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" value="50k-80k" id="salary3">
                            <label class="form-check-label" for="salary3">$50k - $80k</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" value="80k+" id="salary4">
                            <label class="form-check-label" for="salary4">$80k+</label>
                        </div>
                    </div>

                    <!-- Experience Level -->
                    <div class="mb-4">
                        <h6 class="fw-bold">Experience Level</h6>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" value="entry" id="entry">
                            <label class="form-check-label" for="entry">Entry Level</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" value="mid" id="mid">
                            <label class="form-check-label" for="mid">Mid Level</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" value="senior" id="senior">
                            <label class="form-check-label" for="senior">Senior Level</label>
                        </div>
                    </div>

                    <button class="btn btn-outline-primary w-100">Apply Filters</button>
                </div>
            </div>
        </div>

        <!-- Jobs List -->
        <div class="col-lg-9">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h4>{{ number_format(1250) }} Jobs Found</h4>
                <div class="d-flex align-items-center">
                    <span class="me-2">Sort by:</span>
                    <select class="form-select" style="width: auto;">
                        <option>Most Recent</option>
                        <option>Salary High to Low</option>
                        <option>Salary Low to High</option>
                        <option>Most Relevant</option>
                    </select>
                </div>
            </div>

            <!-- Job Cards -->
            @for($i = 1; $i <= 10; $i++)
            <div class="card mb-4 shadow-sm job-card">
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-8">
                            <div class="d-flex align-items-start">
                                <div class="company-logo me-3 mt-1">
                                    <i class="fas fa-building fa-2x text-primary"></i>
                                </div>
                                <div class="flex-grow-1">
                                    <h5 class="mb-1">
                                        <a href="{{ route('jobs.show', $i) }}" class="text-decoration-none">
                                            @php
                                                $jobTitles = [
                                                    'Senior Software Developer',
                                                    'Marketing Manager',
                                                    'Data Scientist',
                                                    'UX/UI Designer',
                                                    'Project Manager',
                                                    'Business Analyst',
                                                    'DevOps Engineer',
                                                    'Content Writer',
                                                    'Sales Representative',
                                                    'HR Specialist'
                                                ];
                                            @endphp
                                            {{ $jobTitles[($i-1) % count($jobTitles)] }}
                                        </a>
                                    </h5>
                                    <p class="text-muted mb-2">
                                        <i class="fas fa-building me-1"></i>TechCorp Company {{ $i }}
                                        <span class="mx-2">•</span>
                                        <i class="fas fa-map-marker-alt me-1"></i>New York, NY
                                    </p>
                                    <p class="mb-3">We are seeking a talented professional to join our dynamic team. This role offers excellent growth opportunities...</p>
                                    <div class="job-tags">
                                        <span class="badge bg-primary me-2">Full-time</span>
                                        <span class="badge bg-success me-2">Remote</span>
                                        <span class="badge bg-info">$60k-$80k</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4 text-end">
                            <div class="job-actions">
                                <button class="btn btn-outline-danger btn-sm mb-2" title="Save Job">
                                    <i class="fas fa-heart"></i>
                                </button>
                                <div class="text-muted small mb-3">Posted {{ rand(1, 7) }} days ago</div>
                                <a href="{{ route('jobs.show', $i) }}" class="btn btn-primary w-100">View Job</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @endfor

            <!-- Pagination -->
            <nav aria-label="Jobs pagination">
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
    .job-card:hover {
        transform: translateY(-2px);
        transition: transform 0.3s ease;
        box-shadow: 0 4px 15px rgba(0,0,0,0.1) !important;
    }
    
    .job-card h5 a:hover {
        color: #0066cc !important;
    }
    
    .form-check-input:checked {
        background-color: #0066cc;
        border-color: #0066cc;
    }
</style>
@endsection

@section('page_scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Heart/save functionality
    document.querySelectorAll('.btn-outline-danger').forEach(btn => {
        btn.addEventListener('click', function() {
            this.classList.toggle('btn-outline-danger');
            this.classList.toggle('btn-danger');
            
            const icon = this.querySelector('i');
            icon.classList.toggle('fas');
            icon.classList.toggle('far');
        });
    });
    
    // Filter functionality
    document.querySelectorAll('.form-check-input').forEach(checkbox => {
        checkbox.addEventListener('change', function() {
            // Here you would typically filter the jobs
            console.log('Filter changed:', this.value, this.checked);
        });
    });
});
</script>
@endsection

