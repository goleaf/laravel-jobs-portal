@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="flex flex-col md:flex-row justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-900">{{ __('messages.job_types') }}</h1>
        
        <div class="mt-4 md:mt-0">
            <button 
                onclick="Livewire.emit('openJobTypeModal')" 
                class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-primary-600 hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500">
                <x-icons.add class="w-5 h-5 mr-2" />
                {{ __('messages.job_type.new_job_type') }}
            </button>
        </div>
    </div>
    
    @include('job-types.add-modal')
    @include('job-types.edit-modal')
    
    @include('livewire.base-table')
</div>
@endsection

@push('scripts')
<script src="{{ asset('js/job_types/job_types.js') }}"></script>
@endpush 