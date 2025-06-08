@extends('layouts.app')
@section('title')
    {{ __('messages.job_type.job_types') }}
@endsection
@section('content')
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mx-auto px-4 mx-auto px-4 mx-auto px-4 mx-auto mx-auto px-4 py-6">
        <div class="flex flex- flex-1">
            <div class="flex justify-between items-center mb-4">
                <h1 class="text-2xl font-semibold text-gray-900">{{ __('messages.job_type.job_types') }}</h1>
                <button id="addJobTypeBtn" type="button" class="border border-gray-300 bg-transparent">
                    <x-icons.add class="w-5 h-5 mr-2" />
                    {{ __('messages.job_type.new_job_type') }}
                </button>
            </div>
            
            @include('flash::message')
            
            <div class="bg-white shadow-md rounded -lg overflow-hidden">
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