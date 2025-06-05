@extends('layouts.app')
@section('title')
    {{ __('messages.cms_services') }}
@endsection
@push('css')
    <link rel="stylesheet" href="{{ asset('css/header-padding.css') }}">
@endpush
@section('content')
<div class="container mx-auto px-4 mx-auto fluid">
    <div class="flex flex-col">
        @include('flash::message')
        @include('layouts.errors')
        <div class="bg-white shadow rounded-lg overflow-hidden">
            <div class="bg-white shadow rounded-lg overflow-hidden body">
                {{ Form::open(['route' => 'cms.services.update','files' => true]) }}
                @include('cms_services.fields')
                {{ Form::close() }}
            </div>
        </div>
    </div>
</div>
@endsection

