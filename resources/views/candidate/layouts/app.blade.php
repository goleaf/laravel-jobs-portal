@php
    $settings = settings();
@endphp
<!DOCTYPE html>
<html lang="en">

<head>
    <base href="../">
    <title>@yield('title') | {{ getAppName() }}</title>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="shortcut icon" href="{{ getSettingValue('favicon') }}" /><link rel="stylesheet" type="text/css" href="{{ asset('assets/css/third-party.css') }}">
    @if (getLoggedInUser()->theme_mode)
        <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/custom-dark.css') }}">
        <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/style.dark.css') }}">
        <link rel="stylesheet" type="text/css" href="{{ asset('css/plugins.dark.css') }}">
    @else
        <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/style.css') }}">
        <link rel="stylesheet" type="text/css" href="{{ asset('css/plugins.css') }}">
    @endif
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/custom.css') }}">
    @livewireStyles
    @routes
    @livewireScripts
    @vite(['resources/css/app.css', 'resources/js/admin.js'])
</head>



<body class="overflow-x-hidden">
    <div class="flex flex-col flex-root">
        <div class="flex flex-col flex-column-fluid">
            <div class="header fixed-header">
                @include('candidate.layouts.header')
            </div>
            <div class="theme-wrapper flex flex-col flex- flex flex-wrap -fluid">
                <div class="flex flex-col flex- flex flex-wrap -fluid">
                    <div class="flex flex-col flex-column-fluid pt-7">
                        <div class="content flex-column-fluid">
                            <div class="w-full container mx-auto px-4 mx-auto -xxl">
                                @yield('content')
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="w-full container mx-auto px-4 mx-auto -xxl">
                @include('layouts.footer')
            </div>
            @include('candidate_profile.edit_profile_modal')
            @include('candidate_profile.change_password_modal')
        </div>
    </div>
    <script data-turbo-eval="false">
        var hostUrl = 'assets/';
        let getLoggedInUserLang = '{{ getCurrentLanguageCode() }}';
        let defaultCountryCodeValue = "{{ getSettingValue('default_country_code') }}"
        Lang.setLocale(getLoggedInUserLang)
    </script>
    @stack('scripts')
</body>

</html>
