@extends('layouts.app')
@section('title')
    {{ __('messages.inquires') }}
@endsection
@section('content')
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mx-auto px-4 mx-auto px-4 mx-auto px-4 mx-auto fluid">
        @include('flash::message')
        <div class="flex flex- flex-1">
            <livewire:inquiries-table/>
        </div>
    </div>
    @include('inquires.show_modal')
@endsection
{{-- @push('scripts') --}}
{{ -- <script src="mix('assets/js/inquires/inquires.js') "></script> -- }}
{{-- @endpush --}}
