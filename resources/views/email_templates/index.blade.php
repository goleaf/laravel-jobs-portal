@extends('layouts.app')
@section('title')
    {{ __('messages.email_templates') }}
@endsection
@push('css')
    <link rel="stylesheet" href="{{ asset('css/header-padding.css') }}">
@endpush
@section('content')
    <div class="container mx-auto px-4 mx-auto fluid">
        <div class="flex flex-col">
            @include('flash::message')
            <livewire:email-template-table/>
        </div>
    </div>
@endsection
