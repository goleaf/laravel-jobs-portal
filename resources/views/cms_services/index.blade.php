@extends('layouts.app')
@section('title')
    {{ __('messages.cms_services') }}
@endsection
@push('css')
    <link rel="stylesheet" href="{{ asset('css/header-padding.css') }}">
@endpush
@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mx-auto px-4 mx-auto px-4 mx-auto px-4 mx-auto fluid">
    <div class="flex flex- flex-1">
        @include('flash::message')
        @include('layouts.errors')
        <div class="bg-white shadow rounded -lg overflow-hidden">
            <div class="bg-white shadow rounded -lg overflow-hidden body">
                {{ Form::open(['route' => 'cms.services.update','files' => true]) }}
                @include('cms_services.fields')
                {{ Form::close() }}
            </div>
        </div>
    </div>
</div>
@endsection

