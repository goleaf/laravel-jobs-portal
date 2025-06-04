@extends('employer.layouts.app')
@section('title')
    {{ __('messages.job_stage.job_stage')  }}
@endsection
@section('content')
    <div class="flex flex-col">
        @include('flash::message')
        <livewire:job-stage-table/>
    </div>
    @include('employer.job_stages.add_modal')
    @include('employer.job_stages.edit_modal')
    @include('employer.job_stages.show_modal')
@endsection
{{ --@push('scripts')-- }}
{{ --    <script src="{{mix('assets/js/job_stages/job_stages.js') }}"></script>--}}
{{ --@endpush-- }}
