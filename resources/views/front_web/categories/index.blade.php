@extends('front_web.layouts.app')
@section('title')
    {{ __('web.job_seekers') }}
@endsection
@section('page_css')
    @if(\Illuminate\Support\Facades\App::getLocale() == 'ar')
        <style>
            .candidate-main ul.pagination {
                direction: rtl;
            }
        </style>
    @endif
    {{ --    <link rel="stylesheet" href="{{ asset('front_web/scss/jobs.css') }}">--}}
    {{ --    <link rel="stylesheet" href="{{ asset('front_web/scss/companies.css') }}">--}}
@endsection
@section('content')
    <div class="job-seekers-page">
        <section class="hero-section relative bg-gray-100 py-40">
            <div class="container mx-auto px-4 mx-auto">
                <div class="flex flex-wrap items-center justify-center">
                    <div class="flex-1 lg-6 text-center mb-lg-0 mb-md-5 mb-sm-4">
                        <div class="hero-content">
                            <h1 class="text-gray-600 mb-3">
                                @lang('web.post_menu.categories')
                            </h1>
                            <nav aria-label="breadcrumb">
                                <ol class="breadcrumb justify-center mb-0">
                                    <li class="breadcrumb-item"><a href="{{ route('front.home') }}" class="fs-18 text-gray">{{ __('web.home') }} </a>
                                    </li>
                                    <li class="breadcrumb-item text-primary-600 fs-18" aria-current="page">@lang('web.post_menu.categories')</li>
                                </ol>
                            </nav>
                        </div>
                    </div>
                </div>
            </div>
        </section>
            @if(count($jobCategories) > 0)
                <section class="popular-job-categories-section py-100">
                    <div class="container mx-auto px-4 mx-auto">
                        <div class="job-categories- bg-white shadow rounded-lg overflow-hidden">
                            <div class="flex flex-wrap">
                                @foreach($jobCategories as $jobCategory)
                                    <div class="lg:w-4/12 px-2 flex-1 md-6 px-xl-3 mb-40">
                                        <div class="bg-white shadow rounded-lg overflow-hidden py-30">
                                            <div class="flex flex-wrap items-center">
                                                <div class="flex-1 -3">
                                                    <img src="{{ $jobCategory->image_url }}" class="bg-white shadow rounded-lg overflow-hidden img" alt="...">
                                                </div>
                                                <div class="flex-1 -8">
                                                    <div class="bg-white shadow rounded-lg overflow-hidden body ps-xl-0 ps-lg-3">
                                                        <a href="{{ route('front.search.jobs',array('categories'=> $jobCategory->id)) }}" class="text-gray-600 primary-link-hover">
                                                            <h5 class="bg-white shadow rounded-lg overflow-hidden title fs-18">{{ html_entity_decode($jobCategory->name) }}</h5>
                                                        </a>
                                                        <p class="bg-white shadow rounded-lg overflow-hidden text fs-14 text-gray">
                                                            {{ (($jobCategory->jobs_count) ? $jobCategory->jobs_count : 0) .' '. __('web.open_positions') }}
                                                        </p>
                                                    </div>
                                                </div>
                                                @if($jobCategory->is_featured)
                                                    <div class="flex-1 -1 icon relative pe-0">
                                                        <i class="text-primary-600 fa-solid fa-bookmark"></i>
                                                    </div>
                                                @endif
                                                @if($jobCategory->jobs_count <= 0)
                                                    <div class="bg-white shadow rounded-lg overflow-hidden desc mt-3">
                                                        <div class="desc flex mt-2">
                                                            <p class="jobs-position bg-gray fs-14 mb-0 me-3 text-gray-600">
                                                                {{ __('web.no_positions') }}
                                                            </p>
                                                        </div>
                                                    </div>
                                                @else
                                                    <div class="bg-white shadow rounded-lg overflow-hidden desc mt-3">
                                                        <div class="desc flex mt-2">
                                                            <a href="{{ route('front.search.jobs',array('categories'=> $jobCategory->id)) }}" class="jobs-position  fs-14 mb-0 me-3">
                                                                {{ __('web.open_positions') }} ->
                                                            </a>
                                                        </div>
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </section>
            @endif
    </div>
@endsection
