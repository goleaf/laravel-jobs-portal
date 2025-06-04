{{-- <div class="lg:w-4/12 px-2 flex-1 -md-6 px-xl-3 mb-40"> --}}
{{--    <div class="bg-white shadow rounded-lg overflow-hidden py-30"> --}}
{{--        <div class="flex flex-wrap items-center"> --}}
{{--            <div class="flex-1 -3"> --}}
{{--                <img src="{{$job->company->company_url}}" class="bg-white shadow rounded-lg overflow-hidden -img" alt="..."> --}}
{{--            </div> --}}
{{--            @dd($job->company->location) --}}
{{--            <div class="flex-1 -8"> --}}
{{--                <div class="bg-white shadow rounded-lg overflow-hidden -body p-0"> --}}
{{--                    @if (Str::length($job->job_title) < 35) --}}
{{--                        <a href="{{ route('front.job.details',$job->job_id) }}" --}}
{{--                           class="text-gray-600 primary-link-hover"> --}}
{{--                            <h5 class="bg-white shadow rounded-lg overflow-hidden -title fs-18 mb-0"> --}}
{{--                                {{ html_entity_decode($job->job_title) }} --}}
{{--                            </h5> --}}
{{--                        </a> --}}
{{--                    @else --}}
{{--                        <a href="{{ route('front.job.details',$job->job_id) }}" --}}
{{--                           data-toggle="tooltip" data-placement="bottom" class="hover-color" --}}
{{--                           title="{{ html_entity_decode($job->job_title) }}"> --}}
{{--                            <h5 class="bg-white shadow rounded-lg overflow-hidden -title fs-18 mb-0"> --}}
{{--                                {{ Str::limit(html_entity_decode($job->job_title),30,'...') }} --}}
{{--                            </h5> --}}
{{--                        </a> --}}
{{--                    @endif --}}
{{--                </div> --}}
{{--            </div> --}}
{{--            @if ($job->activeFeatured) --}}
{{--                <div class="flex-1 -1 icon relative pe-0"> --}}
{{--                    <i class="text-primary-600 fa-solid fa-bookmark"></i> --}}
{{--                </div> --}}
{{--            @endif --}}
{{--        </div> --}}
{{--        <div class="bg-white shadow rounded-lg overflow-hidden -desc mt-4"> --}}
{{--            <div class="desc flex mb-2"> --}}
{{--                <i class="fa-solid fa-briefcase text-gray me-3 fs-18"></i> --}}
{{--                <p class="fs-14 text-gray mb-0">{{$job->jobCategory->name}}</p> --}}
{{--            </div> --}}
{{--            @if ($job->country_name) --}}
{{--                <div class="desc flex"> --}}
{{--                    <i class="fa-solid fa-location-dot text-gray me-3 fs-18"></i> --}}
{{--                    @if (Str::length($job->full_location) < 45) --}}
{{--                        <p class="fs-14 text-gray"> {{ $job->full_location }} </p> --}}
{{--                    @else --}}
{{--                        <p class="fs-14 text-gray" data-toggle="tooltip" data-placement="bottom" --}}
{{--                           title="{{$job->full_location}}"> --}}
{{--                            {{ Str::limit($job->full_location,45,'...') }} --}}
{{--                        </p> --}}
{{--                    @endif --}}
{{--                </div> --}}
{{--            @endif --}}
{{--            <div class="desc flex mt-2"> --}}
{{--                @foreach ($job->jobsSkill->take(1) as $skills) --}}
{{--                    <p class="text text-primary-600 fs-14 mb-0 me-3">{{$skills->name}}</p> --}}
{{--                    @if (count($job->jobsSkill) - 1 > 0) --}}
{{--                        <p class="fs-14 text text-primary-600 mb-0"> --}}
{{--                            {{'+'.(count($job->jobsSkill) -1)}}</p> --}}
{{--                    @endif --}}
{{--                @endforeach --}}
{{--            </div> --}}
{{--        </div> --}}
{{--    </div> --}}
{{-- </div> --}}


