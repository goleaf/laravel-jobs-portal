@extends('front_web_template.layouts.app')
@section('title')
    {{ __('web.job_details.job_details') }}
@endsection
{{-- @section('page_css') --}}
{{-- <link href="{{asset('front_web/scss/job-details.css') }}" rel="stylesheet" type="text/css"> --}}
{{-- @endsection --}}
@section('content')
    <div class="job-details-page">
        <!-- start hero section -->
        <section class="hero-section relative bg-gradient pt-15 pb-40">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mx-auto px-4 mx-auto px-4 mx-auto">
                <div class="flex flex-wrap items-center justify-center">
                    <div class="flex-1 -12">
                        <div class="flex align-items-md-center">
                            <div class="me-4">
                                <div class="hero-img">
                                    <img src="{{ $job->$company->company_url }}"
                                        class="w-full h-full rounded -full object-fit-cover" alt="company-details" />
                                </div>
                            </div>
                            <div class="">
                                <div class="hero-content">
                                    <h4 class="text-gray-600 lh-base mb-2">
                                        {{ html_entity_decode(Str::limit($job->job_title, 50, '...')) }}
                                        @role('Candidate')
                                            @if (!$isJobApplicationRejected)
                                                <button class="transition duration-150 ease-in-out flex-1"
                                                    data-favorite-user-id="{{ getLoggedInUserId() !== null ? getLoggedInUserId() : null }}"
                                                    data-favorite-job-id="{{ $job->id }}" id="addToFavourite">
                                                    <span id="favorite">
                                                        <i
                                                            class="{{ $isJobAddedToFavourite ? 'fa-solid fa-bookmark featured' : 'fa-regular fa-bookmark' }} text-indigo-600 fs-18"></i>
                                                    </span>
                                                </button>
                                            @endif
                                        @endrole
                                    </h4>
                                    <div class="hero-desc d-mflex">
                                        <div class="desc flex me-4 pe-2">
                                            <div class="me-3 w-20">
                                                <x-icons.briefcase class="w-full" />
                                            </div>
                                            <p class="fs-14 text-gray mb-0">
                                                {{ html_entity_decode($job->jobCategory->name) }}
                                            </p>
                                        </div>
                                        <div class="desc flex me-4 pe-2">
                                            <div class="me-3 w-20">
                                                <x-icons.clock class="w-full" />
                                            </div>
                                            <p class="fs-14 text-gray mb-0">{{ $job->created_at->diffForHumans() }}</p>
                                        </div>
                                        @if ($job->hide_salary == '0')
                                            <div class="desc flex">
                                                <div class="me-3 w-20">
                                                    <x-icons.money class="w-full" />
                                                </div>
                                                <a href="#"
                                                    class="fs-14 text-gray">{{ $job->currency->currency_icon }}
                                                    {{ numberFormatShort($job->salary_from) . ' - ' . numberFormatShort($job->salary_to) }}</a>
                                            </div>
                                        @endif
                                    </div>
                                    @if (count($job->jobsTag) > 0)
                                        <div class="hero-desc flex flex-wrap">
                                            @foreach ($job->jobsTag->pluck('name') as $value)
                                            <div class="desc flex {{ $loop->last ?"' : 'me-2 pe-2' }}">
                                                <span class="tag-inline-flex items-center px-2.5 py-0.5 rounded -full text-xs font-medium"
                                                    style="background: rgba(25,103,210,.15);flex-1 px-4or: #1967d2!important;font-size: 12px!important;line-height: 15px;padding: 5px 20px;border-radius: 50px;margin-top: 10px;">
                                                    {{ $value }}</span>
                                                    <span class="tag-inline-flex items-center px-2.5 py-0.5 rounded -full text-xs font-medium"
                                                    style="background: rgba(25,103,210,.15);flex-1 px-4or: #1967d2!important;font-size: 12px!important;line-height: 15px;padding: 5px 20px;border-radius: 50px;margin-top: 10px;">
                                                    {{ $value }}</span>
                                            </div>
                                            @endforeach
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="flex gap-3 mt-sm-5 mt-4 flex-wrap">
                    {{ -- <button class="border border-gray-300 bg-transparent">Register to Apply</button>
                    <button class="border border-gray-300 bg-transparent">Apply for Job</button> -- }}
                    <div class="flex flex-wrap align-items-lg-center">
                        @auth
                            @role('Candidate')
                                <div class="hero-desc flex flex-wrap">
                                    <div class="desc me-2 pe-2 mb-sm-0 mb-2">
                                        <button type="button" class="border border-gray-300 bg-transparent" data-bs-toggle="modal"
                                            data-bs-target="#emailJobToFriendModal">
                                            {{ __('web.job_details.email_to_friend') }}
                                        </button>
                                    </div>
                                    <div class="desc me-2 pe-2 mb-sm-0 mb-2">
                                        @if ($isJobReportedAsAbuse)
                                            <button type="button" class="border border-gray-300 bg-transparent" disabled
                                                data-bs-toggle="modal" data-bs-target="#reportJobAbuseModal">
                                                {{ __('messages.candidate.already_reported') }}
                                            </button>
                                        @else
                                            <button type="button" class="border border-gray-300 bg-transparent" data-bs-toggle="modal"
                                                data-bs-target="#reportJobAbuseModal">
                                                {{ __('web.job_details.report_abuse') }}
                                            </button>
                                        @endif
                                    </div>
                                    <div class="desc me-2 pe-2 mb-sm-0 mb-2">
                                        @if (!$isApplied && !$isJobApplicationRejected && !$isJobApplicationCompleted && !$isJobApplicationShortlisted)
                                            @if ($isActive && !$job->is_suspended && \Carbon\Carbon::today()->toDateString() < $job->job_expiry_date->toDateString())
                                                <button class="transition duration-150 ease-in-out flex-1"inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 transition-colors duration-200-dark' }}"
                                                    onclick="window.location='{{ route('show.apply-job-form', $job->job_id) }}'">
                                                    {{ $isJobDrafted ? __('web.job_details.edit_draft') : __('web.job_details.apply_for_job') }}
                                                </button>
                                            @endif
                                        @else
                                            <button
                                                class="border border-gray-300 bg-transparent">{{ __('web.job_details.already_applied') }}</button>
                                        @endif
                                    </div>
                                </div>
                            @endrole
                        @else
                            @if ($isActive && !$job->is_suspended && \Carbon\Carbon::today()->toDateString() < $job->job_expiry_date->toDateString())
                                <div class="hero-desc flex flex-wrap">
                                    <div class="desc flex me-4 pe-2">
                                        <button class="border border-gray-300 bg-transparent"
                                            onclick="window.location='{{ route('candidate.register') }}'">{{ __('web.job_details.register_to_apply') }}
                                        </button>
                                    </div>
                                    <div class="desc flex me-4 pe-2">
                                        <button class="border border-gray-300 bg-transparent"
                                            onclick="window.location='{{ route('candidate.') }}'">
                                            {{ __('web.job_details.apply_for_job') }}
                                        </button>
                                    </div>
                                </div>
                            @endif
                        @endauth
                    </div>
                </div>
            </div>
        </section>
        <!-- end hero section -->

        <!-- start job-details section -->
        <section class="job-details-section py-60 mb-sm-4">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mx-auto px-4 mx-auto px-4 mx-auto">
                <div class="job- bg-white shadow rounded -lg overflow-hidden">
                    <div class="flex flex-wrap">
                        @if ($job->is_suspended || !$isActive)
                            <div class="md:w-full flex-1 sm-12">
                                <div class="px-4 py-3 rounded-md border border border-gray-300 -gray-300 mb-4 p-4 rounded -md mb-4 warning text-yellow-600 bg-transparent" role="rounded-md p-4">
                                    {{ __('web.job_details.job_is') }}
                                    <strong> {{ \App\Models\Job::STATUS[$job->status] }}.</strong>
                                </div>
                            </div>
                        @endif
                        @if (Session::has('warning'))
                            <div class="md:w-full flex-1 sm-12">
                                <div class="px-4 py-3 rounded-md border border border-gray-300 -gray-300 mb-4 p-4 rounded -md mb-4 warning" role="rounded-md p-4">
                                    {{ Session::get('warning') }}
                                    <a href="{{ route('candidate.', ['section' => 'resume']) }}"
                                        class="p-4 rounded -md mb-4 link ml-2">{{ __('web.job_details.click_here') }}</a>
                                    {{ __('web.job_details.to_upload_resume') }}
                                    .
                                </div>
                            </div>
                        @endif
                        <div class="flex-1 lg-8">
                            <div class="Job Description mb-lg-5 mb-4">
                                <h5 class="fs-18 text-gray-600 mb-4">@lang('web.web_jobs.job_description')</h5>
                                @if ($job->description)
                                    <p class="job-description">
                                        {{ nl2br($job->description) }}
                                    </p>
                                @else
                                    <p class="fs-16 text-gray mb-5 pb-lg-4">{{ __('messages.common.n/a') }}</p>
                                @endif
                            </div>
                            <div class="key-responsibilities mb-lg-5 mb-4">
                                <h5 class="fs-18 text-gray-600 mb-4">@lang('web.web_jobs.key_responsibilities')</h5>
                                @if ($job->key_responsibilities)
                                    <div class="key-responsibilities">
                                        {!! nl2br($job->key_responsibilities) !!}
                                    </div>
                                @else
                                    <p class="fs-16 text-gray mb-5 pb-lg-4">{{ __('messages.common.n/a') }}</p>
                                @endif

                            </div>
                            <div class="skill-experience mb-lg-5 mb-4">
                                <h5 class="fs-18 text-gray-600 mb-4">@lang('web.web_jobs.Skill_Experience')</h5>
                                @if (!empty($skills))
                                <ul>
                                    @foreach ($skills as $id => $skill)
                                    <li>{{ $skill }}</li>
                                    @endforeach
                                </ul>
                                @else
                                    <p class="fs-16 text-gray mb-5 pb-lg-4">{{ __('messages.common.n/a') }}</p>
                                @endif
                            </div>
                            <div class="transition duration-150 ease-in-out flex-1">
                                <p class="fs-14 text-gray-600 mb-2">@lang('web.web_jobs.share_this_job')</p>
                                <div class="social-media flex">
                                    <div class="social-media-experiment flex">
                                        <div>
                                            <a href="https://www.facebook.com/sharer/sharer.php?u={{ rawurlencode(url()->current()) }}"
                                               target="_blank" class="me-2 flex justify-center items-center">
                                                <x-icons.facebook class="w-5 h-5 text-white" />
                                            </a>
                                        </div>
                                        <div>
                                            <a href="https://www.linkedin.com/shareArticle/?mini=true&url={{ rawurlencode(url()->current()) }}"
                                               target="_blank" class="me-2 flex justify-center items-center">
                                                <x-icons.linkedin class="w-5 h-5 text-white" />
                                            </a>
                                        </div>
                                        <div>
                                            <a href="https://www.twitter.com/share?url={{ rawurlencode(url()->current()) }}"
                                               target="_blank" class="me-2 flex justify-center items-center">
                                                <x-icons.twitter class="w-5 h-5 text-white" />
                                            </a>
                                        </div>
                                        <div>
                                            <a href="https://plus.google.com/share?url={{ rawurlencode(url()->current()) }}"
                                               target="_blank" class="me-2 flex justify-center items-center">
                            <div class="share-this-job mb-lg-0 mb-40">
                                <h5 class="fs-18 text-gray-600 mb-4">@lang('web.apply_for_job.share_this_job'):</h5>
                                <div class="social-media flex flex-wrap">
                                    <a href="https://www.facebook.com/sharer/sharer.php?u={{ rawurlencode(url()->current()) }}"
                                        target="_blank" class="social-media-item bg-indigo-600 -600 text-decoration-none me-2 mb-2">
                                        <x-icons.facebook class="w-5 h-5 text-white" />
                                    </a>
                                    <a href="https://www.linkedin.com/shareArticle?mini=true&url={{ rawurlencode(url()->current()) }}&title={{ rawurlencode($job->job_title) }}"
                                        target="_blank" class="social-media-item bg-indigo-600 -600 text-decoration-none me-2 mb-2">
                                        <x-icons.linkedin class="w-5 h-5 text-white" />
                                    </a>
                                    <a href="https://www.twitter.com/share?url={{ rawurlencode(url()->current()) }}&text={{ rawurlencode($job->job_title) }}"
                                        target="_blank" class="social-media-item bg-indigo-600 -600 text-decoration-none me-2 mb-2">
                                        <x-icons.twitter class="w-5 h-5 text-white" />
                                    </a>
                                    <a href="https://plus.google.com/share?url={{ rawurlencode(url()->current()) }}"
                                        target="_blank" class="social-media-item bg-indigo-600 -600 text-decoration-none me-2 mb-2">
                                        <x-icons.google-plus class="w-5 h-5 text-white" />
                                    </a>
                                    <a href="https://pinterest.com/pin/create/button/?url={{ rawurlencode(url()->current()) }}"
                                        target="_blank" class="social-media-item bg-indigo-600 -600 text-decoration-none me-2 mb-2">
                                        <x-icons.pinterest class="w-5 h-5 text-white" />
                                    </a>
                                </div>
                            </div>
                        </div>
                        <div class="flex-1 lg-4">
                            <div class="job-desc-right br-10 px-40 bg-gray-100 mb-40">
                                <div class="pb-2">
                                    <h5 class="fs-18 text-gray-900 mb-4">@lang('web.web_jobs.job_overview')</h5>
                                    <div class="desc-box flex justify-between mb-4">
                                        <div class="desc flex">
                                            <div class="me-2 w-20">
                                                <x-icons.calendar class="w-full h-full" />
                                            </div>

                                            <p class="fs-14 text-gray-600 mb-0">@lang('web.job_details.date_posted'):</p>
                                        </div>
                                        <p class="fs-14 text-gray text-end mb-0">
                                            {{ \Carbon\Carbon::parse($job->created_at)->translatedFormat('jS M, Y') }}</p>
                                    </div>
                                    <div class="desc-box flex justify-between mb-4">
                                        <div class="desc flex">
                                            <div class="me-2 w-20">
                                                <x-icons.briefcase class="w-full h-full" />
                                            </div>
                                            <p class="fs-14 text-gray-600 mb-0">@lang('web.web_jobs.expiration_date'):</p>
                                        </div>
                                        <p class="fs-14 text-gray text-end mb-0">
                                            {{ \Carbon\Carbon::parse($job->job_expiry_date)->translatedFormat('jS M, Y') }}
                                        </p>
                                    </div>
                                    <div class="desc-box flex justify-between mb-4">
                                        <div class="desc flex">
                                            <div class="me-2 w-20">
                                                <x-icons.location-detailed class="w-full h-full" />
                                            </div>
                                            <p class="fs-14 text-gray-600 mb-0">@lang('web.common.location'):</p>
                                        </div>
                                        <p class="fs-14 text-gray text-end mb-0">
                                            @if (!empty($job->city_id))
                                                {{ $job->city_name }} ,
                                            @endif
                                            @if (!empty($job->state_id))
                                                {{ $job->state_name }},
                                            @endif
                                            @if (!empty($job->country_id))
                                                {{ $job->country_name }}
                                            @endif
                                            @if (empty($job->country_id))
                                                {{ __('web.job_details.location_information_not_available') }}
                                            @endif
                                        </p>
                                    </div>
                                    <div class="desc-box flex justify-between mb-4">
                                        <div class="desc flex">
                                            <div class="me-2 w-20">
                                                <x-icons.business-case class="w-full h-full" />
                                            </div>
                                            <p class="fs-14 text-gray-600 mb-0">@lang('messages.job.job_type'):</p>
                                        </div>
                                        <p class="fs-14 text-gray text-end mb-0">
                                            {{ $job->jobType ? html_entity_decode($job->jobType->name) : __('messages.common.n/a') }}
                                        </p>
                                    </div>
                                    @if ($job->jobShift)
                                        <div class="desc-box flex justify-between mb-4">
                                            <div class="desc flex">
                                                <div class="me-2 w-20">
                                                    <x-icons.clock class="w-full h-full" />
                                                </div>
                                                <p class="fs-14 text-gray-600 mb-0">@lang('messages.job.job_shift'):</p>
                                            </div>
                                            <p class="fs-14 text-gray text-end mb-0">
                                                {{ html_entity_decode($job->jobShift->shift) }}</p>
                                        </div>
                                    @endif
                                    <div class="desc-box flex justify-between mb-4">
                                        <div class="desc flex">
                                            <div class="me-2 w-20">
                                                <x-icons.functional-area class="w-full h-full" />
                                            </div>
                                            <p class="fs-14 text-gray-600 mb-0">@lang('messages.job.functional_area'):</p>
                                        </div>
                                        <p class="fs-14 text-gray text-end mb-0">
                                            {{ html_entity_decode($job->functionalArea->name) }}
                                        </p>
                                    </div>
                                    <div class="desc-box flex justify-between mb-4">
                                        <div class="desc flex">
                                            <div class="me-2 w-20">
                                                <x-icons.team-members class="w-full h-full" />
                                            </div>
                                            <p class="fs-14 text-gray-600 mb-0">@lang('messages.positions'):</p>
                                        </div>
                                        <p class="fs-14 text-gray text-end mb-0">
                                            {{ isset($job->position) ? $job->position : '0' }}</p>
                                    </div>
                                    <div class="desc-box flex justify-between mb-4">
                                        <div class="desc flex">
                                            <div class="me-2 w-20">
                                                <x-icons.experience class="w-full h-full" />
                                            </div>
                                            <p class="fs-14 text-gray-600 mb-0">@lang('messages.job_experience.job_experience'):</p>
                                        </div>
                                        <p class="fs-14 text-gray text-end mb-0">
                                            {{ isset($job->experience) ? $job->experience . ' ' . __('messages.candidate_profile.year') : 'No experience' }}
                                        </p>
                                    </div>
                                    <div class="desc-box flex justify-between mb-4">
                                        <div class="desc flex">
                                            <div class="me-2 w-20">
                                                <x-icons.salary class="w-full h-full" />
                                            </div>
                                            <p class="fs-14 text-gray-600 mb-0">@lang('messages.job.salary_period'):</p>
                                        </div>
                                        <p class="fs-14 text-gray text-end mb-0">
                                            {{ $job->salaryPeriod->period }}
                                        </p>
                                    </div>
                                    <div class="desc-box flex justify-between mb-4">
                                        <div class="desc flex">
                                            <div class="me-2 w-20">
                                                <x-icons.freelance class="w-full h-full" />
                                            </div>
                                            <p class="fs-14 text-gray-600 mb-0">@lang('messages.job.is_freelance'):</p>
                                        </div>
                                        <p class="fs-14 text-gray text-end mb-0">
                                            {{ $job->is_freelance == 1 ? __('messages.common.yes') : __('messages.common.no') }}
                                        </p>
                                    </div>
                                </div>
                                <div class="desc-box">
                                    <h5 class="fs-18 text-gray-600 mb-4">@lang('web.job_details.job_skills')</h5>
                                    <div class="flex flex-wrap gap-3">
                                        @if ($job->jobsSkill->isNotEmpty())
                                        <ul>
                                            @foreach ($job->jobsSkill->pluck('name') as $key => $value)
                                                <li
                                                    class="fs-14 text-gray py-2 {{ $loop->last ?"' : 'me-4' }}">
                                                    {{ html_entity_decode($value) }}</li>
                                            @endforeach
                                        </ul>
                                        @else
                                            <p class="fs-14 text-gray bg-white py-2 br-gray px-3">
                                                {{ __('messages.common.n/a') }}</p>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            <div class="company-overview br-10 px-40 bg-gray-100">
                                <h5 class="fs-18 text-gray-600 mb-4">@lang('web.job_details.company_overview')</h5>
                                <div class="company-profile flex items-center mb-4">
                                    <div class="profile">
                                        <img src="{{ $job->$company->company_url }}" class="w-full h-full object-fit-cover"
                                            alt="company-details" />
                                    </div>
                                    <div class="desc ms-4">
                                        <p class="fs-18 text-gray-600 mb-0">
                                            {{ html_entity_decode($job->$company->$user->first_name) }}</p>
                                        <a hred="{{ route('front.', $job->$company->unique_id) }}"
                                            class="fs-14 text-indigo-600 -600">@lang('web.web_jobs.view_company_profile')</a>
                                    </div>
                                </div>
                                <div class="desc-box flex justify-between mb-4">
                                    <p class="fs-14 text-gray-600 mb-0">@lang('web.web_jobs.founded_in'):</p>
                                    <p class="fs-14 text-gray text-end mb-0">{{ $job->$company->established_in }}</p>
                                </div>
                                @if ($job->$company->$user->phone)
                                    <div class="desc-box flex justify-between mb-2">
                                        <p class="fs-14 text-gray-600 mb-0">@lang('web.web_jobs.phone'):</p>
                                        <p class="fs-14 text-gray text-end mb-0">{{ $job->$company->$user->phone }}</p>
                                    </div>
                                @endif
                                <div class="desc-box flex justify-between mb-4">
                                    <p class="fs-14 text-gray-600 mb-0">@lang('web.common.location'):</p>
                                    @if (!empty($job->$company->location))
                                        <p class="fs-14 text-gray text-end mb-0">{{ $job->$company->location }}</p>
                                    @else
                                        <p class="fs-14 text-gray text-end mb-0">
                                            {{ __('web.job_details.location_information_not_available') }}
                                        </p>
                                    @endif
                                </div>
                                <a href="{{ route('front.', $job->$company->unique_id) }}"
                                    class="border border-gray-300 bg-transparent">
                                    {{ __('web.companies_menu.opened_jobs') }} : {{ $jobsCount ? $jobsCount : 0 }}
                                </a>
                                @if ($job->$company->website)
                                    <div class="bg-white shadow rounded -lg overflow-hidden desc mt-3">
                                        <div class="desc flex mt-2">
                                            <a href="{{ $job->$company->website }}"
                                                class="jobs-position fs-14 text-indigo-600-600"
                                                target="_blank">{{ $job->$company->website }}</a>
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>
                        @if (count($getRelatedJobs) > 0)
                            <div class="flex flex-wrap job-details-related-jobs our-latest-jobs">
                                <h5 class="fs-18 text-gray-600 mt-5 mb-4 pb-2">
                                    @lang('web.job_details.related_jobs')
                                </h5>
                                @foreach ($getRelatedJobs as $relatedJob)
                                    @if ($relatedJob->status != \App\Models\Job::STATUS_DRAFT)
                                        <div class="lg:w-4/12 px-2 flex-1 md-6 px-xl-3 mb-40">
                                            <div class="bg-white shadow rounded -lg overflow-hidden py-30">
                                                @if (Str::length($relatedJob['job_title']) < 35)
                                                    <a href="{{ route('front.', $relatedJob['job_id']) }}"
                                                        class="text-gray-600 primary-link-hover">
                                                        <h5 class="bg-white shadow rounded -lg overflow-hidden title fs-20 mb-2">
                                                            {{ html_entity_decode($relatedJob['job_title']) }}
                                                        </h5>
                                                    </a>
                                                @else
                                                    <a href="{{ route('front.', $relatedJob['job_id']) }}"
                                                        data-toggle="tooltip" data-placement="bottom" class="hover-flex-1 px-4or"
                                                        title="{{ html_entity_decode($relatedJob['job_title']) }}">
                                                        <h5 class="bg-white shadow rounded -lg overflow-hidden title fs-20 mb-2">
                                                            {{ Str::limit(html_entity_decode($relatedJob['job_title']), 30, '...') }}
                                                        </h5>
                                                    </a>
                                                @endif
                                                <div class="mt-2 flex flex-wrap items-center">
                                                    @if (isset($relatedJob->jobShift->shift))
                                                        <span class="text text-indigo-600 -600 fs-12 mb-0 me-3 related-jobs">
                                                            {{ $relatedJob->jobShift->shift }}
                                                        </span>
                                                    @endif
                                                </div>
                                                <div class="flex justify-between items-center">
                                                    <div class="flex items-center">
                                                        <div class="me-4">
                                                            <img src="{{ $relatedJob->$company->company_url }}"
                                                                class="bg-white shadow rounded -lg overflow-hidden img" alt="..." />
                                                        </div>
                                                        <div class="">
                                                            <div class="bg-white shadow rounded -lg overflow-hidden body p-0">
                                                                <a
                                                                    href="{{ route('front.', $relatedJob->$company->unique_id) }}">
                                                                    <p class="bg-white shadow rounded -lg overflow-hidden title fs-18 mb-0 text-indigo-600 -600">
                                                                        {{ $relatedJob->$company->$user->first_name }}</p>
                                                                </a>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="icon relative pe-0">
                                                        <i class="text-indigo-600 -600 fa-solid fa-bookmark"></i>
                                                    </div>
                                                </div>
                                                <div
                                                    class="bg-white shadow rounded -lg overflow-hidden desc flex flex- flex-1 justify-between h-full mt-4">
                                                    <div class="desc">
                                                        <div class="flex mb-1">
                                                            <div class="me-3 w-20">
                                                                <x-icons.briefcase class="w-full" />
                                                            </div>
                                                            <p class="fs-14 text-gray mb-0">
                                                                {{ !empty($job->jobCategory->name) ? $job->jobCategory->name : '' }}
                                                            </p>
                                                        </div>
                                                        <div class="flex mb-2">
                                                            <div class="me-3 w-20">
                                                                <x-icons.location class="w-full" />
                                                            </div>

                                                            <p class="fs-14 text-gray mb-0">
                                                                {{ !empty($job->full_location) ? $job->full_location : 'Location Info. not available.' }}
                                                            </p>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endif
                                @endforeach
                                @if ($getRelatedJobs->count() > 0)
                                    <div class="flex flex-wrap justify-center">
                                        <div class="flex-1 -8 text-center">
                                            <a href="{{ route('front.', ['categories' => $relatedJob->jobCategory->id]) }}"
                                                class="border border-gray-300 bg-transparent">
                                                @lang('web.common.show_all')</a>
                                        </div>
                                    </div>
                                @endif
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </section>
        <!-- end job-details section -->
    </div>
    @role('Candidate')
        @include('front_web_template.jobs.email_to_friend')
        @include('front_web_template.jobs.report_job_modal')
    @endrole
    {{ Form::hidden('isJobAddedToFavourite', $isJobAddedToFavourite, ['id' => 'isJobAddedToFavourite']) }}
    {{ Form::hidden('removeFromFavorite', __('web.job_details.remove_from_favorite'), ['id' => 'removeFromFavorite']) }}
    {{ Form::hidden('addToFavorites', __('web.job_details.add_to_favorite'), ['id' => 'addToFavorites']) }}
@endsection
{{-- @section('page_scripts') --}}
{{--  --}}
{{-- @endsection --}}

@push('scripts')
    @vite('resources/js/pages/job_details.js')
@endpush
