@extends('layouts.simple')

@section('title', 'About Us')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <h1 class="text-center mb-5">About Our Job Portal</h1>
            
            <div class="text-center mb-5">
                <i class="fas fa-briefcase fa-5x text-primary mb-3"></i>
                <p class="lead">Connecting talent with opportunity since 2024</p>
            </div>
            
            <div class="row">
                <div class="col-md-6 mb-4">
                    <h3><i class="fas fa-bullseye text-primary me-2"></i>Our Mission</h3>
                    <p>To bridge the gap between talented professionals and forward-thinking companies, creating meaningful career opportunities and driving business success.</p>
                </div>
                
                <div class="col-md-6 mb-4">
                    <h3><i class="fas fa-eye text-primary me-2"></i>Our Vision</h3>
                    <p>To be the leading job portal that empowers careers and transforms the way people find jobs and companies hire talent.</p>
                </div>
            </div>
            
            <h3 class="mt-5 mb-4">Why Choose Us?</h3>
            <div class="row">
                <div class="col-md-4 mb-3">
                    <div class="card h-100 border-primary">
                        <div class="card-body text-center">
                            <i class="fas fa-search fa-3x text-primary mb-3"></i>
                            <h5>Smart Matching</h5>
                            <p class="text-muted">Advanced algorithms to match candidates with perfect job opportunities.</p>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-4 mb-3">
                    <div class="card h-100 border-success">
                        <div class="card-body text-center">
                            <i class="fas fa-shield-alt fa-3x text-success mb-3"></i>
                            <h5>Secure Platform</h5>
                            <p class="text-muted">Your data is protected with enterprise-level security measures.</p>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-4 mb-3">
                    <div class="card h-100 border-info">
                        <div class="card-body text-center">
                            <i class="fas fa-headset fa-3x text-info mb-3"></i>
                            <h5>24/7 Support</h5>
                            <p class="text-muted">Round-the-clock customer support to help you succeed.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 