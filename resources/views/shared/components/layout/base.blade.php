<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" dir="{{ __('messages.direction') }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    <title>{{ $title ?? config('app.name', 'JobPortal') }}</title>
    
    <!-- SEO Meta Tags -->
    @if(isset($seoTitle))
        <meta name="title" content="{{ $seoTitle }}">
    @endif
    
    @if(isset($seoDescription))
        <meta name="description" content="{{ $seoDescription }}">
    @endif
    
    @if(isset($seoKeywords))
        <meta name="keywords" content="{{ $seoKeywords }}">
    @endif
    
    <!-- Open Graph Meta Tags -->
    @if(isset($ogTitle))
        <meta property="og:title" content="{{ $ogTitle }}">
    @endif
    
    @if(isset($ogDescription))
        <meta property="og:description" content="{{ $ogDescription }}">
    @endif
    
    @if(isset($ogImage))
        <meta property="og:image" content="{{ $ogImage }}">
    @endif
    
    <meta property="og:url" content="{{ url()->current() }}">
    <meta property="og:type" content="website">
    
    <!-- Favicon -->
    <link rel="icon" href="{{ asset('favicon.ico') }}" type="image/x-icon">
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
    
    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    <!-- Additional Head Content -->
    @stack('head')
    
    <!-- Custom Styles -->
    @stack('styles')
</head>
<body class="font-sans antialiased {{ $bodyClass ?? '' }}">
    <!-- Page Content -->
    <main class="min-h-screen">
        {{ $slot }}
    </main>
    
    <!-- Flash Messages -->
    <x-shared.components.flash-messages />
    
    <!-- Additional Scripts -->
    @stack('scripts')
    
    <!-- Livewire Scripts -->
    @livewireScripts
</body>
</html> 