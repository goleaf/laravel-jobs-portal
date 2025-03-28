@extends('front_web_template.layouts.app')
@section('title')
    {{ __('web.job_details.job_details') }}
@endsection
{{-- @section('page_css') --}}
{{--    <link href="{{asset('front_web/scss/job-details.css')}}" rel="stylesheet" type="text/css"> --}}
{{-- @endsection --}}
@section('content')
    <div class="job-details-page">
        <!-- start hero section -->
        <section class="hero-section position-relative bg-gradient pt-15 pb-40">
            <div class="container">
                <div class="row align-items-center justify-content-center">
                    <div class="col-12">
                        <div class="d-flex align-items-md-center">
                            <div class="me-4">
                                <div class="hero-img">
                                    <img src="{{ $job->company->company_url }}"
                                        class="w-100 h-100 rounded-circle object-fit-cover" alt="company-details" />
                                </div>
                            </div>
                            <div class="">
                                <div class="hero-content">
                                    <h4 class="text-secondary lh-base mb-2">
                                        {{ html_entity_decode(Str::limit($job->job_title, 50, '...')) }}
                                        @role('Candidate')
                                            @if (!$isJobApplicationRejected)
                                                <button class="btn p-0 ms-5"
                                                    data-favorite-user-id="{{ getLoggedInUserId() !== null ? getLoggedInUserId() : null }}"
                                                    data-favorite-job-id="{{ $job->id }}" id="addToFavourite">
                                                    <span id="favorite">
                                                        <i
                                                            class=" {{ $isJobAddedToFavourite ? 'fa-solid fa-bookmark featured' : 'fa-regular fa-bookmark' }}  text-primary fs-18"></i>
                                                    </span>
                                                </button>
                                            @endif
                                        @endrole
                                    </h4>
                                    <div class="hero-desc d-md-flex">
                                        <div class="desc d-flex me-4 pe-2">
                                            <div class="me-3 w-20">
                                                <x-icons.briefcase class="w-100" />
                                            </div>
                                            <p class="fs-14 text-gray mb-0">
                                                {{ html_entity_decode($job->jobCategory->name) }}
                                            </p>
                                        </div>
                                        <div class="desc d-flex me-4 pe-2">
                                            <div class="me-3 w-20">
                                                <x-icons.clock class="w-100" />
                                            </div>
                                            <p class="fs-14 text-gray mb-0">{{ $job->created_at->diffForHumans() }}</p>
                                        </div>
                                        @if ($job->hide_salary == '0')
                                            <div class="desc d-flex">
                                                <div class="me-3 w-20">
                                                    <x-icons.money class="w-100" />
                                                </div>
                                                <a href="#"
                                                    class="fs-14 text-gray">{{ $job->currency->currency_icon }}
                                                    {{ numberFormatShort($job->salary_from) . ' - ' . numberFormatShort($job->salary_to) }}</a>
                                            </div>
                                        @endif
                                    </div>
                                    @if (count($job->jobsTag) > 0)
                                        <div class="hero-desc d-flex flex-wrap">
                                            @foreach ($job->jobsTag->pluck('name') as $value)
                                            <div class="desc d-flex {{ $loop->last ? '' : 'me-2 pe-2' }}">
                                                <span class="tag-badge"
                                                    style="background: rgba(25,103,210,.15);color: #1967d2!important;font-size: 12px!important;line-height: 15px;padding: 5px 20px;border-radius: 50px;margin-top: 10px;">
                                                    {{ $value }}</span>
                                                    <span class="tag-badge"
                                                    style="background: rgba(25,103,210,.15);color: #1967d2!important;font-size: 12px!important;line-height: 15px;padding: 5px 20px;border-radius: 50px;margin-top: 10px;">
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
                <div class="d-flex gap-3 mt-sm-5 mt-4 flex-wrap">
                    {{-- <button class="btn btn-light">Register to Apply</button>
                    <button class="btn btn-primary">Apply for Job</button> --}}
                    <div class="row align-items-lg-center">
                        @auth
                            @role('Candidate')
                                <div class="hero-desc d-flex flex-wrap">
                                    <div class="desc me-2 pe-2 mb-sm-0 mb-2">
                                        <button type="button" class="btn btn-primary btn-primary-register emailJobToFriend" data-bs-toggle="modal"
                                            data-bs-target="#emailJobToFriendModal">
                                            {{ __('web.job_details.email_to_friend') }}
                                        </button>
                                    </div>
                                    <div class="desc me-2 pe-2 mb-sm-0 mb-2">
                                        @if ($isJobReportedAsAbuse)
                                            <button type="button" class="btn btn-primary btn-primary-register reportJobAbuse" disabled
                                                data-bs-toggle="modal" data-bs-target="#reportJobAbuseModal">
                                                {{ __('messages.candidate.already_reported') }}
                                            </button>
                                        @else
                                            <button type="button" class="btn btn-primary btn-primary-register reportJobAbuse" data-bs-toggle="modal"
                                                data-bs-target="#reportJobAbuseModal">
                                                {{ __('web.job_details.report_abuse') }}
                                            </button>
                                        @endif
                                    </div>
                                    <div class="desc me-2 pe-2 mb-sm-0 mb-2">
                                        @if (!$isApplied && !$isJobApplicationRejected && !$isJobApplicationCompleted && !$isJobApplicationShortlisted)
                                            @if ($isActive && !$job->is_suspended && \Carbon\Carbon::today()->toDateString() < $job->job_expiry_date->toDateString())
                                                <button class="btn {{ $isJobDrafted ? 'btn-primary' : 'btn-dark' }} "
                                                    onclick="window.location='{{ route('show.apply-job-form', $job->job_id) }}'">
                                                    {{ $isJobDrafted ? __('web.job_details.edit_draft') : __('web.job_details.apply_for_job') }}
                                                </button>
                                            @endif
                                        @else
                                            <button
                                                class="btn btn-primary btn-primary-register ml-2">{{ __('web.job_details.already_applied') }}</button>
                                        @endif
                                    </div>
                                </div>
                            @endrole
                        @else
                            @if ($isActive && !$job->is_suspended && \Carbon\Carbon::today()->toDateString() < $job->job_expiry_date->toDateString())
                                <div class="hero-desc d-flex flex-wrap">
                                    <div class="desc d-flex me-4 pe-2">
                                        <button class="btn btn-primary btn-primary-register mb-3"
                                            onclick="window.location='{{ route('candidate.register') }}'">{{ __('web.job_details.register_to_apply') }}
                                        </button>
                                    </div>
                                    <div class="desc d-flex me-4 pe-2">
                                        <button class="btn btn-primary btn-primary-register mb-3"
                                            onclick="window.location='{{ route('front.candidate.login') }}'">
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
            <div class="container">
                <div class="job-card">
                    <div class="row">
                        @if ($job->is_suspended || !$isActive)
                            <div class="col-md-12 col-sm-12">
                                <div class="alert alert-warning text-warning bg-transparent" role="alert">
                                    {{ __('web.job_details.job_is') }}
                                    <strong> {{ \App\Models\Job::STATUS[$job->status] }}.</strong>
                                </div>
                            </div>
                        @endif
                        @if (Session::has('warning'))
                            <div class="col-md-12 col-sm-12">
                                <div class="alert alert-warning" role="alert">
                                    {{ Session::get('warning') }}
                                    <a href="{{ route('candidate.profile', ['section' => 'resume']) }}"
                                        class="alert-link ml-2 ">{{ __('web.job_details.click_here') }}</a>
                                    {{ __('web.job_details.to_upload_resume') }}
                                    .
                                </div>
                            </div>
                        @endif
                        <div class="col-lg-8">
                            <div class="Job Description mb-lg-5 mb-4">
                                <h5 class="fs-18 text-secondary mb-4">@lang('web.web_jobs.job_description')</h5>
                                @if ($job->description)
                                    <p class="job-description">
                                        {!! nl2br($job->description) !!}
                                    </p>
                                @else
                                    <p class="fs-16 text-gray mb-5 pb-lg-4">{{ __('messages.common.n/a') }}</p>
                                @endif
                            </div>
                            <div class="key-responsibilities mb-lg-5 mb-4">
                                <h5 class="fs-18 text-secondary mb-4">@lang('web.web_jobs.key_responsibilities')</h5>
                                @if ($job->key_responsibilities)
                                    <div class="key-responsibilities">
                                        {!! nl2br($job->key_responsibilities) !!}
                                    </div>
                                @else
                                    <p class="fs-16 text-gray mb-5 pb-lg-4">{{ __('messages.common.n/a') }}</p>
                                @endif

                            </div>
                            <div class="skill-experience mb-lg-5 mb-4">
                                <h5 class="fs-18 text-secondary mb-4">@lang('web.web_jobs.Skill_Experience')</h5>
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
                            <div class="share-this-job mb-lg-0 mb-40">
                                <h5 class="fs-18 text-secondary mb-4">@lang('web.apply_for_job.share_this_job'):</h5>
                                <div class="icon-box d-flex">
                                    <div class="social-icon me-sm-4 me-3 d-flex align-items-center justify-content-center">
                                        <a href="{{ $url['facebook'] }}" target="_blank" class="facebook d-flex"
                                            title="@lang('web.web_jobs.facebook')">
                                            <div class="icon d-flex">
                                                <i class="fa-brands fa-facebook-f text-white"></i>
                                            </div>
                                        </a>
                                    </div>
                                    <div class="social-icon me-sm-4 me-3 d-flex align-items-center justify-content-center">
                                        <a href="https://www.linkedin.com/shareArticle/?url={{ rawurlencode(URL::to('/job-details/' . $job->job_id)) }}"
                                            title="@lang('web.web_jobs.linkedin')" target="_blank" class="linkedin d-flex">
                                            <div class="icon d-flex">
                                                <i class="fa-brands fa-linkedin-in text-white"></i>
                                            </div>
                                        </a>
                                    </div>
                                    <div class="social-icon me-sm-4 me-3 d-flex align-items-center justify-content-center">
                                        <a href="{{ $url['twitter'] }}" target="_blank" class="twitter d-flex"
                                            title="@lang('web.web_jobs.twitter')">
                                            <div class="icon d-flex">
                                                <i class="fa-brands fa-twitter text-white"></i>
                                            </div>
                                        </a>
                                    </div>
                                    <div class="social-icon me-sm-4 me-3 d-flex align-items-center justify-content-center">
                                        <a href="{{ $url['gmail'] }}" target="_blank" class="google d-flex"
                                            title="@lang('web.web_jobs.google')">
                                            <div class="icon d-flex">
                                                <i class="fa-brands fa-google-plus-g text-white"></i>
                                            </div>
                                        </a>
                                    </div>
                                    <div class="social-icon me-sm-4 me-3 d-flex align-items-center justify-content-center">
                                        <a href="{{ $url['pinterest'] }}" target="_blank" class="pinterest d-flex"
                                            title="@lang('web.web_jobs.pinterest')">
                                            <div class="icon d-flex">
                                                <i class="fa-brands fa-pinterest-p text-white"></i>
                                            </div>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-4">
                            <div class="job-desc-right br-10 px-40 bg-light mb-40">
                                <div class="pb-2">
                                    <h5 class="fs-18 text-dark mb-4">@lang('web.web_jobs.job_overview')</h5>
                                    <div class="desc-box d-flex justify-content-between mb-4">
                                        <div class="desc d-flex">
                                            <div class="me-2 w-20">
                                                <x-icons.calendar class="w-full h-full" />
                                            </div>

                                            <p class="fs-14 text-secondary mb-0">@lang('web.job_details.date_posted'):</p>
                                        </div>
                                        <p class="fs-14 text-gray text-end mb-0">
                                            {{ \Carbon\Carbon::parse($job->created_at)->translatedFormat('jS M, Y') }}</p>
                                    </div>
                                    <div class="desc-box d-flex justify-content-between mb-4">
                                        <div class="desc d-flex">
                                            <div class="me-2 w-20">
                                                <x-icons.briefcase class="w-full h-full" />
                                            </div>
                                            <p class="fs-14 text-secondary mb-0">@lang('web.web_jobs.expiration_date'):</p>
                                        </div>
                                        <p class="fs-14 text-gray text-end mb-0">
                                            {{ \Carbon\Carbon::parse($job->job_expiry_date)->translatedFormat('jS M, Y') }}
                                        </p>
                                    </div>
                                    <div class="desc-box d-flex justify-content-between mb-4">
                                        <div class="desc d-flex">
                                            <div class="me-2 w-20">
                                                <x-icons.location-detailed class="w-full h-full" />
                                            </div>
                                            <p class="fs-14 text-secondary mb-0">@lang('web.common.location'):</p>
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
                                    <div class="desc-box d-flex justify-content-between mb-4">
                                        <div class="desc d-flex">
                                            <div class="me-2 w-20">
                                                <x-icons.business-case class="w-full h-full" />
                                            </div>
                                            <p class="fs-14 text-secondary mb-0">@lang('messages.job.job_type'):</p>
                                        </div>
                                        <p class="fs-14 text-gray text-end mb-0">
                                            {{ $job->jobType ? html_entity_decode($job->jobType->name) : __('messages.common.n/a') }}
                                        </p>
                                    </div>
                                    @if ($job->jobShift)
                                        <div class="desc-box d-flex justify-content-between mb-4">
                                            <div class="desc d-flex">
                                                <div class="me-2 w-20">
                                                    <x-icons.clock class="w-full h-full" />
                                                </div>
                                                <p class="fs-14 text-secondary mb-0">@lang('messages.job.job_shift'):</p>
                                            </div>
                                            <p class="fs-14 text-gray text-end mb-0">
                                                {{ html_entity_decode($job->jobShift->shift) }}</p>
                                        </div>
                                    @endif
                                    <div class="desc-box d-flex justify-content-between mb-4">
                                        <div class="desc d-flex">
                                            <div class="me-2 w-20">
                                                <x-icons.functional-area class="w-full h-full" />
                                            </div>
                                            <p class="fs-14 text-secondary mb-0">@lang('messages.job.functional_area'):</p>
                                        </div>
                                        <p class="fs-14 text-gray text-end mb-0">
                                            {{ html_entity_decode($job->functionalArea->name) }}
                                        </p>
                                    </div>
                                    <div class="desc-box d-flex justify-content-between mb-4">
                                        <div class="desc d-flex">
                                            <div class="me-2 w-20">
                                                <x-icons.team-members class="w-full h-full" />
                                            </div>
                                            <p class="fs-14 text-secondary mb-0">@lang('messages.positions'):</p>
                                        </div>
                                        <p class="fs-14 text-gray text-end mb-0">
                                            {{ isset($job->position) ? $job->position : '0' }}</p>
                                    </div>
                                    <div class="desc-box d-flex justify-content-between mb-4">
                                        <div class="desc d-flex">
                                            <div class="me-2 w-20">
                                                <x-icons.experience class="w-full h-full" />
                                            </div>
                                            <p class="fs-14 text-secondary mb-0">@lang('messages.job_experience.job_experience'):</p>
                                        </div>
                                        <p class="fs-14 text-gray text-end mb-0">
                                            {{ isset($job->experience) ? $job->experience . ' ' . __('messages.candidate_profile.year') : 'No experience' }}
                                        </p>
                                    </div>
                                    <div class="desc-box d-flex justify-content-between mb-4">
                                        <div class="desc d-flex">
                                            <div class="me-2 w-20">
                                                <x-icons.salary class="w-full h-full" />
                                            </div>
                                            <p class="fs-14 text-secondary mb-0">@lang('messages.job.salary_period'):</p>
                                        </div>
                                        <p class="fs-14 text-gray text-end mb-0">
                                            {{ $job->salaryPeriod->period }}
                                        </p>
                                    </div>
                                    <div class="desc-box d-flex justify-content-between mb-4">
                                        <div class="desc d-flex">
                                            <div class="me-2 w-20">
                                                <x-icons.freelance class="w-full h-full" />
                                            </div>
                                            <p class="fs-14 text-secondary mb-0">@lang('messages.job.is_freelance'):</p>
                                        </div>
                                        <p class="fs-14 text-gray text-end mb-0">
                                            {{ $job->is_freelance == 1 ? __('messages.common.yes') : __('messages.common.no') }}
                                        </p>
                                    </div>
                                </div>
                                <div class="desc-box">
                                    <h5 class="fs-18 text-secondary mb-4">@lang('web.job_details.job_skills')</h5>
                                    <div class="d-flex flex-wrap gap-3">
                                        @if ($job->jobsSkill->isNotEmpty())
                                        <ul>
                                            @foreach ($job->jobsSkill->pluck('name') as $key => $value)
                                                <li
                                                    class="fs-14 text-gray py-2 {{ $loop->last ? '' : 'me-4' }} ">
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
                            <div class="company-overview br-10 px-40 bg-light">
                                <h5 class="fs-18 text-secondary mb-4">@lang('web.job_details.company_overview')</h5>
                                <div class="company-profile d-flex align-items-center mb-4">
                                    <div class="profile">
                                        <img src="{{ $job->company->company_url }}" class="w-100 h-100 object-fit-cover"
                                            alt="company-details" />
                                    </div>
                                    <div class="desc ms-4">
                                        <p class="fs-18 text-secondary mb-0">
                                            {{ html_entity_decode($job->company->user->first_name) }}</p>
                                        <a hred="{{ route('front.company.details', $job->company->unique_id) }}"
                                            class="fs-14 text-primary">@lang('web.web_jobs.view_company_profile')</a>
                                    </div>
                                </div>
                                <div class="desc-box d-flex justify-content-between mb-4">
                                    <p class="fs-14 text-secondary mb-0">@lang('web.web_jobs.founded_in'):</p>
                                    <p class="fs-14 text-gray text-end mb-0">{{ $job->company->established_in }}</p>
                                </div>
                                @if ($job->company->user->phone)
                                    <div class="desc-box d-flex justify-content-between mb-2">
                                        <p class="fs-14 text-secondary mb-0">@lang('web.web_jobs.phone'):</p>
                                        <p class="fs-14 text-gray text-end mb-0">{{ $job->company->user->phone }}</p>
                                    </div>
                                @endif
                                <div class="desc-box d-flex justify-content-between mb-4">
                                    <p class="fs-14 text-secondary mb-0">@lang('web.common.location'):</p>
                                    @if (!empty($job->company->location))
                                        <p class="fs-14 text-gray text-end mb-0">{{ $job->company->location }}</p>
                                    @else
                                        <p class="fs-14 text-gray text-end mb-0">
                                            {{ __('web.job_details.location_information_not_available') }}
                                        </p>
                                    @endif
                                </div>
                                <a href="{{ route('front.company.details', $job->company->unique_id) }}"
                                    class="jobs-position col-12 btn btn-light">
                                    {{ __('web.companies_menu.opened_jobs') }} : {{ $jobsCount ? $jobsCount : 0 }}
                                </a>
                                @if ($job->company->website)
                                    <div class="card-desc mt-3">
                                        <div class="desc  d-flex mt-2">
                                            <a href="{{ $job->company->website }}"
                                                class="jobs-position fs-14 text-primary"
                                                target="_blank">{{ $job->company->website }}</a>
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>
                        @if (count($getRelatedJobs) > 0)
                            <div class="row job-details-related-jobs our-latest-jobs">
                                <h5 class="fs-18 text-secondary mt-5 mb-4 pb-2">
                                    @lang('web.job_details.related_jobs')
                                </h5>
                                @foreach ($getRelatedJobs as $relatedJob)
                                    @if ($relatedJob->status != \App\Models\Job::STATUS_DRAFT)
                                        <div class="col-lg-4 col-md-6 px-xl-3 mb-40">
                                            <div class="card py-30">
                                                @if (Str::length($relatedJob['job_title']) < 35)
                                                    <a href="{{ route('front.job.details', $relatedJob['job_id']) }}"
                                                        class="text-secondary primary-link-hover">
                                                        <h5 class="card-title fs-20 mb-2">
                                                            {{ html_entity_decode($relatedJob['job_title']) }}
                                                        </h5>
                                                    </a>
                                                @else
                                                    <a href="{{ route('front.job.details', $relatedJob['job_id']) }}"
                                                        data-toggle="tooltip" data-placement="bottom" class="hover-color"
                                                        title="{{ html_entity_decode($relatedJob['job_title']) }}">
                                                        <h5 class="card-title fs-20 mb-2">
                                                            {{ Str::limit(html_entity_decode($relatedJob['job_title']), 30, '...') }}
                                                        </h5>
                                                    </a>
                                                @endif
                                                <div class="mt-2 d-flex flex-wrap align-items-center">
                                                    @if (isset($relatedJob->jobShift->shift))
                                                        <span class="text text-primary fs-12 mb-0 me-3 related-jobs">
                                                            {{ $relatedJob->jobShift->shift }}
                                                        </span>
                                                    @endif
                                                </div>
                                                <div class="d-flex justify-content-between align-items-center">
                                                    <div class="d-flex align-items-center">
                                                        <div class="me-4">
                                                            <img src="{{ $relatedJob->company->company_url }}"
                                                                class="card-img" alt="..." />
                                                        </div>
                                                        <div class="">
                                                            <div class="card-body p-0">
                                                                <a
                                                                    href="{{ route('front.company.details', $relatedJob->company->unique_id) }}">
                                                                    <p class="card-title fs-18 mb-0 text-primary">
                                                                        {{ $relatedJob->company->user->first_name }}</p>
                                                                </a>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="icon position-relative pe-0">
                                                        <i class="text-primary fa-solid fa-bookmark"></i>
                                                    </div>
                                                </div>
                                                <div
                                                    class="card-desc d-flex flex-column justify-content-between h-100 mt-4">
                                                    <div class="desc">
                                                        <div class="d-flex mb-1">
                                                            <div class="me-3 w-20">
                                                                <x-icons.briefcase class="w-100" />
                                                            </div>
                                                            <p class="fs-14 text-gray mb-0">
                                                                {{ !empty($job->jobCategory->name) ? $job->jobCategory->name : '' }}
                                                            </p>
                                                        </div>
                                                        <div class="d-flex mb-2">
                                                            <div class="me-3 w-20">
                                                                <x-icons.location class="w-100" />
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
                                    <div class="row justify-content-center">
                                        <div class="col-8 text-center">
                                            <a href="{{ route('front.search.jobs', ['categories' => $relatedJob->jobCategory->id]) }}"
                                                class="btn btn-primary btn-primary-register mb-40 mt-lg-4">
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
{{--    <script> --}}
{{-- let addJobFavouriteUrl = "{{ route('save.favourite.job') }}"; --}}
{{-- let reportAbuseUrl = "{{ route('report.job.abuse') }}"; --}}
{{-- let emailJobToFriend = "{{ route('email.job') }}"; --}}
{{--        let isJobAddedToFavourite = "{{ $isJobAddedToFavourite }}"; --}}
{{-- let removeFromFavorite = "{{ __('web.job_details.remove_from_favorite') }}"; --}}
{{-- let addToFavorites = "{{ __('web.job_details.add_to_favorite') }}"; --}}
{{--    </script> --}}
{{-- @endsection --}}
