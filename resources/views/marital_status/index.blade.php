@extends('layouts.app')
@section('title')
    {{ __('messages.marital_statuses') }}
@endsection
@section('content')
    <div class="container mx-auto px-4 mx-auto -fluid">
        <div class="flex flex-col">
            @include('flash::message')
            <livewire:marital-status-table/>
        </div>
    </div>
    @include('marital_status.add_modal')
    @include('marital_status.edit_modal')
    @include('marital_status.show_modal')
    {{Form::hidden('maritalStatusData',true,['id'=>'indexMaritalStatusData'])}}
@endsection
{{--@push('scripts')--}}
{{--    <script src="{{ mix('assets/js/marital_status/marital_status.js')}}"></script>--}}
{{--@endpush--}}
