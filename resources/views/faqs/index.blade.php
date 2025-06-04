@extends('layouts.app')
@section('title')
    {{ __('messages.faq.faq')  }}
@endsection
@push('css')
    <link rel="stylesheet" href="{{ asset('css/header-padding.css')  }}">
@endpush
@section('content')
    <div class="container mx-auto px-4 mx-auto -fluid">
        <div class="flex flex-col">
            @include('flash::message')
            <livewire:f-a-q-table/>
        </div>
    </div>
    @include('faqs.add_modal')
    @include('faqs.edit_modal')
    @include('faqs.show_modal')
@endsection
@push('scripts')
{{ --    <script src="{{mix('assets/js/faqs/faqs.js') }}"></script>--}}
@endpush
