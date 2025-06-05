
@push('styles')
    @vite('resources/css/pages/index.css')
@endpush
@extends('front_web_template.layouts.app')
@section('title')
    {{ __('web.job_seekers') }}
@endsection
@section('page_css')
    @if (\Illuminate\Support\Facades\App::getLocale() == 'ar')
        
    @endif
@endsection
@section('content')
    <div class="job-seekers-page">
        <section class="hero-section relative bg-gradient pt-15 pb-40">
            <div class="container mx-auto px-4 mx-auto px-4 mx-auto px-4 mx-auto">
                <div class="flex flex-wrap items-center justify-center">
                    <div class="flex-1 lg-6 text-center mb-lg-0 mb-md-5 mb-sm-4">
                        <div class="hero-content">
                            <h1 class="text-gray-600 mb-2">@lang('web.post_menu.categories')</h1>
                            <nav aria-label="breadcrumb">
                                <ol class="breadcrumb justify-center mb-0">
                                    <li class="breadcrumb-item">
                                        <a href="{{ route('front.home') }}" class="fs-18 text-gray">{{ __('web.home') }}
                                        </a>
                                    </li>
                                    <li class="breadcrumb-item text-indigo-600-600 fs-18" aria-current="page">
                                        @lang('web.post_menu.categories')
                                    </li>
                                </ol>
                            </nav>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        {{-- @dd($jobCategories) --}}
        @if (count($jobCategories) > 0)

            <section class="popular-job-categories-section py-100">
                <div class="container mx-auto px-4 mx-auto px-4 mx-auto px-4 mx-auto">
                    <div class="job- bg-white shadow rounded -lg overflow-hidden">
                        <div class="flex flex-wrap">

                            @foreach ($jobCategories as $jobCategory)
                                <div class="lg:w-4/12 px-2 flex-1 md-6 px-xl-3 mb-40">
                                    <div class="bg-white shadow rounded -lg overflow-hidden py-30">
                                        <div class="flex justify-between items-center">
                                            <div class="flex items-center">
                                                <div class="me-4">
                                                    <img src="{{ $jobCategory->image_url }}" class="bg-white shadow rounded -lg overflow-hidden img"
                                                        alt="...">
                                                </div>
                                                <div class="">
                                                    <div class="bg-white shadow rounded -lg overflow-hidden body p-0">
                                                        <a href="{{ route('front.', ['categories' => $jobCategory->id]) }}"
                                                            class="text-gray-600 primary-link-hover">
                                                            <h5 class="bg-white shadow rounded -lg overflow-hidden title fs-18">
                                                                {{ html_entity_decode($jobCategory->name) }}</h5>
                                                        </a>
                                                        <p class="bg-white shadow rounded -lg overflow-hidden text fs-14 text-gray">
                                                            {{ ($jobCategory->jobs_count ? $jobCategory->jobs_count : 0) . ' ' . __('web.open_positions') }}
                                                        </p>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="icon relative pe-0">
                                                @if ($jobCategory->is_featured)
                                                    <div class="flex-1 -1 icon relative pe-0">
                                                        <i class="text-indigo-600 -600 fa-solid fa-bookmark"></i>
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                        {{ -- <div class="bg-white shadow rounded -lg overflow-hidden desc flex flex- flex-1 justify-between h-full mt-4">
                                            <div class="desc flex">
                                                <p class="text text-indigo-600 -600 fs-14 mb-0 me-3">
                                                    {{ !empty($job->jobsSkill[0]->name) ? $job->jobsSkill[0]->name : 'Skill' }}asd
                                                </p>
                                                <p class="fs-14 text text-indigo-600 -600 mb-0">asdasdasd</p>
                                            </div>
                                        </div> --}}
                                        @if ($jobCategory->jobs_count <= 0)
                                            <div class="bg-white shadow rounded -lg overflow-hidden desc mt-3">
                                                <div class="desc flex mt-2">
                                                    <p class="jobs-position text text-indigo-600 -600 fs-14 mb-0 me-3">
                                                        {{ __('web.no_positions') }}
                                                    </p>
                                                </div>
                                            </div>
                                        @else
                                            <div class="bg-white shadow rounded -lg overflow-hidden desc mt-3">
                                                <div class="desc flex mt-2">
                                                    <a href="{{ route('front.', ['categories' => $jobCategory->id]) }}"
                                                        class="jobs-position text text-indigo-600 -600 fs-14 mb-0 me-3">
                                                        {{ __('web.open_positions') }} ->
                                                    </a>
                                                </div>
                                            </div>
                                        @endif

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
