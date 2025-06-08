@extends('layouts.app')

@section('title')
    {{ __('messages.job_types') }}
@endsection

@section('content')
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mx-auto px-4 mx-auto px-4 mx-auto px-4 mx-auto mx-auto px-4 py-6">
        <livewire:job-type-table />
    </div>
@endsection

@section('scripts')
    
@endsection

@push('scripts')
    @vite('resources/js/components/index.js')
@endpush
