<!DOCTYPE html>
<html lang="en">
<!--begin::Head-->

<head>
    <base href="../">
    <title>@yield('title') | {{ getAppName() }}</title>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="shortcut icon" href="{{ getSettingValue('favicon') }}" />
    <!--begin::Fonts--><!--end::Fonts-->

    @vite(['resources/js/app.js'])
    
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/third-party.css') }}">
    @if (getLoggedInUser()->theme_mode)
        <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/custom-dark.css') }}">
        <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/style.dark.css') }}">
        <link rel="stylesheet" type="text/css" href="{{ asset('css/plugins.dark.css') }}">
    @else
        <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/style.css') }}">
        <link rel="stylesheet" type="text/css" href="{{ asset('css/plugins.css') }}">
    @endif
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/custom.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/pagination-fix.css') }}">
    @livewireStyles
    @routes

    <!-- Add Alpine.js CDN -->@livewireScripts<script src="{{ asset('js/third-party.js') }}"></script>
    <script src="{{ asset('js/pages.js') }}"></script>

</head>
<!--end::Head-->
<!--begin::Body-->

<body class="overflow-x-hidden">
    <div class="flex flex- flex-1 flex-root vh-100">
        <div class="flex flex- flex flex-wrap flex-column-fluid">
            @include('layouts.sidebar')
            <div class="wrapper flex flex- flex-1 flex- flex flex-wrap fluid">
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mx-auto px-4 mx-auto px-4 mx-auto px-4 mx-auto fluid flex align-items-stretch justify-between px-0">
                    @include('layouts.header')
                </div>
                <div class="content flex flex- flex-1 flex-column-fluid pt-7">
                    @yield('header_toolbar')
                    <div class="flex flex-wrap flex-column-fluid">
                        @yield('content')
                    </div>
                </div>
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mx-auto px-4 mx-auto px-4 mx-auto px-4 mx-auto fluid">
                    @include('layouts.footer')
                </div>
            </div>
        </div>
    </div>
    {{ Illuminate\Support\Facades\Log::info(Config::get('app.locale')) }}
    {{ Illuminate\Support\Facades\Log::info(getLoggedInUser()->language) }}
    @include('user_profile.edit_profile_modal')
    @include('user_profile.change_password_modal')

    <!--begin::Javascript-->
    {{ Form::hidden('profile-phone-no', old('region_code') . old('phone'), ['id' => 'profilePhoneNo']) }}

    
    <!--end::Page Custom Javascript-->
    <!--end::Javascript-->

    <!-- Notification Component -->
    <x-notification />
</body>
<!--end::Body-->

</html>

@push('scripts')
    @vite('resources/js/components/app.js')
@endpush
