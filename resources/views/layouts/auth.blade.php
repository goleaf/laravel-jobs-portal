
@push('styles')
    @vite('resources/css/components/auth.css')
@endpush
@php
    $settings = settings();
    $lang = session()->get('languageName');
@endphp
<!DOCTYPE html>
<html lang="en">
<!--begin::Head-->
<head>
    <base href="../../../">
    <title>@yield('title') | {{ getAppName() }}</title>
    <meta charset="utf-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1"/>
    <link rel="shortcut icon" href="{{ getSettingValue('favicon') }}" type="image/x-icon">
    <!--begin::Fonts-->
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <!--end::Fonts-->
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/third-party.css') }}">
    <!--begin::Global Stylesheets Bundle(used by all pages)-->
    <link href="{{ mix('assets/css/style.css') }}" rel="stylesheet">
    <link href="{{ mix('css/plugins.css') }}" rel="stylesheet">
    <link href="{{ mix('assets/css/custom-auth.css') }}" rel="stylesheet">
    {{-- <link href="{{ mix('css/front-pages.css') }}" rel="stylesheet" type="text/css"> --}}

    
{{-- <link href="{{ asset('assets/plugins/plugins.bundle.css') }}" rel="stylesheet" type="text/css"/> --}}
{{-- <link href="{{ asset('assets/css/style.bundle.css') }}" rel="stylesheet" type="text/css"/> --}}
<!--end::Global Stylesheets Bundle-->
</head>
<!--end::Head-->
<!--begin::Body-->
<body {{ $lang == 'pt' || $lang == 'fr' || $lang == 'es' ? 'languages' : '' }}>
<!--begin::Main-->
<div class="flex flex- flex-1 flex-root">
    <div class="flex flex- flex flex-wrap flex-column-fluid">
        <div class="flex flex- flex-1 flex- flex flex-wrap fluid">
            <header class="bg-gradient">
                <nav class="bg-white shadow-sm border-b border border border-gray-300 -gray-300 -gray-200 bg-white shadow-sm expand-lg">
                    <div class="flex items-center my-3 mx-5 ms-auto">
                        <ul class="bg-white shadow-sm nav flex justify-end align-items-lg-center w-full">
                            <li class="">
                                <div class="relative inline-block text-left">
                                    <a class="rounded-md transition" type="button"
                                            aria-expanded="false">
                                        {{ getCurrentLanguageName() }}
                                    </a>
                                    <ul class="language- relative inline-block text-left -menu language-menu">
                                        @foreach (getUserLanguages() as $key => $value)
                                            <li class="languageSelection {{ checkLanguageSession() == $key ? 'languageSelection-active' : '' }}"
                                                data-prefix-value="{{ $key }}">
                                                <a href="javascript:void(0)"
                                                    class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 text-gray flex items-center {{ checkLanguageSession() == $key ?"active' : '' }}">
                                                    @if (array_key_exists($key, \App\Models\User::LANGUAGES_IMAGE))
                                                        @foreach (\App\Models\User::LANGUAGES_IMAGE as $imageKey => $imageValue)
                                                            @if ($imageKey == $key)
                                                                <img class="me-2 country-flag"
                                                                    src="{{ asset($imageValue) }}" />
                                                            @endif
                                                        @endforeach
                                                    @else
                                                        <i class="fa fa-flag me-2 fs-7 text-red-600" aria-hidden="true"
                                                            style="width: 20px;"></i>
                                                    @endif
                                                    {{ $value }}
                                                </a>
                                            </li>
                                        @endforeach
                                    </ul>
                                </div>
                            </li>
                        </ul>
                    </div>
                </nav>
            </header>
            <div class="content flex flex- flex-1 flex-column-fluid pt-7">
                <div class="flex flex-wrap flex-column-fluid">
                    @yield('content')
                </div>
            </div>
            <div class="container mx-auto px-4 mx-auto px-4 mx-auto px-4 mx-auto fluid">
                <footer class="border border border-gray-300 -gray-300 -top w-full pt-4 mt-7 text-center">
{{-- <p class="fs-6 text-gray-600">{{$settings['copy_right_text'] }} <a href="{{ route('front.home') }}" class="text-decoration-none"> --}}
{{-- {{$settings['application_name'] }}</a> --}}
{{-- </p> --}}
                </footer>
            </div>
        </div>
    </div>
</div>

<script src="{{ mix('js/auth-third-party.js') }}"></script>


<script src="{{ asset('assets/js/custom/custom.js') }}"></script>
<script src="{{ asset('assets/js/auto_fill/auto_fill.js') }}"></script>
</body>
<!--end::Body-->
</html>

@push('scripts')
    @vite('resources/js/components/auth.js')
@endpush
