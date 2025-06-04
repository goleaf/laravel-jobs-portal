<div class="col-xl-3 lg:w-4/12 px-2 flex-1 -md-6 mb-40">
    <div class="bg-white shadow rounded-lg overflow-hidden py-30">
        @if($company->activeFeatured)
            <span><i class="fas fa-bookmark bookmark-class"></i></span>
        @endif
        <div class="flex flex-wrap items-center">
            <div class="flex-1 -3">
                <img src="{{ $company->company_url }}" class="bg-white shadow rounded-lg overflow-hidden -img img-border" alt="">
            </div>
            <div class="flex-1 -9 px-3">
                <div class="bg-white shadow rounded-lg overflow-hidden -body p-0">
                    <a href="{{ route('front.company.details', $company->unique_id) }}"
                       class="text-gray-600 primary-link-hover">
                        <h5 class="bg-white shadow rounded-lg overflow-hidden -title   fs-20 mb-0">
                            {!! $company->user->first_name !!}</h5>
                    </a>
                    <div class="flex">
                        {{--                    @if(!empty($company->industry->name))--}}
                        {{--                        <div class="desc flex mb-2">--}}
                        {{--                            <i class="fa-solid fa-briefcase text-gray me-3 fs-18"></i>--}}
                        {{--                            <p class="fs-14 text-gray mb-0">{{$company->industry->name}}</p>--}}
                        {{--                        </div>--}}
                        {{--                    @endif--}}
                        @if(!empty($company->location) || !empty($company->location2))
                            <div class="desc location-text flex">
                                <i class="fa-solid fa-location-dot  me-1 mt-1 fs-18"></i>
                                <span class="">
                                    {{-- {{ (isset($company->location)) ? html_entity_decode(Str::limit($company->location)) : __('messages.common.n/a') }}{{ (isset($company->location2)) ? ','.html_entity_decode(Str::limit($company->location2,10,'...')) : '' }}--}}
                                 {{ $company->user->city_name.', '.$company->user->country_name }}
                                </span>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
        @php
            $open_jobs = $company->jobs->where('status', App\Models\Job::STATUS_OPEN)->count()
        @endphp
            @if($open_jobs <= 0)
                <div class="bg-white shadow rounded-lg overflow-hidden -desc mt-3">
                    <div class="desc flex mt-2">
                        <p class="jobs-position bg-gray fs-14 mb-0 me-3 text-gray-600">
                            {{ __('web.no_positions') }}
                        </p>
                    </div>
                </div>
            @else
                <div class="bg-white shadow rounded-lg overflow-hidden -desc mt-3">
                    <div class="desc flex mt-2">
                        <a href="{{ route('front.company.details', $company->unique_id) }}"
                           class="jobs-position  fs-14 mb-0 me-3">
                            {{ $open_jobs }} {{__('web.open_positions')}}
                        </a>
                    </div>
                </div>
            @endif

    </div>
</div>


