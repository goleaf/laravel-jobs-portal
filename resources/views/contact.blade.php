@extends('layouts.simple')

@section('title', __('Contact Us'))

@section('content')
<div class="container-fluid">
    <!-- Hero Section -->
    <div class="row bg-primary text-white py-5 mb-0">
        <div class="col-12 text-center">
            <h1 class="display-4 fw-bold mb-4">{{ __('Contact Us') }}</h1>
            <p class="lead">{{ __('We\'d love to hear from you. Get in touch with our team.') }}</p>
        </div>
    </div>

    <div class="container py-5">
        <div class="row">
            <!-- Contact Form -->
            <div class="col-lg-8 mb-5">
                <div class="card shadow-lg border-0">
                    <div class="card-header bg-transparent border-0 pt-4 pb-0">
                        <h3 class="fw-bold text-center mb-0">{{ __('Send us a Message') }}</h3>
                        <p class="text-center text-muted">{{ __('Fill out the form below and we\'ll get back to you as soon as possible.') }}</p>
                    </div>
                    <div class="card-body p-5">
                        @if (session('success'))
                            <div class="alert alert-success alert-dismissible fade show" role="alert">
                                {{ session('success') }}
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        @endif

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

                        <form method="POST" action="{{ route('contact') }}" id="contactForm">
                            @csrf

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="first_name" class="form-label">{{ __('First Name') }}</label>
                                        <div class="input-group">
                                            <span class="input-group-text">
                                                <i class="fas fa-user"></i>
                                            </span>
                                            <input type="text" 
                                                   class="form-control @error('first_name') is-invalid @enderror" 
                                                   id="first_name" 
                                                   name="first_name" 
                                                   value="{{ old('first_name') }}" 
                                                   required
                                                   placeholder="{{ __('Enter your first name') }}">
                                            @error('first_name')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="last_name" class="form-label">{{ __('Last Name') }}</label>
                                        <div class="input-group">
                                            <span class="input-group-text">
                                                <i class="fas fa-user"></i>
                                            </span>
                                            <input type="text" 
                                                   class="form-control @error('last_name') is-invalid @enderror" 
                                                   id="last_name" 
                                                   name="last_name" 
                                                   value="{{ old('last_name') }}"
                                                   placeholder="{{ __('Enter your last name') }}">
                                            @error('last_name')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="email" class="form-label">{{ __('Email Address') }}</label>
                                        <div class="input-group">
                                            <span class="input-group-text">
                                                <i class="fas fa-envelope"></i>
                                            </span>
                                            <input type="email" 
                                                   class="form-control @error('email') is-invalid @enderror" 
                                                   id="email" 
                                                   name="email" 
                                                   value="{{ old('email') }}" 
                                                   required
                                                   placeholder="{{ __('Enter your email address') }}">
                                            @error('email')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="phone" class="form-label">{{ __('Phone Number') }} <small class="text-muted">({{ __('Optional') }})</small></label>
                                        <div class="input-group">
                                            <span class="input-group-text">
                                                <i class="fas fa-phone"></i>
                                            </span>
                                            <input type="tel" 
                                                   class="form-control @error('phone') is-invalid @enderror" 
                                                   id="phone" 
                                                   name="phone" 
                                                   value="{{ old('phone') }}"
                                                   placeholder="{{ __('Enter your phone number') }}">
                                            @error('phone')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="subject" class="form-label">{{ __('Subject') }}</label>
                                <div class="input-group">
                                    <span class="input-group-text">
                                        <i class="fas fa-tag"></i>
                                    </span>
                                    <select class="form-select @error('subject') is-invalid @enderror" 
                                            id="subject" 
                                            name="subject" 
                                            required>
                                        <option value="">{{ __('Select a subject') }}</option>
                                        <option value="general" {{ old('subject') == 'general' ? 'selected' : '' }}>{{ __('General Inquiry') }}</option>
                                        <option value="support" {{ old('subject') == 'support' ? 'selected' : '' }}>{{ __('Technical Support') }}</option>
                                        <option value="billing" {{ old('subject') == 'billing' ? 'selected' : '' }}>{{ __('Billing Question') }}</option>
                                        <option value="partnership" {{ old('subject') == 'partnership' ? 'selected' : '' }}>{{ __('Partnership Opportunity') }}</option>
                                        <option value="feedback" {{ old('subject') == 'feedback' ? 'selected' : '' }}>{{ __('Feedback') }}</option>
                                        <option value="other" {{ old('subject') == 'other' ? 'selected' : '' }}>{{ __('Other') }}</option>
                                    </select>
                                    @error('subject')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="message" class="form-label">{{ __('Message') }}</label>
                                <textarea class="form-control @error('message') is-invalid @enderror" 
                                          id="message" 
                                          name="message" 
                                          rows="6" 
                                          required
                                          placeholder="{{ __('Please describe your inquiry in detail...') }}">{{ old('message') }}</textarea>
                                @error('message')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <div class="form-text">{{ __('Minimum 10 characters required') }}</div>
                            </div>

                            <div class="mb-4">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="newsletter" name="newsletter" {{ old('newsletter') ? 'checked' : '' }}>
                                    <label class="form-check-label" for="newsletter">
                                        {{ __('I would like to receive updates and newsletters') }}
                                    </label>
                                </div>
                            </div>

                            <div class="d-grid">
                                <button type="submit" class="btn btn-primary btn-lg">
                                    <i class="fas fa-paper-plane me-2"></i>{{ __('Send Message') }}
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
                        <div class="card h-100 border-0 shadow-sm">
                            <div class="card-body p-4">
                                <h5 class="card-title fw-bold mb-4">{{ __('Get in Touch') }}</h5>
                                
                                <div class="contact-item mb-3">
                                    <div class="d-flex align-items-center">
                                        <div class="contact-icon me-3">
                                            <i class="fas fa-map-marker-alt fa-lg text-primary"></i>
                                        </div>
                                        <div>
                                            <h6 class="mb-1">{{ __('Address') }}</h6>
                                            <p class="text-muted mb-0">
                                                123 Business District<br>
                                                Suite 400<br>
                                                New York, NY 10001
                                            </p>
                                        </div>
                                    </div>
                                </div>

                                <div class="contact-item mb-3">
                                    <div class="d-flex align-items-center">
                                        <div class="contact-icon me-3">
                                            <i class="fas fa-phone fa-lg text-success"></i>
                                        </div>
                                        <div>
                                            <h6 class="mb-1">{{ __('Phone') }}</h6>
                                            <p class="text-muted mb-0">
                                                <a href="tel:+1234567890" class="text-decoration-none">+1 (234) 567-8900</a>
                                            </p>
                                        </div>
                                    </div>
                                </div>

                                <div class="contact-item mb-3">
                                    <div class="d-flex align-items-center">
                                        <div class="contact-icon me-3">
                                            <i class="fas fa-envelope fa-lg text-info"></i>
                                        </div>
                                        <div>
                                            <h6 class="mb-1">{{ __('Email') }}</h6>
                                            <p class="text-muted mb-0">
                                                <a href="mailto:contact@jobportal.com" class="text-decoration-none">contact@jobportal.com</a>
                                            </p>
                                        </div>
                                    </div>
                                </div>

                                <div class="contact-item">
                                    <div class="d-flex align-items-center">
                                        <div class="contact-icon me-3">
                                            <i class="fas fa-clock fa-lg text-warning"></i>
                                        </div>
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

                    <!-- Social Media -->
                    <div class="col-12 mb-4">
                        <div class="card h-100 border-0 shadow-sm">
                            <div class="card-body p-4 text-center">
                                <h5 class="card-title fw-bold mb-3">{{ __('Follow Us') }}</h5>
                                <div class="d-flex justify-content-center gap-3">
                                    <a href="#" class="btn btn-outline-primary">
                                        <i class="fab fa-facebook-f"></i>
                                    </a>
                                    <a href="#" class="btn btn-outline-info">
                                        <i class="fab fa-twitter"></i>
                                    </a>
                                    <a href="#" class="btn btn-outline-danger">
                                        <i class="fab fa-instagram"></i>
                                    </a>
                                    <a href="#" class="btn btn-outline-primary">
                                        <i class="fab fa-linkedin-in"></i>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- FAQ Link -->
                    <div class="col-12">
                        <div class="card h-100 border-0 shadow-sm">
                            <div class="card-body p-4 text-center">
                                <i class="fas fa-question-circle fa-3x text-primary mb-3"></i>
                                <h5 class="card-title fw-bold">{{ __('Need Quick Answers?') }}</h5>
                                <p class="text-muted mb-3">{{ __('Check out our frequently asked questions for immediate help.') }}</p>
                                <a href="#" class="btn btn-primary">
                                    <i class="fas fa-external-link-alt me-2"></i>{{ __('View FAQ') }}
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
.bg-primary {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%) !important;
}

.card {
    transition: transform 0.2s ease-in-out;
}

.card:hover {
    transform: translateY(-2px);
}

.input-group-text {
    background-color: #f8f9fa;
    border-right: none;
}

.form-control, .form-select {
    border-left: none;
}

.form-control:focus, .form-select:focus {
    border-color: #667eea;
    box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
}

.btn-primary {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    border: none;
}

.btn-primary:hover {
    background: linear-gradient(135deg, #5a6fd8 0%, #6a4190 100%);
    transform: translateY(-1px);
}

.contact-icon {
    width: 40px;
    text-align: center;
}

.btn-outline-primary:hover,
.btn-outline-info:hover,
.btn-outline-danger:hover {
    transform: translateY(-1px);
}
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('contactForm');
    const submitBtn = form.querySelector('button[type="submit"]');
    
    form.addEventListener('submit', function() {
        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>{{ __("Sending...") }}';
        submitBtn.disabled = true;
    });
});
</script>
@endpush 