@extends('layouts.app')
@section('title')
    {{ __('messages.job.new_job') }}
@endsection
@section('header_toolbar')
    <div class="container mx-auto px-4 mx-auto fluid">
        <div class="d-md-flex items-center justify-between mb-5">
            <h1 class="mb-0">@yield('title')</h1>
            <div class="text-end mt-4 mt-md-0">
                <a href="{{ route('admin.jobs.index') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 border-transparent text-sm font-medium rounded-md transition duration-150 ease-in-out px-4 py-2 rounded font-medium transition-colors outline-primary">{{ __('messages.common.back') }}</a>
            </div>
        </div>
    </div>
@endsection
@section('content')
    <div class="container mx-auto px-4 mx-auto fluid">
        <div class="flex flex-col">
            <div class="flex flex-wrap">
                <div class="flex-1 -12">
                    @include('layouts.errors')
                </div>
            </div>
            <div class="bg-white shadow rounded-lg overflow-hidden">
                <div class="bg-white shadow rounded-lg overflow-hidden body">
                    {{ Form::open(['route' => 'admin.jobs.store','id' => 'createJobForm']) }}

                    @include('jobs.fields')

                    {{ Form::close() }}
                </div>
            </div>
        </div>
        @include('jobs.modals.job_type')
        @include('jobs.modals.job_category')
        @include('jobs.modals.skills')
        @include('jobs.modals.salary_periods')
        @include('jobs.modals.countries')
        @include('jobs.modals.states')
        @include('jobs.modals.cities')
        @include('jobs.modals.career_levels')
        @include('jobs.modals.job_shifts')
        @include('jobs.modals.job_tags')
        @include('jobs.modals.required_degree_levels')
        @include('jobs.modals.functional_areas')
        {{ Form::hidden('employerPanel',false,['class'=>'jobEmployeePanel']) }}
        {{ Form::hidden('default-document-image-url', asset('front_web/images/job-categories.png'), ['id' => 'defaultDocumentImageUrl']) }}
        {{ Form::hidden('isEdit',false,['class'=>'isEdit']) }}
    </div>
@endsection
{{ --@push('scripts')-- }}
    {{ --    <script src="{{ asset('assets/js/autonumeric/autoNumeric.min.js') }}"></script>--}}
{{ --@endpush-- }}
