<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    <title>{{ $title ?? 'Laravel App' }}</title>
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700&display=swap" rel="stylesheet" />
    
    <!-- Vite Assets -->
    @vite('resources/css/app.css')
    
    <!-- Additional Meta Tags -->
    <meta name="description" content="Professional Job Portal - Find your dream job or hire the best talent">
    <meta name="keywords" content="jobs, careers, employment, hiring, recruitment">
    <meta name="author" content="Job Portal">
    
    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="/favicon.ico">
    
    @stack('head')
</head>
<body class="font-sans antialiased bg-gray-50 dark:bg-gray-900 text-gray-900 dark:text-gray-100">
    <!-- Vue.js SPA Mount Point -->
    <div id="app"></div>
    
    <!-- Loading Fallback -->
    <noscript>
        <div class="min-h-screen flex items-center justify-center bg-gray-50">
            <div class="text-center">
                <h1 class="text-2xl font-bold text-gray-900 mb-4">JavaScript Required</h1>
                <p class="text-gray-600">This application requires JavaScript to be enabled in your browser.</p>
            </div>
        </div>
    </noscript>
    
    @stack('scripts')
</body>
</html> 