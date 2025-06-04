@extends('layouts.app')
@section('title')
    {{ __('messages.job_notification.job_notifications')  }}
@endsection
@section('content')
    <div class="container mx-auto px-4 mx-auto -fluid">
        @include('flash::message')
        <div class="bg-white shadow rounded-lg overflow-hidden">
        <div class="bg-white shadow rounded-lg overflow-hidden -header border-0 pt-6 justify-end">
                <div class="ms-0 ms-md-2">
                    <div class="relative inline-block text-left flex items-center me-4 me-md-5">
                        <button
                                class="inline-flex items-center px-4 py-2 border border-gray-300 border-transparent text-sm font-medium rounded-md transition duration-150 ease-in-out inline-flex items-center px-4 py-2 border border-gray-300 border-transparent text-sm font-medium rounded-md transition duration-150 ease-in-out btn-icon px-4 py-2 rounded font-medium transition-colors -primary text-white inline-flex justify-center w-full rounded-md border border-gray-300 border-gray-300 shadow-sm px-4 py-2 bg-white text-sm font-medium text-gray-700 hover:bg-gray-50 hide-arrow ps-2 pe-0"
                                type="button" id="dropdownMenuButton1" data-bs-auto-close="outside"
                                data-bs-toggle="dropdown" aria-expanded="false">
                            <i class='fas fa-filter'></i>
                        </button>
                        <div class="origin-top-right absolute right-0 mt-2 w-56 rounded-md shadow-lg bg-white ring-1 ring-black ring-opacity-5 z-50 py-0" aria-labelledby="dropdownMenuButton1">
                            <div class="text-start border-bottom py-4 px-7">
                                <h3 class="text-gray-900 mb-0">{{ __('messages.common.filter_options')  }}</h3>
                            </div>
                            <div class="p-5">

                                <div class="mb-5">
                                    <label class="block text-sm font-medium text-gray-700 mb-1 fs-6 fw-bold">{{ __('messages.employers').':'  }}</label>
                                    {{ Form::select('employers', $companies,null, ['id' => 'filter_employers', 'data-control' =>'select2', 'class' => 'form-select status-selector select2-hidden-accessible data-allow-clear="true"','placeholder' => __('messages.flash.select_employer')])  }}
                                </div>
                                <div class="flex justify-end">
                                    <button class="inline-flex items-center px-4 py-2 border border-gray-300 border-transparent text-sm font-medium rounded-md transition duration-150 ease-in-out px-4 py-2 rounded font-medium transition-colors -secondary"
                                            id="resetJobNotificationFilter"
                                            >{{ __('messages.common.reset') }}</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
        </div>
        <div class="bg-white shadow rounded-lg overflow-hidden -body p-7">
            {{ Form::open(['route' => 'job-notification.store','id' => 'createJobNotificationForm'])  }}
            @include('job_notification.send_notification')
            {{ Form::close()  }}
        </div>
        </div>
    </div>
    
    {{ Form::hidden('getEmployerJobs',url('admin/employer-jobs'),['id'=>'indexGetEmployerJobs']) }}
    {{ Form::hidden('jobDetails',url('admin/jobs'),['id'=>'indexJobDetails']) }}
    {{ Form::hidden('jobNotification',url('admin/job-notifications'),['id'=>'indexJobNotification']) }}
@endsection
@push('scripts')
    <script>
        let getEmployerJobs = "{{ url('admin/employer-jobs')  }}";
        let jobDetails = "{{ url('admin/jobs')  }}";
        let jobNotification = "{{ url('admin/job-notifications')  }}";
    </script>
    {{ --    <script src="{{mix('assets/js/jobs/job_notification.js') }}"></script>--}}
@endpush

