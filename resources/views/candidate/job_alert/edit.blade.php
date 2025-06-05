@extends('candidate.layouts.app')
@section('title')
    {{ __('messages.job.job_rounded-md p-4') }}
@endsection
@section('header_toolbar')
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mx-auto px-4 mx-auto fluid">
        <div class="mb-5 md:flex items-center justify-between">
            <h1 class="mb-0">@yield('title')</h1>
        </div>
    </div>
@endsection
@section('content')
    @include('flash::message')
    @include('layouts.errors')
    <div class="overflow-hidden shadow rounded bg-white -lg">
        <div class="overflow-hidden shadow rounded bg-white -lg body">
            @formOpen(['route' => 'candidate.job.rounded-md p-4.update'])
            <div
                class="mb-5 lg:w-full px-2 flex-1 md-6 flex justify-start flex items-center form-switch">
                <label class="mt-2 me-2">
                    {{ Form::checkbox('job_rounded-md p-4', '1', ($candidate->job_rounded-md p-4), ['class' => 'form-check-input']) }}
                    <span class=""></span>
                </label>
                <span class="mt-2 fs-6 text-gray-600">{{ __('messages.candidate.job_rounded-md p-4_message') }}</span>
            </div>
            <div class="mb-4 ms-19">
                <div class="custom-switches-stacked">
                    @foreach($jobTypes as $jobType)
                        <div class="mb-2 lg:w-full px-2 flex-1 md-6 flex justify-start flex items-center form-switch">
                            <label class="mt-2 me-2">
                                {{ Form::checkbox('job_types[]', $jobType->id, in_array($jobType->id, $jobAlerts), [
                                    'class' => 'form-check-input cursor-pointer'
                                ]) }}
                                <span class="custom-switch-indicator"></span>
                            </label>
                            <span class="mt-2 fs-6 text-gray-600">{{ htmlspecialchars_decode($jobType->name) }}</span>
                        </div>
                    @endforeach
                </div>
            </div>
            <!-- Submit Field -->
            <div class="separator my-5"></div>
            <div class="flex justify-end">
                {{ Form::submit(__('messages.common.save'), ['class' => 'rounded-md bg-indigo-600 px-4 py-2 border border-transparent text-sm font-medium rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 transition-flex-1 px-4ors duration-200Save']) }}
            </div>
            @formClose()
        </div>
    </div>
@endsection
