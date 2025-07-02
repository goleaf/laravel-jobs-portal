<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    <title>{{ $title ?? 'Job Portal - Find Your Dream Job' }}</title>
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700&display=swap" rel="stylesheet" />
    
    <!-- Vite Assets -->
    @vite(['resources/css/app.css', 'resources/js/main.js'])
    
    <!-- Additional Meta Tags -->
    <meta name="description" content="Professional Job Portal - Find your dream job or hire the best talent">
    <meta name="keywords" content="jobs, careers, employment, hiring, recruitment">
    <meta name="author" content="Job Portal">
    
    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="/favicon.ico">
    
    @stack('head')
</head>
<body class="font-sans antialiased bg-gray-50 dark:bg-gray-900 text-gray-900 dark:text-gray-100">
    <!-- Loading Indicator -->
    <div id="initial-loader" class="fixed inset-0 bg-white z-50 flex items-center justify-center">
        <div class="text-center">
            <div class="inline-block animate-spin rounded-full h-8 w-8 border-b-2 border-indigo-600 mb-4"></div>
            <h2 class="text-xl font-semibold text-gray-900 mb-2">Loading Job Portal...</h2>
            <p class="text-gray-600">Please wait while we prepare your experience</p>
        </div>
    </div>

    <!-- Vue.js SPA Mount Point -->
    <div id="app"></div>
    
    <!-- Loading Fallback -->
    <noscript>
        <div class="min-h-screen flex items-center justify-center bg-gray-50">
            <div class="text-center">
                <h1 class="text-2xl font-bold text-gray-900 mb-4">JavaScript Required</h1>
                <p class="text-gray-600">This application requires JavaScript to be enabled in your browser.</p>
                <p class="text-gray-600 mt-2">Please enable JavaScript and refresh the page.</p>
            </div>
        </div>
    </noscript>
    
    @stack('scripts')

    <style>
        /* Hide loading indicator once Vue app is loaded */
        .vue-app-loaded #initial-loader {
            display: none;
        }
        
        /* Smooth transition for Vue app mounting */
        #app {
            opacity: 0;
            transition: opacity 0.3s ease-in-out;
        }
        
        .vue-app-loaded #app {
            opacity: 1;
        }
    </style>
</body>
</html> 