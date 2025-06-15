<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    <title>@yield('title', config('app.name', 'Job Portal'))</title>
    
    @vite(['resources/css/app.css', 'resources/js/app.ts'])
    
    @stack('head')
</head>
<body class="font-sans antialiased">
    <div id="app">
        <!-- Navigation -->
        <nav class="bg-white shadow">
            <div class="max-w-7xl mx-auto px-4">
                <div class="flex justify-between h-16">
                    <div class="flex items-center">
                        <a href="{{ url('/') }}" class="text-xl font-semibold">
                            {{ config('app.name', 'Job Portal') }}
                        </a>
                    </div>
                    <div class="flex items-center space-x-4">
                        @auth
                            <span class="text-gray-700">{{ auth()->user()->name }}</span>
                            <form method="POST" action="{{ route('logout') }}" class="inline">
                                @csrf
                                <button type="submit" class="text-gray-700 hover:text-gray-900">
                                    Logout
                                </button>
                            </form>
                        @else
                            <a href="{{ route('login') }}" class="text-gray-700 hover:text-gray-900">Login</a>
                            <a href="{{ route('register') }}" class="text-gray-700 hover:text-gray-900">Register</a>
                        @endauth
                    </div>
                </div>
            </div>
        </nav>

        <!-- Page Content -->
        <main class="min-h-screen bg-gray-50">
            @yield('content')
        </main>
    </div>
    
    @stack('scripts')
</body>
</html> 