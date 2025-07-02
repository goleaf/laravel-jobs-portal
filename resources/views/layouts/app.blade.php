<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="locale" content="{{ app()->getLocale() }}">
    
    <title>{{ config('app.name', 'Laravel') }}</title>
    <meta name="description" content="@yield('description', __('common.site_description'))">
    <meta name="keywords" content="@yield('keywords', __('common.site_keywords'))">
    
    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">
    
    <!-- Preload critical fonts -->
    <link rel="preload" href="{{ asset('fonts/inter-var.woff2') }}" as="font" type="font/woff2" crossorigin>
    
    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/main.js'])
    
    <!-- Fonts -->
    <!-- Local fonts loaded via Vite -->
    
    <!-- Additional page styles -->
    @stack('styles')
    
    <!-- PWA Meta -->
    <meta name="theme-color" content="#1f2937">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    
    <!-- Open Graph / Facebook -->
    <meta property="og:type" content="website">
    <meta property="og:url" content="{{ url()->current() }}">
    <meta property="og:title" content="@yield('title', config('app.name'))">
    <meta property="og:description" content="@yield('description', __('common.site_description'))">
    <meta property="og:image" content="@yield('og_image', asset('images/og-default.jpg'))">
    
    <!-- Twitter -->
    <meta property="twitter:card" content="summary_large_image">
    <meta property="twitter:url" content="{{ url()->current() }}">
    <meta property="twitter:title" content="@yield('title', config('app.name'))">
    <meta property="twitter:description" content="@yield('description', __('common.site_description'))">
    <meta property="twitter:image" content="@yield('og_image', asset('images/og-default.jpg'))">
</head>
<body class="font-sans antialiased">
    <div class="min-h-screen bg-gray-100">
        {{-- @include('layouts.navigation') --}}

        <!-- Main app wrapper -->
        <div id="app" class="min-h-screen flex flex-col">
            <!-- Header -->
            <header class="bg-white dark:bg-gray-800 shadow-sm border-b border-gray-200 dark:border-gray-700 sticky top-0 z-40">
                <x-ui.header />
            </header>

            <!-- Main content area -->
            <main id="main-content" class="flex-1 flex flex-col">
                <!-- Breadcrumbs -->
                @if (!request()->routeIs('home'))
                    <x-ui.breadcrumbs />
                @endif

                <!-- Flash messages -->
                <x-ui.flash-messages />

                <!-- Page content -->
                <div class="flex-1">
                    @yield('content')
                </div>
            </main>

            <!-- Footer -->
            <footer class="bg-gray-900 dark:bg-gray-950 text-white mt-auto">
                <x-ui.footer />
            </footer>
        </div>

        <!-- Mobile menu overlay -->
        <div id="mobile-menu-overlay" class="fixed inset-0 bg-gray-900/50 backdrop-blur-sm z-30 hidden lg:hidden"></div>

        <!-- Initialize app -->
        <script>
            // Initialize theme
            if (localStorage.theme === 'dark' || (!('theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
                document.documentElement.classList.add('dark')
            } else {
                document.documentElement.classList.remove('dark')
            }

            // Initialize language and locale
            window.App = {
                locale: '{{ app()->getLocale() }}',
                fallbackLocale: '{{ config('app.fallback_locale') }}',
                csrfToken: '{{ csrf_token() }}',
                baseUrl: '{{ url('/') }}',
                translations: @json(__('common'))
            };

            // Initialize multilingual system
            document.addEventListener('DOMContentLoaded', function() {
                if (window.UniversalI18nSystem) {
                    window.i18n = new UniversalI18nSystem();
                }
            });
        </script>

        <!-- Page-specific JavaScript -->
        @stack('page-scripts')
    </div>
</body>
</html> 