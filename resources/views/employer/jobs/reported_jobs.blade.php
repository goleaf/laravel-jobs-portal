@extends('layouts.app')
@section('title')
    {{ __('messages.reported_jobs') }}
@endsection
@push('css')
    <link rel="stylesheet" href="{{ asset('css/header-padding.css') }}">
@endpush
@section('content')
    <div class="container mx-auto px-4 mx-auto -fluid">
        <div class="flex flex-col">
            @include('flash::message')
            <livewire:reported-job-table />
        </div>
        @include('employer.jobs.show_reported_job_modal')
    </div>
@endsection
@push('scripts')
{{--    <script src="{{mix('assets/js/jobs/reported_jobs.js')}}"></script>--}}
@endpush

