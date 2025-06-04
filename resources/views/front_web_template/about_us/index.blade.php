@extends('front_web_template.layouts.app')
@section('title')
    {{ __('messages.about_us')  }}
@endsection
{{ -- @section('page_css') -- }}
{{ --    <link rel="stylesheet" href="{{ asset('front_web/scss/about-us.css')  }}"> --}}
{{ -- @endsection -- }}
@section('content')
    <div class="About Us-page">
        <!-- start hero section -->
        <section class="hero-section relative bg-gradient pt-15 pb-40">
            <div class="container mx-auto px-4 mx-auto">
                <div class="flex flex-wrap items-center justify-center">
                    <div class="flex-1 -lg-6 text-center mb-lg-0 mb-md-5 mb-sm-4">
                        <div class="hero-content">
                            <h1 class="text-gray-600 mb-2">{{ __('web.about_us')  }}</h1>
                            <nav aria-label="breadcrumb">
                                <ol class="breadcrumb justify-center mb-0">
                                    <li class="breadcrumb-item">
                                        <a href="{{ route('front.home')  }}" class="fs-18 text-gray">{{ __('web.home')  }}
                                        </a>
                                    </li>
                                    <li class="breadcrumb-item text-primary-600 fs-18" aria-current="page">
                                        {{ __('web.about_us')  }}
                                    </li>
                                </ol>
                            </nav>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <!-- end hero section -->

        <!-- start-about-section -->
        <div class="about-section pt-60 pb-100">
            <div class="container mx-auto px-4 mx-auto">
                <div class="about-infyjob">
                    <h5 class="fs-18 text-gray-600 mb-3">{{ __('web.about_us')  }}</h5>
                    <p class="fs-16 text-gray mb-0">
                        {!! getSettingValue('about_us') !!}
                    </p>
                </div>
            </div>
        </div>
        <!-- end-about-section -->

        <!-- start-how-it-works section -->
        <section class="how-it-works-section bg-gray-100 pt-100 pb-60">
            <div class="container mx-auto px-4 mx-auto">
                <div class="overflow-hidden pb-60">
                    <div class="section-heading text-center">
                        <h2 class="text-gray-600 mb-0 inline-block">{{ __('web.about_us_menu.how_it_works')  }}?</h2>
                    </div>
                </div>
                <div class="work-process">
                    <div class="flex flex-wrap justify-center">
                        <div class="flex-1 -xxl-10">
                            <div class="flex flex-wrap justify-center relative">
                                <div class="flex-1 -lg-4 text-center px-xl-5 px-lg-4 mb-40">
                                    <div class="img bg-white mx-auto flex justify-center items-center mb-4">
                                        <img src="{{ $settings['about_image_one']  }}" />
                                    </div>
                                    <div class="bg-white shadow rounded-lg overflow-hidden -body p-0 pt-lg-2">
                                        <h5 class="fs-18 text-gray-600">{{ $settings['about_title_one']  }}</h5>
                                        <p class="fs-14 text-gray mb-0">
                                            {{ $settings['about_description_one']  }}
                                        </p>
                                    </div>
                                </div>
                                <div class="flex-1 -lg-4 text-center px-xl-5 px-lg-4 mb-40">
                                    <div class="img bg-white mx-auto flex justify-center items-center mb-4">
                                        <img src="{{ $settings['about_image_two']  }}" />
                                    </div>
                                    <div class="bg-white shadow rounded-lg overflow-hidden -body p-0 pt-lg-2">
                                        <h5 class="fs-18 text-gray-600">{{ $settings['about_title_two']  }}</h5>
                                        <p class="fs-14 text-gray mb-0">
                                            {{ $settings['about_description_two']  }}
                                        </p>
                                    </div>
                                </div>
                                <div class="flex-1 -lg-4 text-center px-xl-5 px-lg-4 mb-40">
                                    <div class="img bg-white mx-auto flex justify-center items-center mb-4">
                                        <img src="{{ $settings['about_image_three']  }}" />
                                    </div>
                                    <div class="bg-white shadow rounded-lg overflow-hidden -body p-0 pt-lg-2">
                                        <h5 class="fs-18 text-gray-600">{{ $settings['about_title_three']  }}</h5>
                                        <p class="fs-14 text-gray mb-0">
                                            {{ $settings['about_description_three']  }}
                                        </p>
                                    </div>
                                </div>
                                <div class="arrow1 absolute lg:block hidden">
                                    <img src="{{ asset('img_template/arrow-1.png')  }}" />
                                </div>
                                <div class="arrow2 absolute lg:block hidden">
                                    <img src="{{ asset('img_template/arrow-2.png')  }}" />
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <!-- end-how-it-works section -->

        <!-- start question-section -->
        {{ -- <section class="question-section py-100">
            <div class="container mx-auto px-4 mx-auto">
                <div class="overflow-hidden pb-60">
                    <div class="section-heading text-center">
                        <h2 class="text-gray-600 mb-0 inline-block">
                            {{ __('web.about_us_menu.frequently_asked_questions')  }}
                        </h2>
                    </div>
                </div>
                <div class="questions">
                    <div class="flex flex-wrap justify-center">
                        <div class="flex-1 -lg-10">
                            @if (count($faqLists) > 0)
                                <div class="accordion" id="accordionExample">
                                    @foreach ($faqLists as $key => $faqList)
                                        <div class="accordion-item br-10">
                                            <h2 class="accordion-header" id="heading-{{ $key  }}">
                                                <button class="accordion-button collapsed fs-18 p-3" type="button"
                                                    data-bs-toggle="collapse"
                                                    data-bs-target="#collapse-{{ $key  }}" aria-expanded="false"
                                                    aria-controls="collapse-{{ $key  }}">
                                                    {{ html_entity_decode($faqList->title)  }}
                                                </button>
                                            </h2>
                                            <div id="collapse-{{ $key  }}" class="accordion-collapse collapse "
                                                aria-labelledby="heading-{{ $key  }}"
                                                data-bs-parent="#accordionExample">
                                                <div class="accordion-body">
                                                    {!! nl2br($faqList->description) !!}
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <div>
                                    <h5 class="text-center">{{ __('web.about_us_menu.faq_not_available')  }}.</h5>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </section> --}}
        <section class="question-section py-100">
            <div class="container mx-auto px-4 mx-auto">
                <div class="overflow-hidden pb-60">
                    <div class="section-heading text-center">
                        <h2 class="text-gray-600 mb-0 inline-block">
                            {{ __('web.about_us_menu.frequently_asked_questions')  }}
                        </h2>
                    </div>
                </div>
                <div class="questions">
                    <div class="flex flex-wrap justify-center">
                        <div class="flex-1 -lg-10">
                            @if (count($faqLists) > 0)
                                <div class="accordion" id="accordionExample">
                                    @foreach ($faqLists as $key => $faqList)
                                        <div class="accordion-item br-10">
                                            <h2 class="accordion-header" id="heading-{{ $key  }}">
                                                <button class="accordion-button @if ($key !== 1) collapsed @endif fs-18 p-3" type="button"
                                                    data-bs-toggle="collapse" data-bs-target="#collapse-{{ $key  }}"
                                                    aria-expanded="true" aria-controls="collapse-{{ $key  }}">
                                                    {{ html_entity_decode($faqList->title)  }}
                                                </button>
                                            </h2>
                                            <div id="collapse-{{ $key  }}"
                                                class="accordion-collapse collapse @if ($key == 1) show @endif"
                                                aria-labelledby="heading-{{ $key  }}"
                                                data-bs-parent="#accordionExample">
                                                <div class="accordion-body">
                                                    <p class="fs-14 text-gray">
                                                        {!! nl2br($faqList->description) !!}
                                                    </p>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <div>
                                    <h5 class="text-center">{{ __('web.about_us_menu.faq_not_available')  }}.</h5>
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