{{-- <div class="lg:w-4/12 px-2 flex-1 -md-6 px-xl-3 mb-40">
    <div class="bg-white shadow rounded-lg overflow-hidden py-30">
        <div class="flex justify-between items-center">
            <div class="flex items-center">
                <div class="me-4">
                    <img src="{{ $job->company->company_url }}" class="bg-white shadow rounded-lg overflow-hidden -img" alt="..." />
                </div>
                <div class="">
                    <div class="bg-white shadow rounded-lg overflow-hidden -body p-0">
                        @if (Str::length($job->job_title) < 35)
                            <a href="" class="text-gray-600 primary-link-hover"
                                title="{{ html_entity_decode($job->job_title) }}">
                                <h5 class="bg-white shadow rounded-lg overflow-hidden -title fs-18 mb-0 inline-block">
                                    {{ ucfirst($job->job_title) }}

                                </h5>
                            </a>
                        @else
                            <a href="{{ route('front.job.details', $job->job_id) }}"
                                class="text-gray-600 primary-link-hover"
                                title="{{ html_entity_decode($job->job_title) }}">
                                <h5 class="bg-white shadow rounded-lg overflow-hidden -title fs-18 mb-0 inline-block">
                                    {{ Str::limit(html_entity_decode($job->job_title), 30, '...') }}
                                </h5>
                            </a>
                        @endif
                    </div>
                </div>
            </div>
            @if ($job->activeFeatured)
                <div class="icon relative pe-0">
                    <i class="text-primary-600 fa-solid fa-bookmark"></i>
                </div>
            @else
                <div class="icon relative pe-0">
                    <i class="fa-regular fa-bookmark"></i>
                </div>
            @endif
        </div>
        <div class="bg-white shadow rounded-lg overflow-hidden -desc flex flex-col justify-between h-full mt-4">
            <div class="desc">
                <div class="flex mb-1">
                    <div class="me-3 w-20">
                        <i class="fa-solid fa-location-dot text-gray me-3 fs-18"></i>
                    </div>
                    <p class="fs-14 text-gray mb-0">
                        {{ !empty($job->full_location) ? $job->full_location : 'Location Info. not available.' }}
                    </p>
                </div>
                <div class="flex mb-2">
                    @if (isset($job->jobShift->shift))
                        <p class="fs-14 text-gray mb-0">
                            {{ $job->jobShift->shift }}
                        </p>
                    @endif
                </div>

                <div class="flex mb-2">
                    <div class="me-3 w-20">
                        {{ $job->currency->currency_icon }}&nbsp</span>
                    </div>

                    {{ $job->salary_from }} - {{ $job->salary_to }}
                </div>
            </div>
            <div class="desc flex">
                <a href="{{ route('front.job.details', $job->job_id) }}" class="inline-flex items-center px-4 py-2 border border-gray-300 border-transparent text-sm font-medium rounded-md transition duration-150 ease-in-out px-4 py-2 rounded font-medium transition-colors -primary"
                    style="padding:5px 15px !important;">{{ __('messages.view_details') }}</a>
            </div>
        </div>
        <div class="flex-1 -12 d-sm-none block">
            <div class="bg-white shadow rounded-lg overflow-hidden -body p-0 ps-xl-3">
                @if (Str::length($job->job_title) < 35)
                    <a href="{{ route('front.job.details', $job->job_id) }}" class="text-gray-600 primary-link-hover"
                        title="{{ html_entity_decode($job->job_title) }}">
                        <h5 class="bg-white shadow rounded-lg overflow-hidden -title fs-18 mb-0 inline-block">
                            {{ html_entity_decode($job->job_title) }}

                        </h5>
                    </a>
                @else
                    <a href="{{ route('front.job.details', $job->job_id) }}" class="text-gray-600 primary-link-hover"
                        title="{{ html_entity_decode($job->job_title) }}">
                        <h5 class="bg-white shadow rounded-lg overflow-hidden -title fs-18 mb-0 inline-block">
                            {{ Str::limit(html_entity_decode($job->job_title), 30, '...') }}
                        </h5>
                    </a>
                @endif
                @if (isset($job->jobShift->shift))
                    <span class="text text-primary-600 fs-12 mb-0 me-3">
                        {{ $job->jobShift->shift }}
                    </span>
                @endif
                <div class="flex-1 -xl-12">
                    <div class="bg-white shadow rounded-lg overflow-hidden -desc flex flex-wrap mt-2">

                        <div class="desc flex me-4">
                            <i class="fa-solid fa-location-dot text-gray me-3 fs-18"></i>
                            <p class="fs-14 text-gray mb-2">
                                {{ !empty($job->full_location) ? $job->full_location : 'Location Info. not available.' }}
                            </p>
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
    </div>
</div> --}}

<div class="lg:w-4/12 px-2 flex-1 -md-6 px-xl-3 mb-40">
    <div class="bg-white shadow rounded-lg overflow-hidden py-30">
        <div class="flex justify-between items-center">
            <div class="flex items-center">
                <div class="me-4">
                    <img src="{{ $job->company->company_url }}" class="bg-white shadow rounded-lg overflow-hidden -img" alt="..." />
                </div>
                <div class="">
                    <div class="bg-white shadow rounded-lg overflow-hidden -body p-0">
                        @if (Str::length($job->job_title) < 35)
                            <a href="{{ route('front.job.details', $job->job_id) }}"
                                class="text-gray-600 primary-link-hover"
                                title="{{ html_entity_decode($job->job_title) }}">
                                <h5 class="bg-white shadow rounded-lg overflow-hidden -title fs-18 mb-0 inline-block">
                                    {{ ucfirst($job->job_title) }}

                                </h5>
                            </a>
                        @else
                            <a href="{{ route('front.job.details', $job->job_id) }}"
                                class="text-gray-600 primary-link-hover"
                                title="{{ html_entity_decode($job->job_title) }}">
                                <h5 class="bg-white shadow rounded-lg overflow-hidden -title fs-18 mb-0 inline-block">
                                    {{ Str::limit(html_entity_decode($job->job_title), 30, '...') }}
                                </h5>
                            </a>
                        @endif
                    </div>
                </div>
            </div>
            <div class="icon relative pe-0">
                @if ($job->activeFeatured)
                    <div
                        class="md:w-1/12 col-sm-1 flex-1 -8 justify-end bookmark-icon relative pe-0 float-end flex">
                        <i class="text-primary-600 fa-solid fa-bookmark"></i>
                    </div>
                @else
                    <div
                        class="md:w-1/12 col-sm-1 flex-1 -8 bookmark-icon justify-end relative pe-0 float-end flex text-gray">
                        <i class="fa-regular fa-bookmark"></i>
                    </div>
                @endif
            </div>
        </div>
        <div class="bg-white shadow rounded-lg overflow-hidden -desc flex flex-col justify-between h-full mt-4">
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
            <div class="desc flex">
                <p class="text text-primary-600 fs-14 mb-0 me-3">
                    {{ !empty($job->jobsSkill[0]->name) ? $job->jobsSkill[0]->name : 'Skill' }}
                </p>
                <p class="fs-14 text text-primary-600 mb-0">{{ $job->jobsSkill->count() }}+</p>
            </div>
        </div>
    </div>
