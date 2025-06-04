<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ __('Companies') }} - {{ config('app.name') }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
        <div class="container">
            <a class="navbar-brand" href="{{ route('front.home') }}">{{ config('app.name') }}</a>
            <div class="navbar-nav ms-auto">
                <a class="nav-link" href="{{ route('jobs.index') }}">{{ __('Jobs') }}</a>
                <a class="nav-link" href="{{ route('companies.index') }}">{{ __('Companies') }}</a>
                <a class="nav-link" href="{{ route('about-us') }}">{{ __('About Us') }}</a>
                <a class="nav-link" href="{{ route('contact') }}">{{ __('Contact') }}</a>
                @guest
                    <a class="nav-link" href="{{ route('login') }}">{{ __('Login') }}</a>
                    <a class="nav-link" href="{{ route('register') }}">{{ __('Register') }}</a>
                @else
                    <a class="nav-link" href="{{ route('dashboard') }}">{{ __('Dashboard') }}</a>
                    <form method="POST" action="{{ route('logout') }}" class="d-inline">
                        @csrf
                        <button type="submit" class="nav-link btn btn-link">{{ __('Logout') }}</button>
                    </form>
                @endguest
            </div>
        </div>
    </nav>

    <div class="container mt-5">
        <div class="row">
            <div class="col-md-12">
                <h1>{{ __('Companies') }}</h1>
                <p class="lead">{{ __('Discover top companies and their job opportunities') }}</p>
                
                <!-- Search Form -->
                <div class="card mb-4">
                    <div class="card-body">
                        <form method="GET" action="{{ route('companies.index') }}">
                            <div class="row">
                                <div class="col-md-4">
                                    <input type="text" name="search" class="form-control" placeholder="{{ __('Company name...') }}" value="{{ request('search') }}">
                                </div>
                                <div class="col-md-3">
                                    <input type="text" name="location" class="form-control" placeholder="{{ __('Location...') }}" value="{{ request('location') }}">
                                </div>
                                <div class="col-md-3">
                                    <select name="industry" class="form-control">
                                        <option value="">{{ __('All Industries') }}</option>
                                        <option value="technology">{{ __('Technology') }}</option>
                                        <option value="healthcare">{{ __('Healthcare') }}</option>
                                        <option value="finance">{{ __('Finance') }}</option>
                                    </select>
                                </div>
                                <div class="col-md-2">
                                    <button type="submit" class="btn btn-primary w-100">{{ __('Search') }}</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
                
                <!-- Company Listings -->
                <div class="row">
                    @for($i = 1; $i <= 6; $i++)
                    <div class="col-md-6 mb-4">
                        <div class="card">
                            <div class="card-body">
                                <div class="d-flex align-items-center mb-3">
                                    <div class="bg-primary rounded p-3 me-3">
                                        <i class="fas fa-building text-white fa-2x"></i>
                                    </div>
                                    <div>
                                        <h5 class="card-title mb-0">{{ __('Sample Company') }} {{ $i }}</h5>
                                        <small class="text-muted">{{ __('Technology') }}</small>
                                    </div>
                                </div>
                                <p class="card-text">{{ __('This is a sample company description that showcases what the company does and their mission.') }}</p>
                                <div class="d-flex justify-content-between align-items-center">
                                    <span class="badge bg-info">{{ rand(10, 100) }} {{ __('Open Jobs') }}</span>
                                    <small class="text-muted">{{ __('New York, NY') }}</small>
                                </div>
                                <div class="mt-3">
                                    <a href="{{ route('company.show', $i) }}" class="btn btn-primary">{{ __('View Company') }}</a>
                                    <button class="btn btn-outline-secondary">{{ __('Follow') }}</button>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endfor
                </div>
                
                <!-- Pagination -->
                <nav aria-label="Company listings pagination">
                    <ul class="pagination justify-content-center">
                        <li class="page-item disabled">
                            <span class="page-link">{{ __('Previous') }}</span>
                        </li>
                        <li class="page-item active">
                            <span class="page-link">1</span>
                        </li>
                        <li class="page-item">
                            <a class="page-link" href="#">2</a>
                        </li>
                        <li class="page-item">
                            <a class="page-link" href="#">3</a>
                        </li>
                        <li class="page-item">
                            <a class="page-link" href="#">{{ __('Next') }}</a>
                        </li>
                    </ul>
                </nav>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

