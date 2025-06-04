@extends('layouts.simple')

@section('title', __('Icon Documentation'))

@section('content')
<div class="container py-5">
    <div class="row">
        <div class="col-12">
            <h1 class="display-4 fw-bold text-center mb-5">{{ __('Icon Documentation') }}</h1>
            <p class="lead text-center text-muted mb-5">{{ __('A comprehensive guide to using icons in your components') }}</p>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-3">
            <div class="card border-0 shadow-sm sticky-top" style="top: 2rem;">
                <div class="card-body">
                    <h5 class="card-title">{{ __('Icon Categories') }}</h5>
                    <ul class="nav nav-pills flex-column">
                        <li class="nav-item">
                            <a class="nav-link" href="#basic-icons">{{ __('Basic Icons') }}</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="#business-icons">{{ __('Business Icons') }}</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="#social-icons">{{ __('Social Icons') }}</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="#interface-icons">{{ __('Interface Icons') }}</a>
                        </li>
                    </ul>
                </div>
            </div>
        </div>

        <div class="col-lg-9">
            <!-- Basic Icons Section -->
            <section id="basic-icons" class="mb-5">
                <h2 class="fw-bold mb-4">{{ __('Basic Icons') }}</h2>
                <div class="row">
                    @php
                    $basicIcons = [
                        'fas fa-home' => 'Home',
                        'fas fa-user' => 'User',
                        'fas fa-envelope' => 'Email',
                        'fas fa-phone' => 'Phone',
                        'fas fa-map-marker-alt' => 'Location',
                        'fas fa-search' => 'Search',
                        'fas fa-star' => 'Star',
                        'fas fa-heart' => 'Heart',
                        'fas fa-cog' => 'Settings',
                        'fas fa-bell' => 'Notification',
                        'fas fa-calendar' => 'Calendar',
                        'fas fa-clock' => 'Clock'
                    ];
                    @endphp

                    @foreach($basicIcons as $icon => $name)
                    <div class="col-md-4 col-lg-3 mb-4">
                        <div class="card text-center h-100 border-0 shadow-sm">
                            <div class="card-body">
                                <i class="{{ $icon }} fa-2x text-primary mb-3"></i>
                                <h6 class="card-title">{{ $name }}</h6>
                                <code class="small">{{ $icon }}</code>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </section>

            <!-- Business Icons Section -->
            <section id="business-icons" class="mb-5">
                <h2 class="fw-bold mb-4">{{ __('Business Icons') }}</h2>
                <div class="row">
                    @php
                    $businessIcons = [
                        'fas fa-briefcase' => 'Briefcase',
                        'fas fa-building' => 'Building',
                        'fas fa-chart-bar' => 'Chart',
                        'fas fa-dollar-sign' => 'Money',
                        'fas fa-handshake' => 'Handshake',
                        'fas fa-trophy' => 'Trophy',
                        'fas fa-bullhorn' => 'Marketing',
                        'fas fa-laptop' => 'Technology',
                        'fas fa-users' => 'Team',
                        'fas fa-file-alt' => 'Document',
                        'fas fa-clipboard' => 'Clipboard',
                        'fas fa-calculator' => 'Calculator'
                    ];
                    @endphp

                    @foreach($businessIcons as $icon => $name)
                    <div class="col-md-4 col-lg-3 mb-4">
                        <div class="card text-center h-100 border-0 shadow-sm">
                            <div class="card-body">
                                <i class="{{ $icon }} fa-2x text-success mb-3"></i>
                                <h6 class="card-title">{{ $name }}</h6>
                                <code class="small">{{ $icon }}</code>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </section>

            <!-- Social Icons Section -->
            <section id="social-icons" class="mb-5">
                <h2 class="fw-bold mb-4">{{ __('Social Icons') }}</h2>
                <div class="row">
                    @php
                    $socialIcons = [
                        'fab fa-facebook-f' => 'Facebook',
                        'fab fa-twitter' => 'Twitter',
                        'fab fa-linkedin-in' => 'LinkedIn',
                        'fab fa-instagram' => 'Instagram',
                        'fab fa-youtube' => 'YouTube',
                        'fab fa-github' => 'GitHub',
                        'fab fa-google' => 'Google',
                        'fab fa-whatsapp' => 'WhatsApp'
                    ];
                    @endphp

                    @foreach($socialIcons as $icon => $name)
                    <div class="col-md-4 col-lg-3 mb-4">
                        <div class="card text-center h-100 border-0 shadow-sm">
                            <div class="card-body">
                                <i class="{{ $icon }} fa-2x text-info mb-3"></i>
                                <h6 class="card-title">{{ $name }}</h6>
                                <code class="small">{{ $icon }}</code>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </section>

            <!-- Interface Icons Section -->
            <section id="interface-icons" class="mb-5">
                <h2 class="fw-bold mb-4">{{ __('Interface Icons') }}</h2>
                <div class="row">
                    @php
                    $interfaceIcons = [
                        'fas fa-plus' => 'Add',
                        'fas fa-minus' => 'Remove',
                        'fas fa-edit' => 'Edit',
                        'fas fa-trash' => 'Delete',
                        'fas fa-save' => 'Save',
                        'fas fa-download' => 'Download',
                        'fas fa-upload' => 'Upload',
                        'fas fa-print' => 'Print',
                        'fas fa-share' => 'Share',
                        'fas fa-eye' => 'View',
                        'fas fa-eye-slash' => 'Hide',
                        'fas fa-lock' => 'Lock'
                    ];
                    @endphp

                    @foreach($interfaceIcons as $icon => $name)
                    <div class="col-md-4 col-lg-3 mb-4">
                        <div class="card text-center h-100 border-0 shadow-sm">
                            <div class="card-body">
                                <i class="{{ $icon }} fa-2x text-warning mb-3"></i>
                                <h6 class="card-title">{{ $name }}</h6>
                                <code class="small">{{ $icon }}</code>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </section>

            <!-- Usage Examples -->
            <section class="mb-5">
                <h2 class="fw-bold mb-4">{{ __('Usage Examples') }}</h2>
                <div class="card border-0 shadow-sm">
                    <div class="card-body">
                        <h5 class="card-title">{{ __('Basic Usage') }}</h5>
                        <p>{{ __('Use FontAwesome icons in your Blade templates:') }}</p>
                        <pre class="bg-light p-3 rounded"><code>&lt;i class="fas fa-home"&gt;&lt;/i&gt; {{ __('Home') }}
