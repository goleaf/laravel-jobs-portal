<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'Forms') - {{ config('app.name') }}</title>

    <!-- Fonts -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap">

    <!-- Styles -->
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
    
    <!-- Scripts -->
    <script src="{{ asset('js/app.js') }}" defer></script>
</head>
<body class="font-sans antialiased bg-gray-100 min-h-screen flex flex-col">
    <header class="bg-white shadow-sm">
        <div class="container mx-auto px-4 py-4">
            <div class="flex justify-between items-center">
                <a href="{{ route('front') }}" class="text-xl font-bold text-blue-600">
                    {{ config('app.name', 'Job Portal') }}
                </a>
                <nav class="space-x-4">
                    <a href="{{ route('front') }}" class="text-gray-600 hover:text-blue-600">Home</a>
                    <a href="{{ route('contact') }}" class="text-gray-600 hover:text-blue-600">Contact</a>
                    <a href="{{ url('forms/validation') }}" class="text-gray-600 hover:text-blue-600">Validation Example</a>
                    <a href="{{ url('forms/alpine') }}" class="text-gray-600 hover:text-blue-600">Alpine.js Example</a>
                </nav>
            </div>
        </div>
    </header>

    <main class="flex-grow">
        <div class="container mx-auto px-4 py-8">
            @if(session('success'))
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
                    {{ session('success') }}
                </div>
            @endif

            @if(session('error'))
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                    {{ session('error') }}
                </div>
            @endif

            @yield('content')
        </div>
    </main>

    <footer class="bg-white shadow-sm mt-auto">
        <div class="container mx-auto px-4 py-4">
            <div class="text-center text-gray-500 text-sm">
                &copy; {{ date('Y') }} {{ config('app.name', 'Job Portal') }}. All rights reserved.
            </div>
        </div>
    </footer>

    @stack('scripts')
</body>
</html> 