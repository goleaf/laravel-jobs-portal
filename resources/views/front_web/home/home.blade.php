<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', __('home.job_portal')) }} - {{ __('home.home') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.ts'])
</head>
<body class="bg-gray-50 font-sans antialiased">
    <div class="min-h-screen">
        <!-- Simple Header -->
        <header class="bg-white shadow">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between h-16">
                    <div class="flex items-center">
                        <h1 class="text-xl font-bold text-gray-900">{{ config('app.name', __('home.job_portal')) }}</h1>
                    </div>
                    <div class="flex items-center space-x-4">
                        <a href="{{ route('jobs.index') }}" class="text-gray-700 hover:text-blue-600">{{ __('home.nav_jobs') }}</a>
                        <a href="{{ route('companies.index') }}" class="text-gray-700 hover:text-blue-600">{{ __('home.nav_companies') }}</a>
                        <a href="{{ route('contact') }}" class="text-gray-700 hover:text-blue-600">{{ __('home.nav_contact') }}</a>
                    </div>
                </div>
            </div>
        </header>

        <!-- Main Content -->
        <main>
            <div class="max-w-7xl mx-auto py-12 px-4 sm:px-6 lg:px-8">
                <div class="text-center">
                    <h1 class="text-4xl font-bold text-gray-900 sm:text-6xl">
                        {{ __('home.welcome_title') }}
                    </h1>
                    <p class="mt-6 text-lg leading-8 text-gray-600">
                        {{ __('home.welcome_description') }}
                    </p>
                    <div class="mt-10 flex items-center justify-center gap-x-6">
                        <a href="{{ route('jobs.index') }}" 
                           class="rounded-md bg-blue-600 px-3.5 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-blue-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-blue-600">
                            {{ __('home.browse_jobs') }}
                        </a>
                        <a href="{{ route('companies.index') }}" 
                           class="text-sm font-semibold leading-6 text-gray-900">
                            {{ __('home.view_companies') }} <span aria-hidden="true">→</span>
                        </a>
                    </div>
                </div>

                <!-- Stats Section -->
                <div class="mt-16">
                    <div class="mx-auto max-w-7xl">
                        <div class="grid grid-cols-1 gap-x-8 gap-y-16 text-center lg:grid-cols-3">
                            <div class="mx-auto flex max-w-xs flex-col gap-y-4">
                                <dt class="text-base leading-7 text-gray-600">{{ __('home.stats_jobs_available') }}</dt>
                                <dd class="order-first text-3xl font-semibold tracking-tight text-gray-900 sm:text-5xl">1,000+</dd>
                            </div>
                            <div class="mx-auto flex max-w-xs flex-col gap-y-4">
                                <dt class="text-base leading-7 text-gray-600">{{ __('home.stats_companies') }}</dt>
                                <dd class="order-first text-3xl font-semibold tracking-tight text-gray-900 sm:text-5xl">500+</dd>
                            </div>
                            <div class="mx-auto flex max-w-xs flex-col gap-y-4">
                                <dt class="text-base leading-7 text-gray-600">{{ __('home.stats_success_stories') }}</dt>
                                <dd class="order-first text-3xl font-semibold tracking-tight text-gray-900 sm:text-5xl">10,000+</dd>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </main>

        <!-- Simple Footer -->
        <footer class="bg-gray-900">
            <div class="max-w-7xl mx-auto py-12 px-4 sm:px-6 lg:px-8">
                <div class="text-center">
                    <p class="text-base text-gray-400">&copy; {{ date('Y') }} {{ config('app.name') }}. {{ __('home.all_rights_reserved') }}</p>
                </div>
            </div>
        </footer>
</div>
    {{-- @vite(['resources/js/app.js']) --}}
</body>
</html>