&lt;i class="fas fa-user fa-2x"&gt;&lt;/i&gt; {{ __('Large User Icon') }}
&lt;i class="fas fa-star text-warning"&gt;&lt;/i&gt; {{ __('Colored Star') }}</code></pre>

                        <h5 class="card-title mt-4">{{ __('Button Icons') }}</h5>
                        <p>{{ __('Icons work great in buttons:') }}</p>
                        <div class="mb-3">
                            <button class="btn btn-primary me-2">
                                <i class="fas fa-plus me-1"></i>{{ __('Add Item') }}
                            </button>
                            <button class="btn btn-danger me-2">
                                <i class="fas fa-trash me-1"></i>{{ __('Delete') }}
                            </button>
                            <button class="btn btn-success">
                                <i class="fas fa-save me-1"></i>{{ __('Save') }}
                            </button>
                        </div>
                        <pre class="bg-light p-3 rounded"><code>&lt;button class="btn btn-primary"&gt;
    &lt;i class="fas fa-plus me-1"&gt;&lt;/i&gt;{{ __('Add Item') }}
&lt;/button&gt;</code></pre>
                    </div>
                </div>
            </section>
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
    transform: translateY(-2px);
}

code {
    background-color: #f8f9fa;
    padding: 2px 4px;
    border-radius: 3px;
    font-size: 0.875em;
}

pre code {
    background-color: transparent;
    padding: 0;
}
</style>
@endpush 