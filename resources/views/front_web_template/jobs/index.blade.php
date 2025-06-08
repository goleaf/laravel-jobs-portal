
@push('styles')
    @vite('resources/css/pages/index.css')
@endpush
@extends('front_web_template.layouts.app')
@section('title')
    {{ __('web.job_menu.search_job') }}
@endsection
@section('page_css')
    @if (\Illuminate\Support\Facades\App::getLocale() == 'ar')
        
    @endif
    {{ -- <link href="asset('front_web/scss/jobs.css') " rel="stylesheet" type="text/css"> -- }}
@endsection
@section('content')
    <div class="Find Jobs-page">
        <section class="hero-section relative bg-gradient pt-15 pb-40">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mx-auto px-4 mx-auto px-4 mx-auto px-4 mx-auto">
                <div class="flex flex-wrap items-center justify-center">
                    <div class="flex-1 lg-6 text-center mb-lg-0 mb-md-5 mb-sm-4">
                        <div class="hero-content">
                            <h1 class="text-gray-600 mb-3">
                                @lang('web.web_jobs.find_jobs')
                            </h1>
                            <nav aria-label="breadcrumb">
                                <ol class="breadcrumb justify-center mb-0">
                                    <li class="breadcrumb-item"><a href="{{ route('front.home') }}"
                                            class="fs-18 text-gray">@lang('web.home') </a>
                                    </li>
                                    <li class="breadcrumb-item text-indigo-600-600 fs-18" aria-current="page">@lang('web.jobs')
                                    </li>
                                </ol>
                            </nav>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <section class="latest-job-section py-60">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mx-auto px-4 mx-auto px-4 mx-auto px-4 mx-auto">
                <div class="flex flex-wrap">
                    <div class="flex-1 lg-4 px-lg-3">
                        <div class="latest-job-left br-10 px-40 bg-gray-100 mb-40">
                            <form>
                                <div class="mb-4 mb-md-4 mb-3">
                                    <div class="flex mb-3 justify-between flex-wrap">
                                        <label for="" class="fs-16 text-gray-600 mb-3">@lang('web.web_jobs.search_by_keywords')</label>
                                        <button
                                            class="border border-gray-300 bg-transparent">{{ __('web.reset_filter') }}</button>
                                    </div>
                                    <input type="text" class="w-full px-3 py-2 border border-gray-300 border border border-gray-300 -gray-300 -gray-300 rounded -md focus:outline-none focus:ring-2 focus:ring-primary-500 fs-14 text-gray bg-white br-10 p-3" value="{{ request()->input('keywords') }}"
                                       name="listing-search" id="searchByLocation"
                                       placeholder="@lang('web.web_home.job_title_keywords_company')">
                                </div>
                                <div class="mb-4 mb-md-4 mb-3">
                                    <label for="" class="fs-16 text-gray-600 mb-3">@lang('web.post_menu.categories')</label>
                                    <select class="w-full px-3 py-2 border border-gray-300 border border border-gray-300 -gray-300 -gray-300 rounded -md focus:outline-none focus:ring-2 focus:ring-primary-500 fs-14 text-gray bg-white br-10 p-3" aria-label="None"
                                        data-live-search="true" data-size="5" name="search-categories"
                                        id="searchCategories">
                                        <option value="">@lang('web.job_menu.none')</option>
                                        @foreach ($jobCategories as $key => $value)
                                            <option value="{{ $key }}"
                                                {{ request()->get('categories') == $key ? 'selected' : '' }}>
                                                {{ html_entity_decode($value) }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="mb-4 mb-md-3 mb-3">
                                    <label for="" class="fs-16 text-gray-600">
                                        @lang('messages.candidate.candidate_skill')</label>
                                    @if ($jobSkills->isNotEmpty())
                                        <select class="w-full px-3 py-2 border border-gray-300 border border border-gray-300 -gray-300 -gray-300 rounded -md focus:outline-none focus:ring-2 focus:ring-primary-500 fs-14 text-gray bg-white br-10 p-3" aria-label="None"
                                            data-live-search="true" data-size="5" name="search-skills" id="searchSkill">
                                            <option value="">@lang('web.job_menu.none')</option>
                                            @foreach ($jobSkills as $key => $value)
                                                <option value="{{ $key }}">
                                                    {{ html_entity_decode($value) }}
                                                </option>
                                            @endforeach
                                        </select>
                                    @endif
                                </div>
                                <div class="mb-4 mb-md-4 mb-3">
                                    <label for="" class="fs-16 text-gray-600 mb-3">@lang('messages.candidate.gender')</label>
                                    <select class="w-full px-3 py-2 border border-gray-300 border border border-gray-300 -gray-300 -gray-300 rounded -md focus:outline-none focus:ring-2 focus:ring-primary-500 fs-14 text-gray bg-white br-10 p-3" aria-label="None"
                                        data-live-search="true" data-size="5" name="search-gender" id="searchGender">
                                        <option value="">@lang('web.job_menu.none')</option>
                                        @foreach ($genders as $key => $value)
                                            <option value="{{ $key }}">
                                                {{ html_entity_decode($value) }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="mb-4 mb-md-4 mb-3">
                                    <label for="" class="fs-16 text-gray-600 mb-3">@lang('messages.job.career_level')</label>
                                    @if ($functionalAreas->isNotEmpty())
                                        <select class="w-full px-3 py-2 border border-gray-300 border border border-gray-300 -gray-300 -gray-300 rounded -md focus:outline-none focus:ring-2 focus:ring-primary-500 fs-14 text-gray bg-white br-10 p-3" aria-label="None"
                                            data-live-search="true" data-size="5" name="search-career-level"
                                            id="searchCareerLevel">
                                            <option value="">@lang('web.job_menu.none')</option>
                                            @foreach ($careerLevels as $key => $value)
                                                <option value="{{ $key }}">
                                                    {{ html_entity_decode($value) }}
                                                </option>
                                            @endforeach
                                        </select>
                                    @endif
                                </div>
                                <div class="mb-4 mb-md-4 mb-3">
                                    <label for="" class="fs-16 text-gray-600 mb-3">@lang('messages.job.functional_area')</label>
                                    @if ($functionalAreas->isNotEmpty())
                                        <select class="w-full px-3 py-2 border border-gray-300 border border border-gray-300 -gray-300 -gray-300 rounded -md focus:outline-none focus:ring-2 focus:ring-primary-500 fs-14 text-gray bg-white br-10 p-3" aria-label="None"
                                            data-live-search="true" data-size="5" name="search-functional-area"
                                            id="searchFunctionalArea">
                                            <option value="">@lang('web.job_menu.none')</option>
                                            @foreach ($functionalAreas as $key => $value)
                                                <option value="{{ $key }}">
                                                    {{ html_entity_decode($value) }}
                                                </option>
                                            @endforeach
                                        </select>
                                    @endif
                                </div>
                                @if ($jobTypes->isNotEmpty())
                                    <div class="mb-4 mb-md-4 mb-3">
                                        <label for="" class="fs-16 text-gray-600 mb-3">
                                            @lang('web.job_menu.type')
                                        </label>
                                        @foreach ($jobTypes as $key => $jobType)
                                            @if ($jobType->jobs_count > 0)
                                                @if (Str::length($jobType->name) < 50)
                                                    <div class="mb-4 flex justify-between mb-2">
                                                        <label class="flex items-center label fs-14 text-gray mb-2"
                                                            for="{{ $jobType->id }}">
                                                            {{ html_entity_decode($jobType->name) }}
                                                            {{ $jobType->jobs_count > 0 ? '(' . $jobType->jobs_count . ')' : '' }}
                                                        </label>
                                                        <div class="flex items-center">
                                                            <input class="flex items-center input jobType" type="checkbox"
                                                                role="switch" name="job-type" id="{{ $jobType->id }}"
                                                                value="{{ $jobType->id }}">
                                                        </div>
                                                    </div>
                                                @else
                                                    <div class="mb-4 flex justify-between">
                                                        <label class="flex items-center label fs-14 text-gray mb-2"
                                                            for="{{ $jobType->id }}" data-toggle="tooltip"
                                                            data-placement="bottom" title="{{ $jobType->name }}">
                                                            {{ html_entity_decode(Str::limit($jobType->name, 50, '...')) }}
                                                        </label>
                                                        <div class="flex items-center">
                                                            <input class="flex items-center input jobType" type="checkbox"
                                                                role="switch" name="job-type" id="{{ $jobType->id }}"
                                                                value="{{ $jobType->id }}">
                                                        </div>
                                                    </div>
                                                @endif
                                            @endif
                                        @endforeach
                                    </div>
                                @endif
                                <div class="mb-4 mb-md-4 mb-3">
                                    <label for="" class="fs-16 text-gray-600 mb-3">@lang('web.job_menu.salary_from'):</label>
                                    <input type="text" id="salaryFrom" autocomplete="off" class="slider"
                                        tabindex="-1" readonly="">
                                </div>
                                <div class="mb-4 mb-md-4 mb-3">
                                    <label for="" class="fs-16 text-gray-600 mb-3">@lang('web.job_menu.salary_to'):</label>
                                    <input type="text" id="salaryTo" autocomplete="off" class="slider2"
                                        tabindex="-1" readonly="">
                                </div>
                                <div class="mb-4">
                                    <label for="" class="fs-16 text-gray-600 mb-3">@lang('messages.candidate.experience'):</label>
                                    <input type="text" id="jobExperience" autocomplete="off" class="slider3"
                                        tabindex="-1" readonly="">
                                </div>
                        </div>
                        </form>
                        <div class="job-img mb-40">
                            <img src="{{ isset($advertise_image->value) ? $advertise_image->value : asset('front_web/images/job-img.png') }}"
                                class="w-full">
                        </div>
                    </div>
                    <div class="flex-1 lg-8 px-lg-3">
                        <div class="job- bg-white shadow rounded -lg overflow-hidden">
                            @livewire('job-search')
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
    {{ Form::hidden('jobType', json_encode($input), ['id' => 'input']) }}
@endsection
{{-- @section('page_scripts') --}}
{{--  --}}
{{-- @endsection --}}

@push('scripts')
    @vite('resources/js/pages/index.js')
@endpush
