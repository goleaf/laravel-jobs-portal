@extends('layouts.app')
@section('title')
    {{ __('messages.expired_jobs') }}
@endsection
@include('flash::message')
@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mx-auto px-4 mx-auto fluid">
    <div class="flex-1 px-4 flex flex-">
        @include('flash::message')
        <livewire:job-expired-table />
    </div>
</div>
@endsection
{{-- @push('scripts') --}}
{{ -- <script src="mix('assets/js/job_expired/job_expired.js') "></script> -- }}
{{-- @endpush --}}

