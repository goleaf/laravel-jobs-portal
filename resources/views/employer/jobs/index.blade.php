@extends('employer.layouts.app')
@section('title')
    {{ __('messages.jobs')  }}
@endsection
@section('content')
    <div class="flex flex-col">
        @include('flash::message')
        <livewire:employer-job-table/>
    </div>
    {{ Form::hidden('indexEmployeeJobsData',true,['id'=>'indexEmployeeJobsData']) }}
    {{ Form::hidden('statusArray',json_encode($statusArray),['id'=>'employerJobStatusArray']) }}
@endsection

