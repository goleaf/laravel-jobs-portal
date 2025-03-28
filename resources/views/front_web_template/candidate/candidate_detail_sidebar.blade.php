{{-- <div class="job-desc-right br-10 px-40 bg-gray mb-40 mt-lg-0 mt-md-5 mt-4"> --}}
{{--    <div class="desc-box d-flex justify-content-between mb-2"> --}}
{{--        <div class="d-flex align-items-center mb-3"> --}}
{{--            <i class="fa-solid fa-calendar-days text-primary me-2 fs-18"></i> --}}
{{--            <p class="fs-14 text-secondary mb-0"> --}}
{{--                {{__('messages.candidate_profile.education')}}:</p> --}}
{{--        </div> --}}
{{--        <p class="fs-14 text-gray text-end"> --}}
{{--           {{!empty($candidateDetails->experience) ? $candidateDetails->experience.  ' '.__('messages.candidate_profile.years') : __('messages.common.n/a')}} </p> --}}
{{--    </div> --}}

{{--    @if ($candidateDetails->user->dob) --}}
{{--        <div class="desc-box d-flex justify-content-between mb-2"> --}}
{{--            <div class="d-flex align-items-center mb-3"> --}}
{{--                <i class="fa-solid fa-clock text-primary me-2 fs-18"></i> --}}
{{--                <p class="fs-14 text-secondary mb-0"> --}}
{{--                    <i class="icon icon-expiry"></i>{{__('messages.candidate_profile.age')}}:</p> --}}
{{--            </div> --}}
{{--            <p class="fs-14 text-gray text-end"> --}}
{{--                {{!empty($candidateDetails->user->dob) ?\Carbon\Carbon::parse($candidateDetails->user->dob)->age. ' '.__('messages.candidate_profile.years') : __('messages.common.n/a')}} --}}
{{--            </p> --}}
{{--        </div> --}}
{{--    @endif --}}
{{--    <div class="desc-box d-flex justify-content-between mb-2"> --}}
{{--        <div class="d-flex align-items-center mb-3"> --}}
{{--            <i class="fa-solid fa-location-dot text-primary me-2 fs-18"></i> --}}
{{--            <p class="fs-14 text-secondary mb-0">{{__('messages.candidate.current_salary')}}:</p> --}}
{{--        </div> --}}
{{--        <p class="fs-14 text-gray text-end"> --}}
{{--            {{ !empty($candidateDetails->current_salary) ? $candidateDetails->current_salary : __('messages.common.n/a')}} --}}
{{--        </p> --}}
{{--    </div> --}}
{{--    <div class="desc-box d-flex justify-content-between mb-2"> --}}
{{--        <div class="d-flex align-items-center mb-3"> --}}
{{--            <i class="fa-solid fa-briefcase text-primary me-2 fs-18"></i> --}}
{{--            <p class="fs-14 text-secondary mb-0"> <i class="icon icon-salary"></i>{{__('messages.candidate.expected_salary')}}:</p> --}}
{{--        </div> --}}
{{--        <p class="fs-14 text-gray text-end"> --}}
{{--            {{ !empty($candidateDetails->expected_salary) ? $candidateDetails->expected_salary : __('messages.common.n/a') }}</p> --}}
{{--    </div> --}}

