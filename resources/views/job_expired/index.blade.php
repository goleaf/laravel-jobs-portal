@extends('layouts.app')
@section('title')
    {{ __('messages.expired_jobs') }}
@endsection
@include('flash::message')
@section('content')
<div class="container mx-auto px-4 mx-auto -fluid">
    <div class="flex flex-col">
        @include('flash::message')
        <livewire:job-expired-table />
    </div>
</div>
@endsection
{{--@push('scripts')--}}
{{--    <script src="{{mix('assets/js/job_expired/job_expired.js')}}"></script>--}}
{{--@endpush--}}

