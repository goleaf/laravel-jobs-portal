<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- Title -->
    <title>
        @isset($title)
            {{ $title }} - {{ config('app.name') }}
        @else
            {{ __('auth.title') }} - {{ config('app.name') }}
        @endisset
    </title>

    <!-- SEO Meta Tags -->
    <meta name="description" content="{{ __('auth.meta_description') }}">
    <x-meta-tags />

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700&display=swap" rel="stylesheet" />

    <!-- Styles -->
    @vite(['resources/css/app.css'])
    @stack('styles')

    <!-- Scripts -->
    @vite(['resources/js/app.js'])
    
    <!-- Additional Head Content -->
    @stack('head')
</head>
<body class="font-sans antialiased bg-gray-50 dark:bg-gray-900">
    <div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0">
        <!-- Logo -->
        <div class="mb-6">
            @isset($logo)
                {{ $logo }}
            @else
                <a href="{{ route('home') }}" class="flex items-center justify-center">
                    <img src="{{ asset('images/logo.png') }}" alt="{{ config('app.name') }}" class="h-12 w-auto">
                    <span class="ml-2 text-2xl font-bold text-gray-900 dark:text-white">{{ config('app.name') }}</span>
                </a>
            @endisset
        </div>

        <!-- Auth Card -->
        <div class="w-full sm:max-w-md mt-6 px-6 py-4 bg-white dark:bg-gray-800 shadow-md overflow-hidden sm:rounded-lg">
            <!-- Flash Messages -->
            <x-flash-messages />

            <!-- Page Header -->
            @isset($header)
                <div class="mb-6 text-center">
                    {{ $header }}
                </div>
            @endisset

            <!-- Main Content -->
            {{ $slot }}
        </div>

        <!-- Footer Links -->
        <div class="mt-6 text-center">
            @isset($footerLinks)
                {{ $footerLinks }}
            @else
                <div class="flex flex-col sm:flex-row items-center justify-center space-y-2 sm:space-y-0 sm:space-x-4 text-sm text-gray-600 dark:text-gray-400">
                    <a href="{{ route('home') }}" class="hover:text-gray-900 dark:hover:text-gray-100">
                        {{ __('auth.back_to_home') }}
                    </a>
                    <span class="hidden sm:inline">•</span>
                    <a href="{{ route('privacy-policy') }}" class="hover:text-gray-900 dark:hover:text-gray-100">
                        {{ __('auth.privacy_policy') }}
                    </a>
                    <span class="hidden sm:inline">•</span>
                    <a href="{{ route('terms-conditions') }}" class="hover:text-gray-900 dark:hover:text-gray-100">
                        {{ __('auth.terms_conditions') }}
                    </a>
                </div>
            @endisset
        </div>

        <!-- Language Switcher -->
        <div class="mt-4">
            <x-language-switcher />
        </div>
    </div>

    <!-- Scripts -->
    @stack('scripts')
</body>
</html> 