{{--        <div class="desc-box d-flex justify-content-between mb-2"> --}}
{{--            <div class="d-flex align-items-center mb-3"> --}}
{{--                <i class="fa-solid fa-briefcase text-primary me-2 fs-18"></i> --}}
{{--                <p class="fs-14 text-secondary mb-0"> <i class="icon icon-salary"></i><i class="icon icon-user-2"></i>{{__('messages.candidate.gender')}}:</p> --}}
{{--            </div> --}}
{{--            <p class="fs-14 text-gray text-end"> --}}
{{--                @if ($candidateDetails->user->gender == 0) --}}
{{--                    {{ __('messages.common.male')}} --}}
{{--                @elseif($candidateDetails->user->gender == 1) --}}
{{--                    {{ __('messages.common.female')}} --}}
{{--                @else --}}
{{--                    {{ __('messages.common.n/a')}} --}}
{{--                @endif --}}
{{--               </p> --}}
{{--        </div> --}}
{{--    @if (!empty($candidateDetails->user->facebook_url) || !empty($candidateDetails->user->twitter_url) || !empty($candidateDetails->user->google_plus_url) || !empty($candidateDetails->user->pinterest_url) || !empty($candidateDetails->user->linkedin_url)) --}}
{{--        <div class="sidebar-widget social-media-widget"> --}}
{{--            <h4 class="widget-title">{{__('messages.social_media')}}</h4> --}}
{{--            <div class="widget-content"> --}}
{{--                <div class="social-links"> --}}
{{--                    @if (!empty($candidateDetails->user->facebook_url)) --}}
{{--                        <a href="{{ (isset($candidateDetails->user->facebook_url)) ? addLinkHttpUrl($candidateDetails->user->facebook_url) : 'javascript:void(0)' }}" --}}
{{--                           target="_blank"><i class="fab fa-facebook-f me-2"></i></a> --}}
{{--                    @endif --}}
{{--                    @if (!empty($candidateDetails->user->twitter_url)) --}}
{{--                        <a href="{{ (isset($candidateDetails->user->twitter_url)) ? addLinkHttpUrl($candidateDetails->user->twitter_url) : 'javascript:void(0)' }}" --}}
{{--                           target="_blank"><i class="fab fa-twitter me-2"></i></a> --}}
{{--                    @endif --}}
{{--                    @if (!empty($candidateDetails->user->google_plus_url)) --}}
{{--                        <a href="{{ (isset($candidateDetails->user->google_plus_url)) ? addLinkHttpUrl($candidateDetails->user->google_plus_url) : 'javascript:void(0)' }}" --}}
{{--                           target="_blank"><i class="fab fa-google-plus-g me-2"></i></a> --}}
{{--                    @endif --}}
{{--                    @if (!empty($candidateDetails->user->pinterest_url)) --}}
{{--                        <a href="{{ (isset($candidateDetails->user->pinterest_url)) ? addLinkHttpUrl($candidateDetails->user->pinterest_url) : 'javascript:void(0)' }}" --}}
{{--                           target="_blank"><i class="fab fa-pinterest-p me-2"></i></a> --}}
{{--                    @endif --}}
{{--                    @if (!empty($candidateDetails->user->linkedin_url)) --}}
{{--                        <a href="{{ (isset($candidateDetails->user->linkedin_url)) ? addLinkHttpUrl($candidateDetails->user->linkedin_url) : 'javascript:void(0)' }}" --}}
{{--                           target="_blank"><i class="fab fa-linkedin-in me-2"></i></a> --}}
{{--                    @endif --}}
{{--                </div> --}}
{{--            </div> --}}
{{--        </div> --}}
{{--    @endif --}}
{{-- </div> --}}

{{-- <div class="job-desc-right br-10 px-40 bg-gray mb-40 mt-lg-0 mt-md-5 mt-4"> --}}

{{-- <div class="sidebar-widget"> --}}
{{--    <h4 class="widget-title">{{__('messages.professional_skills')}}</h4> --}}
{{--    <div class="widget-content"> --}}
{{--        <ul class="job-skills ps-0"> --}}
{{--            @if ($candidateDetails->user->candidateSkill->count()) --}}
{{--                @foreach ($candidateDetails->user->candidateSkill as $candidateSkill) --}}
{{--                    <li> --}}
{{--                        <a class="text-hover-primary text-gray cursor-default">{{ html_entity_decode($candidateSkill->name) }}</a> --}}
{{--                    </li> --}}
{{--                @endforeach --}}
{{--            @else --}}
{{--                <h4 class="text-center">{{ __('messages.skill.no_skill_available') }}</h4> --}}
{{--            @endif --}}
{{--        </ul> --}}
{{--    </div> --}}
{{-- </div> --}}
{{-- </div> --}}


