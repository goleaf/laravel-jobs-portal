@extends('front_web.layouts.app')
@section('title')
    {{ __('web.job_details.job_details') }}
@endsection
{{-- @section('page_css') --}}
{{--    <link href="{{asset('front_web/scss/job-details.css')}}" rel="stylesheet" type="text/css"> --}}
{{-- @endsection --}}
@section('content')
    <div class="job-details-page">
        <!-- start hero section -->
        <section class="hero-section relative bg-color-light py-40">
            <div class="container mx-auto px-4 mx-auto">
                <div class="flex flex-wrap items-center justify-center">
                    <div class="flex-1 -12">
                        <div class="flex flex-wrap align-items-lg-center mb-3">
                            <div class="lg:w-1/12 px-2 col-sm-2 flex-1 -3">
                                <div class="job-profile-img mt-md-0 mt-3 pt-sm-1">
                                    <img src="{{ $job->company->company_url }}" alt="job_detail_logo" class="rounded">
                                </div>
                            </div>
                            <div class="col-sm-10 flex-1 -9">
                                <div class="hero-content ps-xl-0 ps-3 ">
                                    <h4 class="text-gray-600 mb-0">
                                        {{ html_entity_decode(Str::limit($job->job_title, 50, '...')) }}
                                        @role('Candidate')
                                            @if (!$isJobApplicationRejected)
                                                <button class="px-4 py-2 rounded font-medium transition-colors p-0 ms-5"
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
                                    <div class="hero-desc flex items-center flex-wrap">
                                        <div class="flex items-center me-4 pe-2">
                                            <i class="fa-solid fa-briefcase text-gray me-3 fs-18"></i>
                                            <p class="fs-14 text-gray mb-0">
                                                {{ html_entity_decode($job->jobCategory->name) }}</p>
                                        </div>
                                        <div class="desc flex items-center me-lg-4 me-2 pe-2">
                                            <i class="fa-solid fa-clock text-gray me-3 fs-18"></i>
                                            <p class="fs-14 text-gray mb-0">
                                                {{ $job->created_at->diffForHumans() }}</p>
                                        </div>
                                        @if ($job->hide_salary == '0')
                                            <div class="desc flex items-center me-lg-4 me-2 pe-2">
                                                <i class="fa-solid fa-money-bill text-gray me-3 fs-18"></i>
                                                <span class="text-gray mb-0">
                                                    {{ $job->currency->currency_icon }}
                                                    {{ numberFormatShort($job->salary_from) . ' - ' . numberFormatShort($job->salary_to) }}</span>
                                            </div>
                                        @endif
                                    </div>
                                    @if (count($job->jobsTag) > 0)
                                        <div class="hero-desc d-md-flex">
                                            @foreach ($job->jobsTag->pluck('name') as $value)
                                                <div class="desc flex {{ $loop->last ?"' : 'me-2 pe-2' }}">
                                                    <span class="tag-badge">
                                                        {{ $value }}</span>
                                                </div>
                                            @endforeach
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div class="flex flex-wrap align-items-lg-center">
                            @auth
                                @role('Candidate')
                                    <div class="hero-desc flex flex-wrap">
                                        <div class="desc flex me-4 pe-2">
                                            <button type="button" class="inline-flex items-center px-4 py-2 border border-gray-300 border-transparent text-sm font-medium rounded-md transition duration-150 ease-in-out px-4 py-2 rounded font-medium transition-colors -outline-success emailJobToFriend"
                                                data-bs-toggle="modal" data-bs-target="#emailJobToFriendModal">
                                                {{ __('web.job_details.email_to_friend') }}
                                            </button>
                                        </div>
                                        <div class="desc flex me-4 pe-2">
                                            @if ($isJobReportedAsAbuse)
                                                <button type="button" class="inline-flex items-center px-4 py-2 border border-gray-300 border-transparent text-sm font-medium rounded-md transition duration-150 ease-in-out px-4 py-2 rounded font-medium transition-colors -outline-danger reportJobAbuse" disabled
                                                    data-bs-toggle="modal" data-bs-target="#reportJobAbuseModal">
                                                    {{ __('messages.candidate.already_reported') }}
                                                </button>
                                            @else
                                                <button type="button" class="inline-flex items-center px-4 py-2 border border-gray-300 border-transparent text-sm font-medium rounded-md transition duration-150 ease-in-out px-4 py-2 rounded font-medium transition-colors -outline-danger reportJobAbuse"
                                                    data-bs-toggle="modal" data-bs-target="#reportJobAbuseModal">
                                                    {{ __('web.job_details.report_abuse') }}
                                                </button>
                                            @endif
                                        </div>
                                        <div class="desc flex me-4 pe-2">
                                            @if (!$isApplied && !$isJobApplicationRejected && !$isJobApplicationCompleted && !$isJobApplicationShortlisted)
                                                @if ($isActive && !$job->is_suspended && \Carbon\Carbon::today()->toDateString() < $job->job_expiry_date->toDateString())
                                                    <button
                                                        class="px-4 py-2 rounded font-medium transition-colors {{ $isJobDrafted ?"btn-outline-primary' : 'btn-outline-dark' }} "
                                                        onclick="window.location='{{ route('show.apply-job-form', $job->job_id) }}'">
                                                        {{ $isJobDrafted ? __('web.job_details.edit_draft') : __('web.job_details.apply_for_job') }}
                                                    </button>
                                                @endif
                                            @else
                                                <button
                                                    class="inline-flex items-center px-4 py-2 border border-gray-300 border-transparent text-sm font-medium rounded-md transition duration-150 ease-in-out px-4 py-2 rounded font-medium transition-colors -outline-primary ml-2">{{ __('web.job_details.already_applied') }}</button>
                                            @endif
                                        </div>
                                    </div>
                                @endrole
                            @else
                                @if ($isActive && !$job->is_suspended && \Carbon\Carbon::today()->toDateString() < $job->job_expiry_date->toDateString())
                                    <div class="hero-desc flex flex-wrap">
                                        {{-- <div class="desc flex me-4 pe-2">
                                            <button class="inline-flex items-center px-4 py-2 border border-gray-300 border-transparent text-sm font-medium rounded-md transition duration-150 ease-in-out px-4 py-2 rounded font-medium transition-colors -outline-dark mb-3"
                                                onclick="window.location='{{ route('candidate.register') }}'">{{ __('web.job_details.register_to_apply') }}
                                            </button>
                                        </div> --}}
                                        <div class="desc flex me-4 pe-2">
                                            <button class="inline-flex items-center px-4 py-2 border border-gray-300 border-transparent text-sm font-medium rounded-md transition duration-150 ease-in-out px-4 py-2 rounded font-medium transition-colors -outline-success mb-3"
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
            </div>
        </section>
        <!-- end hero section -->

        <!-- start job-details section -->
        <section class="job-details-section py-60">
         <div class="container mx-auto px-4 mx-auto">
             <div class="flex flex-wrap">
                 @if($job->is_suspended || !$isActive)
                     <div class="md:w-full flex-1 -sm-12">
                         <div class="px-4 py-3 rounded-md border border-gray-300 mb-4 p-4 rounded-md mb-4 -warning text-yellow-600 bg-transparent" role="alert">
                             {{ __('web.job_details.job_is') }}
                             <strong> {{\App\Models\Job::STATUS[$job->status]}}.</strong>
                         </div>
                     </div>
                 @endif
                 @if(Session::has('warning'))
                     <div class="md:w-full flex-1 -sm-12">
                         <div class="px-4 py-3 rounded-md border border-gray-300 mb-4 p-4 rounded-md mb-4 -warning" role="alert" data-turbo="false">
                             {{ Session::get('warning') }}
                             <a href="{{ route('candidate.profile',['section'=> 'resume']) }}"
                                class="p-4 rounded-md mb-4 -link ml-2">{{ __('web.job_details.click_here') }}</a> {{ __('web.job_details.to_upload_resume') }}
                             .
                         </div>
                     </div>
                 @endif
                 <div class="flex-1 -lg-8">
                     <div class="Job Description ">
                         <h5 class="fs-18 text-gray-600 mb-4">
                             @lang('web.web_jobs.job_description')</h5>
                         @if($job->description)
                             <div class="job-description">
                                 {!! nl2br($job->description) !!}
                             </div>
                         @else
                             <p class="fs-16 text-gray mb-5 pb-lg-4">N/A</p>
                         @endif
                     </div>
                     <div class="Key Responsibilities">
                           <h5 class="fs-18 text-gray-600 mb-4">
                               @lang('web.web_jobs.key_responsibilities')</h5>
                           @if($job->key_responsibilities)
                               <div class="key-responsibilities">
                                   {!! nl2br($job->key_responsibilities) !!}
                               </div>
                           @else
                               <p class="fs-16 text-gray mb-5 pb-lg-4">N/A</p>
                           @endif
                       </div>
                     <div class="share-this-job">
                         <h5 class="fs-18 text-gray-600 mt-5 mb-4 pb-2">
                             @lang('web.apply_for_job.share_this_job'):</h5>
                         <div class="icon-box flex">
                             <div class="social-icon me-sm-4 me-3 flex items-center justify-center">
                                 <a href="{{ $url['facebook'] }}" target="_blank" class="facebook flex" title="@lang('web.web_jobs.facebook')">
                                     <div class="icon flex">
                                         <i class="fa-brands fa-facebook-f text-white"></i>
                                     </div>
                                 </a>
                             </div>
                             <div class="social-icon me-sm-4 me-3 flex items-center justify-center">
                                 <a href="https://www.linkedin.com/shareArticle/?url={{ rawurlencode(URL::to('/job-details/'.$job->job_id ))}}" title="@lang('web.web_jobs.linkedin')"
                                    target="_blank" class="linkedin flex">
                                     <div class="icon flex">
                                         <i class="fa-brands fa-linkedin-in text-white"></i>
                                     </div>
                                 </a>
                             </div>
                             <div class="social-icon me-sm-4 me-3 flex items-center justify-center">
                                 <a href="{{ $url['twitter'] }}"
                                target="_blank" class="twitter flex" title="@lang('web.web_jobs.twitter')">
                                     <div class="icon flex">
                                         <i class="fa-brands fa-twitter text-white"></i>
                                     </div>
                                 </a>
                             </div>
                             <div class="social-icon me-sm-4 me-3 flex items-center justify-center">
                                 <a href="{{ $url['gmail'] }}" target="_blank" class="google flex" title="@lang('web.web_jobs.google')">
                                     <div class="icon flex">
                                         <i class="fa-brands fa-google-plus-g text-white"></i>
                                     </div>
                                 </a>
                             </div>
                             <div class="social-icon me-sm-4 me-3 flex items-center justify-center">
                                 <a href="{{ $url['pinterest'] }}" target="_blank"
                                class="pinterest flex" title="@lang('web.web_jobs.pinterest')">
                                     <div class="icon flex">
                                         <i class="fa-brands fa-pinterest-p text-white"></i>
                                     </div>
                                 </a>
                             </div>
                         </div>
                     </div>
                 </div>

                     <div class="flex-1 -lg-4">

                     <div class="flex-1 -12">
                         <div class="flex-1 -12 mb-40">
                             <div class="job-card bg-white shadow rounded-lg overflow-hidden py-30">
                                 <div class="flex flex-wrap flex justify-content-lg-between">
                                     <div class="flex-1 -5 mt-3">
                                         <i class="fa-solid fa-calendar-days text-primary-600 fs-4"></i>
                                         <p class="details-page- bg-white shadow rounded-lg overflow-hidden -text mb-0" >
                                             @lang('web.job_details.date_posted')</p>
                                         <p class="text-gray-600 fs-14">
                                             {{ \Carbon\Carbon::parse($job->created_at)->translatedFormat('jS M, Y') }}
                                         </p>
                                     </div>
                                     <div class="flex-1 -5 mt-3">
                                         <i class="fa-solid fa-calendar-days text-primary-600 fs-4"></i>
                                         <p class="details-page- bg-white shadow rounded-lg overflow-hidden -text mb-0" >
                                             @lang('web.web_jobs.expiration_date')</p>
                                         <p class="text-gray-600 fs-14">
                                             {{ \Carbon\Carbon::parse($job->job_expiry_date)->translatedFormat('jS M, Y') }}
                                         </p>
                                     </div>
                                     <div class="flex-1 -5 mt-3">
                                         <i class="fa-solid fa-location-dot text-primary-600 fs-4"></i>
                                         <p class="details-page- bg-white shadow rounded-lg overflow-hidden -text mb-0" >
                                             @lang('web.common.location')</p>
                                         <p class="text-gray-600 fs-14">
                                             @if (!empty($job->city_id))
                                                 {{$job->city_name}} ,
                                             @endif
                                             @if (!empty($job->state_id))
                                                 {{$job->state_name}},
                                             @endif
                                             @if (!empty($job->country_id))
                                                 {{$job->country_name}}
                                             @endif
                                             @if (empty($job->country_id))
                                                 {{ __('web.job_details.location_information_not_available') }}
                                             @endif
                                         </p>
                                     </div>
                                     <div class="flex-1 -5 mt-3">
                                         <i class="fa-solid fa-briefcase text-primary-600 fs-4"></i>
                                         <p class="details-page- bg-white shadow rounded-lg overflow-hidden -text mb-0" >
                                             @lang('messages.job.job_type')</p>
                                         <p class="text-gray-600 fs-14">
                                             {{ ($job->jobType) ? html_entity_decode($job->jobType->name) : __('messages.common.n/a') }}
                                         </p>
                                     </div>
                                     @if($job->jobShift)
                                     <div class="flex-1 -5 mt-3">
                                         <i class="fa-solid fa-briefcase text-primary-600 fs-4"></i>
                                         <p class="details-page- bg-white shadow rounded-lg overflow-hidden -text mb-0">
                                             @lang('messages.job.job_shift')
                                         </p>
                                         <p class="text-gray-600 fs-14">
                                             {{ html_entity_decode($job->jobShift->shift) }}
                                         </p>
                                     </div>

                                     @endif
                                     <div class="flex-1 -5 mt-3">
                                         <i class="fa-solid fa-magnifying-glass text-primary-600 fs-4"></i>
                                         <p class="details-page- bg-white shadow rounded-lg overflow-hidden -text mb-0">
                                             @lang('messages.job.functional_area')
                                         </p>
                                         <p class="text-gray-600 fs-14">
                                             {{ html_entity_decode($job->functionalArea->name) }}
                                         </p>
                                     </div>
                                     @if($job->degreeLevel)
                                     <div class="flex-1 -5 mt-3">
                                         <i class="fa fa-graduation-cap text-primary-600 fs-4"></i>
                                         <p class="details-page- bg-white shadow rounded-lg overflow-hidden -text mb-0">
                                             @lang('messages.job.degree_level')
                                         </p>
                                         <p class="text-gray-600 fs-14">
                                             {{ html_entity_decode($job->degreeLevel->name) }}
                                         </p>
                                     </div>
                                     @endif
                                     <div class="flex-1 -5 mt-3">
                                         <i class="fa-solid fa-user-plus text-primary-600 fs-4"></i>
                                         <p class="details-page- bg-white shadow rounded-lg overflow-hidden -text mb-0">
                                             @lang('messages.positions')
                                         </p>
                                         <p class="text-gray-600 fs-14">
                                             {{ isset($job->position)?$job->position:'0' }}
                                         </p>
                                     </div>
                                     <div class="flex-1 -5 mt-3">
                                         <i class="fa-solid fa-briefcase text-primary-600 fs-4"></i>
                                         <p class="details-page- bg-white shadow rounded-lg overflow-hidden -text mb-0">
                                             @lang('messages.job_experience.job_experience')
                                         </p>
                                         <p class="text-gray-600 fs-14">
                                             {{ isset($job->experience) ? $job->experience .' '. __('messages.candidate_profile.year') :'No experience' }}
                                         </p>
                                     </div>

                                     <div class="flex-1 -5 mt-3">
                                         <i class="fa-solid fa-calendar-day text-primary-600 fs-4"></i>
                                         <p class="details-page- bg-white shadow rounded-lg overflow-hidden -text mb-0">
                                             @lang('messages.job.salary_period')
                                         </p>
                                         <p class="text-gray-600 fs-14">
                                             {{ $job->salaryPeriod->period }}
                                         </p>
                                     </div>
                                     <div class="flex-1 -5 mt-3">
                                         <i class="fa-solid fa-laptop text-primary-600 fs-4"></i>
                                         <p class="details-page- bg-white shadow rounded-lg overflow-hidden -text mb-0">
                                             @lang('messages.job.is_freelance')
                                         </p>
                                         <p class="text-gray-600 fs-14">
                                             {{ $job->is_freelance == 1 ? __('messages.common.yes') : __('messages.common.no') }}</p>

                                     </div>
                                 </div>
                             </div>
                         </div>
                     </div>
                     <div class="flex-1 -12">
                         <div class="flex-1 -12 mb-40">
                             <div class="job-card bg-white shadow rounded-lg overflow-hidden py-30">
                                 <div class="flex flex-wrap flex justify-content-lg-between">
                                     <p class="fs-18 text-gray-600 my-4">
                                         @lang('web.job_details.job_skills')</p>
                                     <div class="desc flex flex-wrap">
                                         @if($job->jobsSkill->isNotEmpty())
                                             @foreach($job->jobsSkill->pluck('name') as $key => $value)
                                                 <p class="fs-14 text-gray bg-white py-2 br-gray px-3 {{$loop->last?'':'me-4'}} rounded-3">
                                                     {{html_entity_decode($value) }}</p>
                                             @endforeach
                                         @else
                                             <p class="fs-14 text-gray bg-white py-2 br-gray px-3">N/A</p>
                                         @endif
                                     </div>
                                 </div>
                             </div>
                         </div>
                     </div>
                         <div class="flex-1 -12">
                             <div class="flex-1 -12 mb-40">
                                 <div class="job-card bg-white shadow rounded-lg overflow-hidden py-30">
                                     <div class="flex flex-wrap flex justify-content-lg-between company-overview">
                                         <h5 class="fs-18 text-gray-600 mb-4">
                                             @lang('web.job_details.company_overview')</h5>
                                         <div class="company-profile flex mb-4">
                                             <div class="profile bg-white shadow rounded-lg overflow-hidden -img">
                                                 <img src="{{ $job->company->company_url }}" >
                                             </div>
                                             <div class="desc ms-4">
                                                 <p class="fs-18 text-gray-600 mb-0 ">
                                                     {{ html_entity_decode($job->company->user->first_name) }}</p>
                                                 <a href="{{ route('front.company.details', $job->company->unique_id) }}" class="fs-14 text-primary-600">
                                                     @lang('web.web_jobs.view_company_profile')</a>
                                             </div>
                                         </div>
                                         <div class="desc-box flex justify-between mb-2">
                                             <p class="fs-14 text-gray-600">@lang('web.web_jobs.founded_in'):</p>
                                             <p class="fs-14 text-gray text-end">
                                                 {{$job->company->established_in}}</p>
                                         </div>
                                         @if($job->company->user->phone)
                                             <div class="desc-box flex justify-between mb-2">
                                                 <p class="fs-14 text-gray-600">@lang('web.web_jobs.phone'):</p>
                                                 <p class="fs-14 text-gray text-end">
                                                     {{$job->company->user->phone}}</p>
                                             </div>
                                         @endif
                                         <div class="desc-box flex justify-between mb-2">
                                             <p class="fs-14 text-gray-600">@lang('web.common.location'):</p>
                                             @if (!empty($job->company->location))
                                                 <p class="fs-14 text-gray text-end">{{$job->company->location}}</p>
                                             @else
                                                 <p class="fs-14 text-gray text-end">
                                                     {{ __('web.job_details.location_information_not_available') }}
                                                 </p>
                                             @endif
                                         </div>
                                         <div class="bg-white shadow rounded-lg overflow-hidden -desc mt-3">
                                             <div class="desc flex mt-2">
                                                 <a href="{{ route('front.company.details', $job->company->unique_id) }}"
                                                    class="jobs-position  fs-14 mb-0 me-3">
                                                     {{ __('web.companies_menu.opened_jobs') }} : {{ $jobsCount?$jobsCount : 0 }}
                                                 </a>
                                             </div>
                                         </div>
                                         @if($job->company->website)
                                         <div class="bg-white shadow rounded-lg overflow-hidden -desc mt-3">
                                             <div class="desc flex mt-2">
                                                 <a href="{{$job->company->website}}" target="_blank"
                                                    class="jobs-position  fs-14 mb-0 me-3">
                                                     {{$job->company->website}}
                                                 </a>
                                             </div>
                                         </div>
                                         @endif
                                     </div>
                                 </div>
                             </div>
                         </div>
                 </div>

                     @if(count($getRelatedJobs)>0)
                         <div class="flex flex-wrap our-latest-jobs">
                             <h5 class="fs-18 text-gray-600 mt-5 mb-4 pb-2">
                                 @lang('web.job_details.related_jobs')
                             </h5>
                             @foreach($getRelatedJobs as $relatedJob)
                                 @if($relatedJob->status != \App\Models\Job::STATUS_DRAFT)
                                     <div class="lg:w-4/12 px-2 flex-1 -md-6 px-xl-3 mb-40">
                                         <div class="bg-white shadow rounded-lg overflow-hidden py-30">
                                             <div class="bg-white shadow rounded-lg overflow-hidden -body">
                                                 @if(Str::length($relatedJob['job_title']) < 35)
                                                     <a href="{{ route('front.job.details',$relatedJob['job_id']) }}"
                                                        class="text-gray-600 primary-link-hover">
                                                         <h5 class="bg-white shadow rounded-lg overflow-hidden -title fs-18 mb-0">
                                                             {{ html_entity_decode($relatedJob['job_title']) }}
                                                         </h5>
                                                     </a>
                                                 @else
                                                     <a href="{{ route('front.job.details',$relatedJob['job_id']) }}"
                                                        data-toggle="tooltip" data-placement="bottom"
                                                        class="hover-color"
                                                        title="{{ html_entity_decode($relatedJob['job_title']) }}">
                                                         <h5 class="bg-white shadow rounded-lg overflow-hidden -title fs-18 mb-0">
                                                             {{ Str::limit(html_entity_decode($relatedJob['job_title']),30,'...') }}
                                                         </h5>
                                                     </a>
                                                 @endif
                                                 <div class="mt-2 flex flex-wrap items-center">
                                                     @if(isset($relatedJob->jobShift->shift))
                                                         <span class="text text-primary-600 fs-12 mb-0 me-3 related-jobs">
                                                                   {{$relatedJob->jobShift->shift}}
                                                           </span>
                                                     @endif
                                                     <div class="desc flex">
                                                         <span class="text-gray">
                                                               {{$relatedJob->currency->currency_icon}}&nbsp
                                                         </span>
                                                         <span class="fs-14 text-gray">
                                                                {{ $relatedJob->salary_from}} - {{$relatedJob->salary_to}}
                                                         </span>
                                                     </div>
                                                 </div>
                                                 <div class="mt-3 flex flex-wrap">
                                                     <div class="flex-1 -3">
                                                         <img src="{{$relatedJob->company->company_url}}"
                                                              class="bg-white shadow rounded-lg overflow-hidden -img" alt="...">
                                                     </div>
                                                     <div class="flex-1 -8">

                                                         <a href="{{route('front.company.details', $relatedJob->company->unique_id)}}">
                                                         <p class="mb-0 fs-14">{{$relatedJob->company->user->first_name}}</p>
                                                         </a>
                                                         <div class="desc flex items-center">
                                                             <i class="fa-solid fa-location-dot text-gray me-2 fs-18"></i>
                                                             @if(Str::length($relatedJob->full_location) < 45)
                                                                 <p class="fs-14 text-gray mb-0"> {{ $relatedJob->full_location }} </p>
                                                             @else
                                                                 <p class="fs-14 text-gray mb-0"
                                                                    data-toggle="tooltip" data-placement="bottom"
                                                                    title="{{$relatedJob->full_location}}">
                                                                     {{ Str::limit($relatedJob->full_location,45,'...') }}
                                                                 </p>
                                                             @endif
                                                         </div>
                                                     </div>
                                                     @if($relatedJob->activeFeatured)
                                                         <div class="flex-1 -1 icon relative pe-0 float-end flex items-center">
                                                             <i class="text-primary-600 fa-solid fa-bookmark"></i>
                                                         </div>
                                                     @else
                                                         <div class="flex-1 -1 icon relative pe-0 float-end flex items-center text-gray">
                                                             <i class="fa-regular fa-bookmark"></i>
                                                         </div>
                                                     @endif
                                                 </div>
                                             </div>
                                         </div>
                                     </div>
                                 @endif
                             @endforeach

                             @if(($getRelatedJobs->count() > 0 ))
                                 <div class="flex flex-wrap justify-center">
                                     <div class="flex-1 -8 text-center">
                                         <a href="{{ route('front.search.jobs',array('categories'=> $relatedJob->jobCategory->id)) }}"
                                            class="inline-flex items-center px-4 py-2 border border-gray-300 border-transparent text-sm font-medium rounded-md transition duration-150 ease-in-out px-4 py-2 rounded font-medium transition-colors -primary mb-40 mt-lg-4">
                                             @lang('web.common.show_all')</a>
                                     </div>
                                 </div>
                             @endif
                         </div>
                     @endif

             </div>
         </div>
     </section>
        <!-- end job-details section -->
    </div>
    @role('Candidate')
        @include('front_web.jobs.email_to_friend')
        @include('front_web.jobs.report_job_modal')
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
