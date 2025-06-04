@extends('employer.layouts.app')
@section('title')
    {{ __('messages.job.job_details') }}
@endsection
@section('content')
    <section class="section">
        <div class="section-header">
            <h1>{{ __('messages.job.job_details') }}</h1>
            <div class="section-header-breadcrumb">
                <a href="{{ route('admin.jobs.edit',$job->id) }}"
                   class="inline-flex items-center px-4 py-2 border border-gray-300 border-transparent text-sm font-medium rounded-md transition duration-150 ease-in-out bg-yellow-500 text-white hover:bg-yellow-600 form- px-4 py-2 rounded font-medium transition-colors float-right mr-2">{{ __('messages.common.edit') }}</a>
                <a href="{{ route('job.index') }}"
                   class="inline-flex items-center px-4 py-2 border border-gray-300 border-transparent text-sm font-medium rounded-md transition duration-150 ease-in-out bg-primary-600 text-white hover: bg-primary-600 -700 form- px-4 py-2 rounded font-medium transition-colors float-right">{{ __('messages.common.back') }}</a>
            </div>
        </div>
        <div class="section-body">
            <div class="bg-white shadow rounded-lg overflow-hidden">
                <div class="bg-white shadow rounded-lg overflow-hidden -body">
                    @include('employer.jobs.show_fields')
                </div>
            </div>
        </div>
    </section>
@endsection
