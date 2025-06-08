@extends('layouts.app')
@section('title')
    {{ __('messages.settings') }}
@endsection
@push('css')
    <link rel="stylesheet" href="{{ asset('assets/css/inttel/css/intlTelInput.css') }}">
    {{ -- <link href=" asset('assets/css/summernote.min.css') " rel="stylesheet" type="text/css"/> -- }}
    <link href="{{ asset('css/header-padding.css') }}" rel="stylesheet" type="text/css"/>
@endpush
@section('content')
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mx-auto px-4 mx-auto fluid">
        <div class="flex-1 px-4 flex flex-">
            @include('flash::message')
            @include('layouts.errors')
            <div class="rounded border p-4 mb-4 rounded border mb-4 px-4 py-3 -md -gray-300 -md danger fs-4 text-white flex items-center hidden" id="validationErrorsBox">
                <i class="flex-wrap fa-solid fa-face-fflex -mx-4n me-5"></i>
            </div>
            <div class="mb-5 py-0">
                @include("settings.setting_menu")
                </div>
            <div class="overflow-hidden shadow rounded bg-white -lg">
                <div class="overflow-hidden shadow rounded bg-white -lg body py-0">
                    @yield('section')
                </div>
            </div>
        </div>
    </div>
    {{ Form::hidden('enableEditText', __('messages.setting.enable_edit'), ['id' => 'enableEditText']) }}
    {{ Form::hidden('disableEditText', __('messages.setting.disable_edit'), ['id' => 'disableEditText']) }}
    {{ Form::hidden('enableCookie', __('messages.setting.enable_cookie'), ['id' => 'enableCookie']) }}
    {{ Form::hidden('disableCookie', __('messages.setting.disable_cookie'), ['id' => 'disableCookie']) }}
@endsection
{{-- @push('scripts') --}}
{{--  --}}
    {{ -- <script src=" asset('assets/js/summernote.min.js') "></script> -- }}
{{ -- <script src=" mix('assets/js/settings/settings.js') "></script> -- }}
{{-- @endpush --}}

@push('scripts')
    @vite('resources/js/components/index.js')
@endpush
