<div class="container mx-auto px-4 mx-auto">
    <div class="flex flex-wrap">
        {{-- <div class="flex-1 -lg-4">
            <div class="latest-job-left br-10 px-40 bg-color-light">
                <div class="form-group mb-md-4 mb-3">
                    <div class="flex flex-wrap mb-3 justify-between">
                        <label for="" class="fs-16 text-gray-600 my-auto pb-2">
                            {{ __('web.web_jobs.search_by_keywords') }}</label>
                        <button wire:click="resetFilter()" class="inline-flex items-center px-4 py-2 border border-gray-300 border-transparent text-sm font-medium rounded-md transition duration-150 ease-in-out px-3 py-1.5 text-sm px-4 py-2 rounded font-medium transition-colors -primary reset-filter text-nowrap mb-2"
                            id="btnReset">{{ __('web.reset_filter') }}</button>
                    </div>
                    <input class="w-full px-3 py-2 border border-gray-300 border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-primary-500 fs-14 text-gray bg-white br-10 p-3"
                        wire:model.debounce.100ms="searchByCandidate" type="search" id="searchByCandidate"
                        autocomplete="off" placeholder="@lang('web.common.search')">
                </div>
                <div class="form-group mb-md-4 mb-3">
                    <label for="" class="fs-16 text-gray-600 mb-3">
                        {{ __('web.common.location') }}</label>
                    <input class="w-full px-3 py-2 border border-gray-300 border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-primary-500 fs-14 text-gray bg-white br-10 p-3 search-by-location" type="search"
                        autocomplete="off" placeholder="@lang('web.web_jobSeeker.search_by_location')" name="min" wire:model="location">
                </div>
                <div class="form-group mb-md-4 mb-3">
                    <label for="" class="fs-16 text-gray-600 mb-3">
                        {{ __('messages.candidate.expected_salary') }}</label>
                    <input class="w-full px-3 py-2 border border-gray-300 border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-primary-500 fs-14 text-gray bg-white br-10 p-3" type="text" placeholder="Min"
                        name="min" wire:model="min" autocomplete="off">
                    <input class="w-full px-3 py-2 border border-gray-300 border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-primary-500 fs-14 text-gray bg-white br-10 p-3 mt-2" type="text" placeholder="Max"
                        name="max" wire:model="max" autocomplete="off">
                </div>
                <div class="form-group mb-md-4 mb-3">
                    <label for="" class="fs-16 text-gray-600 mb-3">
                        {{ __('messages.candidate.gender') }}</label>
                    <ul>
                        <li>
                            <input type="radio" name="gender" id="All" value="all"
                                wire:click="changeFilter('gender','all')" wire:model="gender">
                            <label for="All" class="ms-1 my-1"><span
                                    class=""></span>{{ __('messages.common.all') }}</label>
                        </li>
                        <li>
                            <input type="radio" name="gender" id="Male" value="male"
                                wire:click="changeFilter('gender','male')" wire:model="gender">
                            <label for="Male" class="ms-1 my-1"><span
                                    class=""></span>{{ __('messages.common.male') }}</label>
                        </li>
                        <li>
                            <input type="radio" name="gender" id="Female" value="female"
                                wire:click="changeFilter('gender','female')" wire:model="gender">
                            <label for="Female" class="ms-1 my-1"><span
                                    class=""></span>{{ __('messages.common.female') }}
                            </label>
                        </li>
                    </ul>
                </div>
            </div>
        </div> --}}
        <div class="flex-1 -lg-4 px-lg-3">
            <div class="latest-job-left br-10 px-40 bg-gray-100 mb-40">
                @formOpen(['method' => 'GET'])
                    <div class="form-group mb-md-4 mb-3">
                        <div class="flex flex-wrap mb-3 justify-between">
                            {{ Form::label('', __('web.web_jobs.search_by_keywords'), ['class' => 'fs-16 text-secondary mb-3']) }}
                            <button wire:click.prevent="resetFilter()" class="inline-flex items-center px-4 py-2 border border-gray-300 border-transparent text-sm font-medium rounded-md transition duration-150 ease-in-out px-3 py-1.5 text-sm bg-primary-600 text-white hover: bg-primary-600 -700 px-4 py-2 rounded font-medium transition-colors -primary-register reset-filter text-nowrap mb-2 px-3 py-1"
                            id="btnReset">{{ __('web.reset_filter') }}</button>
                        </div>
                        {{ Form::text('searchByCandidate', null, ['class' => 'form-control fs-14 text-gray bg-white br-10 p-3', 'wire:model.debounce.100ms.live' => 'searchByCandidate', 'autocomplete' => 'off', 'placeholder' => __('web.common.search')]) }}
                    </div>
                    <div class="form-group mb-md-4 mb-3">
                        {{ Form::label('', __('web.common.location'), ['class' => 'fs-16 text-secondary mb-3']) }}
                        {{ Form::text('location', null, ['class' => 'form-control fs-14 text-gray bg-white br-10 p-3 search-by-location', 'wire:model.live' => 'location', 'autocomplete' => 'off', 'placeholder' => __('web.web_jobSeeker.search_by_location')]) }}
                    </div>
                    <div class="form-group mb-md-4 mb-3">
                        {{ Form::label('', __('messages.candidate.expected_salary'), ['class' => 'fs-16 text-secondary mb-3']) }}
                        {{ Form::text('min', null, ['class' => 'form-control fs-14 text-gray bg-white br-10 p-3', 'wire:model.live' => 'min', 'autocomplete' => 'off', 'placeholder' => __('web.home_menu.min')]) }}
                        {{ Form::text('max', null, ['class' => 'form-control fs-14 text-gray bg-white br-10 p-3 mt-2', 'wire:model.live' => 'max', 'autocomplete' => 'off', 'placeholder' => __('web.home_menu.max')]) }}
                    </div>
                    <div class="form-group mb-md-4 mb-3">
                        {{ Form::label('', __('messages.candidate.gender'), ['class' => 'fs-16 text-secondary mb-3']) }}
                        <ul class="p-0">
                            <li>
                                {{ Form::radio('gender', 'all', ($gender == 'all'), ['id' => 'All', 'wire:click' => "changeFilter('gender','all')", 'wire:model' => 'gender']) }}
                                {{ Form::label('All', __('messages.common.all'), ['class' => 'ms-1 my-1']) }}
                            </li>
                            <li>
                                {{ Form::radio('gender', 'male', ($gender == 'male'), ['id' => 'Male', 'wire:click' => "changeFilter('gender','male')", 'wire:model' => 'gender']) }}
                                {{ Form::label('Male', __('messages.common.male'), ['class' => 'ms-1 my-1']) }}
                            </li>
                            <li>
                                {{ Form::radio('gender', 'female', ($gender == 'female'), ['id' => 'Female', 'wire:click' => "changeFilter('gender','female')", 'wire:model' => 'gender']) }}
                                {{ Form::label('Female', __('messages.common.female'), ['class' => 'ms-1 my-1']) }}
                            </li>
                        </ul>
                    </div>
                @formClose()
            </div>
        </div>
        {{-- <div class="content-column lg:w-8/12 px-2 md:w-full flex-1 -sm-12">
            <div class="flex flex-wrap">
                @forelse($candidates as $candidate)
                    <div class="lg:w-6/12 px-2 flex-1 -md-6 px-xl-3 mb-40">
                        <div class="bg-white shadow rounded-lg overflow-hidden py-30">
                            <div class="flex flex-wrap items-center">
                                <div class="flex-1 -2">
                                    <img src="{{ $candidate->candidate_url }}" class="bg-white shadow rounded-lg overflow-hidden -img" alt="">
                                </div>
                                <div class="flex-1 -10 px-3">
                                    <div class="bg-white shadow rounded-lg overflow-hidden -body p-0">
                                        <a href="{{ route('front.candidate.details', $candidate->unique_id) }}"
                                            class="text-gray-600 primary-link-hover">
                                            <h5 class="bg-white shadow rounded-lg overflow-hidden -title   fs-20 mb-0">
                                                {!! $candidate->user->full_name !!}</h5>
                                        </a>
                                    </div>
                                    <div class="flex">
                                                                               @if (!empty($candidate->industry))
                                                                                   <div class="desc flex mb-2">
                                                                                       <i class="fa-solid fa-briefcase text-gray me-3 fs-18"></i>
                                                                                       <p class="fs-14 text-gray mb-0">{{$candidate->industry->name}}</p>
                                                                                   </div>
                                                                               @endif
                                        @if (!empty($candidate->full_location) || !empty($candidate->location2))
                                            <div class="desc location-text flex">
                                                <i class="fa-solid fa-location-dot  me-1 mt-1 fs-18"></i>
                                                <span class="">
                                                    {{ isset($candidate->full_location) ? html_entity_decode(Str::limit($candidate->full_location, 10, '...')) : __('messages.common.n/a') }}{{ isset($candidate->location2) ? ',' . html_entity_decode(Str::limit($candidate->location2, 10, '...')) : '' }}</span>
                                            </div>
                                        @endif
                                        @if (!empty($candidate->expected_salary))
                                            <span><i class="fa-solid fa-money-bill-alt text-gray ms-3 me-2"></i></span>
                                            <p class="fs-14 text-gray mb-0">{{ $candidate->expected_salary }}</p>
                                        @endif
                                    </div>
                                </div>
                                <div class="bg-white shadow rounded-lg overflow-hidden -desc mt-3">
                                    <div class="desc flex mt-2">
                                        <a href="{{ route('front.candidate.details', $candidate->unique_id) }}"
                                            class="jobs-position  fs-14 mb-0 me-3">
                                            {{ __('web.web_jobSeeker.view_profile') }}
                                        </a>
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>
                @empty
                @endforelse
            </div>
            @if ($candidates->count() > 0)
                {{ $candidates->links() }}
            @endif
        </div> --}}

        <div class="content-column flex-1 -lg-8 px-lg-3">
            <div class="job- bg-white shadow rounded-lg overflow-hidden">
                @forelse($candidates as $candidate)
                    <div class=" mb-40">
                        <div class="bg-white shadow rounded-lg overflow-hidden py-30 border-0">
                            <div class="d-sm-flex relative">
                                <div class="mb-sm-0 mb-3 me-sm-4">
                                    <img src="{{ asset('img_template/test-job.png') }}" class="bg-white shadow rounded-lg overflow-hidden -img" alt="...">
                                </div>
                                <div class="">
                                    <div class="bg-white shadow rounded-lg overflow-hidden -body p-0">
                                        <a href="{{ route('front.candidate.details', $candidate->unique_id) }}"
                                            class="text-gray-600 primary-link-hover">
                                            <h5 class="bg-white shadow rounded-lg overflow-hidden -title text-gray-600 fs-18 mb-0">{!! $candidate->user->full_name !!}
                                            </h5>
                                        </a>
                                        <div class="candidate-info relative mt-4">
                                            <div class="flex flex-wrap items-center mt-sm-0 ct">
                                                <div class="col-xl-6 md:w-6/12 flex-1 -sm-6">
                                                    <div class="candidate-info-desc flex">
                                                        <div class="me-3 icon-box">
                                                            <x-icons.briefcase class="w-full" />
                                                        </div>
                                                        <p class="fs-14 text-gray mb-0">
                                                            {{ !empty($candidate->industry) ? $candidate->industry->name : '' }}
                                                        </p>
                                                    </div>
                                                </div>
                                                <div class="col-xl-6 md:w-6/12 flex-1 -sm-6 mt-3 mt-sm-0">
                                                    <div class="candidate-info-desc flex">
                                                        <div class="me-3 icon-box">
                                                            <x-icons.location class="w-full" />
                                                        </div>
                                                        <p class="fs-14 text-gray mb-0">
                                                            {{ !empty($candidate->full_location) ? $candidate->full_location : '' }}
                                                        </p>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="flex flex-wrap items-center mt-3">
                                                <div class="col-xl-6 md:w-6/12 flex-1 -sm-6 mt-3 mt-sm-0">
                                                    <div class="candidate-info-desc flex">
                                                        <div class="me-3 icon-box">
                                                            <x-icons.money class="w-full" />
                                                        </div>
                                                        <p class="fs-14 text-gray mb-0">
                                                            {{ !empty($candidate->expected_salary) ? $candidate->currency->currency_icon . ' ' . $candidate->expected_salary : '' }}
                                                        </p>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="desc flex flex-wrap pt-2">
                                            <a href="{{ route('front.candidate.details', $candidate->unique_id) }}"
                                                class="text text-primary-600 mb-0 me-3">
                                                {{ __('web.web_jobSeeker.view_profile') }}
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="flex-1 -md-12 text-center text-gray">
                        @lang('web.job_menu.no_results_found')
                    </div>
                @endforelse
            </div>
            @if ($candidates->count() > 0)
                {{ $candidates->links() }}
            @endif
        </div>
    </div>
</div>
