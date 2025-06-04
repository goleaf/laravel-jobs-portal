@extends('candidate.layouts.app')
@section('title')
    {{ __('messages.favourite_jobs') }}
@endsection
@section('content')
    <div class="flex flex-column">
        <livewire:favourite-job-table/>
    </div>
@endsection
@push('scripts')
    {{--    <script src="{{mix('assets/js/candidate/favourite_jobs.js')}}"></script>--}}
@endpush
