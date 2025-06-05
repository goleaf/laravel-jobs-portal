@extends('employer.layouts.app')
@section('title')
    {{ __('messages.job.job_details') }}
@endsection
@section('content')
    <section class="section">
        <div class="section-header">
            <h1>{{ __('messages.job.job_details') }}</h1>
            <div class="section-header-breadcrumb">
                <a href="{{ route('admin.',$job->id) }}"
                   class="border border-gray-300 bg-transparent">{{ __('messages.common.edit') }}</a>
                <a href="{{ route('employer.jobs.index') }}"
                   class="border border-gray-300 bg-transparent">{{ __('messages.common.back') }}</a>
            </div>
        </div>
        <div class="section-body">
            <div class="bg-white shadow rounded -lg overflow-hidden">
                <div class="bg-white shadow rounded -lg overflow-hidden body">
                    @include('employer.jobs.show_fields')
                </div>
            </div>
        </div>
    </section>
@endsection
