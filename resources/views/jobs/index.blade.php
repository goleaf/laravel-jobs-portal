<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ __('Jobs') }} - {{ config('app.name') }}</title>
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
                <h1>{{ __('Job Listings') }}</h1>
                <p class="lead">{{ __('Find your perfect job opportunity') }}</p>
                
                <!-- Search Form -->
                <div class="card mb-4">
                    <div class="card-body">
                        <form method="GET" action="{{ route('jobs.index') }}">
                            <div class="row">
                                <div class="col-md-4">
                                    <input type="text" name="search" class="form-control" placeholder="{{ __('Job title or keywords...') }}" value="{{ request('search') }}">
                                </div>
                                <div class="col-md-3">
                                    <input type="text" name="location" class="form-control" placeholder="{{ __('Location...') }}" value="{{ request('location') }}">
                                </div>
                                <div class="col-md-3">
                                    <select name="category" class="form-control">
                                        <option value="">{{ __('All Categories') }}</option>
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
                
                <!-- Job Listings -->
                <div class="row">
                    @for($i = 1; $i <= 6; $i++)
                    <div class="col-md-6 mb-4">
                        <div class="card">
                            <div class="card-body">
                                <h5 class="card-title">{{ __('Sample Job Title') }} {{ $i }}</h5>
                                <h6 class="card-subtitle mb-2 text-muted">{{ __('Sample Company') }}</h6>
                                <p class="card-text">{{ __('This is a sample job description that showcases the key responsibilities and requirements for this position.') }}</p>
                                <div class="d-flex justify-content-between align-items-center">
                                    <span class="badge bg-success">{{ __('Full Time') }}</span>
                                    <small class="text-muted">{{ $i }} {{ __('days ago') }}</small>
                                </div>
                                <div class="mt-3">
                                    <a href="{{ route('jobs.show', $i) }}" class="btn btn-primary">{{ __('View Details') }}</a>
                                    <button class="btn btn-outline-secondary">{{ __('Save Job') }}</button>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endfor
                </div>
                
                <!-- Pagination -->
                <nav aria-label="Job listings pagination">
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

