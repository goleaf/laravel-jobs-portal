{{-- SEO Meta Tags Component --}}
@php
    $seoData = \App\Services\SeoService::generateMetaTags($page ?? 'default', $seoData ?? []);
@endphp

<title>{{ $seoData['title'] }}</title>
<meta name="description" content="{{ $seoData['description'] }}">
<meta name="keywords" content="{{ $seoData['keywords'] }}">

{{-- Open Graph Meta Tags --}}
<meta property="og:title" content="{{ $seoData['title'] }}">
<meta property="og:description" content="{{ $seoData['description'] }}">
<meta property="og:image" content="{{ $seoData['image'] }}">
<meta property="og:url" content="{{ $seoData['url'] }}">
<meta property="og:type" content="website">
<meta property="og:site_name" content="{{ config('app.name') }}">

{{-- Twitter Card Meta Tags --}}
<meta name="twitter:card" content="summary_large_image">
<meta name="twitter:title" content="{{ $seoData['title'] }}">
<meta name="twitter:description" content="{{ $seoData['description'] }}">
<meta name="twitter:image" content="{{ $seoData['image'] }}">

{{-- Additional SEO Meta Tags --}}
<meta name="robots" content="index, follow">
<meta name="author" content="{{ config('app.name') }}">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<link rel="canonical" href="{{ $seoData['url'] }}">

{{-- Structured Data --}}
@if(isset($structuredData))
    <script type="application/ld+json">
        {!! json_encode($structuredData, JSON_UNESCAPED_SLASHES) !!}
    </script>
@endif