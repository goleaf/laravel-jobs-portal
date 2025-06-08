@extends('front_web_template.layouts.app')
@section('title')
    {{ __('messages.candidate.candidate_details') }}
@endsection
{{-- @section('page_css') --}}
{{ -- <link href="asset('front_web/scss/candidate-details.css') " rel="stylesheet" type="text/css"> -- }}
{{-- @endsection --}}
{{-- @dd($candidateDetails) --}}
@section('content')
    {{  -- <section class="hero-section relative bg-color py-40">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mx-auto px-4 mx-auto px-4 mx-auto px-4 mx-auto">
            <div class="flex flex-wrap items-center justify-center">
                <div class="flex-1 -12">
                    <div class="flex flex-wrap align-items-lg-center mb-3">
                        <div class="lg:w-1/12 px-2 flex-1 -sm-2 flex-1 -3">
                            <div class="candidate-profile-img mt-md-0 mt-3">
                                <img
                                        src=" (!empty($candidateDetails->$user->avatar)) ? $candidateDetails->$user->avatar : asset('assets/img/infyom-logo.png') "
                                        alt="candidate profile">
                            </div>
                        </div>
                        <div class="flex-1 -sm-10 flex-1 -9">
                            <div class="hero-content ps-xl-0 ps-3">
                                <h4 class="text-gray-600 mb-0">
                                    {{ html_entity_decode($candidateDetails->$user->full_name)  }}
                                </h4>
                                <div class="hero-desc flex items-center flex-wrap">
                                    <div class="flex items-center me-4 pe-2">
                                        <i class="fa-solid fa-briefcase text-gray me-3 fs-18"></i>
                                        <p class="fs-14 text-gray mb-0">
                                            {{ !empty($candidateDetails->functionalArea->name)? $candidateDetails->functionalArea->name : __('messages.common.n/a') }}</p>
                                    </div>

                                    @if (!empty($candidateDetails->$user->country_name))
                                        <div class="desc flex items-center me-lg-4 me-2 pe-2">
                                            <i class="fa-solid fa-location-dot text-gray me-3 fs-18"></i>
                                            <p class="fs-14 text-gray mb-0">
                                                    <span>{{ $candidateDetails->$user->country_name }}
                                                        @if (!empty($candidateDetails->$user->state_name))
                                                            ,{{ $candidateDetails->$user->state_name }}
                                                        @endif
                                                        @if (!empty($candidateDetails->$user->city_name))
                                                            ,{{ $candidateDetails->$user->city_name }}
                                                @endif
                                            </p>
                                        </div>
                                    @endif
                                    <div class="desc flex items-center me-lg-4 me-2 pe-2">
                                        <i class="fa-solid fa-envelope text-gray me-3 fs-18"></i>
                                        <p class="fs-14 text-gray mb-0">
                                            {{ $candidateDetails->$user->email }}
                                        </p>
                                    </div>
                                    @if ($candidateDetails->$user->dob)
                                        <div class="desc flex items-center me-lg-4 me-2 pe-2">
                                            <i class="fa-solid fa-location-dot text-gray me-3 fs-18"></i>
                                            <p class="fs-14 text-gray mb-0">
                                                {{ \Carbon\Carbon::parse($candidateDetails->$user->dob)->translatedFormat('jS M, Y') }}
                                            </p>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div class="flex items-center flex-wrap">
                            @auth
                                @role('Employer')
                                <ul class="post-tags mt-3 ps-0">
                                    @if ($isReportedToCandidate)
                                        <button class="border border-gray-300 bg-transparent" disabled
                                        >{{ __('messages.candidate.already_reported') }}</button>
                                    @else
                                        <button type="button" class="border border-gray-300 bg-transparent"
                                                data-bs-toggle="modal"
                                                data-bs-target="#reportToCandidateModal">
                                            {{ __('messages.candidate.reporte_to_candidate') }}
                                        </button>
                                    @endif
                                </ul>
                                @endrole
                            @endauth
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="about-company-section py-60">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mx-auto px-4 mx-auto px-4 mx-auto px-4 mx-auto">
            <div class="flex flex-wrap">
                <div class="flex-1 lg-8">
                    <div class="mb-5">
                        <h5 class="fs-4 text-gray-600 mb-4">{{ __('messages.candidate_profile.education') }}</h5>
                        <div class="job-description">
                            @forelse($candidateEducations as $candidateEducation)
                                <div class="job-description-block pb-3">
                                    <span class="name">{{ ucfirst($candidateEducation->degreeLevel->name[0]) }}</span>
                                    <div class="job-description-right">
                                        <h5 class="fs-18 text-gary mb-0">{{ $candidateEducation->degreeLevel->name }}</h5>
                                        <span class="text-indigo-600 -600"> {{ ucfirst($candidateEducation->institute) }}</span>
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded -full text-xs font-medium bg-gray-600">{{ $candidateEducation->year }}</span>
                                    </div>
                                </div>
                            @empty
                                <h4 class="text-center">{{ __('messages.candidate.education_not_found') }}</h4>
                            @endforelse
                        </div>
                    </div>
                    <div>
                        <h5 class="fs-4 text-gray-600 mb-4">{{ __('messages.candidate_profile.work_experience') }}</h5>
                        <div class="job-description">
                            @forelse($candidateExperiences as $candidateExperience)
                                <div class="job-description-block pb-3">
                                    <span class="name">{{ ucfirst($candidateExperience->experience_title[0]) }}</span>
                                    <div class="job-description-right">
                                        <div class="info-box">
                                            <h5 class="fs-18 text-gary mb-0">{{ $candidateExperience->experience_title }}</h5>
                                            <span class="text-indigo-600 -600">{{ ucfirst($candidateExperience->company) }}</span>
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded -full text-xs font-medium bg-gray-600"> {{ \Carbon\Carbon::parse($candidateExperience->start_date)->format('Y') }} - {{ ($candidateExperience->currently_working) ? 'present' : \Carbon\Carbon::parse($candidateExperience->end_date)->format('Y') }}</span>
                                        </div>
                                    </div>
                                    @if (!empty($candidateExperience->description))
                                        <div class="mt-2">{{ Str::limit(nl2br($candidateExperience->description),300,'...') }}</div>
                                    @endif
                                </div>
                            @empty
                                <h4 class="text-center">{{ __('messages.candidate.experience_not_found') }}</h4>
                            @endforelse
                        </div>
                    </div>
                </div>
                <div class="flex-1 lg-4">
                    @include('front_web.candidate.candidate_detail_sidebar')
                </div>
            </div>
        </div>
    </section> --}}

    <section class="hero-section relative bg-gradient pt-15 pb-40">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mx-auto px-4 mx-auto px-4 mx-auto px-4 mx-auto">
            <div class="flex flex-wrap items-center justify-center">
                <div class="flex-1 -12">
                    <div class="flex align-items-md-center">
                        <div class="me-4">
                            <div class="hero-img">
                                <img src="{{ !empty($candidateDetails->$user->avatar) ? $candidateDetails->$user->avatar : asset('assets/img/infyom-logo.png') }}"
                                    class="w-full h-full rounded -full object-fit-cover" alt="company-details" />
                            </div>
                        </div>
                        <div class="">
                            <div class="hero-content">
                                <h4 class="text-gray-600 lh-base mb-2">
                                    {{ html_entity_decode($candidateDetails->$user->full_name) }}</h4>
                                <div class="hero-desc d-md-flex">
                                    <div class="flex mb-1">
                                        <div class="me-3 w-20">
                                            <x-icons.briefcase class="w-full" />
                                        </div>
                                        <p class="fs-14 text-gray mb-0">
                                            {{ !empty($candidateDetails->functionalArea->name) ? $candidateDetails->functionalArea->name : __('messages.common.n/a') }}
                                        </p>
                                    </div>
                                    @if (!empty($candidateDetails->$user->country_name))
                                        <div class="flex mb-2">
                                            <div class="me-3 w-20">
                                                <x-icons.location class="w-full" />
                                            </div>
                                            <p class="fs-14 text-gray mb-0">
                                                @if (!empty($candidateDetails->$user->state_name))
                                                    ,{{ $candidateDetails->$user->state_name }}
                                                @endif
                                                @if (!empty($candidateDetails->$user->city_name))
                                                    ,{{ $candidateDetails->$user->city_name }}
                                                @endif
                                            </p>
                                        </div>
                                    @endif
                                    <div class="desc flex me-4 pe-2">
                                        <div class="me-3 w-20">
                                            <x-icons.email class="text-gray-500" />
                                        </div>
                                        <a href="#" class="fs-14 text-gray text-break">{{ $candidateDetails->$user->email }}</a>
                                    </div>
                                    @if ($candidateDetails->$user->dob)
                                        <div class="desc flex">
                                            <div class="me-3 w-20">
                                                <x-icons.calendar class="text-gray-500" />
                                            </div>
                                            <p class="fs-14 text-gray mb-0">
                                                {{ \Carbon\Carbon::parse($candidateDetails->$user->dob)->translatedFormat('jS M, Y') }}
                                            </p>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="job-details-section py-60 mb-sm-4">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mx-auto px-4 mx-auto px-4 mx-auto px-4 mx-auto">
            <div class="flex flex-wrap">
                <div class="flex-1 lg-8">
                    <div class="Job Description mb-lg-5 mb-4">
                        <h5 class="fs-18 text-gray-600 mb-4">{{ __('messages.candidate_profile.education') }}</h5>
                        <div class="job-description">
                            @forelse($candidateEducations as $candidateEducation)
                                <div class="job-description-block pb-3">
                                    <div class="job-description-right">
                                        <h5 class="fs-18 text-gary mb-0">{{ $candidateEducation->degreeLevel->name }}</h5>
                                        <span class="text-gray"> {{ ucfirst($candidateEducation->institute) }}</span>
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded -full text-xs font-medium bg-indigo-600 -600">{{ $candidateEducation->year }}</span>
                                    </div>
                                </div>
                            @empty
                                <div class="flex-1 md-12 text-center text-gray">
                                    {{ __('messages.candidate.education_not_found') }}
                                </div>
                            @endforelse
                        </div>
                    </div>
                    <div class="key-responsibilities mb-lg-5 mb-4">
                        <h5 class="fs-18 text-gray-600 mb-4">{{ __('messages.candidate_profile.work_experience') }}</h5>
                        @forelse($candidateExperiences as $candidateExperience)
                            <div class="job-description-block pb-3">
                                <div class="job-description-right">
                                    <div class="info-box">
                                        <h5 class="fs-18 text-gary mb-3">{{ $candidateExperience->experience_title }}
                                        </h5>
                                        <span class="text-gray">{{ ucfirst($candidateExperience->company) }}</span>
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded -full text-xs font-medium bg-indigo-600 -600">
                                            {{ \Carbon\Carbon::parse($candidateExperience->start_date)->format('Y') }}
                                            -
                                            {{ $candidateExperience->currently_working ? 'present' : \Carbon\Carbon::parse($candidateExperience->end_date)->format('Y') }}</span>
                                    </div>
                                </div>
                                @if (!empty($candidateExperience->description))
                                    <div class="mt-2">{{ Str::limit(nl2br($candidateExperience->description), 300, '...') }}</div>
                                @endif
                            </div>
                        @empty
                            <div class="flex-1 md-12 text-center text-gray">
                                {{ __('messages.candidate.experience_not_found') }}
                            </div>
                        @endforelse
                    </div>
                </div>
                <div class="flex-1 lg-4">
                    @include('front_web_template.candidate.candidate_detail_sidebar')
                </div>
            </div>
        </div>
    </section>
    <!-- end hero section -->
    @role('Employer')
        @include('front_web_template.candidate.report_to_candidate_modal')
    @endrole
    @if (!empty($candidateDetails->$user->facebook_url) || !empty($candidateDetails->$user->twitter_url) || !empty($candidateDetails->$user->google_plus_url) || !empty($candidateDetails->$user->pinterest_url) || !empty($candidateDetails->$user->linkedin_url))
    <div class="flex-1 -12">
        <div class="flex-1 -12 mb-40">
            <div class="job- bg-white overflow-hidden shadow rounded-lg bg-white shadow rounded -lg overflow-hidden py-30">
                <div class="flex flex-wrap flex justify-content-lg-between">
                    <p class="fs-18 text-gray-600">@lang('web.web_company.social_media')</p>
                    <div class="mt-3">
                        @if (!empty($candidateDetails->$user->facebook_url))
                            <a href="{{ (isset($candidateDetails->$user->facebook_url)) ? addLinkHttpUrl($candidateDetails->$user->facebook_url) : 'javascript:void(0)' }}" target="_blank" class="mx-2">
                                <x-icons.facebook class="w-6 h-6" />
                            </a>
                        @endif
                        @if (!empty($candidateDetails->$user->twitter_url))
                            <a href="{{ (isset($candidateDetails->$user->twitter_url)) ? addLinkHttpUrl($candidateDetails->$user->twitter_url) : 'javascript:void(0)' }}"
                               target="_blank" class="mx-2">
                                <x-icons.twitter class="w-6 h-6" />
                            </a>
                        @endif
                        @if (!empty($candidateDetails->$user->google_plus_url))
                            <a href="{{ (isset($candidateDetails->$user->google_plus_url)) ? addLinkHttpUrl($candidateDetails->$user->google_plus_url) : 'javascript:void(0)' }}"
                               target="_blank" class="mx-2">
                                <x-icons.google-plus class="w-6 h-6" />
                            </a>
                        @endif
                        @if (!empty($candidateDetails->$user->pinterest_url))
                            <a href="{{ (isset($candidateDetails->$user->pinterest_url)) ? addLinkHttpUrl($candidateDetails->$user->pinterest_url) : 'javascript:void(0)' }}"
                               target="_blank" class="mx-2">
                                <x-icons.pinterest class="w-6 h-6" />
                            </a>
                        @endif
                        @if (!empty($candidateDetails->$user->linkedin_url))
                            <a href="{{ (isset($candidateDetails->$user->linkedin_url)) ? addLinkHttpUrl($candidateDetails->$user->linkedin_url) : 'javascript:void(0)' }}"
                               target="_blank" class="mx-2">
                                <x-icons.linkedin class="w-6 h-6" />
                            </a>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif
@endsection
{{-- @section('scripts') --}}
{{--  --}}
{{-- @endsection --}}

@push('scripts')
    @vite('resources/js/pages/candidate_details.js')
@endpush
