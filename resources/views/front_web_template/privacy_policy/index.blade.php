@extends('front_web_template.layouts.app')
@section('title')
    {{ __('messages.setting.privacy_policy') }}
@endsection
@section('content')
    <section class="hero-section relative bg-gradient pt-15 pb-40">
        <div class="container mx-auto px-4 mx-auto px-4 mx-auto px-4 mx-auto">
            <div class="flex flex-wrap items-center justify-center">
                <div class="flex-1 lg-6 text-center mb-lg-0 mb-md-5 mb-sm-4">
                    <div class="hero-content">
                        <h1 class="text-gray-600 mb-2">{{ __('messages.setting.privacy_policy') }}</h1>
                        <nav aria-label="breadcrumb">
                            <ol class="breadcrumb justify-center mb-0">
                                <li class="breadcrumb-item">
                                    <a href="{{ route('front.home') }}" class="fs-18 text-gray">{{ __('web.home') }}
                                    </a>
                                </li>
                                <li class="breadcrumb-item text-indigo-600-600 fs-18" aria-current="page">
                                    {{ __('messages.setting.privacy_policy') }}
                                </li>
                            </ol>
                        </nav>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- <section class="tnc-section"> --}}
    {{-- <div class="auto- container mx-auto px-4 mx-auto px-4 mx-auto px-4 mx-auto"> --}}
    {{-- <div class="text-box"> --}}
    {{-- <p>{!! nl2br($privacyPolicy[0]['value']) !!}</p> --}}
    {{-- </div> --}}
    {{-- </div> --}}
    {{-- </section> --}}
    <div class="privacy-policy-section pt-60 pb-100">
        <div class="container mx-auto px-4 mx-auto px-4 mx-auto px-4 mx-auto">
            <div class="about-infyjob">
                <h5 class="fs-18 text-gray-600 mb-3">{{ __('messages.setting.privacy_policy') }}</h5>
                <p class="fs-16 text-gray mb-0">
                    {!! nl2br($privacyPolicy) !!}
                </p>
            </div>
        </div>
    </div>
@endsection

{{-- @section('scripts') --}}
{{--  --}}
{{-- <script src="{{asset('assets/js/front_register/front_register.js') }}"></script> --}}
{{-- @endsection --}}

@push('scripts')
    @vite('resources/js/pages/index.js')
@endpush
