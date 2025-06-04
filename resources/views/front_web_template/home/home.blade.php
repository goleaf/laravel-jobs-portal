@extends('front_web_template.layouts.app')
@section('title')
    {{ __('web.home') }}
@endsection
@section('page_css')
@endsection
@section('content')
    <div class="home-page">
        <!-- start hero section -->
        @if (count($headerSliders) > 0 && $settings->value != 0)
            <section class="hero-section relative">
                <div id="carouselExampleControlshero" class="carousel slide" data-bs-ride="carousel">
                    <div class="carousel-inner">
                        @foreach ($headerSliders as $key => $headerSlider)
                            <div class="bg-image carousel-item {{ $key == 0 ? 'active' : '' }} position-relative pt-100 pb-150"
                                style="position: relative;background-image: url({{ $headerSlider->header_slider_url }});height:624px;color: white; text-align: center;">
                            </div>
                        @endforeach
                        <div class="hero-content-row flex flex-wrap w-full">
                            <div class="flex-1 -lg-6 text-center">
                                <div class="hero-contentt mt-lg-0 mt-md-5 my-4">
                                    <p class="fw-normal text-gray-900 fs-50 mb-0">@lang('web.easy_to_find_your')</p>
                                    <h1 class="fs-44 mb-md-4 mb-3 text-primary-600 fw-bold">
                                        {{ $cmsServices['home_title'] }}
                                    </h1>
                                    <p class="mb-4 fs-18 text-gray">
                                        {{ $cmsServices['home_description'] }}
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <button class="carousel-control-prev" type="button" data-bs-target="#carouselExampleControlshero"
                        data-bs-slide="prev">
                        <i class="icon fa-solid fa-arrow-left text-white"></i>
                    </button>
                    <button class="carousel-control-next" type="button" data-bs-target="#carouselExampleControlshero"
                        data-bs-slide="next">
                        <i class="icon fa-solid fa-arrow-right text-white"></i>
                    </button>
                </div>
            </section>
        @else
            <section class="hero-section relative bg-gradient pt-100 pb-150">
                <div class="container mx-auto px-4 mx-auto">
                    <div class="flex flex-wrap -mx-2 items-center flex-column-reverse flex-lg- flex flex-wrap">
                        <div class="flex-1 -lg-6 text-lg-start text-center">
                            <div class="hero-content mt-lg-0 mt-md-5 my-4">
                                <p class="fw-normal fs-50 mb-0">@lang('web.easy_to_find_your')</p>
                                <h1 class="fs-44 mb-md-4 mb-3 text-primary-600 fw-bold">
                                    {{ $cmsServices['home_title'] }}
                                </h1>
                                <p class="mb-4 fs-18 text-gray pe-lg-5 me-xl-5">
                                    {{ $cmsServices['home_description'] }}
                                </p>
                            </div>

                        </div>
                        @if ($settings->value == 0 || count($headerSliders) <= 0)
                            <div class="flex-1 -lg-6 text-lg-end text-center">
                                <img src="{{ $cmsServices['home_banner'] ? asset($cmsServices['home_banner']) : asset('front_web/images/hero-img.png') }}"
                                    alt="jobs-landing" class="hero-image img-fluid w-full" />
                            </div>
                        @endif
                    </div>
                </div>
            </section>
        @endif

        <!-- end hero section -->

        <!--start find-job section-->
        <section class="find-job-section">
            <div class="container mx-auto px-4 mx-auto">
                <div class="flex flex-wrap justify-center">
                    <div class="col-xl-8 flex-1 -lg-10">
                        <div class="find-job relative bg-white">
                            <form action="{{ route('front.search.jobs') }}" id='searchForm' method="get">
                                <div class="flex flex-wrap items-center justify-content-around m-0">
                                    <div class="flex-1 -lg-5 br-2 ps-lg-4 px-20">
                                        <h3 class="fs-16 text-gray-600 mb-0">@lang('web.home_menu.keywords')</h3>
                                        <input type="text" class="fs-14 text-gray mb-0" name="keywords"
                                            id="search-keywords" placeholder="@lang('web.web_home.job_title_keywords_company')" autocomplete="off" />
                                        <div id="jobsSearchResults" class="absolute w100 job-search" style="display: none;">
                                            <ul class="job-search-dropdown flex space-x-1 submenu">
                                            </ul>
                                        </div>
                                    </div>
                                    <div class="flex-1 -lg-4 br-2 ps-lg-4 px-20 pb-0 autocomplete-wrapper">
                                        <h3 class="fs-16 text-gray-600 mb-0">@lang('web.common.location')</h3>
                                        <input type="text" class="fs-14 text-gray mb-0 pb-4" name="location"
                                            id="search-location" placeholder="@lang('web.web_home.city_or_postcode')" />
                                    </div>
                                    <div class="flex-1 -lg-3 text-center p-lg-3 px-20">
                                        <button class="inline-flex items-center px-4 py-2 border border-gray-300 border-transparent text-sm font-medium rounded-md transition duration-150 ease-in-out bg-blue-600 hover:bg-blue-700 text-white focus:outline-none focus:ring-2 focus:ring-blue-500 bg-primary-600 text-white hover: bg-primary-600 -700 -register find-jobs- px-4 py-2 rounded font-medium transition-colors block p-0 px-2 pt-3 pb-3"
                                            style="width: 100%" type="submit">
                                            @lang('web.web_home.find_jobs')
                                        </button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <!--end find-job section-->

        <!-- start-companies-logo section -->
        @if (count($branding) > 0)
            <section class="comapnies-logo-section my-5">
                <div class="container mx-auto px-4 mx-auto">
                    <div class="slick-slider" id="brandSlider">
                        @foreach ($branding as $brand)
                            <div class="slide flex justify-center items-center m-4">
                                <img src="{{ $brand->branding_slider_url }}" alt="Branding Slider" class="img-fluid" />
                            </div>
                        @endforeach
                    </div>
                </div>
            </section>
        @endif
        <!-- end-companies-logo section -->

        <!-- start-slider-test-img section -->
        @if (count($imageSliders) > 0 && $imageSliderActive->value)
            <section class="{{ $slider->value == 0 ? 'container' : ' ' }} slider-test-section position-relative mb-80">
                <div id="carouselExampleControls" class="carousel slide" data-bs-ride="carousel">
                    <div class="carousel-inner">
                        @foreach ($imageSliders as $key => $imageSlider)
                            <div class="carousel-item {{ $key == 0 ? 'active' : '' }} position-relative">
                                <img src="{{ $imageSlider->image_slider_url }}" class="block w-full slider-img"
                                    alt="..." />
                                @if ($imageSlider->description)
                                    <div class="flex flex-wrap justify-center">
                                        <div class="slider-img-desc flex-1 -10 text-center absolute">
                                            <p class="text-white fs-16">
                                                {!! Str::limit($imageSlider->description, 495, ' ...') !!}
                                            </p>
                                        </div>
                                    </div>
                                @endif
                            </div>
                        @endforeach
                    </div>
                    <button class="carousel-control-prev" type="button" data-bs-target="#carouselExampleControls"
                        data-bs-slide="prev">
                        <i class="icon fa-solid fa-arrow-left text-white"></i>
                    </button>
                    <button class="carousel-control-next" type="button" data-bs-target="#carouselExampleControls"
                        data-bs-slide="next">
                        <i class="icon fa-solid fa-arrow-right text-white"></i>
                    </button>
                </div>
            </section>
        @endif


        <!-- end-slider-test-img section -->

        <!-- start-popular-job-categories-section -->
        @if (count($jobCategories) > 0)
            <section class="popular-job-categories-section mb-80">
                <div class="container mx-auto px-4 mx-auto">
                    <div class="overflow-hidden pb-60">
                        <div class="section-heading text-center">
                            <h2 class="text-gray-600 mb-0 inline-block">
                                @lang('web.home_menu.popular_categories')
                            </h2>
                        </div>
                    </div>

                    <div class="job-categories- bg-white shadow rounded-lg overflow-hidden">
                        <div class="flex flex-wrap">
                            @foreach ($jobCategories as $jobCategory)
                                <div class="col-xl-3 lg:w-4/12 px-2 flex-1 -md-6 mb-40 px-xl-3">
                                    <div class="bg-white shadow rounded-lg overflow-hidden px-20">
                                        <div class="bg-white shadow rounded-lg overflow-hidden -img text-center mb-3">
                                            <img src="{{ $jobCategory->image_url }}" class="h-full" alt="..." />
                                        </div>
                                        <div class="bg-white shadow rounded-lg overflow-hidden -body text-center">
                                            <a href="{{ route('front.search.jobs', ['categories' => $jobCategory->id]) }}"
                                                class="text-gray-600 primary-link-hover">
                                                <h5 class="bg-white shadow rounded-lg overflow-hidden -title fs-18 text-gray-600 mb-0">
                                                    {{ html_entity_decode($jobCategory->name) }}</h5>
                                            </a>
                                            {{-- <p class="bg-white shadow rounded-lg overflow-hidden -text fs-14 text-gray text-primary-600">
                                                {{ ($jobCategory->jobs_count ? $jobCategory->jobs_count : 0) . ' open positions' }}
                                            </p> --}}
                                            @if ($jobCategory->jobs_count <= 0)
                                                <p class="bg-white shadow rounded-lg overflow-hidden -text fs-14 text-gray text-primary-600">
                                                    {{ 'No positions' }}
                                                </p>
                                            @else
                                                <a href="{{ route('front.search.jobs', ['categories' => $jobCategory->id]) }}"
                                                    class="bg-white shadow rounded-lg overflow-hidden -text fs-14 text-gray text-primary-600">
                                                    {{ $jobCategory->jobs_count }} {{ 'open positions' }}
                                                </a>
                                            @endif
                                        </div>

                                    </div>

                                </div>
                            @endforeach
                            <div class="flex-1 -12 text-center">
                                <a href="{{ route('front.job-categories') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 border-transparent text-sm font-medium rounded-md transition duration-150 ease-in-out bg-primary-600 text-white hover: bg-primary-600 -700 px-4 py-2 rounded font-medium transition-colors -primary-register fs-14 mt-3">
                                    @lang('web.common.browse_all')
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        @endif
        <!-- start-popular-job-categories-section -->

        <!-- start latest-job-section -->
        @if (count($latestJobs) > 0)
            <section class="latest-job-section py-100 bg-gray-100">
                <div class="container mx-auto px-4 mx-auto">
                    <div class="overflow-hidden pb-60">
                        <div class="section-heading text-center">
                            <h2 class="text-gray-600 mb-0 inline-block">@lang('web.home_menu.latest_jobs')</h2>
                        </div>
                    </div>
                    <div class="job- bg-white shadow rounded-lg overflow-hidden">
                        <div class="flex flex-wrap">
                            @if (
                                \Illuminate\Support\Facades\Auth::check() && isset(auth()->user()->country_name) && isset($latestJobsEnable)
                                    ? $latestJobsEnable->value
                                    : '')
                                @if (in_array(auth()->user()->country_name, array_column($latestJobs->toArray(), 'country_name')))
                                    @foreach ($latestJobs as $job)
                                        @if ($job->country_name == auth()->user()->country_name)
                                            @include('front_web_template.common.job_card')
                                        @endif
                                    @endforeach
                                    <div class="flex-1 -md-12 text-center">
                                        <a href="{{ route('front.search.jobs') }}"
                                            class="inline-flex items-center px-4 py-2 border border-gray-300 border-transparent text-sm font-medium rounded-md transition duration-150 ease-in-out bg-primary-600 text-white hover: bg-primary-600 -700 px-4 py-2 rounded font-medium transition-colors -primary-register fs-14 mt-3">{{ __('web.common.browse_all') }}</a>
                                    </div>
                                @else
                                    <div class="flex-1 -md-12 text-center">
                                        <a href="{{ route('front.search.jobs') }}"
                                            class="inline-flex items-center px-4 py-2 border border-gray-300 border-transparent text-sm font-medium rounded-md transition duration-150 ease-in-out bg-primary-600 text-white hover: bg-primary-600 -700 px-4 py-2 rounded font-medium transition-colors -primary-register fs-14 mt-3">{{ __('web.common.browse_all') }}</a>
                                    </div>
                                @endif
                            @else
                                @foreach ($latestJobs as $job)
                                    @include('front_web_template.common.job_card')
                                @endforeach
                                <div class="flex-1 -12 text-center">
                                    <a href="{{ route('front.search.jobs') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 border-transparent text-sm font-medium rounded-md transition duration-150 ease-in-out bg-primary-600 text-white hover: bg-primary-600 -700 px-4 py-2 rounded font-medium transition-colors -primary-register fs-14 mt-3">
                                        @lang('web.common.browse_all')
                                    </a>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </section>
        @endif
        <!-- end latest-job-section -->

        <!-- start featured-job-section -->
        @if (count($featuredJobs))
            <section class="featured-job-section py-100">
                <div class="container mx-auto px-4 mx-auto">
                    <div class="overflow-hidden pb-60">
                        <div class="section-heading text-center">
                            <h2 class="text-gray-600 mb-0 inline-block">@lang('web.home_menu.featured_jobs')</h2>
                        </div>
                    </div>
                    <div class="job- bg-white shadow rounded-lg overflow-hidden">
                        <div class="flex flex-wrap">
                            @foreach ($featuredJobs as $job)
                                @include('front_web_template.common.job_card')
                            @endforeach
                        </div>
                        <div class="flex flex-wrap justify-center">
                            <div class="flex-1 -12 text-center">
                                <a class="inline-flex items-center px-4 py-2 border border-gray-300 border-transparent text-sm font-medium rounded-md transition duration-150 ease-in-out bg-primary-600 text-white hover: bg-primary-600 -700 px-4 py-2 rounded font-medium transition-colors -primary-register fs-14 mt-3"
                                    href="{{ route('front.search.jobs', ['is_featured' => true]) }}">
                                    @lang('web.common.browse_all')
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        @endif
        <!-- end featured-job-section -->

        <!-- start featured-company-section -->
        @if (count($featuredCompanies))
            <section class="featured-job-section py-80">
                <div class="container mx-auto px-4 mx-auto">
                    <div class="overflow-hidden pb-60">
                        <div class="section-heading text-center">
                            <h2 class="text-gray-600 mb-0 inline-block">@lang('web.home_menu.featured_companies')</h2>
                        </div>
                    </div>
                    <div class="job- bg-white shadow rounded-lg overflow-hidden">
                        <div class="flex flex-wrap">

                            @foreach ($featuredCompanies->take(8) as $company)
                                @include('front_web_template.common.company_card')
                            @endforeach
                        </div>
                        <div class="flex flex-wrap justify-center">
                            <div class="flex-1 -12 text-center">
                                <a class="inline-flex items-center px-4 py-2 border border-gray-300 border-transparent text-sm font-medium rounded-md transition duration-150 ease-in-out bg-primary-600 text-white hover: bg-primary-600 -700 px-4 py-2 rounded font-medium transition-colors -primary-register fs-14 mt-3"
                                    href="{{ route('front.company.lists', ['is_featured' => true]) }}">
                                    @lang('web.common.browse_all')
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        @endif
        <!-- end featured-company-section -->

        <!-- start notice-section -->
        @if (count($notices) > 0)
            <section class="notice-section {{(count($latestJobs) > 0) ? (count($featuredJobs) > 0) ? 'mt-40' : 'mt-100 mb-100' : 'mb-100'}}">
                <div class="container mx-auto px-4 mx-auto">
                    <div class="notice-content bg-gradient">
                        <div class="overflow-hidden mx-3">
                            <div class="section-heading text-center py-60">
                                <h2 class="text-gray-600 mb-0 inline-block">@lang('web.home_menu.notices')</h2>
                            </div>
                        </div>
                        <div class="autoscroller">
                            <div class="marquee">
                                <div class="flex flex-wrap justify-center">
                                    @foreach ($notices as $key => $notice)
                                        <div class="col-xl-10 flex-1 -11 relative mb-4">
                                            <div class="notice-desc bg-white py-20 px-md-5 px-4">
                                                <p class="text-gray-600">
                                                    {!! nl2br(strip_tags($notice->description)) !!}
                                                </p>
                                                <p class="fs-14 text-gray mb-md-0 mb-5">
                                                    {{ html_entity_decode($notice->title) }}
                                                    | {{ $notice->created_at->diffForHumans() }}
                                                </p>
                                            </div>
                                            <span href="#"
                                                class="bg-primary-600 text-white hover: bg-primary-600 -700 px-4 py-2 rounded font-medium transition-colors -primary-register absolute">{{ \Carbon\Carbon::parse($notice->created_at)->translatedFormat('jS M, Y') }}</span>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        @endif
        <!-- end notice-section -->

        <!-- start testimonial-section -->
        @if (count($testimonials) > 0)
            <section class="testimonial-section overflow-hidden  {{(count($notices) > 0) ? 'mb-100' : 'mt-80 mb-100'}}  {{(count($latestJobs) > 0) ? 'mt-80 mb-100' : ''}}">
                <div class="container mx-auto px-4 mx-auto -fluid p-sm-0">
                    <div class="overflow-hidden pb-60">
                        <div class="section-heading text-center">
                            <h2 class="text-gray-600 mb-0 inline-block">@lang('web.home_menu.testimonials')</h2>
                        </div>
                    </div>
                    <div class="">
                        <div class="testimonial-slider">
                            @foreach ($testimonials as $testimonial)
                                <div>
                                    <div class="testimonial- bg-white shadow rounded-lg overflow-hidden bg-gray-100">
                                        <div class="desc">
                                            <p> {!! !empty(nl2br($testimonial->description)) ? nl2br($testimonial->description) : __('messages.common.n/a') !!} </p>
                                        </div>
                                        <div class="">
                                            <div class="flex justify-between items-end">
                                                <div class="flex items-center me-2">
                                                    <div class="testimonial-profile me-3"> <img
                                                            src="{{ isset($testimonial->customer_image_url) ? $testimonial->customer_image_url : asset('assets/img/infyom-logo.png') }}"
                                                            class="w-full h-full object-fit-cover" alt="profile"> </div>
                                                    <div class="profile-desc">
                                                        <h6 class="fs-18 mb-1">
                                                            {{ html_entity_decode($testimonial->customer_name) }} </h6>
                                                    </div>
                                                </div>
                                                <div class="text-end comma-img"> <img
                                                        src="{{ asset('img_template/comma.png') }}" class="w-full"
                                                        alt="comma"> </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </section>
        @endif
        <!-- end testimonial-section -->

        <!-- start blog-section -->
        @if (count($recentBlog) > 0)
            <section class="recent-blog-section pt-100 pb-60 bg-gray-100">
                <div class="container mx-auto px-4 mx-auto">
                    <div class="overflow-hidden pb-60">
                        <div class="section-heading text-center">
                            <h2 class="text-gray-600 mb-0 inline-block">@lang('messages.recent_blog')</h2>
                        </div>
                    </div>
                    <div class="blog- bg-white shadow rounded-lg overflow-hidden">
                        <div class="flex flex-wrap">
                            @foreach ($recentBlog as $post)
                                <div class="lg:w-4/12 px-2 flex-1 -md-6 mb-40 px-xl-3">
                                    <div class="bg-white shadow rounded-lg overflow-hidden">
                                        <div class="bg-white shadow rounded-lg overflow-hidden -img-top relative">
                                            <img src="{{ empty($post->blog_image_url) ? asset('front_web/images/blog-1.png') : $post->blog_image_url }}"
                                                class="bg-white shadow rounded-lg overflow-hidden -img-top" alt="Employee Motivation" />
                                            <div class="overlay absolute">
                                                <a href="{{ route('front.posts.details', $post->id) }}"
                                                    class="px-4 py-2 rounded font-medium transition-colors text-white fs-16"> {{ __('web.post_menu.read_more') }} </a>
                                            </div>
                                        </div>
                                        <div class="bg-white shadow rounded-lg overflow-hidden -body px-20">
                                            <a href="{{ route('front.posts.details', $post->id) }}"
                                                class="text-gray-600 primary-link-hover">
                                                <h5 class="bg-white shadow rounded-lg overflow-hidden -title fs-18 text-gray-600">
                                                    {{ html_entity_decode($post->title) }}
                                                </h5>
                                            </a>
                                            <p class="bg-white shadow rounded-lg overflow-hidden -text fs-14 text-gray">
                                                {!! !empty($post->description)
                                                    ? Str::limit(strip_tags($post->description), 100, '...')
                                                    : __('messages.common.n/a') !!}
                                            </p>
                                            <span class="fs-14 text-gray">
                                                @if ($post->comments_count == 0 || $post->comments_count == 1)
                                                    {{ \Carbon\Carbon::parse($post->created_at)->translatedFormat('M jS Y') }}
                                                    | {{ $post->comments_count }} Comment
                                                @else
                                                    {{ \Carbon\Carbon::parse($post->created_at)->translatedFormat('M jS Y') }}
                                                    | {{ $post->comments_count }} Comments
                                                @endif
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </section>
        @endif
        <!-- end blog-section -->

        <!-- start-about-section -->
        <section class="about-section py-60 bg-primary-600">
            <div class="container mx-auto px-4 mx-auto">
                <div class="flex flex-wrap justify-between items-center">
                    <div class="col-sm-3 flex-1 -6 text-center mb-sm-0 mb-4">
                        <div class="about-desc">
                            <h3 class="text-white count">
                                {{ $dataCounts['candidates'] }}
                            </h3>
                            <p class="text-white fs-18 mb-0">@lang('messages.front_home.candidates')</p>
                            </p>
                        </div>
                    </div>
                    <div class="col-sm-3 flex-1 -6 text-center mb-sm-0 mb-4">
                        <div class="about-desc">
                            <h3 class="text-white count">
                                {{ $dataCounts['jobs'] }}
                            </h3>
                            <p class="text-white fs-18 mb-0">@lang('messages.front_home.jobs')</p>
                        </div>
                    </div>
                    <div class="col-sm-3 flex-1 -6 text-center">
                        <div class="about-desc">
                            <h3 class="text-white count">
                                {{ $dataCounts['resumes'] }}
                            </h3>
                            <p class="text-white fs-18 mb-0">@lang('messages.front_home.resumes')</p>
                        </div>
                    </div>
                    <div class="col-sm-3 flex-1 -6 text-center">
                        <div class="about-desc">
                            <h3 class="text-white count">
                                {{ $dataCounts['companies'] }}
                            </h3>
                            <p class="text-white fs-18 mb-0">@lang('messages.front_home.companies')</p>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <!-- end-about-section -->

        <!-- start pricing-packages-section -->
        @if (count($plans) > 0)
            <section class="pricing-packages-section pt-100 pb-60">
                <div class="container mx-auto px-4 mx-auto">
                    <div class="overflow-hidden pb-60">
                        <div class="section-heading text-center">
                            <h2 class="text-gray-600 mb-0 inline-block">
                                @lang('web.web_home.pricing_packages')
                            </h2>
                        </div>
                    </div>
                    <section class="slider-test-section pricing-plans relative">
                        <div id="carouselExampleControlsabout" class="carousel slide" data-bs-ride="carousel">
                            <div class="carousel-inner">
                                @foreach ($plansArray as $key => $plans)
                                @php
                                    $isshoebtn = 0;
                                    if ($key >= 1) { $isshoebtn = 1; }
                                @endphp
                                    <div class="carousel-item relative {{ $key == 0 ? "active' : '' }}">
                                        <div class="flex flex-wrap flex justify-center">
                                            @foreach ($plans as $plan)
                                                <div class="pricing-packages-section- bg-white shadow rounded-lg overflow-hidden lg:w-4/12 px-2 flex-1 -sm-6 px-xl-3">
                                                    <div class="pricing-plan-card bg-white shadow rounded-lg overflow-hidden bg-gray-100 shadow-none">
                                                        <div class="bg-white shadow rounded-lg overflow-hidden -body text-center p-0">
                                                            <div class="bg-white shadow rounded-lg overflow-hidden -body-top py-30">
                                                                <p class="mb-2 text-primary-600 fs-18 fw-bold">
                                                                    {{ html_entity_decode(Str::limit($plan['name'], 50, '...')) }}
                                                                </p>
                                                                <div class="text-center flex justify-center">
                                                                    <h3 class="text-gray-600 mb-0">
                                                                        {{ empty($plan['salary_currency']['currency_icon']) ? '$' : $plan['salary_currency']['currency_icon'] }}{{ $plan['amount'] }}
                                                                    </h3>
                                                                    <span class="text-gray mt-xl-4 mt-sm-3 mt-2 ms-1">
                                                                        / {{ __('web.web_home.monthly') }}</span>
                                                                </div>
                                                            </div>
                                                            <div class="bg-white shadow rounded-lg overflow-hidden -body-bottom py-30">
                                                                <div
                                                                    class="text flex items-center justify-center mb-0">
                                                                    <div class="check-box me-3">
                                                                        <i class="fa-solid fa-check text-white fs-14"></i>
                                                                    </div>
                                                                    <span
                                                                        class="">{{ $plan['allowed_jobs'] . ' ' . ($plan['allowed_jobs'] > 1 ? __('messages.plan.jobs_allowed') : __('messages.plan.job_allowed')) }}</span>
                                                                </div>
                                                                <div class="px-4 pt-2">
                                                                    @if (Auth::check() && Auth::user()->hasRole('Candidate'))
                                                                        <a href="#"
                                                                            class="inline-flex items-center px-4 py-2 border border-gray-300 border-transparent text-sm font-medium rounded-md transition duration-150 ease-in-out px-4 py-2 rounded font-medium transition-colors -light w-full fs-14 mt-3 py-2 px-4"
                                                                            data-turbo="false">{{ __('messages.pricing_table.buy_plan') }}</a>
                                                                    @elseif(Auth::check() && Auth::user()->hasRole('Employer'))
                                                                        <a href="{{ route('manage-subscription.index') }}"
                                                                            class="inline-flex items-center px-4 py-2 border border-gray-300 border-transparent text-sm font-medium rounded-md transition duration-150 ease-in-out px-4 py-2 rounded font-medium transition-colors -light w-full fs-14 mt-3 py-2 px-4"
                                                                            data-turbo="false">{{ __('messages.pricing_table.buy_plan') }}</a>
                                                                    @elseif(Auth::check() && Auth::user()->hasRole('Admin'))
                                                                        <a href="#"
                                                                            class="inline-flex items-center px-4 py-2 border border-gray-300 border-transparent text-sm font-medium rounded-md transition duration-150 ease-in-out px-4 py-2 rounded font-medium transition-colors -light w-full fs-14 mt-3 py-2 px-4 hidden"
                                                                            data-turbo="false">{{ __('messages.pricing_table.buy_plan') }}</a>
                                                                    @else
                                                                        <a href="{{ route('employer.register') }}"
                                                                            class="inline-flex items-center px-4 py-2 border border-gray-300 border-transparent text-sm font-medium rounded-md transition duration-150 ease-in-out px-4 py-2 rounded font-medium transition-colors -light w-full fs-14 mt-3 py-2 px-4"
                                                                            data-turbo="false">{{ __('messages.pricing_table.buy_plan') }}</a>
                                                                    @endif
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                            @if ($isshoebtn == 1)
                            <button class="carousel-control-prev about-control-prev" type="button"
                                data-bs-target="#carouselExampleControlsabout" data-bs-slide="prev">
                                <i class="icon fa-solid fa-arrow-left text-primary-600"></i>
                            </button>
                            <button class="carousel-control-next about-control-next" type="button"
                                data-bs-target="#carouselExampleControlsabout" data-bs-slide="next">
                                <i class="icon fa-solid fa-arrow-right text-primary-600"></i>
                            </button>
                            @endif
                        </div>
                    </section>
                </div>
            </section>
        @endif
        <!-- end pricing-packages-section -->
        {{ Form::hidden('homeData', json_encode(getCountries()), ['id' => 'indexHomeData']) }}
    </div>
@endsection
