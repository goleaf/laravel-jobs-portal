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
    <div class="flex-1 px-4 flex flex- flex-root">
        <div class="flex-1 px-4 flex- flex-1 px-4 flex flex- -fluid">
            <div class="header fixed-header">
                @include('candidate.layouts.header')
            </div>
            <div class="flex-wrap flex-1 px-4 theme-wrapper flex flex- flex- flex fluid">
                <div class="flex-wrap flex-1 px-4 flex flex- flex- flex fluid">
                    <div class="flex-1 px-4 flex- flex-1 px-4 flex flex- -fluid pt-7">
                        <div class="flex-1 px-4 flex- content -fluid">
                            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 w-full mx-auto px-4 mx-auto xxl">
                                @yield('content')
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 w-full mx-auto px-4 mx-auto xxl">
                @include('layouts.footer')
            </div>
            @include('candidate_profile.edit_profile_modal')
            @include('candidate_profile.change_password_modal')
        </div>
    </div>
    
    @stack('scripts')
</body>

</html>

@push('scripts')
    @vite('resources/js/components/app.js')
@endpush