</div>


{{-- <div class="lg:w-full px-2 flex-1 -md-6 px-xl-3 mb-40"> --}}
{{--    <div class="bg-white shadow rounded-lg overflow-hidden py-30"> --}}
{{--        <div class="bg-white shadow rounded-lg overflow-hidden -body"> --}}
{{--            @if (Str::length($job->job_title) < 35) --}}
{{--                <a href="{{ route('front.job.details',$job->job_id) }}" class="text-gray-600 primary-link-hover"> --}}
{{--                    <h5 class="bg-white shadow rounded-lg overflow-hidden -title fs-18 mb-0"> --}}
{{--                        {{ html_entity_decode($job->job_title) }} --}}
{{--                    </h5> --}}
{{--                </a> --}}
{{--            @else --}}
{{--                <a href="{{ route('front.job.details',$job->job_id) }}" --}}
{{--                   data-toggle="tooltip" data-placement="bottom" class="hover-color" --}}
{{--                   title="{{ html_entity_decode($job->job_title) }}"> --}}
{{--                    <h5 class="bg-white shadow rounded-lg overflow-hidden -title fs-18 mb-0"> --}}
{{--                        {{ Str::limit(html_entity_decode($job->job_title),30,'...') }} --}}
{{--                    </h5> --}}
{{--                </a> --}}
{{--            @endif --}}
{{--            <div class="mt-2 flex flex-wrap items-center"> --}}
{{--               --}}
{{--                @if (isset($job->jobShift->shift)) --}}
{{--            <span class="text text-primary-600 fs-12 mb-0 me-3"> --}}
{{--                {{$job->jobShift->shift}} --}}
{{--            </span> --}}
{{--                @endif --}}
{{--                <div class="desc flex"> --}}
{{--                                        <span class="text-gray"> --}}
{{--                                            {{$job->currency->currency_icon}}&nbsp</span> --}}
{{--                    <span class="fs-14 text-gray"> --}}
{{--                    {{ $job->salary_from}} - {{$job->salary_to}}</span> --}}
{{--                </div> --}}
{{--            </div> --}}
{{--            <div class="mt-3 flex flex-wrap"> --}}
{{--                <div class="flex-1 -3"> --}}
{{--                    <img src="{{$job->company->company_url}}" class="bg-white shadow rounded-lg overflow-hidden -img" alt="..."> --}}
{{--                </div> --}}
{{--                <div class="flex-1 -8"> --}}
{{--                    <p class="mb-0 fs-14">{{$job->company->user->first_name}}</p> --}}
{{--                    <div class="desc flex items-center"> --}}
{{--                        <i class="fa-solid fa-location-dot text-gray me-2 fs-18"></i> --}}
{{--                        @if (Str::length($job->full_location) < 45) --}}
{{--                            <p class="fs-14 text-gray mb-0"> {{ $job->full_location }} </p> --}}
{{--                        @else --}}
{{--                            <p class="fs-14 text-gray mb-0" data-toggle="tooltip" data-placement="bottom" --}}
{{--                               title="{{$job->full_location}}"> --}}
{{--                                {{ Str::limit($job->full_location,45,'...') }} --}}
{{--                            </p> --}}
{{--                        @endif --}}
{{--                       --}}
{{--                    </div> --}}
{{--                   --}}
{{--                </div> --}}
{{--                @if ($job->activeFeatured) --}}
{{--                    <div class="flex-1 -1 icon relative pe-0 float-end flex items-center"> --}}
{{--                        <i class="text-primary-600 fa-solid fa-bookmark"></i> --}}
{{--                    </div> --}}
{{--                @else --}}
{{--                    <div class="flex-1 -1 icon relative pe-0 float-end flex items-center text-gray"> --}}
{{--                        <i class="fa-regular fa-bookmark"></i> --}}
{{--                    </div> --}}
{{--                @endif --}}
{{--            </div> --}}
{{--        </div> --}}
{{--    </div> --}}
{{-- </div> --}}
