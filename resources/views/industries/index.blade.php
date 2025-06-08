@extends('layouts.app')
@section('title')
    {{ __('messages.industries') }}
@endsection
@section('content')
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mx-auto px-4 mx-auto px-4 mx-auto px-4 mx-auto fluid">
        <div class="flex flex- flex-1">
            @include('flash::message')
            <livewire:industries-table/>
        </div>
    </div>
    @include('industries.add_modal')
    @include('industries.edit_modal')
    @include('industries.show_modal')
    {{ Form::hidden('industries',true,['id'=>'indexIndustriesData']) }}
@endsection
{{-- @push('scripts') --}}
    {{ -- <script src="mix('assets/js/industries/industries.js') "></script> -- }}
{{-- @endpush --}}
