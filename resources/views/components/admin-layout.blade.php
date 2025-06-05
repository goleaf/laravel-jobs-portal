<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- Title -->
    <title>
        @isset($title)
            {{ $title }} - {{ __('admin.title') }} - {{ config('app.name') }}
        @else
            {{ __('admin.title') }} - {{ config('app.name') }}
        @endisset
    </title>

    <!-- SEO Meta Tags -->
    <meta name="robots" content="noindex, nofollow">
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
    <div class="min-h-screen flex">
        <!-- Sidebar -->
        <aside id="admin-sidebar" class="w-64 bg-gray-800 dark:bg-gray-900 text-white flex flex-col fixed inset-y-0 left-0 z-50 lg:static lg:z-auto">
            <!-- Logo -->
            <div class="flex items-center justify-center h-16 bg-gray-900 dark:bg-gray-800">
                <h2 class="text-xl font-bold">{{ __('admin.title') }}</h2>
            </div>

            <!-- Navigation -->
            <nav class="flex-1 px-2 py-4 space-y-2 overflow-y-auto">
                @isset($adminNavigation)
                    {{ $adminNavigation }}
                @else
                    <x-admin-navigation />
                @endisset
            </nav>

            <!-- User Info -->
            <div class="p-4 border-t border-gray-700">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <img class="h-8 w-8 rounded-full" src="{{ auth()->user()->avatar ?? '/images/default-avatar.png' }}" alt="{{ auth()->user()->name }}">
                    </div>
                    <div class="ml-3">
                        <p class="text-sm font-medium">{{ auth()->user()->name }}</p>
                        <p class="text-xs text-gray-400">{{ __('admin.logged_in') }}</p>
                    </div>
                </div>
            </div>
        </aside>

        <!-- Main Content -->
        <div class="flex-1 flex flex-col lg:ml-0">
            <!-- Top Navigation -->
            <header class="bg-white dark:bg-gray-800 shadow-sm border-b border-gray-200 dark:border-gray-700">
                <div class="px-4 sm:px-6 lg:px-8">
                    <div class="flex justify-between items-center h-16">
                        <!-- Mobile menu button -->
                        <button type="button" class="lg:hidden p-2 rounded-md text-gray-400 hover:text-gray-500 hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-inset focus:ring-indigo-500" onclick="toggleSidebar()">
                            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                            </svg>
                        </button>

                        <!-- Breadcrumbs -->
                        @isset($breadcrumbs)
                            <nav class="flex">
                                {{ $breadcrumbs }}
                            </nav>
                        @endisset

                        <!-- Header Actions -->
                        <div class="flex items-center space-x-4">
                            @isset($headerActions)
                                {{ $headerActions }}
                            @endisset

                            <!-- Theme Switcher -->
                            <x-theme-switch />

                            <!-- Language Switcher -->
                            <x-language-switcher />

                            <!-- Logout -->
                            <form method="POST" action="{{ route('logout') }}" class="inline">
                                @csrf
                                <button type="submit" class="text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200">
                                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                                    </svg>
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </header>

            <!-- Flash Messages -->
            <x-flash-messages />

            <!-- Page Content -->
            <main class="flex-1 p-6">
                @isset($pageHeader)
                    <div class="mb-6">
                        {{ $pageHeader }}
                    </div>
                @endisset

                {{ $slot }}
            </main>
        </div>
    </div>

    <!-- Mobile Sidebar Overlay -->
    <div id="sidebar-overlay" class="lg:hidden fixed inset-0 z-40 bg-gray-600 bg-opacity-75 hidden" onclick="toggleSidebar()"></div>

    <!-- Scripts -->
    
    @stack('scripts')
</body>
</html> 
@push('scripts')
    @vite('resources/js/components/admin-layout.js')
@endpush
