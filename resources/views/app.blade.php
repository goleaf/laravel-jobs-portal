<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    <title>{{ $title ?? __('app.default_title') }}</title>
    
    <!-- Vite Assets (includes local fonts) -->
    @vite(['resources/css/app.css'])
    
    <!-- Additional Meta Tags -->
    <meta name="description" content="{{ __('app.meta_description') }}">
    <meta name="keywords" content="{{ __('app.meta_keywords') }}">
    <meta name="author" content="{{ __('app.meta_author') }}">
    
    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="/favicon.ico">
    
    @stack('head')
</head>
<body class="font-sans antialiased bg-gray-50 dark:bg-gray-900 text-gray-900 dark:text-gray-100">
    <!-- Loading Indicator -->
    <div id="initial-loader" class="fixed inset-0 bg-white z-50 flex items-center justify-center">
        <div class="text-center">
            <div class="inline-block animate-spin rounded-full h-8 w-8 border-b-2 border-indigo-600 mb-4"></div>
            <h2 class="text-xl font-semibold text-gray-900 mb-2">{{ __('app.loading_title') }}</h2>
            <p class="text-gray-600">{{ __('app.loading_message') }}</p>
        </div>
    </div>

    <!-- Page Content -->
    @yield('content')
    
    <!-- Loading Fallback -->
    <noscript>
        <div class="min-h-screen flex items-center justify-center bg-gray-50">
            <div class="text-center">
                <h1 class="text-2xl font-bold text-gray-900 mb-4">{{ __('app.javascript_required') }}</h1>
                <p class="text-gray-600">{{ __('app.javascript_required_message') }}</p>
                <p class="text-gray-600 mt-2">{{ __('app.javascript_enable_message') }}</p>
            </div>
        </div>
    </noscript>
    
    @stack('scripts')
</body>
</html> 