@extends('layouts.app')
@section('title')
    {{ __('messages.job_notification.job_notifications') }}
@endsection
@section('content')
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mx-auto px-4 mx-auto fluid">
        @include('flash::message')
        <div class="overflow-hidden shadow rounded bg-white -lg">
        <div class="overflow-hidden shadow rounded border bg-white -lg header -0 pt-6 justify-end">
                <div class="ms-0 ms-md-2">
                    <div class="text-left relative inline-block flex items-center me-4 me-md-5">
                        <button
                                class="border border-gray-300 bg-transparent"
                                type="button" id="dropdownMenuButton1" data-bs-auto-close="outside"
                                data-bs-toggle="dropdown" aria-expanded="false">
                            <i class='fas fa-filter'></i>
                        </button>
                        <div class="shadow rounded mt-2 bg-white origin-top-right absolute right-0 w-56 -md -lg ring-1 ring-black ring-opacity-5 z-50 py-0" aria-labelledby="dropdownMenuButton1">
                            <div class="border text-start -bottom py-4 px-7">
                                <h3 class="mb-0 text-gray-900">{{ __('messages.common.filter_options') }}</h3>
                            </div>
                            <div class="p-5">

                                <div class="mb-5">
                                    <label class="mb-1 block text-sm font-medium text-gray-700 fs-6 fw-bold">{{ __('messages.employers').':' }}</label>
                                    {{ Form::select('employers', $companies,null, ['id' => 'filter_employers', 'data-control' =>'select2', 'class' => 'block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm status-selector select2-hidden-accessible data-allow-clear="true"','placeholder' => __('messages.flash.select_employer')]) }}
                                </div>
                                <div class="flex justify-end">
                                    <button class="border border-gray-300 bg-transparent"
                                            id="resetJobNotificationFilter"
                                            >{{ __('messages.common.reset') }}</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
        </div>
        <div class="overflow-hidden shadow rounded bg-white -lg body p-7">
            {{ Form::open(['route' => 'job-notification.store','id' => 'createJobNotificationForm']) }}
            @include('job_notification.send_notification')
            {{ Form::close() }}
        </div>
        </div>
    </div>
    
    {{ Form::hidden('getEmployerJobs',url('admin/employer-jobs'),['id'=>'indexGetEmployerJobs']) }}
    {{ Form::hidden('jobDetails',url('admin/jobs'),['id'=>'indexJobDetails']) }}
    {{ Form::hidden('jobNotification',url('admin/job-notifications'),['id'=>'indexJobNotification']) }}
@endsection
@push('scripts')
    
    {{-- <script src="{{mix('assets/js/jobs/job_notification.js') }}"></script> --}}
@endpush


@push('scripts')
    @vite('resources/js/components/index.js')
@endpush
