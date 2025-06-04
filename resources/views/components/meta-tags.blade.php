@props(['meta' => []])

@php
    $meta = \App\Services\SEOService::generateMetaTags($meta);
    $ogTags = \App\Services\SEOService::getOpenGraphTags($meta);
    $twitterTags = \App\Services\SEOService::getTwitterCardTags($meta);
@endphp

{{ -- Basic Meta Tags -- }}
<title>{{ $meta['title']  }}</title>
<meta name="description" content="{{ $meta['description']  }}">
<meta name="keywords" content="{{ $meta['keywords']  }}">
<meta name="author" content="{{ config('app.name')  }}">
<meta name="robots" content="index, follow">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

{{ -- Canonical URL -- }}
<link rel="canonical" href="{{ $meta['url']  }}">

{{ -- OpenGraph Tags -- }}
@foreach($ogTags as $property => $content)
    <meta property="{{ $property  }}" content="{{ $content  }}">
@endforeach

{{ -- Twitter Card Tags -- }}
@foreach($twitterTags as $name => $content)
    <meta name="{{ $name  }}" content="{{ $content  }}">
@endforeach

{{ -- Additional Meta Tags -- }}
<meta name="theme-color" content="#3B82F6">
<meta name="msapplication-TileColor" content="#3B82F6">
<meta name="apple-mobile-web-app-capable" content="yes">
<meta name="apple-mobile-web-app-status-bar-style" content="default">

{{ -- Favicon -- }}
<link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico')  }}">
<link rel="apple-touch-icon" href="{{ asset('images/apple-touch-icon.png')  }}">

{{ -- Structured Data -- }}
@if(isset($structuredData))
    <script type="application/ld+json">
        {!! json_encode($structuredData, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT) !!}
    </script>
@endif