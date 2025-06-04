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
            {{ config('app.name') }}
        @endisset
    </title>

    <!-- SEO Meta Tags -->
    @isset($meta)
        {{ $meta }}
    @else
        <x-meta-tags />
    @endisset

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700&display=swap" rel="stylesheet" />

    <!-- Flux Appearance -->
    @fluxAppearance

    <!-- Styles -->
    @vite(['resources/css/app.css'])
    @stack('styles')

    <!-- Scripts -->
    @vite(['resources/js/app.js'])
    
    <!-- Additional Head Content -->
    @stack('head')
</head>
<body class="font-sans antialiased bg-gray-50 dark:bg-gray-900">
    <div class="min-h-screen flex flex-col">
        <!-- Header/Navigation -->
        @isset($header)
            <flux:header class="bg-white dark:bg-gray-800 shadow">
                {{ $header }}
            </flux:header>
        @else
            <x-navigation />
        @endisset

        <!-- Breadcrumbs -->
        @isset($breadcrumbs)
            <nav class="bg-gray-100 dark:bg-gray-700 px-4 py-2">
                {{ $breadcrumbs }}
            </nav>
        @endisset

        <!-- Flash Messages -->
        <x-flash-messages />

        <!-- Main Content -->
        <flux:main class="flex-1">
            @if(isset($sidebar))
                <div class="flex">
                    <!-- Sidebar -->
                    <flux:sidebar class="w-64 bg-white dark:bg-gray-800 shadow-sm">
                        {{ $sidebar }}
                    </flux:sidebar>
                    
                    <!-- Content with Sidebar -->
                    <div class="flex-1 p-6">
                        {{ $slot }}
                    </div>
                </div>
            @else
                <!-- Content without Sidebar -->
                <div class="container mx-auto px-4 py-6">
                    {{ $slot }}
                </div>
            @endif
        </flux:main>

        <!-- Footer -->
        @isset($footer)
            <footer class="bg-gray-100 dark:bg-gray-800 mt-auto">
                {{ $footer }}
            </footer>
        @else
            <x-footer />
        @endisset
    </div>

    <!-- Flux Scripts -->
    @fluxScripts
    
    <!-- Additional Scripts -->
    @stack('scripts')
</body>
</html> 