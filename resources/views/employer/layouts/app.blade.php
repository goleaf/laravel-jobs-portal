<!DOCTYPE html>
<html lang="en">
<!--begin::Head-->
<head>
    @include('google_analytics')
    <base href="../">
    <title>@yield('title') | {{ getAppName() }}</title>
    <meta charset="utf-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1"/>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="shortcut icon" href="{{ getSettingValue('favicon') }}"/><link rel="stylesheet" type="text/css" href="{{ asset('assets/css/third-party.css') }}">
    @if(getLoggedInUser()->theme_mode)
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
<script src="https://js.stripe.com/v3/"></script>


<body class="overflow-x-hidden">
<div class="flex flex- flex-1 flex-root">
    <div class="flex flex- flex-1 flex-column-fluid">
        <div class="header fixed-header">
            @include('employer.layouts.header')
        </div>
        <div class="theme-wrapper flex flex- flex-1 flex- flex flex-wrap fluid">
            <div class="flex flex- flex-1 flex- flex flex-wrap fluid">
                <div class="flex flex- flex-1 flex-column-fluid pt-7">
                    <div class="content flex-column-fluid">
                        <div class="w-full max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mx-auto px-4 mx-auto px-4 mx-auto px-4 mx-auto xxl">
                            @yield('content')
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="w-full max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mx-auto px-4 mx-auto px-4 mx-auto px-4 mx-auto xxl">
            @include('layouts.footer')
        </div>
        @include('employer_profile.edit_profile_modal')
        @include('employer_profile.change_password_modal')
    </div>
</div>
{{ Form::hidden('employerProfileData',true,['id'=>'indexEmployerProfileData']) }}
{{ Form::hidden('default-image-url', asset('assets/img/infyom-logo.png'), ['id' => 'defaultImageUrl']) }}


@stack('scripts')
</body>
</html>

@push('scripts')
    @vite('resources/js/components/app.js')
@endpush
