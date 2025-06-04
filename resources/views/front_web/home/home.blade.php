@extends('front_web.layouts.app')
@section('title')
    {{ __('web.home')  }}
@endsection
{{ -- @section('page_css') -- }}
{{ --    <link href="{{asset('front_web/css/slick.css') }}" rel="stylesheet" type="text/css"> --}}
{{ --    <link href="{{asset('front_web/css/slick-theme.css') }}" rel="stylesheet" type="text/css"> --}}
{{ --    <link href="{{asset('front_web/scss/home.css') }}" rel="stylesheet" type="text/css"> --}}
{{ -- @endsection -- }}
@section('content')
    <div class="home-page" style="overflow-x: hidden;">
        <!-- start hero section -->
        <section class="hero-section relative py-4 bg-color-light">
            @if ($settings->value)
                @if (count($headerSliders) > 0)
                    <div class="banner-carousel">
                        @foreach ($headerSliders as $headerSlider)
                            <div class="bg-image" style="background-image: url({{ $headerSlider->header_slider_url  }});">
                            </div>
                        @endforeach
                    </div>
                @endif
            @endif
            <div class="container mx-auto px-4 mx-auto relative my-5 py-3">
                <div class="flex flex-wrap -mx-2 items-center flex-column-reverse flex-lg- flex flex-wrap">
                    <div
                        class="{{ $settings->value == 1 && count($headerSliders) > 0 ? 'col-lg-8 text-center mx-auto' : 'col-lg-8 text-lg-start text-center align-items-start justify-content-start'  }}">
                        <div class="hero-content mt-lg-0 mt-md-5 my-4">
                            <h1 class="mb-md-4 mb-3 pe-xxl-3">
                                {{ $cmsServices['home_title']  }}
                            </h1>
                            <p class="mb-lg-4 pb-lg-3 mb-4 fs-18 text-gray">
                                {{ $cmsServices['home_description']  }}
                            </p>
                        </div>
                        @if ($settings->value == 1 && count($headerSliders) > 0)
                            <div class="flex items-center justify-center">
                                <div class="find-job relative bg-white shadow-lg bg-body w-3/4">
                                    <form action="{{ route('front.search.jobs')  }}" id='searchForm' method="get">
                                        <div class="flex flex-wrap items-center justify-content-around gx-0">
                                            <div class="flex-1 -lg-5 br-2 mb-lg-0 flex input-text">
                                                <i class="fa-solid fa-magnifying-glass input-icon me-1"></i>
                                                <input type="text" class="fs-14 text-gray mb-0 input" name="keywords"
                                                    id="search-keywords" placeholder="@lang('web.web_home.job_title_keywords_company')" autocomplete="on"
                                                    autofocus>
                                                <div id="jobsSearchResults" class="absolute w100 job-search"></div>
                                            </div>
                                            <div class="flex-1 -lg-4 br-2 flex input-text">
                                                <i class="fa-solid fa-location-dot input-icon me-2"></i>
                                                <input type="text" class="fs-14 text-gray mb-0 input" name="location"
                                                    id="search-location" placeholder="@lang('web.web_home.city_or_postcode')" autocomplete="on">
                                            </div>
                                            <div class="flex-1 -lg-2 text-end me-0">
                                                <button class="inline-flex items-center px-4 py-2 border border-gray-300 border-transparent text-sm font-medium rounded-md transition duration-150 ease-in-out bg-primary-600 text-white hover: bg-primary-600 -700 find-jobs- px-4 py-2 rounded font-medium transition-colors p-2" type="submit">
                                                    @lang('web.web_home.find_jobs')
                                                </button>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        @else
                            <div class="flex items-start justify-start">
                                <div class="find-job relative bg-white shadow-lg bg-body   w-3/4">
                                    <form action="{{ route('front.search.jobs')  }}" id='searchForm' method="get">
                                        <div class="flex flex-wrap items-center justify-content-around gx-0">
                                            <div class="flex-1 -lg-5 br-2 mb-lg-0 flex input-text">
                                                <i class="fa-solid fa-magnifying-glass input-icon me-1"></i>
                                                <input type="text" class="fs-14 text-gray mb-0 input" name="keywords"
                                                    id="search-keywords" placeholder="@lang('web.web_home.job_title_keywords_company')" autocomplete="on"
                                                    autofocus>
                                                <div id="jobsSearchResults" class="absolute w100 job-search"></div>
                                            </div>
                                            <div class="flex-1 -lg-4 br-2 flex input-text">
                                                <i class="fa-solid fa-location-dot input-icon me-2"></i>
                                                <input type="text" class="fs-14 text-gray mb-0 input" name="location"
                                                    id="search-location" placeholder="@lang('web.web_home.city_or_postcode')" autocomplete="on">
                                            </div>
                                            <div class="flex-1 -lg-2 text-end me-3">
                                                <button class="inline-flex items-center px-4 py-2 border border-gray-300 border-transparent text-sm font-medium rounded-md transition duration-150 ease-in-out bg-primary-600 text-white hover: bg-primary-600 -700 find-jobs- px-4 py-2 rounded font-medium transition-colors p-2" type="submit">
                                                    @lang('web.web_home.find_jobs')
                                                </button>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        @endif
                    </div>
                    @if ($settings->value == 0)
                        <div class="flex-1 -lg-4 text-lg-end text-center">
                            <img src="{{ $cmsServices['home_banner'] ? asset($cmsServices['home_banner']) : asset('front_web/images/hero-img.png')  }}"
                                alt="jobs-landing" class="img-fluid" />
                        </div>
                    @endif
                </div>

                <div class="flex flex-wrap pt-5 mt-4 w-3/4 mx-auto">
                    <div class="lg:w-3/12 px-2 flex-1 -sm-6 mb-lg-0 mb-4 py-1 px-md-2">
                        <div class="desc-card bg-white shadow rounded-lg overflow-hidden flex- flex flex-wrap p-3 items-center h-3/4">
                            <div class="flex-1 -6 bg-white shadow rounded-lg overflow-hidden -img flex justify-center items-center rounded-circle shadow"
                                style="width: 50px; height:50px">
                                <i class="fa-solid fa-suitcase" style="font-size: 22px !important;"></i>
                            </div>
                            <div class="flex-1 -6 bg-white shadow rounded-lg overflow-hidden -text py-3">
                                <h3>{{ $dataCounts['jobs']  }}</h3>
                                <p class="mb-0 text-gray">@lang('messages.front_home.jobs')</p>
                            </div>
                        </div>
                    </div>
                    <div class="lg:w-3/12 px-2 flex-1 -sm-6 mb-lg-0 mb-4 py-1 px-md-2">
                        <div class="desc-card bg-white shadow rounded-lg overflow-hidden flex- flex flex-wrap p-3 items-center h-3/4">
                            <div class="bg-white shadow rounded-lg overflow-hidden -img flex justify-center items-center img-fluid rounded-circle shadow"
                                style="width: 50px; height:50px">
                                <i class="fa-solid fa-users" style="font-size: 22px !important;"></i>
                            </div>
                            <div class="bg-white shadow rounded-lg overflow-hidden -text py-3">
                                <h3>{{ $dataCounts['candidates']  }}</h3>
                                <p class="mb-0 text-gray">@lang('messages.front_home.candidates')</p>
                            </div>
                        </div>
                    </div>
                    <div class="lg:w-3/12 px-2 flex-1 -sm-6 mb-sm-0 mb-4 py-1 px-md-2">
                        <div class="desc-card bg-white shadow rounded-lg overflow-hidden flex- flex flex-wrap p-3 items-center h-3/4">
                            <div class="bg-white shadow rounded-lg overflow-hidden -img flex justify-center items-center img-fluid rounded-circle shadow"
                                style="width: 50px; height:50px">
                                <i class="fa-solid fa-building" style="font-size: 22px !important;"></i>

                            </div>
                            <div class="bg-white shadow rounded-lg overflow-hidden -text py-3">
                                <h3>{{ $dataCounts['companies']  }}</h3>
                                <p class="mb-0 text-gray">@lang('messages.front_home.companies')</p>
                            </div>
                        </div>
                    </div>
                    <div class="lg:w-3/12 px-2 flex-1 -sm-6 mb-sm-0 mb-4 py-1 px-md-2">
                        <div class="desc-card bg-white shadow rounded-lg overflow-hidden flex- flex flex-wrap p-3 items-center h-3/4">
                            <div class="bg-white shadow rounded-lg overflow-hidden -img flex justify-center items-center img-fluid rounded-circle shadow"
                                style="width: 50px; height:50px">
                                <i class="fa-regular fa-file" style="font-size: 22px !important;"></i>
                            </div>
                            <div class="bg-white shadow rounded-lg overflow-hidden -text py-3 pr-5">
                                <h3>{{ $dataCounts['resumes']  }}</h3>
                                <p class="mb-0 text-gray">@lang('messages.front_home.resumes')</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <!-- end hero section -->

        <!-- start-companies-logo section -->
        @if (count($branding) > 0)
            <section class="comapnies-logo-section py-80">
                <div class="container mx-auto px-4 mx-auto">
                    <div class="slick-slider">
                        @foreach ($branding as $brand)
                            <div class="slide flex justify-center items-center">
                                <img src="{{ $brand->branding_slider_url  }}" alt="Branding Slider" class="img-fluid" />
                            </div>
                        @endforeach
                    </div>
                </div>
            </section>
        @endif
        <!-- end-companies-logo section -->

        <!-- start-slider-test-img section -->
        @if (count($imageSliders) > 0 && $imageSliderActive->value)
            <section class="{{ $slider->value == 0 ? 'container' : ' '  }} slider-test-section position-relative">
                <div id="carouselExampleControls" class="carousel slide" data-bs-ride="carousel">
                    <div class="carousel-inner">
                        @foreach ($imageSliders as $key => $imageSlider)
                            <div class="carousel-item relative {{ $key == 0 ? "active' : ''  }}">
                                <img src="{{ $imageSlider->image_slider_url  }}" class="block w-full slider-img"
                                    alt="slide">
                                @if ($imageSlider->description)
                                    <div class="flex flex-wrap justify-center">
                                        <div class="slider-img-desc flex-1 -10 text-center absolute">
                                            <div class="slide-desc">
                                                {!! Str::limit($imageSlider->description, 495, ' ...') !!}
                                            </div>
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
            <section class="popular-job-categories-section py-50">
                <div class="container mx-auto px-4 mx-auto">
                    <div class="flex flex-wrap justify-center">
                        <div class="flex-1 -8">
                            <div class="section-heading text-center mx-xxl-4 mx-lg-0 mx-sm-3">
                                <h2 class="text-gray-600 bg-white">
                                    @lang('web.home_menu.popular_categories')
                                </h2>
                            </div>
                        </div>
                    </div>
                    <div class="job-categories- bg-white shadow rounded-lg overflow-hidden">
                        <div class="flex flex-wrap justify-start">

                            @foreach ($jobCategories as $jobCategory)
                                <div class="col-xl-3 lg:w-4/12 px-2 flex-1 -md-6 mb-40">
                                    <div class="bg-white shadow rounded-lg overflow-hidden py-20">
                                        <div class="flex flex-wrap">
                                            <div class="flex items-center">
                                                <div class="flex-1 -3">
                                                    <img src="{{ $jobCategory->image_url  }}" class="bg-white shadow rounded-lg overflow-hidden -img img-border"
                                                        alt="...">
                                                </div>
                                                <div class="flex-1 -9 flex">
                                                    <div class="bg-white shadow rounded-lg overflow-hidden -body ps-xl-0 ps-lg-3">
                                                        <a href="{{ route('front.search.jobs', ['categories' => $jobCategory->id])  }}"
                                                            class="text-gray-600 primary-link-hover">
                                                            <h5 class="bg-white shadow rounded-lg overflow-hidden -title fs-18">
                                                                {{ html_entity_decode($jobCategory->name)  }}</h5>
                                                        </a>
                                                        <p class="bg-white shadow rounded-lg overflow-hidden -text fs-14 text-gray">
                                                            {{ ($jobCategory->jobs_count ? $jobCategory->jobs_count : 0) . ' open positions'  }}
                                                        </p>
                                                    </div>
                                                    @if ($jobCategory->is_featured)
                                                        <div class="flex-1 -1 icon">
                                                            <i class="text-primary-600 fa-solid fa-bookmark"></i>
                                                        </div>
                                                    @endif
                                                </div>
                                            </div>

                                            @if ($jobCategory->jobs_count <= 0)
                                                <div class="bg-white shadow rounded-lg overflow-hidden -desc mt-3">
                                                    <div class="desc flex mt-2">
                                                        <p class="jobs-position bg-gray fs-14 mb-0 me-3 text-gray-600">
                                                            {{ 'No positions'  }}
                                                        </p>
                                                    </div>
                                                </div>
                                            @else
                                                <div class="bg-white shadow rounded-lg overflow-hidden -desc mt-3">
                                                    <div class="desc flex mt-2">
                                                        <a href="{{ route('front.search.jobs', ['categories' => $jobCategory->id])  }}"
                                                            class="jobs-position  fs-14 mb-0 me-3">
                                                            {{ $jobCategory->jobs_count  }} {{ 'open positions'  }}
                                                        </a>
                                                    </div>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                            <div class="flex-1 -12 text-center">
                                <a href="{{ route('front.job-categories')  }}" class="inline-flex items-center px-4 py-2 border border-gray-300 border-transparent text-sm font-medium rounded-md transition duration-150 ease-in-out px-4 py-2 rounded font-medium transition-colors -primary fs-14 mt-3"
                                    style="padding: 7px 15px;">
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
            <section class="latest-job-section py-50 bg-color-light">
                <div class="container mx-auto px-4 mx-auto">
                    <div class="flex flex-wrap justify-center">
                        <div class="flex-1 -8">
                            <div class="section-heading ms-xxl-4 me-xxl-4 ms-md-3 me-md-3 text-center">
                                <h2 class="text-gray-600">
                                    @lang('web.home_menu.latest_jobs')
                                </h2>
                            </div>
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
                                        @if ($$job->country_name == auth()->user()->country_name)
                                            @include('front_web.common.job_card')
                                        @endif
                                    @endforeach
                                    <div class="flex-1 -md-12 text-center">
                                        <a href="{{ route('front.search.jobs')  }}"
                                            class="inline-flex items-center px-4 py-2 border border-gray-300 border-transparent text-sm font-medium rounded-md transition duration-150 ease-in-out px-4 py-2 rounded font-medium transition-colors -primary fs-14 mt-3">{{ __('web.common.browse_all')  }}</a>
                                    </div>
                                @else
                                    <div class="flex-1 -md-12 text-center">
                                        <a href="{{ route('front.search.jobs')  }}"
                                            class="inline-flex items-center px-4 py-2 border border-gray-300 border-transparent text-sm font-medium rounded-md transition duration-150 ease-in-out px-4 py-2 rounded font-medium transition-colors -primary fs-14 mt-3">{{ __('web.common.browse_all')  }}</a>
                                    </div>
                                @endif
                            @else
                                @foreach ($latestJobs as $job)
                                    @include('front_web.common.job_card')
                                @endforeach
                                <div class="flex-1 -12 text-center">
                                    <a href="{{ route('front.search.jobs')  }}" class="inline-flex items-center px-4 py-2 border border-gray-300 border-transparent text-sm font-medium rounded-md transition duration-150 ease-in-out px-4 py-2 rounded font-medium transition-colors -primary fs-14 mt-3">
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
            <section class="latest-job-section py-50">
                <div class="container mx-auto px-4 mx-auto">
                    <div class="flex flex-wrap justify-center">
                        <div class="flex-1 -12">
                            <div class="section-heading text-center">
                                <h2 class="text-gray-600 bg-white">
                                    @lang('web.home_menu.featured_jobs')</h2>
                            </div>
                        </div>
                    </div>
                    <div class="job- bg-white shadow rounded-lg overflow-hidden">
                        <div class="flex flex-wrap">
                            @foreach ($featuredJobs as $job)
                                @include('front_web.common.job_card')
                            @endforeach
                        </div>
                        <div class="flex flex-wrap justify-center">
                            <div class="flex-1 -6 text-center">
                                <a class="inline-flex items-center px-4 py-2 border border-gray-300 border-transparent text-sm font-medium rounded-md transition duration-150 ease-in-out px-4 py-2 rounded font-medium transition-colors -primary fs-14 mt-3"
                                    href="{{ route('front.search.jobs', ['is_featured' => true])  }}">
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
            <section class="latest-job-section py-50">
                <div class="container mx-auto px-4 mx-auto">
                    <div class="flex flex-wrap justify-center">
                        <div class="flex-1 -12">
                            <div class="section-heading text-center">
                                <h2 class="text-gray-600 bg-white">
                                    @lang('web.home_menu.featured_companies')</h2>
                            </div>
                        </div>
                    </div>
                    <div class="job- bg-white shadow rounded-lg overflow-hidden">
                        <div class="flex flex-wrap">
                            @foreach ($featuredCompanies->take(8) as $company)
                                @include('front_web.common.company_card')
                            @endforeach
                        </div>
                        <div class="flex flex-wrap justify-center">
                            <div class="flex-1 -6 text-center">
                                <a class="inline-flex items-center px-4 py-2 border border-gray-300 border-transparent text-sm font-medium rounded-md transition duration-150 ease-in-out px-4 py-2 rounded font-medium transition-colors -primary fs-14 mt-3"
                                   href="{{ route('front.company.lists',['is_featured' => true]) }}">
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
            <section class="notice-section">
                <div class="container mx-auto px-4 mx-auto">
                    <div class="notice-content bg-color-light">
                        <div class="flex flex-wrap justify-center">
                            <div class="flex-1 -8">
                                <div class="section-heading pt-md-3 mt-5 text-center">
                                    <h2 class="text-gray-600">
                                        @lang('web.home_menu.notices')</h2>
                                </div>
                            </div>
                        </div>
                        <div class="autoscroller">
                            <div class="marquee">
                                <div class="flex flex-wrap justify-center me-0">
                                    @foreach ($notices as $key => $notice)
                                        <div
                                            class="col-sm-10 flex-1 -11 relative mb-4 {{ $loop->first ?"' : 'mt-lg-3'  }}">
                                            <div class="notice-desc bg-white py-20 px-md-5 px-4">
                                                <p class="fs-16 text-gray-600">
                                                    {!! nl2br(strip_tags($notice->description)) !!}
                                                </p>
                                                <p class="fs-14 text-gray mb-md-0 mb-5">
                                                    {{ html_entity_decode($notice->title)  }}
                                                    | {{ $notice->created_at->diffForHumans()  }}
                                                </p>
                                            </div>
                                            <span href="#" class="px-4 py-2 rounded font-medium transition-colors -primary absolute">
                                                {{ \Carbon\Carbon::parse($notice->created_at)->translatedFormat('jS M, Y')  }}
                                            </span>
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
            @include('front_web.home.testimonials')
        @endif
        <!-- end testimonial-section -->

        <!-- start blog-section -->
        @if (count($recentBlog) > 0)
            <section class="recent-blog-section py-50 bg-color-light">
                <div class="container mx-auto px-4 mx-auto">
                    <div class="flex flex-wrap justify-center">
                        <div class="flex-1 -12">
                            <div class="section-heading text-center">
                                <h2 class="text-gray-600 mx-xxl-3 mx-xl-5">
                                    @lang('messages.recent_blog')
                                </h2>
                            </div>
                        </div>
                    </div>
                    <div class="blog- bg-white shadow rounded-lg overflow-hidden">
                        <div class="flex flex-wrap">
                            @foreach ($recentBlog as $post)
                                <div class="lg:w-4/12 px-2 flex-1 -md-6 mb-lg-0 mb-sm-5 mb-4">
                                    <div class="bg-white shadow rounded-lg overflow-hidden">
                                        <div class="bg-white shadow rounded-lg overflow-hidden -img-top relative">
                                            <div class="inner-image">
                                                <img src="{{ empty($post->blog_image_url) ? asset('front_web/images/blog-1.png') : $post->blog_image_url  }}"
                                                    class="bg-white shadow rounded-lg overflow-hidden -img-top" alt="Employee Motivation">
                                            </div>
                                            <div class="overlay absolute">
                                                <a href="{{ route('front.posts.details', $post->id)  }}"
                                                    class="px-4 py-2 rounded font-medium transition-colors text-white fs-16">
                                                    {{ __('web.post_menu.read_more')  }}
                                                </a>
                                            </div>
                                        </div>
                                        <div class="bg-white shadow rounded-lg overflow-hidden -body py-30">
                                            <a href="{{ route('front.posts.details', $post->id)  }}"
                                                class="text-gray-600 primary-link-hover">
                                                <h5 class="bg-white shadow rounded-lg overflow-hidden -title fs-18">
                                                    {{ html_entity_decode($post->title)  }}
                                                </h5>
                                            </a>
                                            <div class="blog-desc bg-white shadow rounded-lg overflow-hidden -text mb-3">
                                                {!! !empty($post->description)
                                                    ? Str::limit(strip_tags($post->description), 100, '...')
                                                    : __('messages.common.n/a') !!}
                                            </div>
                                            <span class="fs-14 text-gray">
                                                @if ($post->comments_count == 0 || $post->comments_count == 1)
                                                    {{ \Carbon\Carbon::parse($post->created_at)->translatedFormat('M jS Y')  }}
                                                    | {{ $post->comments_count  }} Comment
                                                @else
                                                    {{ \Carbon\Carbon::parse($post->created_at)->translatedFormat('M jS Y')  }}
                                                    | {{ $post->comments_count  }} Comments
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
        <section class="about-section py-60 bg-gray-600">
            <div class="container mx-auto px-4 mx-auto">
                <div class="flex flex-wrap justify-between items-center">
                    <div class="col-sm-3 flex-1 -6 text-center mb-sm-0 mb-4">
                        <div class="about-desc">
                            <h3 class="text-primary-600 counter" data-duration="3000"
                                data-count="{{ $dataCounts['candidates']  }}"></h3>
                            <p class="text-white fs-18 mb-0">
                                @lang('messages.front_home.candidates')</p>
                        </div>
                    </div>
                    <div class="col-sm-3 flex-1 -6 text-center mb-sm-0 mb-4">
                        <div class="about-desc" data-wow-delay="400ms">
                            <h3 class="text-primary-600 counter" data-duration="3000"
                                data-count="{{ $dataCounts['jobs']  }}"></h3>
                            <p class="text-white fs-18 mb-0">
                                @lang('messages.front_home.jobs')</p>
                        </div>
                    </div>
                    <div class="col-sm-3 flex-1 -6 text-center">
                        <div class="about-desc" data-wow-delay="800ms">
                            <h3 class="text-primary-600 counter" data-duration="3000"
                                data-count="{{ $dataCounts['resumes']  }}"></h3>
                            <p class="text-white fs-18 mb-0">
                                @lang('messages.front_home.resumes')</p>
                        </div>
                    </div>
                    <div class="col-sm-3 flex-1 -6 text-center">
                        <div class="about-desc" data-wow-delay="800ms">
                            <h3 class="text-primary-600 counter" data-count="{{ $dataCounts['companies']  }}"
                                data-duration="3000"></h3>
                            <p class="text-white fs-18 mb-0">
                                @lang('messages.front_home.companies')</p>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <!-- end-about-section -->

        <!-- start pricing-packages-section -->
        @if (count($plans) > 0)
            <section class="pricing-packages-section py-50">
                <div class="container mx-auto px-4 mx-auto">
                    <div class="flex flex-wrap justify-center">
                        <div class="flex-1 -12">
                            <div class="section-heading text-center">
                                <h2 class="text-gray-600 bg-white ms-xl-5 me-xl-4"> @lang('web.web_home.pricing_packages') </h2>
                            </div>
                        </div>
                    </div>
                    <section class="slider-test-section relative">
                        <div id="carouselExampleControl" class="carousel slide" data-bs-ride="carousel">
                            <div class="carousel-inner">
                                @foreach ($plansArray as $key => $plans)
                                    <div class="carousel-item relative {{ $key == 0 ? "active' : ''  }}">
                                        <div class="flex flex-wrap flex justify-center">
                                            @foreach ($plans as $plan)
                                                <div class="lg:w-3/12 px-2 flex-1 -sm-6 my-3">
                                                    <div class="pricing-plan-card bg-white shadow rounded-lg overflow-hidden me-lg-2">
                                                        <div class="bg-white shadow rounded-lg overflow-hidden -body text-center py-4 px-lg-5 px-sm-4">
                                                            <h4 class="mb-0">
                                                                {{ html_entity_decode(Str::limit($plan['name'], 50, '...'))  }}
                                                            </h4>
                                                            <div
                                                                class="bg-white shadow rounded-lg overflow-hidden -body-top text-center flex justify-center">
                                                                <h3 class="text-primary-600">
                                                                    {{ empty($plan['salary_currency']['currency_icon']) ? '$' : $plan['salary_currency']['currency_icon']  }}{{ $plan['amount']  }}
                                                                </h3>
                                                                <span class="text-gray mt-xl-4 mt-sm-3 mt-2 ms-1"> /{{ __('web.web_home.monthly')  }}</span>
                                                            </div>
                                                            <div class="bg-white shadow rounded-lg overflow-hidden -body-bottom">
                                                                <div
                                                                    class="text flex items-center justify-center my-4">
                                                                    <div class="check-box me-2">
                                                                        <i class="fa-solid fa-check text-red-600"></i>
                                                                    </div>
                                                                    <span class="text-gray">
                                                                        {{ $plan['allowed_jobs'] . ' ' . ($plan['allowed_jobs'] > 1 ? __('messages.plan.jobs_allowed') : __('messages.plan.job_allowed'))  }}</span>
                                                                </div>
                                                                @if (Auth::check() && Auth::user()->hasRole('Candidate'))
                                                                    <a href="#"
                                                                        class="inline-flex items-center px-4 py-2 border border-gray-300 border-transparent text-sm font-medium rounded-md transition duration-150 ease-in-out px-4 py-2 rounded font-medium transition-colors -primary fs-14 mt-3 py-2 px-4"
                                                                        data-turbo="false">{{ __('messages.pricing_table.get_started')  }}</a>
                                                                @elseif(Auth::check() && Auth::user()->hasRole('Employer'))
                                                                    <a href="{{ route('manage-subscription.index')  }}"
                                                                        class="inline-flex items-center px-4 py-2 border border-gray-300 border-transparent text-sm font-medium rounded-md transition duration-150 ease-in-out px-4 py-2 rounded font-medium transition-colors -primary fs-14 mt-3 py-2 px-4"
                                                                        data-turbo="false">{{ __('messages.pricing_table.get_started')  }}</a>
                                                                @elseif(Auth::check() && Auth::user()->hasRole('Admin'))
                                                                    <a href="#"
                                                                        class="inline-flex items-center px-4 py-2 border border-gray-300 border-transparent text-sm font-medium rounded-md transition duration-150 ease-in-out px-4 py-2 rounded font-medium transition-colors -primary fs-14 mt-3 py-2 px-4 hidden"
                                                                        data-turbo="false">{{ __('messages.pricing_table.get_started')  }}</a>
                                                                @else
                                                                    <a href="{{ route('employer.register')  }}"
                                                                        class="inline-flex items-center px-4 py-2 border border-gray-300 border-transparent text-sm font-medium rounded-md transition duration-150 ease-in-out px-4 py-2 rounded font-medium transition-colors -primary fs-14 mt-3 py-2 px-4"
                                                                        data-turbo="false">{{ __('messages.pricing_table.get_started')  }}</a>
                                                                @endif
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                            <button class="carousel-control-prev" type="button" data-bs-target="#carouselExampleControl"
                                data-bs-slide="prev">
                                <i class="icon fa-solid fa-arrow-left text-red-600 border-red-600"></i>
                            </button>
                            <button class="carousel-control-next" type="button" data-bs-target="#carouselExampleControl"
                                data-bs-slide="next">
                                <i class="icon fa-solid fa-arrow-right text-red-600 border-red-600"></i>
                            </button>
                        </div>

                    </section>
                </div>
            </section>
        @endif
        {{ Form::hidden('homeData', json_encode(getCountries()), ['id' => 'indexHomeData'])  }}
        <!-- end pricing-packages-section -->
    </div>
@endsection
{{ -- @section('page_scripts') -- }}
{{ --    <script> -- }}
{{ --        // var availableLocation = []; -- }}
{{ -- let jobsSearchUrl = "{{ route('front.search-jobs')  }}"; --}}
{{ --        @foreach (getCountries() as $county) -- }}
{{ --        availableLocation.push("{{ $county   }}"); --}}
{{ --        @endforeach -- }}

{{ -- let color = @json($color); -- }}
{{ -- $.each(color, function (key, val) { -- }}
{{--    $('.color' + key).css({ 'border-left': '5px solid' + val, 'border-bottom': '5px solid' + val }); --}}
{{--    $('.date-color' + key).css({ 'background-color': val }); --}}
{{-- }); --}}
{{ --    </script> -- }}
{{ --    <script src="{{asset('front_web/js/slick.min.js') }}"></script> --}}
{{ --    <script src="{{asset('assets/js/home/home.js') }}"></script> --}}
{{ -- @endsection -- }}
