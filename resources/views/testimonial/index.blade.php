@extends('layouts.app')
@section('title')
    {{ __('messages.testimonial.testimonials') }}
@endsection
@push('css')
    <link rel="stylesheet" href="{{ asset('css/header-padding.css') }}">
@endpush
@section('content')
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mx-auto px-4 mx-auto px-4 mx-auto px-4 mx-auto fluid">
        @include('flash::message')
        <div class="flex flex- flex-1">
            <livewire:testimonial-table/>
        </div>
        @include('testimonial.add_modal')
        @include('testimonial.edit_modal')
        @include('testimonial.show_modal')
        {{ Form::hidden('defaultDocumentImageUrl',asset('assets/img/infyom-logo.png') , ['id' => 'defaultDocumentImageUrl']) }}
    </div>
@endsection
@push('scripts')
    
{{ -- <script src="mix('assets/js/testimonial/testimonial.js') "></script> -- }}
@endpush

@push('scripts')
    @vite('resources/js/components/index.js')
@endpush
