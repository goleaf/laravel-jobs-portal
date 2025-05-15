@php
    $settings = settings();
    $lang = session()->get('languageName');
@endphp
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title') | {{ getAppName() }}</title>
    <link rel="shortcut icon" href="{{ getSettingValue('favicon') }}" type="image/x-icon">
    <link rel="icon" href="{{ getSettingValue('favicon') }}" type="image/x-icon">
    <link href="{{ asset('assets/css/all.min.css') }}" rel="stylesheet" type="text/css">
    <link href="{{ asset('front_web/scss/bootstrap.css') }}" rel="stylesheet" type="text/css">

    <link rel="stylesheet" type="text/css" href="{{ asset('front_web/css/jquery-ui.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/iziToast.min.css') }}">

    <link href="{{ asset('assets/css/front-third-party.css') }}" rel="stylesheet" type="text/css">
    <link href="{{ mix('css/front-pages.css') }}" rel="stylesheet" type="text/css">

    @yield('page_css')
    @livewireStyles
    @routes

    @livewireScripts
    <script src="https://cdn.jsdelivr.net/gh/livewire/turbolinks@v0.1.x/dist/livewire-turbolinks.js"
            data-turbolinks-eval="false" data-turbo-eval="false"></script>
    <script src="https://js.stripe.com/v3/"></script>
    <script src="{{ mix('js/front-third-party.js') }}"></script>
    <script src='https://www.google.com/recaptcha/api.js'></script>
    <script>
        let siteKey = "{{ config('app.google_recaptcha_site_key') }}"
    </script>
    {{-- <script src="{{ mix('js/front_pages.js') }}"></script> --}}
    {{-- <script src="{{ asset('assets/js/custom/custom.js') }}"></script> --}}

    @yield('page_scripts')
    @foreach (googleJobSchema() as $jobSchema)
        {!! nl2br($jobSchema) !!}
    @endforeach
    <script src="{{ mix('js/front_pages.js') }}"></script>
</head>

<body {{ $lang == 'pt' || $lang == 'fr' || $lang == 'es' ? 'languages' : '' }}>
    <span class="header-padding"></span>
    @include('front_web_template.layouts.header')

    @yield('content')

    @if (Request::segment(1) != 'candidate-register' &&
            Request::segment(1) != 'employer-register' &&
            Request::segment(1) != 'users')
        @include('front_web_template.layouts.footer')
    @endif

    {{ Form::hidden('createNewLetterUrl', route('news-letter.create'), ['id' => 'createNewLetterUrl']) }}
    <script data-turbo-eval="false">
        let defaultCountryCodeValue = "{{ getSettingValue('default_country_code') }}";
        let currentFrontLang = "{{ session()->get('languageName') ?? 'en' }}";
        Lang.setLocale(currentFrontLang);
    </script>
     <script src="{{ asset('assets/js/custom/custom.js') }}"></script>
     <script src="{{ asset('assets/js/custom/helpers.js') }}"></script>
    </body>

</html>
