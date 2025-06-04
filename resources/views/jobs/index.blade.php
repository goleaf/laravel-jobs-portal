<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ __('Jobs') }} - {{ config('app.name') }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <nav class="navbar navbar-expand-lg bg-white shadow-sm -dark bg-primary-600">
        <div class="container mx-auto">
            <a class="bg-white shadow-sm -brand" href="{{ route('front.home') }}">{{ config('app.name') }}</a>
            <div class="bg-white shadow-sm -nav ms-auto">
                <a class="text-gray-600 hover:text-gray-900 px-3 py-2 rounded-md text-sm font-medium" href="{{ route('jobs.index') }}">{{ __('Jobs') }}</a>
                <a class="text-gray-600 hover:text-gray-900 px-3 py-2 rounded-md text-sm font-medium" href="{{ route('companies.index') }}">{{ __('Companies') }}</a>
                <a class="text-gray-600 hover:text-gray-900 px-3 py-2 rounded-md text-sm font-medium" href="{{ route('about-us') }}">{{ __('About Us') }}</a>
                <a class="text-gray-600 hover:text-gray-900 px-3 py-2 rounded-md text-sm font-medium" href="{{ route('contact') }}">{{ __('Contact') }}</a>
                @guest
                    <a class="text-gray-600 hover:text-gray-900 px-3 py-2 rounded-md text-sm font-medium" href="{{ route('login') }}">{{ __('Login') }}</a>
                    <a class="text-gray-600 hover:text-gray-900 px-3 py-2 rounded-md text-sm font-medium" href="{{ route('register') }}">{{ __('Register') }}</a>
                @else
                    <a class="text-gray-600 hover:text-gray-900 px-3 py-2 rounded-md text-sm font-medium" href="{{ route('dashboard') }}">{{ __('Dashboard') }}</a>
                    <form method="POST" action="{{ route('logout') }}" class="d-inline">
                        @csrf
                        <button type="submit" class="text-gray-600 hover:text-gray-900 px-3 py-2 rounded-md text-sm font-medium btn px-4 py-2 rounded font-medium transition-colors -link">{{ __('Logout') }}</button>
                    </form>
                @endguest
            </div>
        </div>
    </nav>

    <div class="container mx-auto mt-5">
        <div class="flex flex-wrap">
            <div class="flex-1 -md-12">
                <h1>{{ __('Job Listings') }}</h1>
                <p class="lead">{{ __('Find your perfect job opportunity') }}</p>
                
                <!-- Search Form -->
                <div class="bg-white shadow rounded-lg overflow-hidden mb-4">
                    <div class="bg-white shadow rounded-lg overflow-hidden -body">
                        <form method="GET" action="{{ route('jobs.index') }}">
                            <div class="flex flex-wrap">
                                <div class="flex-1 -md-4">
                                    <input type="text" name="search" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-primary-500" placeholder="{{ __('Job title or keywords...') }}" value="{{ request('search') }}">
                                </div>
                                <div class="flex-1 -md-3">
                                    <input type="text" name="location" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-primary-500" placeholder="{{ __('Location...') }}" value="{{ request('location') }}">
                                </div>
                                <div class="flex-1 -md-3">
                                    <select name="category" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-primary-500">
                                        <option value="">{{ __('All Categories') }}</option>
                                        <option value="technology">{{ __('Technology') }}</option>
                                        <option value="healthcare">{{ __('Healthcare') }}</option>
                                        <option value="finance">{{ __('Finance') }}</option>
                                    </select>
                                </div>
                                <div class="flex-1 -md-2">
                                    <button type="submit" class="btn px-4 py-2 rounded font-medium transition-colors -primary w-full">{{ __('Search') }}</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
                
                <!-- Job Listings -->
                <div class="flex flex-wrap">
                    @for($i = 1; $i <= 6; $i++)
                    <div class="flex-1 -md-6 mb-4">
                        <div class="bg-white shadow rounded-lg overflow-hidden">
                            <div class="bg-white shadow rounded-lg overflow-hidden -body">
                                <h5 class="bg-white shadow rounded-lg overflow-hidden -title">{{ __('Sample Job Title') }} {{ $i }}</h5>
                                <h6 class="bg-white shadow rounded-lg overflow-hidden -subtitle mb-2 text-gray-500">{{ __('Sample Company') }}</h6>
                                <p class="bg-white shadow rounded-lg overflow-hidden -text">{{ __('This is a sample job description that showcases the key responsibilities and requirements for this position.') }}</p>
                                <div class="flex justify-between items-center">
                                    <span class="badge bg-green-600">{{ __('Full Time') }}</span>
                                    <small class="text-gray-500">{{ $i }} {{ __('days ago') }}</small>
                                </div>
                                <div class="mt-3">
                                    <a href="{{ route('jobs.show', $i) }}" class="btn px-4 py-2 rounded font-medium transition-colors -primary">{{ __('View Details') }}</a>
                                    <button class="btn px-4 py-2 rounded font-medium transition-colors -outline-secondary">{{ __('Save Job') }}</button>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endfor
                </div>
                
                <!-- Pagination -->
                <nav aria-label="Job listings pagination">
                    <ul class="pagination justify-center">
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

