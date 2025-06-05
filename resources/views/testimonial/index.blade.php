@extends('layouts.app')
@section('title')
    {{ __('messages.testimonial.testimonials') }}
@endsection
@push('css')
    <link rel="stylesheet" href="{{ asset('css/header-padding.css') }}">
@endpush
@section('content')
    <div class="container mx-auto px-4 mx-auto fluid">
        @include('flash::message')
        <div class="flex flex-col">
            <livewire:testimonial-table/>
        </div>
        @include('testimonial.add_modal')
        @include('testimonial.edit_modal')
        @include('testimonial.show_modal')
        {{ Form::hidden('defaultDocumentImageUrl',asset('assets/img/infyom-logo.png') , ['id' => 'defaultDocumentImageUrl']) }}
    </div>
@endsection
@push('scripts')
    <script>
        let testimonialImageSaveUrl ="{{ route('download.image') }}";
    </script>
{{ --    <script src="{{mix('assets/js/testimonial/testimonial.js') }}"></script>--}}
@endpush
