@extends('candidate.layouts.app')
@section('title')
    {{ __('messages.applied_job.applied_jobs') }}
@endsection
@section('content')
    @include('flash::message')
    <div class="flex-1 px-4 flex flex-">
        @livewire('applied-jobs')
    </div>
    @include('candidate.applied_job.show_applied_jobs_modal')
    @include('candidate.applied_job.schedule_slot_book')
@endsection
