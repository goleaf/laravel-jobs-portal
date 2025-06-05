@extends('front_web.layouts.app')
@section('title')
    {{ __('messages.about_us') }}
@endsection
{{ --@section('page_css')-- }}
{{ --    <link rel="stylesheet" href="{{ asset('front_web/scss/about-us.css') }}">--}}
{{ --@endsection-- }}
@section('content')
    <div class="About Us-page">
        <!-- start hero section -->
        <section class="hero-section relative bg-color-light py-40">
            <div class="container mx-auto px-4 mx-auto">
                <div class="flex flex-wrap items-center justify-center">
                    <div class="flex-1 lg-6 text-center mb-lg-0 mb-md-5 mb-sm-4">
                        <div class="hero-content">
                            <h1 class="text-gray-600 mb-3">
                                {{ __('web.about_us') }}
                            </h1>
                            <nav aria-label="breadcrumb">
                                <ol class="breadcrumb justify-center mb-0">
                                    <li class="breadcrumb-item"><a href="{{ route('front.home') }}" class="fs-18 text-gray">{{ __('web.home') }}</a>
                                    </li>
                                    <li class="breadcrumb-item text-primary-600 fs-18" aria-current="page">{{ __('web.about_us') }}</li>
                                </ol>
                            </nav>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <!-- end hero section -->

        <!-- start-about-section -->
        <div class="about-section py-60">
            <div class="container mx-auto px-4 mx-auto">
                <div class="about-infyjob mb-40">
                    <h5 class="fs-18 text-gray-600 mb-3">{{ __('web.about_us') }}</h5>
                    <p class="fs-16 text-gray">
                        {!! getSettingValue('about_us') !!}
                    </p>
                </div>
            </div>
        </div>
        <!-- end-about-section -->

        <!-- start-how-it-works section -->
        <section class="how-it-works-section bg-color-light py-100">
            <div class="container mx-auto px-4 mx-auto">
                <div class="flex flex-wrap justify-center">
                    <div class="flex-1 -12">
                        <div class="section-heading mx-xxl-0 mx-xl-3 mx-md-2 text-center">
                            <h2 class="text-gray-600">
                                {{ __('web.about_us_menu.how_it_works') }}?</h2>
                            <div class="text-center text-gray">{{ __('web.web_jobSeeker.job_for_anyone_anywhere') }}</div>
                        </div>
                    </div>
                </div>
                <div class="work-process">
                    <div class="flex flex-wrap justify-center">
                        <div class="flex-1 xl-10">
                            <div class="flex flex-wrap justify-center relative">
                                <div class="flex-1 lg-4 text-center">
                                    <div class="img bg-white mx-auto flex justify-center items-center mb-lg-4 mb-3">
                                        <img src="{{ $settings['about_image_one'] }}" >
                                    </div>
                                    <div class="bg-white shadow rounded-lg overflow-hidden body">
                                        <h6 class="fs-18 text-gray-600">
                                            {{ __('web.about_us_menu.step_1') }}</h6>
                                        <h5 class="fs-18 text-gray-600">
                                            {{ $settings['about_title_one'] }}</h5>
                                        <p class="fs-14 text-gray">
                                            {{ $settings['about_description_one'] }}
                                        </p>
                                    </div>
                                </div>
                                <div class="flex-1 lg-4 text-center">
                                    <div class="img bg-white mx-auto flex justify-center items-center mb-lg-4 mb-3">
                                        <img src="{{ $settings['about_image_two'] }}" >
                                    </div>
                                    <div class="bg-white shadow rounded-lg overflow-hidden body">
                                        <h6 class="fs-18 text-gray-600">
                                            {{ __('web.about_us_menu.step_2') }}</h6>
                                        <h5 class="fs-18 text-gray-600">
                                            {{ $settings['about_title_two'] }}</h5>
                                        <p class="fs-14 text-gray">
                                            {{ $settings['about_description_two'] }}</p>
                                    </div>
                                </div>
                                <div class="flex-1 lg-4 text-center">
                                    <div class="img bg-white mx-auto flex justify-center items-center mb-lg-4 mb-3">
                                        <img src="{{ $settings['about_image_three'] }}" >
                                    </div>
                                    <div class="bg-white shadow rounded-lg overflow-hidden body">
                                        <h6 class="fs-18 text-gray-600">
                                            {{ __('web.about_us_menu.step_3') }}</h6>
                                        <h5 class="fs-18 text-gray-600">
                                            {{ $settings['about_title_three'] }}</h5>
                                        <p class="fs-14 text-gray">
                                            {{ $settings['about_description_three'] }}</p>
                                    </div>
                                </div>
                                <div class="arrow1 absolute lg:block hidden">
                                    <img src="{{ asset('front_web/images/arrow-1.png') }}">
                                </div>
                                <div class="arrow2 absolute lg:block hidden">
                                    <img src="{{ asset('front_web/images/arrow-2.png') }}">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <!-- end-how-it-works section -->

        <!-- start question-section -->
        <section class="question-section py-100">
            <div class="container mx-auto px-4 mx-auto">
                <div class="flex flex-wrap justify-center">
                    <div class="flex-1 -8">
                        <div class="section-heading mx-xxl-5 text-center">
                            <h2 class="text-gray-600 bg-white">
                                {{ __('web.about_us_menu.frequently_asked_questions') }}
                            </h2>
                        </div>
                    </div>
                </div>
                <div class="questions">
                    <div class="flex flex-wrap justify-center">
                        <div class="flex-1 lg-10">
                            @if(count($faqLists) > 0)
                                <div class="accordion" id="accordionExample">
                                    @foreach($faqLists as $key => $faqList)
                                        <div class="accordion-item br-10">
                                            <h2 class="accordion-header" id="heading-{{ $key }}">
                                                <button class="accordion-button collapsed fs-18 p-3" type="button" data-bs-toggle="collapse" data-bs-target="#collapse-{{ $key }}" aria-expanded="false" aria-controls="collapse-{{ $key }}"> {{ html_entity_decode($faqList->title) }}
                                                </button>
                                            </h2>
                                            <div id="collapse-{{ $key }}" class="accordion-collapse collapse" aria-labelledby="heading-{{ $key }}" data-bs-parent="#accordionExample">
                                                <div class="accordion-body">
                                                    {!! nl2br( $faqList->description) !!}
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <div>
                                    <h5 class="text-center">{{ __('web.about_us_menu.faq_not_available') }}.</h5>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <!-- end question-section -->
    </div>
@endsection
