<section class="overflow-hidden testimonial-section py-100">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mx-auto px-4 mx-auto">
        <div class="flex-wrap flex justify-center">
            <div class="xl:w-3/12 px-4 flex-1 px-4 lg:w-4/12 px-2 -sm-6 flex-1 -7">
                <div class="section-heading">
                    <h2 class="bg-white text-center text-gray-600 mx-xxl-3 mx-xl-0 mx-lg-2 mx-md-4">@lang('web.home_menu.testimonials')</h2>
                </div>
            </div>
        </div>
        <div class="testimonial">
            <div class="flex-wrap flex testimonial-block justify-center">
                <div class="flex-1 lg-9 testimonial-carousel">
                    @foreach($testimonials as $testimonial)
                        <div class="overflow-hidden shadow rounded bg-white testimonial- -lg">
                            <div class="flex-wrap flex justify-content-md-between justify-center">
                                <div class="flex-1 px-4 md:w-3/12 -sm-6 flex-1 -8 flex justify-center">
                                    <div class="relative">
                                        <div class="testimonial-img">
                                            <img src="{{ isset($testimonial->customer_image_url)? $testimonial->customer_image_url:asset('assets/img/infyom-logo.png') }}" alt="profile">
                                        </div>
                                        <div class="comma absolute">
                                            <img src="{{ asset('front_web/images/comma.png') }}" alt="comma">
                                        </div>
                                    </div>
                                </div>
                                <div class="flex-1 px-4 -lg-9 flex-1 md-8 profile-desc ps-lg-5 ps-md-3">
                                    <div class="flex-wrap flex-wrap flex-1 px-4 flex- flex mx-2 -reverse flex-md- flex">
                                        <div class="flex-1 -12">
                                            <div class="testimonial-desc fs-16 text-gray">
                                                {{ !empty(nl2br($testimonial->description))?nl2br($testimonial->description) : __('messages.common.n/a') }}
                                            </div>
                                        </div>
                                        <div class="text-center flex-1 -12 text-md-start">
                                            <p class="mt-3 fs-18 text-gray-600 mb-md-0">{{ html_entity_decode($testimonial->customer_name) }}
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
