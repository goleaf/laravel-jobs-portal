@extends('layouts.app')
@section('title')
    {{ __('messages.job_type.job_types') }}
@endsection
@section('content')
    <div class="container mx-auto px-4 py-6">
        <div class="flex flex-col">
            <div class="flex justify-between items-center mb-4">
                <h1 class="text-2xl font-semibold text-gray-900">{{ __('messages.job_type.job_types') }}</h1>
                <button id="addJobTypeBtn" type="button" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-primary-600 hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500">
                    <x-icons.add class="w-5 h-5 mr-2" />
                    {{ __('messages.job_type.new_job_type') }}
                </button>
            </div>
            
            @include('flash::message')
            
            <div class="bg-white shadow-md rounded-lg overflow-hidden">
                <livewire:job-type-table />
            </div>
        </div>
    </div>
    @include('job-types.add_modal')
    @include('job-types.edit_modal')
    <input type="hidden" id="indexJobTypeData" value="true">
@endsection

@push('scripts')
    <script src="{{ asset('assets/js/job_types/job_types.js') }}"></script>
@endpush 