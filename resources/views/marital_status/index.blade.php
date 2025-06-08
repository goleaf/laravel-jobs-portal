@extends('layouts.app')
@section('title')
    {{ __('messages.marital_statuses') }}
@endsection
@section('content')
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mx-auto px-4 mx-auto fluid">
        <div class="flex-1 px-4 flex flex-">
            @include('flash::message')
            <livewire:marital-status-table/>
        </div>
    </div>
    @include('marital_status.add_modal')
    @include('marital_status.edit_modal')
    @include('marital_status.show_modal')
    {{ Form::hidden('maritalStatusData',true,['id'=>'indexMaritalStatusData']) }}
@endsection
{{-- @push('scripts') --}}
{{ -- <script src=" mix('assets/js/marital_status/marital_status.js') "></script> -- }}
{{-- @endpush --}}
