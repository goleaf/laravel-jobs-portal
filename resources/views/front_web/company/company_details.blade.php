@extends('front_web.layouts.app')
@section('title')
    {{ __('web.job_details.job_details') }}
@endsection
{{-- @section('page_css') --}}
{{ -- <link href="asset('front_web/scss/company-details.css') " rel="stylesheet" type="text/css"> -- }}
{{-- @endsection --}}
@section('content')
    <div class="company-details-page">
        <!-- start hero section -->
        <section class="hero-section relative bg-color-light py-40">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mx-auto px-4 mx-auto px-4 mx-auto">
                <div class="flex flex-wrap items-center justify-center">
                    <div class="flex-1 -12">
                        <div class="flex flex-wrap align-items-lg-center mb-3">
                            <div class="lg:w-1/12 px-2 flex-1 -sm-2 flex-1 -3">
                                <div class="company-profile-img mt-md-0 mt-3">
                                    <img src="{{ !empty($companyDetail->company_url) ? $companyDetail->company_url : asset('assets/img/infyom-logo.png') }}"
                                        alt="job_detail_logo">
                                </div>
                            </div>
                            <div class="flex-1 -sm-10 flex-1 -9">
                                <div class="hero-content ps-xl-0 ps-3">
                                    <h4 class="text-gray-600 mb-0">
                                        {{ html_entity_decode($companyDetail->$user->full_name) }}
                                    </h4>
                                    <div class="hero-desc flex items-center flex-wrap">
                                        <div class="flex items-center me-4 pe-2">
                                            <i class="fa-solid fa-briefcase text-gray me-3 fs-18"></i>
                                            <p class="fs-14 text-gray mb-0">
                                                {{ !empty($companyDetail->industry->name) ? $companyDetail->industry->name : __('messages.common.n/a') }}
                                            </p>
                                        </div>
                                        {{  -- @if (!empty($companyDetail->$user->city_id) || !empty($companyDetail->$user->state_id) || !empty($companyDetail->$user->country_id))
                                            <div class="desc flex items-center me-lg-4 me-2 pe-2">
                                                <i class="fa-solid fa-location-dot text-gray me-3 fs-18"></i>
                                                <p class="text-gray fs-14 mb-0">
                                                     isset($companyDetail->location) ? html_entity_decode($companyDetail->location) : __('messages.common.n/a') 
                                                    {{ isset($companyDetail->location2) ? ', ' . html_entity_decode($companyDetail->location2) : ''  }}
                                                </p>
                                            </div>
                                        @endif --}}
                                        @isset($companyDetail->$user->phone)
                                            <div class="desc flex items-center me-lg-4 me-2 pe-2">
                                                <i class="fa-solid fa-phone text-gray me-3 fs-18"></i>
                                                <p class="fs-14 text-gray mb-0">
                                                    {{ $companyDetail->$user->phone }}</p>
                                            </div>
                                        @endisset
                                        <div class="desc flex items-center me-lg-4 me-2 pe-2">
                                            <i class="fa-solid fa-envelope text-gray me-3 fs-18"></i>
                                            <p class="fs-14 text-gray mb-0">
                                                {{ $companyDetail->$user->email }}</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @role('Candidate')
                            <div class="flex flex-wrap align-items-lg-center">
                                <div class="hero-desc d-md-flex">
                                    <div class="desc flex me-4 pe-2">
                                        <a href="javascript:void(0)" class="border border-gray-300 bg-transparent"
                                            data-favorite-user-id="{{ getLoggedInUserId() !== null ? getLoggedInUserId() : null }}"
                                            data-favorite-company_id="{{ $companyDetail->id }}" id="addToFavourite">
                                            <i class="favouriteIcon"></i>
                                            <span class="favouriteText"></span>
                                        </a>
                                    </div>
                                    <div class="desc flex me-4 pe-2">
                                        @if ($isReportedToCompany)
                                            <button type="button" class="border border-gray-300 bg-transparent" disabled
                                                data-bs-toggle="modal" data-bs-target="#reportToCompanyModal">
                                                {{ __('messages.candidate.already_reported') }}
                                            </button>
                                        @else
                                            <button data-bs-toggle="modal" data-bs-target="#reportToCompanyModal"
                                                class="border border-gray-300 bg-transparent"disabled' : '' }}"
                                                {{ $isReportedToCompany ? 'style=pointer-events:none;' : '' }}>{{ __('messages.company.report_to_company') }}
                                            </button>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @endrole
                    </div>
                </div>
            </div>
        </section>
        <!-- end hero section -->

        <!-- start about-comapany section -->
        <section class="about-company-section py-60">
         <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mx-auto px-4 mx-auto px-4 mx-auto">
             <div class="flex flex-wrap">
                 <div class="flex-1 lg-8">
                     <div class="about-company-left">
                         <h5 class="fs-18 text-gray-600">@lang('web.web_company.about_company')</h5>
                         <div class="job-description mb-5 pb-lg-2">
                             {!! nl2br($companyDetail->details) !!}
                         </div>
                     </div>

                 </div>
                 <div class="flex-1 lg-4">
                     <div class="flex-1 -12">
                         <div class="flex-1 -12 mb-40">
                             <div class="job- bg-white overflow-hidden shadow rounded-lg bg-white shadow rounded -lg overflow-hidden py-30">
                                 <div class="flex flex-wrap flex justify-content-lg-between">
                                     <div class="flex-1 -5 mt-3">
                                         <i class="fa-solid fa-cake-candles text-indigo-600 -600 fs-4"></i>
                                         <p class="details-page- bg-white shadow rounded -lg overflow-hidden text mb-0" >
                                             @lang('web.web_jobs.founded_in')</p>
                                         <p class="text-gray-600 fs-14">
                                             {{ !empty($companyDetail->established_in)? $companyDetail->established_in : __('messages.common.n/a') }}
                                         </p>
                                     </div>
                                     <div class="flex-1 -5 mt-3">
                                         <i class="fa-regular fa-map text-indigo-600 -600 fs-4"></i>
                                         <p class="details-page- bg-white shadow rounded -lg overflow-hidden text mb-0">
                                             @lang('web.web_company.ownership')
                                         </p>
                                         <p class="text-gray-600 fs-14">
                                             {{ !empty($companyDetail->ownerShipType->name)? $companyDetail->ownerShipType->name : __('messages.common.n/a') }}
                                         </p>
                                     </div>
                                     <div class="flex-1 -5 mt-3">
                                         <i class="fa-solid fa-users text-indigo-600 -600 fs-4"></i>
                                         <p class="details-page- bg-white shadow rounded -lg overflow-hidden text mb-0" >
                                             @lang('web.web_company.company_size')
                                         </p>
                                         <p class="text-gray-600 fs-14">
                                             {{ !empty($companyDetail->companySize->size)? $companyDetail->companySize->size : __('messages.common.n/a') }}
                                         </p>
                                     </div>

                                 </div>
                             </div>
                         </div>
                     </div>
                     <div class="flex-1 -12">
                         <div class="flex-1 -12 mb-40">
                             <div class="job- bg-white overflow-hidden shadow rounded-lg bg-white shadow rounded -lg overflow-hidden py-30">
                                 <div class="flex flex-wrap flex justify-content-lg-between">
                                     @if($companyDetail->$user->phone)
                                         <div class="flex-1 -10 m-3 flex items-center">
                                             <i class="fa-solid fa-phone text-indigo-600 -600 fs-4"></i>
                                             <div class="mx-3">
                                                 <p class="details-page- bg-white shadow rounded -lg overflow-hidden text mb-0">
                                                     @lang('web.web_jobs.phone')
                                                 </p>
                                                 <p class="text-gray-600 fs-14 mb-0">
                                                     {{ $companyDetail->$user->phone }}</p>
                                             </div>
                                         </div>
                                         <hr>
                                     @endif
                                     <div class="flex-1 -10 m-3 flex items-center">
                                         <i class="fa-solid fa-location-dot text-indigo-600 -600 fs-4"></i>
                                         <div class="mx-3">
                                             <p class="details-page- bg-white shadow rounded -lg overflow-hidden text mb-0">
                                                 @lang('web.common.location')
                                             </p>
                                             <p class="text-gray-600 fs-14 mb-0">
                                                 {{ (isset($companyDetail->location)) ? html_entity_decode(Str::limit($companyDetail->location,12,'...')) : __('messages.common.n/a') }} {{ (isset($companyDetail->location2)) ? ','.html_entity_decode(Str::limit($companyDetail->location2,12,'...')) : '' }}</p>
                                         </div>
                                     </div>
                                     <hr>
                                     @isset($companyDetail->website)
                                         <div class="flex-1 -10 m-3 flex items-center">
                                             <i class="fa-solid fa-globe text-indigo-600 -600 fs-4"></i>
                                             <div class="mx-3">
                                                 <p class="details-page- bg-white shadow rounded -lg overflow-hidden text mb-0">
                                                     @lang('web.common.location')
                                                 </p>
                                                 <p class="text-gray-600 fs-14 mb-0">
                                                     <a href="{{ (isset($companyDetail->website))
                                     ?
                                         (!str_contains($companyDetail->website,'https://')
                                         ? 'https://'.$companyDetail->website
                                         : $companyDetail->website)
                                     : 'javascript:void(0)' }}"
                                                        target="_blank">{{ (isset($companyDetail->website)) ? preg_replace("(^https?://www.)","", $companyDetail->website) : 'N/A' }}</a>
                                                 </p>
                                             </div>
                                         </div>
                                         <hr>
                                     @endisset
                                         <div class="flex-1 -10 m-3 flex items-center">
                                             <i class="fa-regular fa-envelope text-indigo-600 -600 fs-4"></i>
                                             <div class="mx-3">
                                                 <p class="details-page- bg-white shadow rounded -lg overflow-hidden text mb-0">
                                                     @lang('web.common.email')
                                                 </p>
                                                 <p class="text-gray-600 fs-14 mb-0">
                                                     {{ $companyDetail->$user->email }}</p>
                                             </div>
                                         </div>
                                 </div>
                             </div>
                         </div>
                     </div>
                     @if(isset($companyDetail->$user->facebook_url) || isset($companyDetail->$user->twitter_url) || isset($companyDetail->$user->pinterest_url) || isset($companyDetail->$user->google_plus_url) || isset($companyDetail->$user->linkedin_url))
                     <div class="flex-1 -12">
                         <div class="flex-1 -12 mb-40">
                             <div class="job- bg-white overflow-hidden shadow rounded-lg bg-white shadow rounded -lg overflow-hidden py-30">
                                 <div class="flex flex-wrap flex justify-content-lg-between">
                                         <p class="fs-18 text-gray-600">@lang('web.web_company.social_media')</p>
                                         <div class="mt-3">
                                             @if(isset($companyDetail->$user->facebook_url))
                                                 <a href="{{ (isset($companyDetail->$user->facebook_url)) ? addLinkHttpUrl($companyDetail->$user->facebook_url) : 'javascript:void(0)' }}"
                                                    class="ms-2" target="_blank">
                                                     <i class="fa-brands fa-facebook-f fs-3 mx-2"></i></a>
                                             @endif
                                             @if(isset($companyDetail->$user->linkedin_url))
                                                 <a href="{{ (isset($companyDetail->$user->linkedin_url)) ? addLinkHttpUrl($companyDetail->$user->linkedin_url) : 'javascript:void(0)' }}"
                                                    class="ms-2" target="_blank">
                                                     <i class="fa-brands fa-linkedin-in fs-3 mx-2"></i></a>
                                             @endif
                                             @if(isset($companyDetail->$user->twitter_url))
                                                 <a href="{{ (isset($companyDetail->$user->twitter_url)) ? addLinkHttpUrl($companyDetail->$user->twitter_url) : 'javascript:void(0)' }}"
                                                    class="ms-2" target="_blank">
                                                     <i class="fa-brands fa-twitter fs-3 mx-2"></i></a>
                                             @endif
                                             @if(isset($companyDetail->$user->google_plus_url))
                                                 <a href="{{ (isset($companyDetail->$user->google_plus_url)) ? addLinkHttpUrl($companyDetail->$user->google_plus_url) : 'javascript:void(0)' }}"
                                                    class="ms-2" target="_blank">
                                                     <i class="fa-brands fa-google-plus-g fs-3 mx-2"></i></a>
                                             @endif
                                             @if(isset($companyDetail->$user->pinterest_url))
                                                 <a href="{{ (isset($companyDetail->$user->pinterest_url)) ? addLinkHttpUrl($companyDetail->$user->pinterest_url) : 'javascript:void(0)' }}"
                                                    class="ms-2" target="_blank">
                                                     <i class="fa-brands fa-pinterest-p fs-3 mx-2"></i></a>
                                             @endif
                                         </div>

                                 </div>
                             </div>
                         </div>
                     </div>
                     @endif
                 </div>
                 <div class="our-latest-jobs">
                     <h5 class="fs-18 text-gray-600 mb-40">
                         {{ ($jobDetails->count() > 0 ) ? __('web.company_details.our_latest_jobs')  : __('web.home_menu.latest_job_not_available') }}
                     </h5>
                     <div class="flex flex-wrap">
                         @foreach($jobDetails as $job)
                             <div class="flex-1 -12 px-xl-3 mb-20">
                                 <div class="bg-white shadow rounded -lg overflow-hidden py-30 border border border-gray-300 -gray-300 -left-color">
                                     <div class="flex flex-wrap relative">
                                         <div class="flex-1 -xl-1 md:w-2/12 flex-1 -3 mb-md-0 mb-3">
                                             <img src="{{ $job->$company->company_url }}" class="bg-white shadow rounded -lg overflow-hidden img" alt="">
                                         </div>
                                         <div class="flex-1 -xl-10 md:w-9/12 flex-1 -sm-8 flex-1 -12">
                                             <div class="bg-white shadow rounded -lg overflow-hidden body p-0 ps-xl-3">
                                                 <a href="{{ route('front.',$job['job_id']) }}"
                                                    class="text-gray-600 primary-link-hover">
                                                     <h5 class="bg-white shadow rounded -lg overflow-hidden title fs-18 mb-0 inline-block">
                                                         {{ html_entity_decode(Str::limit($job['job_title'], 50)) }}

                                                     </h5>
                                                 </a>
                                                 @if(isset($job->jobShift->shift))
                                                     <span class="text text-indigo-600 -600 fs-12 mb-0 me-3">
                             {{ $job->jobShift->shift }}
                             </span>
                                                 @endif

                                                 <div class="flex-1 xl-12">
                                                     <div class="bg-white shadow rounded -lg overflow-hidden desc flex flex-wrap mt-2">

                                                         <div class="desc flex me-4">
                                                             <i class="fa-solid fa-location-dot text-gray me-3 fs-18"></i>
                                                             <p class="fs-14 text-gray mb-2">
                                                                 {{ (!empty($job->full_location)) ? $job->full_location : 'Location Info. not available.' }}</p>
                                                         </div>
                                                         <div class="desc flex">
                                     <span class="text-gray">
                                         {{ $job->currency->currency_icon }}&nbsp</span>
                                                             <p class="fs-14 text-gray mb-2">
                                                                 {{ $job->salary_from }} - {{ $job->salary_to }}</p>
                                                         </div>
                                                     </div>
                                                 </div>
                                             </div>
                                         </div>
                                         @if($job->activeFeatured)
                                             <div class="bookmark text-end absolute">
                                                 <i class="text-indigo-600 -600 fa-solid fa-bookmark"></i>
                                             </div>
                                         @endif
                                     </div>
                                 </div>
                             </div>
                         @endforeach
                         @if(($jobDetails->count() > 0 ))
                             <div class="flex flex-wrap justify-center">
                                 <div class="flex-1 -8 text-center">
                                     <a href="{{ route('front.',array('company'=> $companyDetail->id)) }}"
                                        class="border border-gray-300 bg-transparent">
                                         @lang('web.common.show_all')</a>
                                 </div>
                             </div>
                         @endif
                     </div>
                 </div>
             </div>
         </div>
     </section>

    @role('Candidate')
        @include('front_web.company.report_to_company_modal')
    @endrole
    <!-- end about-comapany section -->
    {{ Form::hidden('isCompanyAddedToFavourite', $isCompanyAddedToFavourite, ['id' => 'isCompanyAddedToFavourite']) }}
    {{ Form::hidden('followText', __('web.company_details.follow'), ['id' => 'followText']) }}
    {{ Form::hidden('unfollowText', __('web.company_details.unfollow'), ['id' => 'unfollowText']) }}
    </div>
@endsection
{{-- @section('page_scripts') --}}
{{--  --}}
{{-- @endsection --}}

@push('scripts')
    @vite('resources/js/pages/company_details.js')
@endpush