{{-- <div class="col-12">
    <div class="col-12 mb-40">
        <div class="job-card card py-30">
            <div class="row d-flex justify-content-lg-between">
                <div class="col-5 mt-3">
                    <i class="fa-solid fa-calendar-days text-primary fs-4"></i>
                    <p class="details-page-card-text mb-0" >
                        {{__('messages.candidate_profile.education')}}</p>
                    <p class="text-secondary fs-14">
                        {{!empty($candidateDetails->experience) ? $candidateDetails->experience.  ' '.__('messages.candidate_profile.years') : __('messages.common.n/a')}}
                    </p>
                </div>
                @if ($candidateDetails->user->dob)
                    <div class="col-5 mt-3">
                        <i class="fa-solid fa-cake-candles text-primary fs-4"></i>
                        <p class="details-page-card-text mb-0" >
                            {{__('messages.candidate_profile.age')}}</p>
                        <p class="text-secondary fs-14">
                            {{!empty($candidateDetails->user->dob) ?\Carbon\Carbon::parse($candidateDetails->user->dob)->age. ' '.__('messages.candidate_profile.years') : __('messages.common.n/a')}}
                        </p>
                    </div>
                @endif
                <div class="col-5 mt-3">
                    <i class="fa-solid fa-wallet text-primary fs-4"></i>
                    <p class="details-page-card-text mb-0" >
                        {{__('messages.candidate.current_salary')}}</p>
                    <p class="text-secondary fs-14">
                        {{ !empty($candidateDetails->current_salary) ? $candidateDetails->current_salary : __('messages.common.n/a')}}
                    </p>
                </div>
                <div class="col-5 mt-3">
                    <i class="fa-solid fa-wallet text-primary fs-4"></i>
                    <p class="details-page-card-text mb-0" >
                        {{__('messages.candidate.expected_salary')}}</p>
                    <p class="text-secondary fs-14">
                        {{ !empty($candidateDetails->expected_salary) ? $candidateDetails->expected_salary : __('messages.common.n/a') }}
                    </p>
                </div>
                <div class="col-5 mt-3">
                    <i class="fa-solid fa-venus text-primary fs-4"></i>
                    <p class="details-page-card-text mb-0" >
                        {{__('messages.candidate.gender')}}</p>
                    <p class="text-secondary fs-14">
                        @if ($candidateDetails->user->gender == 0)
                            {{ __('messages.common.male')}}
                        @elseif($candidateDetails->user->gender == 1)
                            {{ __('messages.common.female')}}
                        @else
                            {{ __('messages.common.n/a')}}
                        @endif
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>
@if (!empty($candidateDetails->user->facebook_url) || !empty($candidateDetails->user->twitter_url) || !empty($candidateDetails->user->google_plus_url) || !empty($candidateDetails->user->pinterest_url) || !empty($candidateDetails->user->linkedin_url))
<div class="col-12">
    <div class="col-12 mb-40">
        <div class="job-card card py-30">
            <div class="row d-flex justify-content-lg-between">
                <p class="fs-18 text-secondary">@lang('web.web_company.social_media')</p>
                <div class="mt-3">
                    @if (!empty($candidateDetails->user->facebook_url))
                        <a href="{{ (isset($candidateDetails->user->facebook_url)) ? addLinkHttpUrl($candidateDetails->user->facebook_url) : 'javascript:void(0)' }}" target="_blank"><i class="fab fa-facebook-f mx-2 fs-3"></i></a>
                    @endif
                    @if (!empty($candidateDetails->user->twitter_url))
                        <a href="{{ (isset($candidateDetails->user->twitter_url)) ? addLinkHttpUrl($candidateDetails->user->twitter_url) : 'javascript:void(0)' }}"
                           target="_blank"><i class="fab fa-twitter mx-2 fs-3"></i></a>
                    @endif
                    @if (!empty($candidateDetails->user->google_plus_url))
                        <a href="{{ (isset($candidateDetails->user->google_plus_url)) ? addLinkHttpUrl($candidateDetails->user->google_plus_url) : 'javascript:void(0)' }}"
                           target="_blank"><i class="fab fa-google-plus-g mx-2 fs-3"></i></a>
                    @endif
                    @if (!empty($candidateDetails->user->pinterest_url))
                        <a href="{{ (isset($candidateDetails->user->pinterest_url)) ? addLinkHttpUrl($candidateDetails->user->pinterest_url) : 'javascript:void(0)' }}"
                           target="_blank"><i class="fab fa-pinterest-p mx-2 fs-3"></i></a>
                    @endif
                    @if (!empty($candidateDetails->user->linkedin_url))
                        <a href="{{ (isset($candidateDetails->user->linkedin_url)) ? addLinkHttpUrl($candidateDetails->user->linkedin_url) : 'javascript:void(0)' }}"
                           target="_blank"><i class="fab fa-linkedin-in mx-2 fs-3"></i></a>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endif
<div class="col-12">
    <div class="col-12 mb-40">

        <div class="job-card card py-30">
            <p class="fs-18 text-secondary">{{__('messages.professional_skills')}}</p>
            <div class="row d-flex justify-content-lg-between">
                @if ($candidateDetails->user->candidateSkill->count())
                    @foreach ($candidateDetails->user->candidateSkill as $candidateSkill)
                        <li>
                            <a class="text-hover-primary text-gray cursor-default">{{ html_entity_decode($candidateSkill->name) }}</a>
                        </li>
                    @endforeach
                @else
                    <h4 class="text-center">{{ __('messages.skill.no_skill_available') }}</h4>
                @endif
            </div>
        </div>
    </div>
</div>

<div class="job-desc-right br-10 px-40 bg-light mb-40">
    <div class="pb-2">
        <div class="desc-box d-flex justify-content-between mb-4">
            <div class="desc d-flex">
                <div class="me-2 w-20">
                    <x-icons.calendar-candidate />
                </div>
                <p class="fs-14 text-secondary mb-0">{{ __('messages.candidate_profile.education') }}:</p>
            </div>
            <p class="fs-14 text-gray text-end mb-0">
                {{ !empty($candidateDetails->experience) ? $candidateDetails->experience . ' ' . __('messages.candidate_profile.years') : __('messages.common.n/a') }}
            </p>
        </div>
        <div class="desc-box d-flex justify-content-between mb-4">
            <div class="desc d-flex">
                <div class="me-2 w-20">
                    <x-icons.salary />
                </div>
                <p class="fs-14 text-secondary mb-0">{{ __('messages.candidate.current_salary') }}:</p>
            </div>
            <p class="fs-14 text-gray text-end mb-0">
                {{ !empty($candidateDetails->current_salary) ? $candidateDetails->current_salary : __('messages.common.n/a') }}
            </p>
        </div>
        <div class="desc-box d-flex justify-content-between mb-4">
            <div class="desc d-flex">
                <div class="me-2 w-20">
                    <x-icons.salary />
                </div>
                <p class="fs-14 text-secondary mb-0">{{ __('messages.candidate.expected_salary') }}:</p>
            </div>
            <p class="fs-14 text-gray text-end mb-0">
                {{ !empty($candidateDetails->expected_salary) ? $candidateDetails->expected_salary : __('messages.common.n/a') }}
            </p>
        </div>
        <div class="desc-box d-flex justify-content-between mb-4">
            <div class="desc d-flex">
                <div class="me-2 d-flex w-20">
                    <x-icons.gender />
                </div>
                <p class="fs-14 text-secondary mb-0">{{ __('messages.candidate.gender') }}:</p>
            </div>
            <p class="fs-14 text-gray text-end mb-0">
                @if ($candidateDetails->user->gender == 0)
                    {{ __('messages.common.male') }}
                @elseif($candidateDetails->user->gender == 1)
                    {{ __('messages.common.female') }}
                @else
                    {{ __('messages.common.n/a') }}
                @endif
            </p>
        </div>
    </div>
    <div class="desc-box">
        <h5 class="fs-18 text-secondary mb-4">{{ __('messages.professional_skills') }}</h5>
        <div class="d-flex flex-wrap gap-3">
            @if ($candidateDetails->user->candidateSkill->count())
                    <ul>
                        @foreach ($candidateDetails->user->candidateSkill as $candidateSkill)
                            <li class="fs-14 text-gray py-2 {{ $loop->last ? '' : 'me-4' }} ">
                                {{ html_entity_decode($candidateSkill->name) }}</li>
                        @endforeach
                    </ul>
                @else
                    <p class="fs-14 text-gray bg-white py-2 br-gray px-3">
                        {{ __('messages.skill.no_skill_available') }}</p>
                @endif
        </div>
    </div>
</div>

@if (!empty($candidateDetails->available_for_hire))
    <div class="job-desc-right br-10 px-40 bg-gray">
        <p class="text text-success fs-18 mb-0">{{ __('messages.candidate.available_for_hire') }}</p>
    </div>
@endif
<div class="job-desc-right br-10 px-40 bg-gray mt-50">
    @if (isset($candidateDetails->expected_salary) && ($candidateDetails->expected_salary != 0))
        <div class="desc-box d-flex justify-content-between mb-4">
            <div class="desc d-flex">
                <div class="me-2 w-20">
                    <x-icons.money class="w-full h-full" />
                </div>
                <p class="fs-14 text-secondary mb-0">{{ __('messages.candidate.expected_salary') }}:</p>
            </div>
            <p class="fs-14 text-gray text-end mb-0">{{ $candidateDetails->currency->currency_icon }}{{ $candidateDetails->expected_salary }}</p>
        </div>
    @endif
    @if (isset($candidateDetails->experience) && $candidateDetails->experience != 0)
        <div class="desc-box d-flex justify-content-between mb-4">
            <div class="desc d-flex">
                <div class="me-2 w-20">
                    <x-icons.experience class="w-full h-full" />
                </div>
                <p class="fs-14 text-secondary mb-0">{{ __('messages.candidate.experience') }}:</p>
            </div>
            <p class="fs-14 text-gray text-end mb-0">{{ $candidateDetails->experience }}
                {{ $candidateDetails->experience > 1 ? __('messages.candidate.years') : __('messages.candidate.year') }}</p>
        </div>
    @endif
    @if (!empty($candidateDetails->industry))
        <div class="desc-box d-flex justify-content-between mb-4">
            <div class="desc d-flex">
                <div class="me-2 w-20">
                    <x-icons.briefcase class="w-full h-full" />
                </div>
                <p class="fs-14 text-secondary mb-0">{{ __('messages.candidate.industry') }}:</p>
            </div>
            <p class="fs-14 text-gray text-end mb-0">{{ $candidateDetails->industry->name }}</p>
        </div>
    @endif
    @if (!empty($candidateDetails->functionalArea))
        <div class="desc-box d-flex justify-content-between mb-4">
            <div class="desc d-flex">
                <div class="me-2 w-20">
                    <x-icons.functional-area class="w-full h-full" />
                </div>
                <p class="fs-14 text-secondary mb-0">{{ __('messages.candidate.functional_area') }}:</p>
            </div>
            <p class="fs-14 text-gray text-end mb-0">{{ html_entity_decode($candidateDetails->functionalArea->name) }}
            </p>
        </div>
    @endif
    @if (!empty($candidateDetails->careerLevel))
        <div class="desc-box d-flex justify-content-between mb-4">
            <div class="desc d-flex">
                <div class="me-2 w-20">
                    <x-icons.badge class="w-full h-full" />
                </div>
                <p class="fs-14 text-secondary mb-0">{{ __('messages.candidate.career_level') }}:</p>
            </div>
            <p class="fs-14 text-gray text-end mb-0">{{ html_entity_decode($candidateDetails->careerLevel->level_name) }}
            </p>
        </div>
    @endif
    @if (isset($candidateDetails->gender) && $candidateDetails->gender !== \App\Models\Candidate::DEFAULT)
        <div class="desc-box d-flex justify-content-between mb-4">
            <div class="desc d-flex">
                <div class="me-2 w-20">
                    <x-icons.gender class="w-full h-full" />
                </div>
                <p class="fs-14 text-secondary mb-0">{{ __('messages.candidate.gender') }}:</p>
            </div>
            <p class="fs-14 text-gray text-end mb-0">{{ $candidateDetails->gender == 0 ? __('messages.common.male') : __('messages.common.female') }}</p>
        </div>
    @endif
    @if (!empty($candidateDetails->dob))
        <div class="desc-box d-flex justify-content-between mb-4">
            <div class="desc d-flex">
                <div class="me-2 w-20">
                    <x-icons.calendar-days class="w-full h-full" />
                </div>
                <p class="fs-14 text-secondary mb-0">{{ __('messages.candidate.birth_date') }}:</p>
            </div>
            <p class="fs-14 text-gray text-end mb-0">{{ \Carbon\Carbon::parse($candidateDetails->user->dob)->format('jS M, Y') }}</p>
        </div>
    @endif
    @if (!empty($candidateSkills->count()))
        <div class="desc-box">
            <div class="desc d-flex mb-2">
                <div class="me-2 w-20">
                    <x-icons.chart class="w-full h-full" />
                </div>
                <p class="fs-14 text-secondary mb-0">{{ __('messages.candidate.skills') }}:</p>
            </div>
            <div class="d-flex flex-wrap">
                @foreach ($candidateSkills as $candidateSkill)
                    <div class="fs-14 text-gray bg-white py-2 br-gray px-3 mb-1 me-1">
                        {{ html_entity_decode($candidateSkill->name) }}</div>
                @endforeach
            </div>
        </div>
    @endif
</div>
