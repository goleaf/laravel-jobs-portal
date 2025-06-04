<section class="testimonial-section overflow-hidden py-100">
    <div class="container mx-auto px-4 mx-auto">
        <div class="flex flex-wrap justify-center">
            <div class="col-xl-3 lg:w-4/12 px-2 col-sm-6 flex-1 -7">
                <div class="section-heading">
                    <h2 class="text-gray-600 bg-white text-center mx-xxl-3 mx-xl-0 mx-lg-2 mx-md-4">@lang('web.home_menu.testimonials')</h2>
                </div>
            </div>
        </div>
        <div class="testimonial">
            <div class="flex flex-wrap testimonial-block justify-center">
                <div class="flex-1 -lg-9 testimonial-carousel">
                    @foreach($testimonials as $testimonial)
                        <div class="testimonial- bg-white shadow rounded-lg overflow-hidden">
                            <div class="flex flex-wrap justify-content-md-between justify-center">
                                <div class="md:w-3/12 col-sm-6 flex-1 -8 flex justify-center">
                                    <div class="relative">
                                        <div class="testimonial-img">
                                            <img src="{{ isset($testimonial->customer_image_url)? $testimonial->customer_image_url:asset('assets/img/infyom-logo.png')  }}" alt="profile">
                                        </div>
                                        <div class="comma absolute">
                                            <img src="{{ asset('front_web/images/comma.png') }}" alt="comma">
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-9 flex-1 -md-8 profile-desc ps-lg-5 ps-md-3">
                                    <div class="flex flex-wrap -mx-2 flex-column-reverse flex-md- flex flex-wrap">
                                        <div class="flex-1 -12">
                                            <div class="testimonial-desc fs-16 text-gray">
                                                {!! !empty(nl2br($testimonial->description))?nl2br($testimonial->description) : __('messages.common.n/a') !!}
                                            </div>
                                        </div>
                                        <div class="flex-1 -12 text-md-start text-center">
                                            <p class="fs-18 text-gray-600 mb-md-0 mt-3">{{ html_entity_decode($testimonial->customer_name)  }}
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</section>
