@extends('candidate.layouts.app')
@section('title')
    {{ __('messages.candidate.dashboard') }}
@endsection
@push('css')
    <link rel="stylesheet" href="{{ asset('assets/css/candidate-dashboard.css') }}">
@endpush
@section('content')
@include('flash::message')
<div class="overflow-hidden shadow rounded mb-5 bg-white -lg mb-xl-10">
    <div class="overflow-hidden shadow rounded pb-0 bg-white -lg body pt-9">
        <div class="flex-wrap mb-3 flex flex-sm-nowrap">
            <div class="mb-4 me-7">
                <div class="">
                    <img height="150" width="150" src="{{ getCompanyLogo() }}" alt="image"
                         style="object-fit: cover">
                </div>
            </div>
            <div class="flex-wrap flex-gflex -mx-4-1">
                <div class="flex-wrap mb-2 flex justify-between items-start">
                    <div class="flex-1 px-4 flex flex-">
                        <div class="mb-2 items-center">
                            <a class="text-gray-900 text-hover-primary fs-2 me-1 text-decoration-none">{{ html_entity_decode($user->full_name) }}</a>
                        </div>
                        <div class="flex-wrap mb-4 fs-6 pe-2">
                            <a class="mb-2 flex items-center text-gray-600 text-hover-primary me-5 text-decoration-none">
                                <i class="fa fa-phone"></i>&nbsp;
                                {{ !empty($user->phone) ?  $user->phone : __('messages.candidate_dashboard.no_not_available') }}
                            </a>
                            <a class="mb-2 flex items-center text-gray-600 text-hover-primary me-5 text-decoration-none">
                                <i class="fa-solid fa-location-dot fs-3 me-2"></i>
                                {{ !empty($candidate->city_name) ?  $candidate->city_name. ', '  .$candidate->state_name . ', ' . $candidate->country_name : (!empty($candidate->country_id) ? $candidate->country_name :   __('messages.candidate_dashboard.location_information')) }}
                            </a>
                            <a class="mb-2 flex items-center text-gray-600 text-hover-primary text-decoration-none">
                                <i class="fa-solid fa-envelope  me-2"></i>
                                {{ $user->email }}</a>
                        </div>
                    </div>
                    <div class="flex my-4">
                        <a href="{{ route('candidate.dashboard') }}" class="border border-gray-300 bg-transparent">
                            {{ __('messages.user.edit_profile') }}
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="flex-wrap flex g-5 g-xl-8">
    <div class="flex-1 px-4 flex-1 px-4 -xxl-4 -xl-4 flex-1 sm-6 widget">
        <div class="shadow rounded bg-green-600 -md -10 p-xxl-10 px-5 py-10 flex items-center justify-between my-sm-3 my-2">
            <div class="rounded bg-green-300 widget-icon -10 me-2 flex items-center justify-center">
                <i class="fa fa-eye text-white fs-1-xl"></i>
            </div>
            <div class="text-end text-white">
                <h2 class="fs-1-xxl text-white">{{ numberFormatShort($user->profile_views) }}</h2>
                <h3 class="mb-0 fs-4 fw-light fs-1-xl">{{ __('messages.candidate_dashboard.profile_views') }}</h3>
            </div>
        </div>
    </div>
    <div class="flex-1 px-4 flex-1 px-4 -xxl-4 -xl-4 flex-1 sm-6 widget">
        <a href="{{ route('favourite.companies') }}" class="text-decoration-none">
        <div class="shadow rounded bg-gray-800 -md -10 p-xxl-10 px-5 py-10 flex items-center justify-between my-sm-3 my-2">
            <div class="rounded bg-gray-700 widget-icon -10 me-2 flex items-center justify-center">
                <i class="fas fa-users  fs-1-xl {{ getLoggedInUser()->theme_mode ? 'text-gray-500' : 'text-white' }}"></i>
            </div>
            <div class="text-gray-100 text-end">
                <h2 class="text-gray-100 fs-1-xxl">{{ numberFormatShort($followings) }}</h2>
                <h3 class="mb-0 fs-4 fw-light fs-1-xl">{{ __('messages.candidate_dashboard.followings') }}</h3>
            </div>
        </div>
        </a>
    </div>
    <div class="flex-1 px-4 flex-1 px-4 -xxl-4 -xl-4 flex-1 sm-6 widget">
        <div class="shadow rounded bg-yellow-1/20 -md -10 p-xxl-10 px-5 py-10 flex items-center justify-between my-sm-3 my-2">
            <div class="rounded bg-yellow-300 widget-icon -10 me-2 flex items-center justify-center">
                <i class="fa fa-briefcase fs-1-xl text-white"></i>
            </div>
            <div class="text-end text-white">
                <h2 class="fs-1-xxl text-white">{{ numberFormatShort($resumes) }}</h2>
                <h3 class="mb-0 fs-4 fw-light">{{ __('messages.apply_job.resume') }}</h3>
            </div>
        </div>
    </div>
</div>

@endsection
