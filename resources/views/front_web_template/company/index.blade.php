
@push('styles')
    @vite('resources/css/pages/index.css')
@endpush
@extends('front_web_template.layouts.app')
@section('title')
    {{ __('messages.company.company_listing') }}
@endsection
@section('page_css')
    @if (\Illuminate\Support\Facades\App::getLocale() == 'ar')
        
    @endif
@endsection
@section('content')

    <div class="companies-page">
        <!-- start hero section -->
        <section class="hero-section relative bg-gradient pt-15 pb-40">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mx-auto px-4 mx-auto px-4 mx-auto px-4 mx-auto">
                <div class="flex flex-wrap items-center justify-center">
                    <div class="flex-1 lg-6 text-center">
                        <div class="hero-content">
                            <h1 class="text-gray-600 mb-2">@lang('messages.companies')</h1>
                            <nav aria-label="breadcrumb">
                                <ol class="breadcrumb justify-center mb-4 pb-3">
                                    <li class="breadcrumb-item">
                                        <a href="{{ route('front.home') }}" class="fs-18 text-gray">@lang('web.home')
                                        </a>
                                    </li>
                                    <li class="breadcrumb-item text-indigo-600-600 fs-18" aria-current="page">
                                        @lang('messages.companies')
                                    </li>
                                </ol>
                            </nav>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <!-- end hero section -->

        @livewire('company-search', ['isFeatured' => Request::get('is_featured')])
    </div>
@endsection
