@extends('candidate.layouts.app')
@section('title')
    {{ __('messages.job.job_alert') }}
@endsection
@section('header_toolbar')
    <div class="container mx-auto px-4 mx-auto -fluid">
        <div class="d-md-flex items-center justify-between mb-5">
            <h1 class="mb-0">@yield('title')</h1>
        </div>
    </div>
@endsection
@section('content')
    @include('flash::message')
    @include('layouts.errors')
    <div class="bg-white shadow rounded-lg overflow-hidden">
        <div class="bg-white shadow rounded-lg overflow-hidden -body">
            @formOpen(['route' => 'candidate.job.alert.update'])
            <div
                class="lg:w-full px-2 flex-1 -md-6 mb-5 flex justify-start flex items-center form-switch">
                <label class="mt-2 me-2">
                    {{ Form::checkbox('job_alert', '1', ($candidate->job_alert), ['class' => 'form-check-input']) }}
                    <span class=""></span>
                </label>
                <span class="mt-2 fs-6 text-gray-600">{{ __('messages.candidate.job_alert_message') }}</span>
            </div>
            <div class="form-group ms-19">
                <div class="custom-switches-stacked">
                    @foreach($jobTypes as $jobType)
                        <div class="lg:w-full px-2 flex-1 -md-6 mb-2 flex justify-start flex items-center form-switch">
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
                {{ Form::submit(__('messages.common.save'), ['class' => 'btn btn-primary me-3 btnSave']) }}
            </div>
            @formClose()
        </div>
    </div>
@endsection
