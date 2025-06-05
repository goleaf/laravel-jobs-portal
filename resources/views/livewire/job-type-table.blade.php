@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 mx-auto px-4 mx-auto px-4 mx-auto mx-auto px-4 py-6">
    <div class="flex flex- flex-1 md:flex- flex flex-wrap justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-900">{{ __('messages.job_types') }}</h1>
        
        <div class="mt-4 md: mt-0">
            <button 
                onclick="Livewire.emit('openJobTypeModal')" 
                class="border border-gray-300 bg-transparent">
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