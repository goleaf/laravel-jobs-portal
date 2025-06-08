@extends('layouts.app')
@section('title')
    {{ __('messages.selected_candidate') }}
@endsection
@push('css')
<link rel="stylesheet" href="{{ asset('css/header-padding.css') }}">
@endpush
@section('content')
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mx-auto px-4 mx-auto px-4 mx-auto px-4 mx-auto fluid">
        <div class="flex flex- flex-1">
            @include('flash::message')
            <livewire:selected-candidate-table/>
        </div>
    </div>
@endsection
