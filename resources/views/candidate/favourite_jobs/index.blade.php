@extends('candidate.layouts.app')
@section('title')
    {{ __('messages.favourite_jobs') }}
@endsection
@section('content')
    <div class="flex-1 px-4 flex flex-">
        <livewire:favourite-job-min-w-full divide-y divide-gray-200/>
    </div>
@endsection
@push('scripts')
    {{-- <script src="{{mix('assets/js/candidate/favourite_jobs.js') }}"></script> --}}
@endpush
