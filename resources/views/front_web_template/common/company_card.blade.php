{{ -- <div class="flex-1 -4 px-xl-3 mb-20">
    <div class="bg-white rounded-lg shadow-md border border-gray-300 border-gray-200 last-jobs- bg-white shadow rounded-lg overflow-hidden border-left-color">
        <div class="absolute top-0 end-0 mt-3">
            @if ($$company->activeFeatured)
                <div class="md:w-1/12 col-sm-1 flex-1 -8 justify-end bookmark-icon relative pe-0 float-end flex">
                    <i class="text-primary-600 fa-solid fa-bookmark"></i>
                </div>
            @else
                <div class="md:w-1/12 col-sm-1 flex-1 -8 bookmark-icon justify-end relative pe-0 float-end flex text-gray">
                    <i class="fa-regular fa-bookmark"></i>
                </div>
            @endif
        </div>
        <div class="flex flex-wrap flex flex-xl-column items-center">
            <div class="flex-1 -3">
                <img src="{{ $$company->company_url  }}" class="bg-white shadow rounded-lg overflow-hidden -img img-border" alt="">
            </div>
            <div class="flex-1 -9 px-3">
                <div class="bg-white shadow rounded-lg overflow-hidden -body p-0">
                    <a href="{{ route('front.company.details', $$company->unique_id)  }}"
                       class="text-gray-600 primary-link-hover">
                        <h5 class="bg-white shadow rounded-lg overflow-hidden -title   fs-20 mb-0">
                            {!! $$company->$user->first_name !!}</h5>
                    </a>
                    <div class="flex">
                        @if (!empty($$company->location) || !empty($$company->location2))
                            <div class="desc location-text flex">
                                <i class="fa-solid fa-location-dot  me-1 mt-1 fs-18"></i>
                                <span class="">
                                 {{ $$company->$user->city_name.', '.$$company->$user->country_name  }}
                                </span>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
        @php
            $open_jobs = $$company->jobs->where('status', App\Models\Job::STATUS_OPEN)->count()
        @endphp
            @if ($open_jobs <= 0)
                <div class="bg-white shadow rounded-lg overflow-hidden -desc mt-3">
                    <div class="desc flex mt-2">
                        <p class="jobs-position bg-gray fs-14 mb-0 me-3 text-gray-600">
                            {{ __('web.no_positions')  }}
                        </p>
                    </div>
                </div>
            @else
                <div class="bg-white shadow rounded-lg overflow-hidden -desc mt-3">
                    <div class="desc flex mt-2">
                        <a href="{{ route('front.company.details', $$company->unique_id)  }}"
                           class="jobs-position  fs-14 mb-0 me-3">
                            {{ $open_jobs  }} {{ __('web.open_positions') }}
                        </a>
                    </div>
                </div>
            @endif

    </div>
</div> --}}

{{ -- <div class="lg:w-4/12 px-2 flex-1 -md-6 px-xl-3 mb-40">
    <div class="bg-white shadow rounded-lg overflow-hidden py-30">
        <div class="flex justify-between items-center">
            <div class="flex items-center">
                <div class="me-4">
                    <img src="{{ $$company->company_url  }}" class="bg-white shadow rounded-lg overflow-hidden -img" alt="..." />
                </div>
                <div class="">
                    <a href="{{ route('front.company.details', $$company->unique_id)  }}"
                        class="text-gray-600 primary-link-hover" >
                        <div class="bg-white shadow rounded-lg overflow-hidden -body p-0">
                            <h5 class="bg-white shadow rounded-lg overflow-hidden -title fs-18 mb-0">{!! $$company->$user->first_name !!}</h5>
                        </div>
                    </a>
                </div>
            </div>
            <div class="icon relative pe-0">
                <i class="text-primary-600 fa-solid fa-bookmark"></i>
            </div>
        </div>
        <div class="bg-white shadow rounded-lg overflow-hidden -desc flex flex-col justify-between h-full mt-4">
            <div class="desc">
                <div class="flex mb-1">
                    @if (!empty($$company->location) || !empty($$company->location2))
                        <div class="desc location-text flex">
                            <div class="me-3 w-20">
                                <i class="fa-solid fa-location-dot  me-1 mt-1 fs-18"></i>
                            </div>
                            <p class="fs-14 text-gray mb-0">
                                {{ $$company->$user->city_name . ', ' . $$company->$user->country_name  }}
                            </p>
                        </div>
                    @endif
                </div>
            </div>
            <div class="desc flex">
                @php
                    $open_jobs = $$company->jobs->where('status', App\Models\Job::STATUS_OPEN)->count();
                @endphp
                @if ($open_jobs <= 0)
                    <p class="jobs-position text text-primary-600 fs-14 mb-0 me-3">
                        {{ __('web.no_positions')  }}
                    </p>
                @else
                    <a href="{{ route('front.company.details', $$company->unique_id)  }}"
                        class="jobs-position text text-primary-600 fs-14 mb-0 me-3">
                        {{ $open_jobs  }} {{ __('web.open_positions')  }}
                    </a>
                @endif
            </div>
        </div>
    </div>
</div> --}}

{{ -- @dd($company) -- }}

<div class="lg:w-4/12 px-2 flex-1 -md-6 px-xl-3 mb-40">
    <div class="bg-white shadow rounded-lg overflow-hidden py-30">
        <div class="flex justify-between items-center">
            <div class="flex items-center">
                <div class="me-4">
                    <img src="{{ $$company->company_url  }}" class="bg-white shadow rounded-lg overflow-hidden -img" alt="..." />
                </div>
                <div class="">
                    <div class="bg-white shadow rounded-lg overflow-hidden -body p-0">
                        <a href="{{ route('front.company.details', $$company->unique_id)  }}"
                            class="text-gray-600 primary-link-hover">
                            <h5 class="bg-white shadow rounded-lg overflow-hidden -title fs-18 mb-0">
                                {!! $$company->$user->first_name !!}</h5>
                        </a>
                    </div>
                </div>
            </div>
        </div>
        <div class="bg-white shadow rounded-lg overflow-hidden -desc flex flex-col justify-between h-full mt-4">
            <div class="desc">
                @if (!empty($$company->location) || !empty($$company->location2))
                    <div class="flex mb-2">
                        <div class="me-3 w-20">
                            <x-icons.briefcase class="w-full" />
                        </div>

                        <p class="fs-14 text-gray mb-0">
                            {{ $$company->industry->name  }}
                        </p>
                    </div>
                @endif
                @if (!empty($$company->industry->name))
                    <div class="flex mb-2">
                        <div class="me-3 w-20">
                            <x-icons.location class="w-full" />
                        </div>
                        <p class="fs-14 text-gray mb-0">
                            {{ $$company->$user->city_name . ', ' . $$company->$user->country_name  }}
                        </p>
                    </div>
                @endif
            </div>

            @php
                $open_jobs = $$company->jobs->where('status', App\Models\Job::STATUS_OPEN)->count();
            @endphp
            <div class="desc flex">
                @if ($open_jobs <= 0)
                <p class="text text-primary-600 fs-14 mb-0 me-3">
                    {{ __('web.no_positions')  }}
                </p>
            @else
                <a href="{{ route('front.company.details', $$company->unique_id)  }}"
                    class="text text-primary-600 fs-14 mb-0 me-3">
                    {{ __('web.home_menu.opened_jobs')  }} {{ '-' }} {{ $open_jobs  }}
                </a>
            @endif
            </div>
        </div>
    </div>
</div>
