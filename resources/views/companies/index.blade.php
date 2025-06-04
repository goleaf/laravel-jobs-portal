<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ __('Companies') }} - {{ config('app.name') }}</title></head>
<body>
    <nav class="bg-white shadow-sm border-b border-gray-200 navbar-expand-lg bg-white shadow-sm -dark bg-primary-600">
        <div class="container mx-auto px-4 mx-auto">
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
                    <form method="POST" action="{{ route('logout') }}" class="inline">
                        @csrf
                        <button type="submit" class="text-gray-600 hover:text-gray-900 px-3 py-2 rounded-md text-sm font-medium inline-flex items-center px-4 py-2 border border-gray-300 border-transparent text-sm font-medium rounded-md transition duration-150 ease-in-out px-4 py-2 rounded font-medium transition-colors -link">{{ __('Logout') }}</button>
                    </form>
                @endguest
            </div>
        </div>
    </nav>

    <div class="container mx-auto px-4 mx-auto mt-5">
        <div class="flex flex-wrap">
            <div class="flex-1 -md-12">
                <h1>{{ __('Companies') }}</h1>
                <p class="lead">{{ __('Discover top companies and their job opportunities') }}</p>
                
                <!-- Search Form -->
                <div class="bg-white shadow rounded-lg overflow-hidden mb-4">
                    <div class="bg-white shadow rounded-lg overflow-hidden -body">
                        <form method="GET" action="{{ route('companies.index') }}">
                            <div class="flex flex-wrap">
                                <div class="flex-1 -md-4">
                                    <input type="text" name="search" class="w-full px-3 py-2 border border-gray-300 border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-primary-500" placeholder="{{ __('Company name...') }}" value="{{ request('search') }}">
                                </div>
                                <div class="flex-1 -md-3">
                                    <input type="text" name="location" class="w-full px-3 py-2 border border-gray-300 border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-primary-500" placeholder="{{ __('Location...') }}" value="{{ request('location') }}">
                                </div>
                                <div class="flex-1 -md-3">
                                    <select name="industry" class="w-full px-3 py-2 border border-gray-300 border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-primary-500">
                                        <option value="">{{ __('All Industries') }}</option>
                                        <option value="technology">{{ __('Technology') }}</option>
                                        <option value="healthcare">{{ __('Healthcare') }}</option>
                                        <option value="finance">{{ __('Finance') }}</option>
                                    </select>
                                </div>
                                <div class="flex-1 -md-2">
                                    <button type="submit" class="inline-flex items-center px-4 py-2 border border-gray-300 border-transparent text-sm font-medium rounded-md transition duration-150 ease-in-out px-4 py-2 rounded font-medium transition-colors -primary w-full">{{ __('Search') }}</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
                
                <!-- Company Listings -->
                <div class="flex flex-wrap">
                    @for($i = 1; $i <= 6; $i++)
                    <div class="flex-1 -md-6 mb-4">
                        <div class="bg-white shadow rounded-lg overflow-hidden">
                            <div class="bg-white shadow rounded-lg overflow-hidden -body">
                                <div class="flex items-center mb-3">
                                    <div class="bg-primary-600 rounded p-3 me-3">
                                        <i class="fas fa-building text-white fa-2x"></i>
                                    </div>
                                    <div>
                                        <h5 class="bg-white shadow rounded-lg overflow-hidden -title mb-0">{{ __('Sample Company') }} {{ $i }}</h5>
                                        <small class="text-gray-500">{{ __('Technology') }}</small>
                                    </div>
                                </div>
                                <p class="bg-white shadow rounded-lg overflow-hidden -text">{{ __('This is a sample company description that showcases what the company does and their mission.') }}</p>
                                <div class="flex justify-between items-center">
                                    <span class="badge bg-blue-500">{{ rand(10, 100) }} {{ __('Open Jobs') }}</span>
                                    <small class="text-gray-500">{{ __('New York, NY') }}</small>
                                </div>
                                <div class="mt-3">
                                    <a href="{{ route('company.show', $i) }}" class="inline-flex items-center px-4 py-2 border border-gray-300 border-transparent text-sm font-medium rounded-md transition duration-150 ease-in-out px-4 py-2 rounded font-medium transition-colors -primary">{{ __('View Company') }}</a>
                                    <button class="inline-flex items-center px-4 py-2 border border-gray-300 border-transparent text-sm font-medium rounded-md transition duration-150 ease-in-out px-4 py-2 rounded font-medium transition-colors -outline-secondary">{{ __('Follow') }}</button>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endfor
                </div>
                
                <!-- Pagination -->
                <nav aria-label="Company listings pagination">
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
    </div></body>
</html>

