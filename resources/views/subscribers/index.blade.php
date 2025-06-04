@extends('layouts.app')
@section('title')
    {{ __('messages.subscribers')  }}
@endsection
@push('css')
        <link rel="stylesheet" href="{{ asset('css/header-padding.css')  }}">
@endpush
@section('content')
<div class="container mx-auto px-4 mx-auto -fluid">
    <div class="flex flex-col">
        @include('flash::message')
        <livewire:subscriber-table/>
    </div>
</div>
@endsection
{{ --@push('scripts')-- }}
{{ --    <script src="{{mix('assets/js/subscribers/subscribers.js') }}"></script>--}}
{{ --@endpush-- }}
