
@push('styles')
    @vite('resources/css/pages/index.css')
@endpush
@extends('front_web.layouts.app')
@section('title')
    {{ __('web.job_seekers') }}
@endsection
@section('page_css')
    @if(\Illuminate\Support\Facades\App::getLocale() == 'ar')
        
    @endif
{{ -- <link rel="stylesheet" href=" asset('front_web/scss/jobs.css') "> -- }}
{{ -- <link rel="stylesheet" href=" asset('front_web/scss/companies.css') "> -- }}
@endsection
@section('content')
    <div class="job-seekers-page">
        <section class="hero-section relative bg-color-light py-40">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mx-auto px-4 mx-auto">
                <div class="flex-wrap flex items-center justify-center">
                    <div class="text-center flex-1 lg-6 mb-lg-0 mb-md-5 mb-sm-4">
                        <div class="hero-content">
                            <h1 class="mb-3 text-gray-600">
                                @lang('web.job_seekers')
                            </h1>
                            <nav aria-label="breadcrumb">
                                <ol class="mb-0 flex space-x-2 text-sm justify-center">
                                    <li class="flex space-x-2 text-sm -item"><a href="{{ route('front.home') }}" class="fs-18 text-gray">{{ __('web.home') }} </a>
                                    </li>
                                    <li class="flex space-x-2 text-sm -item text-indigo-600-600 fs-18" aria-current="page">@lang('web.job_seekers')</li>
                                </ol>
                            </nav>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <section class="latest-job-section py-60">
            @livewire('candidate-search')
        </section>
    </div>
@endsection
{{-- @section('scripts') --}}
{{--  --}}
{{-- @endsection --}}

@push('scripts')
    @vite('resources/js/pages/index.js')
@endpush
